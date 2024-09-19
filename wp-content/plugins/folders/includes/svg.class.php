<?php
if(!class_exists('enshrined\svgSanitize\Sanitizer') ) {
    $svgFiles = array(
        'libraries/svg-sanitizer/src/data/AttributeInterface.php',
        'libraries/svg-sanitizer/src/data/TagInterface.php',
        'libraries/svg-sanitizer/src/data/AllowedAttributes.php',
        'libraries/svg-sanitizer/src/data/AllowedTags.php',
        'libraries/svg-sanitizer/src/data/XPath.php',
        'libraries/svg-sanitizer/src/ElementReference/Resolver.php',
        'libraries/svg-sanitizer/src/ElementReference/Subject.php',
        'libraries/svg-sanitizer/src/ElementReference/Usage.php',
        'libraries/svg-sanitizer/src/Exceptions/NestingException.php',
        'libraries/svg-sanitizer/src/Helper.php',
        'libraries/svg-sanitizer/src/Sanitizer.php'
    );

    foreach( $svgFiles as $svgFile ) {
        $real_path = WCP_FOLDERS_PLUGIN_PATH . $svgFile;
        if(file_exists( $real_path ) ) {
            require_once( $real_path );
        }
    }
}

if(!function_exists('isGzipped') ) {
    function isGzipped( $data ){
        if( function_exists('mb_strpos') ){
            return 0 === mb_strpos( $data, "\x1f" . "\x8b" . "\x08" );
        }else{
            return 0 === strpos( $data, "\x1f" . "\x8b" . "\x08" );
        }
    }
}

if(!function_exists('sanitizeSvgFileContent')) {
    function sanitizeSvgFileContent($filePath)
    {
        $data = file_get_contents( $filePath );

        $is_zipped = isGzipped( $data );
        if( $is_zipped ){
            $data = gzdecode( $data );
            if( $data === false ){
                return false;
            }
        }

        // load sanitizer
        $sanitizer = new \enshrined\svgSanitize\Sanitizer();
        $sanitizer->removeXMLTag( true );
        $sanitizer->minify( true );

        $clean = $sanitizer->sanitize( $data );

        if( $clean === false ){

            return false;
        }

        if( $is_zipped ){
            $clean = gzencode( $clean );
        }

        // Save cleaned file
        file_put_contents( $filePath, $clean );

        return true;
    }
}