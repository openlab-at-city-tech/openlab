<template>
    <div id="trp-span trp-actions"></div>
</template>

<script>
    import utils from '../utils'
    import axios from 'axios'

    export default{
        props:[
            'dictionary',
            'settings',
            'iframe',
            'dataAttributes',
            'mergeRules',
            'ajax_url',
            'nonces',
            'mergeData',
            'editorStrings',
            'currentLanguage'
        ],
        data(){
            return{
                hoveredStringId       : '',
                hoveredStringSelector : '',
                hoveredTarget         : '',
                counter               : 0
            }
        },
        methods:{
          showPencilIcon( element ){
            if( !this.dictionary || this.dictionary.length < 1 )
              return

            let self = this
            let target = element.target
            let relatedNode, relatedNodeAttr, position, stringSelector, stringId, mergeOrSplit

            if( self.hoveredTarget != '' && target.isSameNode( self.hoveredTarget ) )
              return

            //if other icons are showing, remove them
            self.removePencilIcon()

            //remove highlight class
            self.removeHighlight( false )

            //insert button HTML
            //target.insertAdjacentHTML( position, this.getTrpSpan() )
            this.iframe.body.insertAdjacentHTML( 'afterbegin', this.getTrpSpan() )

            //inserted node
            let trpSpan = self.iframe.getElementsByTagName( 'trp-span' )[0]

            if( !trpSpan )
              return

            relatedNode = target;

            //edit string button
            let editButton = this.iframe.querySelector( 'trp-edit' )
            let foundNonGettext = false

            self.dataAttributes.forEach( function( baseSelector ) {

              self.$parent.prepareSelectorStrings( baseSelector ).forEach( function( selector ) {

                relatedNodeAttr = relatedNode.getAttribute( selector )

                if ( relatedNodeAttr ) {
                  stringId = relatedNodeAttr
                  stringSelector = selector
                  if ( ! stringSelector.includes( 'data-trpgettextoriginal' ) ){
                    // includes at least one data-base-selector that is not gettext. Useful for determining edit pencil color
                    foundNonGettext = true
                  }
                }
              })
            })

            self.hoveredStringSelector = stringSelector
            self.hoveredStringId       = stringId
            self.hoveredTarget         = target

            // show green edit pencil
            if ( foundNonGettext ){
              editButton.classList.remove( 'trp-gettext-pencil' )
            }else{
              editButton.classList.add( 'trp-gettext-pencil' )
            }

            //figure out if split or merge is available
            mergeOrSplit = self.checkMergeOrSplit( target )

            if( !self.mergeData.includes( stringId ) ) {
              editButton.style.display = 'inline-block'

              //add class to highlight text
              if( !target.classList.contains( 'trp-highlight' ) )
                target.className += ' trp-highlight'
            }

            //merge or split event listeners
            if( mergeOrSplit != 'none' && !self.mergeData.includes( stringId ) ) {
              let button = this.iframe.querySelector( 'trp-' + mergeOrSplit )

              button.style.display = 'inline-block'

              //setup event listeners for merge and split
              if( mergeOrSplit == 'split' )
                button.addEventListener( 'click', self.splitHandler )
              else if( mergeOrSplit == 'merge' )
                button.addEventListener( 'click', self.mergeHandler )
            }

            editButton.addEventListener( 'click', self.editHandler )

            // Function to calculate and set position of trpSpan
            const setPosition = () => {
              const targetRect = target.getBoundingClientRect();
              const bodyRect = this.iframe.body.getBoundingClientRect();
              const trpSpanRect = trpSpan.getBoundingClientRect();

              // Get computed padding values for the body element
              const bodyComputedStyle = window.getComputedStyle(this.iframe.body);
              const bodyPaddingLeft = parseFloat(bodyComputedStyle.paddingLeft);
              const bodyPaddingRight = parseFloat(bodyComputedStyle.paddingRight);
              const bodyPaddingTop = parseFloat(bodyComputedStyle.paddingTop);
              const bodyWidth = parseFloat(bodyComputedStyle.width);

              // Detect if the language is RTL
              const isRTL = window.getComputedStyle(this.iframe.body).direction === 'rtl';

              // Calculate the position
              let leftPosition;
              let topPosition = targetRect.top - bodyRect.top - bodyPaddingTop;

              if (isRTL) {
                leftPosition = targetRect.right - bodyPaddingRight;

                // Ensure the button is visible
                if (leftPosition > (bodyWidth - trpSpanRect.width)){
                  leftPosition = bodyWidth - trpSpanRect.width - 15;
                }

                trpSpan.style.left = `${leftPosition}px`;
              } else {
                leftPosition = targetRect.left - bodyPaddingLeft - trpSpanRect.width;

                // Ensure the button is visible
                if (leftPosition < trpSpanRect.width ) {
                  leftPosition = 1;
                }

                trpSpan.style.left = `${leftPosition}px`;
              }

              if (topPosition < 16) {
                topPosition = 16;
              }

              // Apply absolute positioning to the trpSpan
              trpSpan.style.position = 'absolute';
              trpSpan.style.top = `${topPosition}px`;
              trpSpan.style.zIndex = '9999999999';
            };

            // Initial positioning
            setPosition();

            // Add scroll event listener to recalculate position on scroll
            this.iframe.addEventListener('scroll', setPosition);
          },
            editHandler( event ){
                event.preventDefault()
                event.stopPropagation()

                if( this.$parent.mergingString )
                    this.removeHighlight( true )

                this.$parent.mergeData      = []

                let hoveredStringIndex = this.$parent.getStringIndex( this.hoveredStringSelector, this.hoveredStringId )

                this.$parent.selectedStringNode = this.hoveredTarget
                if ( this.$parent.selectedString === hoveredStringIndex ) {
                    this.$parent.selectedString = null
                    this.$nextTick( () => {
                        this.$parent.selectedString = hoveredStringIndex
                    })
                } else {
                    this.$parent.selectedString = hoveredStringIndex
                }

                this.$parent.translationNotLoadedYet  = ( hoveredStringIndex === null )

                jQuery( '#trp-string-categories' ).select2( 'close' )
            },
            splitHandler( event ) {
                event.preventDefault()
                event.stopPropagation()
                this.$parent.mergingString = false

                let split = confirm( this.editorStrings.split_confirmation )

                if( split === false )
                    return

                let strings = []
                let hoveredStringIndex = this.$parent.getStringIndex( this.hoveredStringSelector, this.hoveredStringId )
                strings.push( this.dictionary[ hoveredStringIndex ].original )

                let data = new FormData()
                    data.append( 'action', 'trp_split_translation_block' )
                    data.append( 'security', this.nonces['splittbnonce'] )
                    data.append( 'strings', JSON.stringify( strings ) )

                let self = this

                axios.post(this.ajax_url, data)
                    .then(function (response) {
                        window.location.reload()
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            mergeHandler( event ) {
                event.preventDefault()
                event.stopPropagation()

                let self = this
                let parent, isDeprecated = null, deprecatedString = null, stringId

                self.$parent.mergingString = true

                //remove classes
                let previouslyHighlighted = this.iframe.getElementsByClassName( 'trp-create-translation-block' )

                if( previouslyHighlighted.length > 0 ) {
                    let i

                    for ( i = 0; i < previouslyHighlighted.length; i++ ) {
                        previouslyHighlighted[i].classList.remove( 'trp-highlight' )
                        previouslyHighlighted[i].classList.remove( 'trp-create-translation-block' )
                    }
                }

                parent = self.hoveredTarget.closest( self.mergeRules.top_parents )

                //remove highlight classes from children
                parent.querySelectorAll( '.trp-highlight' ).forEach( function(node) {
                    node.classList.remove( 'trp-highlight' )
                })

                //determine the strings that are being prepared for merging (no gettext)
                self.$parent.mergeData = []

                parent.querySelectorAll( '[data-trp-translate-id]' ).forEach( function( node ) {
                    stringId = node.getAttribute( 'data-trp-translate-id' )

                    if ( stringId )
                        self.$parent.mergeData.push( stringId )
                })

                //check if we have existing translations for this block
                isDeprecated = parent.getAttribute( 'data-trp-translate-id-deprecated' )

                if( isDeprecated )
                    deprecatedString = self.$parent.getStringIndex( 'data-trp-translate-id', isDeprecated )

                parent.setAttribute( 'data-trp-translate-id', 'trp_creating_translation_block' )

                parent.className += ' trp-highlight trp-create-translation-block'

                //create a placeholder string for the dictionary
                let dummyString = {
                    type              : 'regular',
                    attribute         : '',
                    block_type        : '1',
                    dbID              : 'create_translation_block' + this.counter,
                    original          : self.stripEditorData( parent ),
                    selector          : 'data-trp-translate-id',
                    translationsArray : {}
                }
                this.counter++

                let dummyTranslations = {}

                let defaultLanguage = this.settings['default-language']

                //populate translationsArray
                self.settings['translation-languages'].forEach( function( languageCode  ){
                    if( languageCode != defaultLanguage ) {
                        dummyTranslations = {
                            block_type : '1',
                            id         : languageCode,
                            status     : '0',
                            translated : '',
                            editedTranslation: ''
                        }

                        //populate existing translations
                        if( deprecatedString ) {
                            dummyTranslations.translated        = self.dictionary[deprecatedString].translationsArray[languageCode].translated
                            dummyTranslations.editedTranslation = self.dictionary[deprecatedString].translationsArray[languageCode].translated
                        }

                        dummyString.translationsArray[languageCode] = dummyTranslations
                    }
                })

                //add item to dictionary and set selectedString as the index
                self.$parent.selectedString = self.dictionary.push( dummyString ) - 1

            },
            removePencilIcon(){
                let icons = this.iframe.querySelectorAll( 'trp-span' )

                if ( icons.length > 0 ) {
                    icons.forEach( function( icon ) {
                        icon.remove()
                    })
                }
            },
            checkMergeOrSplit( target ){
                if( !this.mergeRules || !this.mergeRules.self_object_type || !this.mergeRules.top_parents )
                    return 'none'

                let hoveredStringIndex = this.$parent.getStringIndex( this.hoveredStringSelector, this.hoveredStringId )

                if( hoveredStringIndex === null )
                    hoveredStringIndex = this.$parent.selectedString

                if( typeof this.dictionary[hoveredStringIndex] != 'undefined' && this.dictionary[hoveredStringIndex].block_type == 1 )
                    return 'split'

                let self = this
                let parentNode, childNodes, incompatibleSiblings

                let action = 'none'

                //check if target is the correct object type
                this.mergeRules.self_object_type.forEach( function( thisObjectType ) {

                    if( target.tagName.toLowerCase() == thisObjectType ) {
                        //get parent based on merge rules
                        parentNode = target.closest( self.mergeRules.top_parents )

                        if( parentNode != null ) {
                            //get childrens that are of the correct type based on parent,
                            self.mergeRules.self_object_type.forEach( function( selfObjectType ) {
                                childNodes = parentNode.querySelectorAll( selfObjectType )

                                if( childNodes.length > 1 ) {
                                    //check if between the children we have incompatible siblings (gettext or dynamic strings)
                                    incompatibleSiblings = parentNode.querySelectorAll( self.mergeRules.incompatible_siblings )

                                    if ( incompatibleSiblings.length == 0 )
                                        action = 'merge'
                                }
                            })
                        }
                    }
                })

                return action
            },
            stripEditorData( target ){
                let copy = target.cloneNode( true )
                let self = this

                let buttons = copy.querySelector( 'trp-span' )

                if( buttons )
                    buttons.remove()

                /** In case we are in secondary language and the strings that will be merged are already translated,
                 *  we must use the originals of these strings instead of what is in the preview iframe HTML page at this point
                 */
                if ( this.settings['default-language'] != this.currentLanguage ){
                    copy.querySelectorAll( '[data-trp-translate-id]' ).forEach( function( node ) {
                        let stringId = node.getAttribute( 'data-trp-translate-id' )
                        let index = self.$parent.getStringIndex( 'data-trp-translate-id', stringId )
                        if ( self.dictionary[index].translationsArray[self.currentLanguage] && self.dictionary[index].translationsArray[self.currentLanguage].status != 0 ) {
                            node.innerHTML = node.innerText.replace( self.dictionary[index].translationsArray[self.currentLanguage].translated, self.dictionary[index].original )
                        }
                    })
                }

                copy.querySelectorAll( 'translate-press, trp-wrap, trp-highlight' ).forEach( function( node ) {
                    utils.unwrap( node )
                })

                let attributesToReplace = [ 'href', 'target' ]

                attributesToReplace.forEach( function( attribute ) {
                    copy.querySelectorAll( '[data-trp-original-' + attribute + ']' ).forEach( function( node ) {
                        let dataTrpOriginalAttribute = 'data-trp-original-' + attribute;
                        node.setAttribute( attribute, node.getAttribute( dataTrpOriginalAttribute ) )
                        node.removeAttribute(dataTrpOriginalAttribute)
                    })
                })

                let node
                let otherAttributes = [ 'data-trp-placeholder', 'data-trp-unpreviewable' ]
                let attributesToRemove = otherAttributes.concat( self.$parent.prepareSelectorStrings( 'data-trp-translate-id' ), self.$parent.prepareSelectorStrings( 'data-trp-node-group' ), self.$parent.prepareSelectorStrings( 'data-trp-node-description' ) )

                attributesToRemove.forEach( function( attribute ) {
                    copy.querySelectorAll( '[' + attribute + ']' ).forEach( function( node ) {
                        node.removeAttribute( attribute )
                    })
                })

                return copy.innerHTML

            },
            removeHighlight( removeFromBlocks = true ){
                let previouslyHighlighted = this.iframe.getElementsByClassName( 'trp-highlight' )

                if( previouslyHighlighted.length > 0 ) {
                    let i

                    for ( i = 0; i < previouslyHighlighted.length; i++ ) {

                        if ( removeFromBlocks )
                            previouslyHighlighted[i].classList.remove( 'trp-highlight' )
                        else if ( !removeFromBlocks && !previouslyHighlighted[i].classList.contains( 'trp-create-translation-block' ) )
                            previouslyHighlighted[i].classList.remove( 'trp-highlight' )
                    }
                }

                return true
            },
            getTrpSpan() {
                return '<trp-span><div class="trp-editor-action-hover-container"><trp-merge title="'+ this.editorStrings.merge +'" class="trp-icon trp-merge" ></trp-merge><trp-split title="'+ this.editorStrings.split +'" class="trp-icon trp-split"></trp-split><trp-edit title="'+ this.editorStrings.edit +'" class="trp-icon trp-edit-translation" ></trp-edit></div></trp-span>'
            }
        }
    }
</script>
