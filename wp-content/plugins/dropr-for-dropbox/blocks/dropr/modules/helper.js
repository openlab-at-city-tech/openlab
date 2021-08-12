class DroprHelper {
    static isValidMSExtension( url ) {
        const msExtensions = ["doc", "pot", "pps", "ppt", "xla", "xls", "xlt", "xlw", "docx", "dotx", "dotm", "xlsx", "xlsm", "pptx"];
        const substr = typeof url !== 'undefined' ? url.split( '.' ).pop() : '';
        let ext = substr.replace( "?dl=0", "" );
        ext = ext.replace( "?raw=1", "" );
        return jQuery.inArray( ext, msExtensions ) !== -1;
    }
}

export default DroprHelper;