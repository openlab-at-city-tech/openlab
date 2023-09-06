(function(wp){
    var el = wp.element.createElement,
        registerBlockType = wp.blocks.registerBlockType,
        withSelect = wp.data.withSelect,
        BlockControls = wp.editor.BlockControls,
        AlignmentToolbar = wp.editor.AlignmentToolbar,
        InspectorControls = wp.blocks.InspectorControls,
        ServerSideRender = wp.components.ServerSideRender,
        __ = wp.i18n.__,
        Text = wp.components.TextControl,
        aysSelect = wp.components.SelectControl,
        createBlock = wp.blocks.createBlock,
        select = wp.data.select,
        dispatch = wp.data.dispatch;

    var iconEl = el(
        'svg', 
        { 
            width: 24,
            height: 24,
            viewBox: '0 0 297 297',
            style: {
                transform: 'rotate(135deg)'
            }
        },
        el(
            'path',
            { 
                d: "m293.98 118.57c-0.989-4.833-2.213-9.581-3.659-14.231-0.362-1.162-0.737-2.319-1.126-3.469-0.778-2.3-1.612-4.575-2.498-6.823-0.443-1.124-0.9-2.241-1.369-3.352-1.409-3.331-2.936-6.6-4.576-9.802-1.093-2.134-2.237-4.239-3.429-6.312-5.96-10.365-13.135-19.943-21.331-28.539-1.639-1.719-3.319-3.399-5.038-5.038-8.596-8.196-18.174-15.371-28.539-21.331-2.073-1.192-4.178-2.336-6.312-3.429-3.202-1.64-6.471-3.167-9.802-4.576-1.11-0.47-2.228-0.926-3.352-1.369-2.248-0.886-4.523-1.72-6.823-2.498-1.15-0.389-2.306-0.765-3.469-1.126-4.65-1.446-9.398-2.67-14.231-3.659-9.668-1.979-19.677-3.018-29.929-3.018s-20.261 1.039-29.928 3.017c-4.833 0.989-9.581 2.213-14.231 3.659-1.162 0.362-2.319 0.737-3.469 1.126-2.3 0.778-4.575 1.612-6.823 2.498-1.124 0.443-2.241 0.9-3.352 1.369-3.331 1.409-6.6 2.936-9.802 4.576-2.134 1.093-4.239 2.237-6.312 3.429-10.365 5.961-19.943 13.136-28.539 21.331-1.719 1.639-3.399 3.319-5.038 5.038-8.196 8.597-15.371 18.175-21.331 28.54-1.192 2.073-2.336 4.178-3.429 6.312-1.64 3.202-3.167 6.471-4.576 9.802-0.47 1.11-0.926 2.228-1.369 3.352-0.886 2.248-1.72 4.523-2.498 6.823-0.389 1.15-0.765 2.306-1.126 3.469-1.446 4.65-2.67 9.398-3.659 14.231-1.979 9.667-3.018 19.676-3.018 29.928 0 21.785 4.691 42.474 13.118 61.113 0.991 2.193 2.035 4.357 3.128 6.492 1.64 3.202 3.393 6.336 5.253 9.398 1.24 2.041 2.528 4.05 3.863 6.025 0.667 0.988 1.346 1.967 2.036 2.937 1.38 1.941 2.806 3.847 4.276 5.717s2.983 3.704 4.539 5.5 3.154 3.555 4.793 5.274 3.319 3.399 5.038 5.038c8.596 8.196 18.174 15.371 28.539 21.331 2.073 1.192 4.178 2.335 6.312 3.429 3.202 1.64 6.471 3.167 9.802 4.576 1.11 0.47 2.228 0.926 3.352 1.369 2.248 0.886 4.523 1.72 6.823 2.498 1.15 0.389 2.306 0.765 3.469 1.126 4.65 1.446 9.398 2.67 14.231 3.659 9.667 1.978 19.676 3.017 29.928 3.017s20.261-1.039 29.928-3.017c4.833-0.989 9.581-2.213 14.231-3.659 1.162-0.362 2.319-0.737 3.469-1.126 2.3-0.778 4.575-1.612 6.823-2.498 1.124-0.443 2.241-0.9 3.352-1.369 3.331-1.409 6.6-2.936 9.802-4.576 2.134-1.093 4.239-2.237 6.312-3.429 10.365-5.96 19.943-13.135 28.539-21.331 1.719-1.639 3.399-3.319 5.038-5.038s3.237-3.478 4.793-5.274 3.07-3.63 4.539-5.5c1.47-1.87 2.895-3.776 4.276-5.717 0.69-0.97 1.369-1.949 2.036-2.937 1.334-1.975 2.622-3.984 3.863-6.025 1.86-3.062 3.613-6.196 5.253-9.398 1.093-2.135 2.136-4.299 3.128-6.492 8.427-18.639 13.118-39.328 13.118-61.113 0-10.252-1.039-20.261-3.017-29.928zm-159.82-69.034c0-7.698 6.302-13.938 14-13.938s14 6.24 14 13.938v42.095c0 7.698-6.302 13.938-14 13.938s-14-6.24-14-13.938v-42.095zm109.32 102.7c-2.609 49.571-43.087 89.358-92.696 91.101-54.66 1.921-99.868-41.992-99.868-96.236 0-29.416 13.742-55.79 34.349-73.467 8.148-6.99 20.743-1.228 20.743 9.507 0 3.708-1.662 7.201-4.485 9.606-15.281 13.024-25.284 32.549-25.284 54.353 0 39.125 32.05 70.956 71.175 70.956s70.919-31.831 70.919-70.956c0-21.124-8.839-40.118-23.92-53.128-2.802-2.417-4.45-5.903-4.45-9.603 0-10.838 12.8-16.648 20.923-9.473 21.176 18.705 34.213 46.571 32.594 77.34z",
                fill: '#e84c3d'
            } 
        )
    );

    if( wp.blocks && wp.blocks.updateCategory ){
        wp.blocks.updateCategory( 'quiz-maker', { icon: iconEl } );
    }

//    var quizMakerMapSelectToProps = function( select ) {
//        if(select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner &&
//           (select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner != undefined ||
//            select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner != null ) ){
//            return {
//                quizzes: select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner,
//                metaFieldValue: select( 'core/editor' )
//                    .getEditedPostAttribute( 'meta' )
//                    [ 'sidebar_plugin_meta_block_field' ]
//            };
//        }else{
//            return {
//                quizzes: __( "Something goes wrong please reload page" )
//            };
//        }
//    }
//
//    var quizMakerMetaBlockField = function( props ) {
//        if ( ! props.quizzes ) {
//            return __("Loading...");
//        }
//        if( typeof props.quizzes != "object"){
//            return props.quizzes;
//        }
//
//        if ( props.quizzes.length === 0 ) {
//            return __("There are no quizzes yet");
//        }
//        var quizner = [];
//        quizner.push({ label: __("-Select Quiz-"), value: ''});
//        for(let i in props.quizzes){
//            let quizData = {
//                    value: props.quizzes[i].id,
//                    label: props.quizzes[i].title,
//                }
//            quizner.push(quizData)
//        }
//        var aysElement = el(
//            aysSelect, {
//                className: 'ays_quiz_maker_block_select',
//                label: 'Select Quiz for adding to post content',
//                value: props.metaFieldValue,
//                onChange: function( content ) {
//                    props.shortcode = "[ays_quiz id="+content+"]";
//                    props.metaFieldValue = parseInt(content);
//                    let block = wp.blocks.createBlock( 'quiz-maker/quiz', {
//                        shortcode: "[ays_quiz id="+content+"]",
//                        quizzes: props.quizzes,
//                        metaFieldValue: parseInt(content)
//                    } );
//                    wp.data.dispatch( 'core/editor' ).insertBlocks( block );
//                },
//                options: quizner
//            },
//        );
//        return el(
//            "div",
//            {
//                className: 'ays_quiz_maker_block_container',
//                key: "inspector",                        
//            },
//            aysElement
//        );
//    }
//    var quizMakerMetaBlockFieldWithData = withSelect( quizMakerMapSelectToProps )( quizMakerMetaBlockField );
//    if(wp.plugins){
//        wp.plugins.registerPlugin( 'quiz-maker-sidebar', {
//            render: function() {
//                return el( wp.editPost.PluginSidebar,
//                    {
//                        name: 'quiz-maker',
//                        icon: iconEl,
//                        title: 'Quiz Maker',
//                    },
//                    el( 'div',
//                        { className: 'quiz-maker-sidebar-content' },
//                        el( quizMakerMetaBlockFieldWithData )
//                    )
//                );
//            }
//        } );
//    }

    var supports = {
        customClassName: false
    };

    registerBlockType( 'quiz-maker/quiz', {
        title: __('Quiz Maker'),
        category: 'quiz-maker',
        icon: iconEl,
        supports: supports,
        example: {
            attributes: {
                cover: ays_quiz_block_ajax.quiz_preview,
            }
        },
        edit: withSelect( function( select ) {
            if(select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner &&
               (select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner != undefined ||
                select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner != null ) ){
                return {
                    quizzes: select( 'core/blocks' ).getBlockType( 'quiz-maker/quiz' ).attributes.idner
                };
            }else{
                return {
                    quizzes: __( "Something goes wrong please reload page" )
                };
            }
        } )( function( props ) {

            if ( ! props.quizzes ) {
                return __("Loading...");
            }
            if( typeof props.quizzes != "object"){
                return props.quizzes;
            }

            if ( props.quizzes.length === 0 ) {
                return __("There are no quizzes yet");
            }
            var status = 0;
            if(props.attributes.metaFieldValue > 0){            
                status = 1;
            }
            var quizner = [];
            quizner.push({ label: __("-Select Quiz-"), value: '0'});
            for(var i in props.quizzes){
                var quizData = {
                        value: props.quizzes[i].id,
                        label: props.quizzes[i].title,
                    }
                quizner.push(quizData)
            }
            var aysElement = el(
                aysSelect, {
                    className: 'ays_quiz_maker_block_select',
                    label: 'Select Quiz',
                    value: props.attributes.metaFieldValue,
                    onChange: function( content ) {
                        var c = content;
                        if(isNaN(content)){
                            c = '';
                        }
                        status = 1;
                        wp.data.dispatch( 'core/editor' ).updateBlockAttributes( props.clientId, {
                            shortcode: "[ays_quiz id="+c+"]",
                            metaFieldValue: parseInt(c)
                        } );
                    },
                    options: quizner
                }
            );

            var aysElement2 = el(
                aysSelect, {
                    className: 'ays_quiz_maker_block_select',
                    label: '',
                    value: props.attributes.metaFieldValue,
                    onChange: function( content ) {
                        var c = content;
                        if(isNaN(content)){
                            c = '';
                        }
                        wp.data.dispatch( 'core/editor' ).updateBlockAttributes( props.clientId, {
                            shortcode: "[ays_quiz id="+c+"]",
                            metaFieldValue: parseInt(c)
                        } );

                        // return 
                    },
                    options: quizner
                },
                // el(ServerSideRender, {
                //     key: "editable",
                //     block: "quiz-maker/quiz",
                //     attributes:  props.attributes
                // })
            );
            var res = el(
                wp.element.Fragment,
                {},
                el(
                    BlockControls,
                    props
                ),
                el(
                    wp.editor.InspectorControls,
                    {},
                    el(
                        wp.components.PanelBody,
                        {},
                        el(
                            "div",
                            {
                                className: 'ays_quiz_maker_block_container',
                                key: "inspector",
                            },
                            aysElement
                        )
                    )
                ),
                // aysElement2,
                el(ServerSideRender, {
                    key: "editable",
                    block: "quiz-maker/quiz",
                    attributes:  props.attributes
                }),
                el(
                    "div",
                    {
                        className: 'ays_quiz_maker_block_select_quiz',
                        key: "inspector",
                    },
                    aysElement2
                )
            );
            var res2 = el(
                wp.element.Fragment,
                {},
                el(
                    BlockControls,
                    props
                ),
                el(
                    wp.editor.InspectorControls,
                    {},
                    el(
                        wp.components.PanelBody,
                        {},
                        el(
                            "div",
                            {
                                className: 'ays_quiz_maker_block_container',
                                key: "inspector",
                            },
                            aysElement
                        )
                    )
                ),
                el(ServerSideRender, {
                    key: "editable",
                    block: "quiz-maker/quiz",
                    attributes:  props.attributes
                })
            );

            aysBlockLoaded();

            if(status == 1){
                return res2;
            }else{
                return res;
            }
        }),

        save: function(e) {
            var t = e.attributes,
                n = t.metaFieldValue;

            resolveBlocks();

            return n ? el("div", null, '[ays_quiz id="'+n+'"]') : null
        }
    } );

    function resolveBlocks(id){
        var blocks = id ?
            select('core/block-editor').getBlock(id).innerBlocks
            : select('core/block-editor').getBlocks();

        if ( Array.isArray(blocks) ) {
            blocks.map( function(block){
                if(block.name == 'quiz-maker/quiz'){
                    if (!block.isValid) {
                        var newBlock = createBlock( block.name, block.attributes, block.innerblocks);
                        dispatch('core/block-editor').replaceBlock( block.clientId, newBlock );
                    } else {
                        resolveBlocks(block.clientId)
                    };
                }
            } );
        };
    };

    aysBlockLoaded();
    function aysBlockLoaded(){
        var blockLoaded = false;
        var blockLoadedInterval = setInterval(function() {
            if (jQuery(document).find('.for_quiz_rate_avg.ui.rating').length > 0) {
                //Actual functions goes here
                blockLoaded = true;
            }
            if ( blockLoaded ) {
                clearInterval( blockLoadedInterval );
                jQuery(document).find('.for_quiz_rate_avg.ui.rating').rating('disable');
            }
        }, 500);
    }
})(wp);
