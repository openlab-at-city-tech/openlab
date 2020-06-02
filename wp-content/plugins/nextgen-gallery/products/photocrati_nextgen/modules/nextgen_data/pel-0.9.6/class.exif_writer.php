<?php

// This file resides in the PEL directory so that it is not processed during the NextGen
// build process as the NGG package files cannot contain PHP 5.3+ code yet. See the
// C_Exif_Writer_Wrapper class which loads this file outside of the POPE module system.

/*
 * TAKE NOTE: when upgrading PEL check that the changes made to PelIfd.php in commit 7317 / 825b17c599b6
 * have been applied or adopted from upstream.
 */

require_once('autoload.php');

use lsolesen\pel\PelDataWindow;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelEntryShort;

use lsolesen\pel\PelInvalidArgumentException;
use lsolesen\pel\PelIfdException;
use lsolesen\pel\PelInvalidDataException;
use lsolesen\pel\PelJpegInvalidMarkerException;

class C_Exif_Writer
{
    /**
     * @param $filename
     * @return array|null
     */
    static public function read_metadata($filename)
    {
        if (!self::is_jpeg_file($filename))
            return NULL;

        try {
            $data = new PelDataWindow(@file_get_contents($filename));
            $exif = new PelExif();

            if (PelJpeg::isValid($data)) {
                $jpeg = $file = new PelJpeg();
                $jpeg->load($data);
                $exif = $jpeg->getExif();

                if ($exif === NULL) {
                    $exif = new PelExif();
                    $jpeg->setExif($exif);

                    $tiff = new PelTiff();
                    $exif->setTiff($tiff);
                } else {
                    $tiff = $exif->getTiff();
                }

            } elseif (PelTiff::isValid($data)) {
                $tiff = $file = new PellTiff();
                $tiff->load($data);
            } else {
                return NULL;
            }

            $ifd0 = $tiff->getIfd();
            if ($ifd0 === NULL) {
                $ifd0 = new PelIfd(PelIfd::IFD0);
                $tiff->setIfd($ifd0);
            }
            $tiff->setIfd($ifd0);
            $exif->setTiff($tiff);

            $retval = array(
                'exif' => $exif,
                'iptc' => NULL
            );

            @getimagesize($filename, $iptc);
            if (!empty($iptc['APP13']))
                $retval['iptc'] = $iptc['APP13'];
        }
        catch (PelIfdException $exception)               { return NULL; }
        catch (PelInvalidArgumentException $exception)   { return NULL; }
        catch (PelInvalidDataException $exception)       { return NULL; }
        catch (PelJpegInvalidMarkerException $exception) { return NULL; }
        catch (Exception $exception)                     { return NULL; }

        return $retval;
    }

    /**
     * @param $origin_file
     * @param $destination_file
     * @return bool|int FALSE on failure or (int) number of bytes written
     */
    static public function copy_metadata($origin_file, $destination_file)
    {
        if (!self::is_jpeg_file($origin_file))
            return FALSE;

        // Read existing data from the source file
        $metadata = self::read_metadata($origin_file);
        if (!empty($metadata) && is_array($metadata))
            return self::write_metadata($destination_file, $metadata);
        else
            return FALSE;
    }

    /**
     * @param $filename
     * @param $metadata
     * @return bool|int FALSE on failure or (int) number of bytes written
     */
    static public function write_metadata($filename, $metadata)
    {
        if (!self::is_jpeg_file($filename) || !is_array($metadata))
            return FALSE;

        try {
            // Copy EXIF data to the new image and write it
            $new_image = new PelJpeg($filename);
            $new_image->setExif($metadata['exif']);
            $new_image->saveFile($filename);

            // Copy IPTC / APP13 to the new image and write it
            if ($metadata['iptc'])
            {
                return self::write_IPTC($filename, $metadata['iptc']);
            }
        }
        catch (PelInvalidArgumentException $exception) {
            return FALSE;
        }
        catch (PelJpegInvalidMarkerException $exception) {
            return FALSE;
        }
    }

    /**
     * @param string $filename
     * @param array $data
     * @return bool|int FALSE on failure or (int) number of bytes written
     */
    static public function write_IPTC($filename, $data)
    {
        if (!self::is_jpeg_file($filename))
            return FALSE;

        $length = strlen($data) + 2;

        // Avoid invalid APP13 regions
        if ($length > 0xFFFF)
            return FALSE;

        // Wrap existing data in segment container we can embed new content in
        $data = chr(0xFF) . chr(0xED) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF) . $data;

        $new_file_contents = @file_get_contents($filename);

        if (!$new_file_contents || strlen($new_file_contents) <= 0)
            return FALSE;

        $new_file_contents = substr($new_file_contents, 2);

        // Create new image container wrapper
        $new_iptc = chr(0xFF) . chr(0xD8);

        // Track whether content was modified
        $new_fields_added = !$data;

        // This can cause errors if incorrectly pointed at a non-JPEG file
        try {
            // Loop through each JPEG segment in search of region 13
            while ((substr($new_file_contents, 0, 2) & 0xFFF0) === 0xFFE0) {

                $segment_length = (substr($new_file_contents, 2, 2) & 0xFFFF);
                $segment_number = (substr($new_file_contents, 1, 1) & 0x0F);

                // Not a segment we're interested in
                if ($segment_length <= 2)
                    return FALSE;

                $current_segment = substr($new_file_contents, 0, $segment_length + 2);

                if ((13 <= $segment_number) && (!$new_fields_added)) {
                    $new_iptc .= $data;
                    $new_fields_added = TRUE;
                    if (13 === $segment_number)
                        $current_segment = '';
                }

                $new_iptc .= $current_segment;
                $new_file_contents = substr($new_file_contents, $segment_length + 2);
            }
        } catch (Exception $exception) {
            return FALSE;
        }

        if (!$new_fields_added)
            $new_iptc .= $data;

        if ($file = @fopen($filename, 'wb'))
            return @fwrite($file, $new_iptc . $new_file_contents);
        else
            return FALSE;
    }

    /**
     * Determines if the file extension is .jpg or .jpeg
     *
     * @param $filename
     * @return bool
     */
    static public function is_jpeg_file($filename)
    {
        $extension = M_I18n::mb_pathinfo($filename, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), array('jpeg', 'jpg', 'jpeg_backup', 'jpg_backup')) ? TRUE : FALSE;
    }

    /**
     * Sets the EXIF' Orientation field to 1 aka Default or "TopLeft"
     *
     * This method is necessary to prevent images rotated by NextGen to appear even further rotated.
     * @param array $exif
     * @return array
     */
    static public function reset_orientation($exif = array())
    {
        $tiff = $exif->getTiff();
        if (empty($tiff))
            return $exif;

        $ifd0 = $tiff->getIfd();
        if (empty($ifd0))
            return $exif;

        $orientation = $ifd0->getEntry(PelTag::ORIENTATION);
        if (empty($orientation))
            return $exif;

        $orientation = new PelEntryShort(PelTag::ORIENTATION, 1);
        $ifd0->addEntry($orientation);

        return $exif;
    }
}