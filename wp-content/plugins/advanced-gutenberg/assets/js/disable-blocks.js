window.addEventListener('load', function () {
    if (typeof wp.blocks !== 'undefined') {
        if ( wp.data.select("core/blocks").getBlockType( "advgb/summary" ) ) {
            wp.blocks.unregisterBlockType( "advgb/summary" );
        }
    }
});
