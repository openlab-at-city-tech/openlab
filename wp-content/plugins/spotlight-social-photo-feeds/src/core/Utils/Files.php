<?php

namespace RebelCode\Spotlight\Instagram\Utils;

use RebelCode\Spotlight\Instagram\ErrorLog;
use RuntimeException;
use Throwable;

/**
 * Utility functions for dealing with files.
 *
 * @since 0.4.1
 */
class Files
{
    /**
     * Deletes a directory, recursively.
     *
     * @since 0.4.1
     *
     * @param string $path The absolute path to the directory to delete.
     */
    public static function rmDirRecursive(string $path)
    {
        $dir = @opendir($path);
        if (!is_resource($dir)) {
            ErrorLog::message('Unable to open directory: ' . $path);
            return;
        }

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $path . '/' . $file;
                if (is_dir($full)) {
                    static::rmDirRecursive($full);
                } else {
                    @unlink($full);
                }
            }
        }
        closedir($dir);
        @rmdir($path);
    }

    /**
     * Downloads a remote file.
     *
     * @param string $url The URL that points to the resource to be downloaded.
     * @param string $filepath The path to the file to which the resource will be downloaded to.
     */
    public static function download(string $url, string $filepath)
    {
        $curl = curl_init($url);

        if (!$curl) {
            throw new RuntimeException(
                'Unable to initialize curl. Please check if the curl extension is enabled.'
            );
        }

        $file = @fopen($filepath, 'wb');

        if (!$file) {
            throw new RuntimeException(
                'Failed to open file for download: ' . $filepath
            );
        }

        try {
            // SET UP CURL
            {
                curl_setopt($curl, CURLOPT_FILE, $file);
                curl_setopt($curl, CURLOPT_FAILONERROR, true);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_ENCODING, '');
                curl_setopt($curl, CURLOPT_TIMEOUT, 10);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                }
            }

            $success = curl_exec($curl);

            if (!$success) {
                throw new RuntimeException(
                    'File download failed: ' . curl_error($curl)
                );
            }
        } catch (Throwable $e) {
            ErrorLog::exception($e);
        } finally {
            curl_close($curl);
            fclose($file);
        }
    }
}
