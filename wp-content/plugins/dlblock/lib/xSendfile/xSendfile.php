<?php 
namespace XSendfile;

class XSendfile
{

    const SERVER_TYPE_APACHE = "Apache";
    const SERVER_TYPE_NGINX = "Nginx";
    const SERVER_TYPE_LIGHTTPD = "Lighttpd";

    public static function xSendfile($file, $downFilename=null, $serverType=null, $cache=true)
    {
        if($cache){
            if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
                $modifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
                $modifiedSince = strtotime($modifiedSince);
                if(filemtime($file)==$modifiedSince){
                    header("HTTP/1.1 304: Not Modified");
                    exit;
                }
            }

            if(isset($_SERVER['IF-NONE-MATCH']) && $_SERVER['IF-NONE-MATCH']==md5(filemtime($file))){
                header("HTTP/1.1 304: Not Modified");
                exit;
            }
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file);
        if($mime){
            header("Content-type: $mime");
        }else{
            header("Content-type: application/octet-stream");
        }

        if($downFilename){
            $filename = $downFilename;
        }else{
            $filename = basename($file);
        }
        $encodedFilename = rawurlencode($filename);
        $userAgent = $_SERVER["HTTP_USER_AGENT"]; 
        // support ie
        if(preg_match("/MSIE/", $userAgent) || preg_match("/Trident\/7.0/", $userAgent)) {
            header('Content-Disposition: attachment; filename="' . $encodedFilename . '"');
        // support firefox
        } else if (preg_match("/Firefox/", $userAgent)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $encodedFilename . '"');
        // support safari and chrome
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
   
        header("Content-Length: ". filesize($file));

        if($cache){
            header("Last-Modified: ". gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
            header("Expires: ". gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
            header("Cache-Control: max-age=2592000");
            header('Etag: " ' . md5(filemtime($file)) . '"');
        }
        
        if($serverType){
            switch ($serverType) {
                case self::SERVER_TYPE_APACHE:
                    header("X-Sendfile: $file");
                    break;
                case self::SERVER_TYPE_NGINX:
                    header("X-Accel-Redirect: $file");
                    break;
                case self::Lighttpd:
                    header("X-LIGHTTPD-send-file: $file");
                    break;
                    
                default:
                    # code...
                    break;
            }
        }else{
            ob_clean();
            flush();
            // unknown server , use php stream
            readfile($file);
        }
    }
}