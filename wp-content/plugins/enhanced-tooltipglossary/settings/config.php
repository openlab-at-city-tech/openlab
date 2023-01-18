<?php

$config = [
    'abbrev'   => 'cmtt',
    'tabs'     => [
        '0'  => 'Installation Guide',
        '1'  => 'General Settings',
        '2'  => 'Glossary Index Page',
        '3'  => 'Glossary Term',
        '4'  => 'Tooltip',
        '50' => 'Labels',
        '99' => 'Upgrade',
    ],
    'settings' => [
        'cmtt_glossaryOnMainQuery'                       => ['value' => 1], //Show on Main Query only
        'cmtt_glossaryID'                                => ['value' => -1], //The ID of the main Glossary Page
        'cmtt_glossaryPermalink'                         => ['value' => 'glossary',], //Set permalink name
        'cmtt_glossaryOnlySingle'                        => ['value' => 0,], //Show on Home and Category Pages or just single post pages?
        'cmtt_glossaryFirstOnly'                         => ['value' => 0,], //Search for all occurances in a post or only one?
        'cmtt_removeGlossaryCreateListFilter'            => ['value' => 0,], //Remove the Glossary Index List after first run
        'cmtt_glossaryOnlySpaceSeparated'                => ['value' => 1,], //Search only for words separated by spaces
        'cmtt_script_in_footer'                          => ['value' => 1,], //Place the scripts in the footer not the header
        'cmtt_add_structured_data_term_page'             => ['value' => 1,], // Add Structured data to the Term page
        'cmtt_show_desc_inlist_table'                    => ['value' => 0,], // Limit number of char for text in the description column=> if set to 0=> don't show
        'cmtt_glossaryOnPosttypes'                       => ['value' => ['post', 'page', 'glossary'],], //Default post types where the terms are highlighted
        'cmtt_disable_metabox_all_post_types'            => ['value' => 0,], //show disable metabox for all post types
        'cmtt_glossaryTurnOnAmp'                         => ['value' => 0,], // Turn on AMP version of the plugin
        'cmtt_glossaryEnableAjaxComplete'                => ['value' => 1,], // Turn on AMP version of the plugin
        'cmtt_glossaryDoubleclickEnabled'                => ['value' => 0,],
        'cmtt_glossaryDoubleclickService'                => ['value' => 0,],
        'cmtt_glossaryShowShareBox'                      => ['value' => 0,], //Show/hide the Share This box on top of the Glossary Index Page
        'cmtt_glossaryShowShareBoxTermPage'              => ['value' => 0,], //Show/hide the Share This box on top of the Glossary Term Page
        'cmtt_glossaryShowShareBoxLabel'                 => ['value' => 'Share This',], //Label of the Sharing Box on the Glossary Index Page
        'cmtt_glossaryTooltipDescLength'                 => ['value' => 300,], //Limit the length of the definision shown on the Glossary Index Page
        'cmtt_glossaryDiffLinkClass'                     => ['value' => 0,], //Use different class to style glossary list
        'cmtt_glossaryListTiles'                         => ['value' => 0,], // Display glossary terms list as tiles
        'cmtt_glossaryListTermLink'                      => ['value' => 0,], //Remove links from glossary index to glossary page
        'cmtt_index_letters'                             => ['value' => ['a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'm',
                'n',
                'o',
                'p',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z'
            ]
        ],
        'cmtt_glossaryTooltipDesc'                       => ['value' => 0,], // Display description in glossary list
        'cmtt_glossaryTooltipDescExcerpt'                => ['value' => 0,], // Display excerpt in glossary list
        'cmtt_glossaryServerSidePagination'              => ['value' => 0,], //paginate server side or client side (with alphabetical index)
        'cmtt_glossaryPaginationRound'                   => ['value' => 0,],
        'cmtt_glossaryPaginationPosition'                => ['value' => 'bottom',],
        'cmtt_glossaryScrollTop'                         => ['value' => 0,],
        'cmtt_perPage'                                   => ['value' => 0,], //pagination on "glossary page" withing alphabetical navigation
        'cmtt_glossaryRunApiCalls'                       => ['value' => 0,], //exclude the API calls from the glossary main page
        'cmtt_index_includeNum'                          => ['value' => 1,],
        'cmtt_limitNum'                                  => ['value' => 0,],
        'cmtt_letter_width'                              => ['value' => 0,],
        'cmtt_index_includeAll'                          => ['value' => 1,],
        'cmtt_index_showEmpty'                           => ['value' => 1,],
        'cmtt_index_allLabel'                            => ['value' => 'ALL',],
        'cmtt_glossary_addBackLink'                      => ['value' => 1,],
        'cmtt_addBacklinksOnce'                          => ['value' => 0,],
        'cmtt_glossary_addBackLinkBottom'                => ['value' => 1,],
        'cmtt_glossary_backLinkText'                     => ['value' => '&laquo; Back to Glossary Index',],
        'cmtt_glossary_backLinkBottomText'               => ['value' => '&laquo; Back to Glossary Index',],
        'cmtt_glossary_showRelatedArticles'              => ['value' => 1,],
        'cmtt_glossary_showRelatedArticlesCount'         => ['value' => 5,],
        'cmtt_glossary_showCustomRelatedArticlesCount'   => ['value' => 5,],
        'cmtt_glossary_showRelatedArticlesGlossaryCount' => ['value' => 5,],
        'cmtt_glossary_showRelatedArticlesTitle'         => ['value' => 'Related Articles:'],
        'cmtt_glossary_showRelatedArticlesPostTypesArr'  => ['value' => array('post', 'page', 'glossary'),],
        'cmtt_glossary_relatedArticlesPrefix'            => ['value' => 'Glossary: ',],
        'cmtt_glossary_relatedArticlesPagination'        => ['value' => 0,],
        'cmtt_glossary_addSynonyms'                      => ['value' => 1,],
        'cmtt_glossary_addSynonymsTitle'                 => ['value' => 'Synonyms: '],
        'cmtt_glossary_addSynonymsTooltip'               => ['value' => 0,],
        'cmtt_glossaryReferral'                          => ['value' => false,],
        'cmtt_glossaryAffiliateCode'                     => ['value' => '',],
        'cmtt_glossaryBeforeTitle'                       => ['value' => '',], //Text which shows up before the title on the term page
        'cmtt_glossaryTooltip'                           => ['value' => 1,], //Use tooltips on glossary items?
        'cmtt_glossaryAddTermTitle'                      => ['value' => 1,], //Add the term title to the glossary?
        'cmtt_glossaryTooltipStripShortcode'             => ['value' => 0,], //Strip the shortcodes from glossary page before placing the tooltip?
        'cmtt_glossaryFilterTooltip'                     => ['value' => 1,], //Clean the tooltip text from uneeded chars?
        'cmtt_glossaryFilterTooltipA'                    => ['value' => 0,], //Clean the tooltip anchor tags
        'cmtt_glossaryLimitTooltip'                      => ['value' => 0,], // Limit the tooltip length  ?
        'cmtt_glossaryTermDetailsLink'                   => ['value' => 'Term details',], // Label of the link to term's details
        'cmtt_glossaryExcerptHover'                      => ['value' => 0,], //Search for all occurances in a post or only one?
        'cmtt_glossaryProtectedTags'                     => ['value' => 1,], //Aviod the use of Glossary in Protected tags?
        'cmtt_glossaryCaseSensitive'                     => ['value' => 0,], //Case sensitive?
        'cmtt_glossaryRemoveCommentsTermPage'            => ['value' => 1,], //Remove the comments from term page
        'cmtt_glossaryInNewPage'                         => ['value' => 0,], //In New Page?
        'cmtt_glossaryTermLink'                          => ['value' => 0,], //Remove links to glossary page
        'cmtt_showTitleAttribute'                        => ['value' => 0,], //show HTML title attribute
        'cmtt_tooltipIsClickable'                        => ['value' => 0,],
        'cmtt_tooltipLinkUnderlineStyle'                 => ['value' => 'dotted',],
        'cmtt_tooltipLinkUnderlineWidth'                 => ['value' => 1,],
        'cmtt_tooltipLinkUnderlineColor'                 => ['value' => '#000000',],
        'cmtt_tooltipLinkColor'                          => ['value' => '#000000',],
        'cmtt_tooltipLinkHoverUnderlineStyle'            => ['value' => 'solid',],
        'cmtt_tooltipLinkHoverUnderlineWidth'            => ['value' => '1',],
        'cmtt_tooltipLinkHoverUnderlineColor'            => ['value' => '#333333',],
        'cmtt_tooltipLinkHoverColor'                     => ['value' => '#333333',],
        'cmtt_tooltipBackground'                         => ['value' => '#666666',],
        'cmtt_tooltipForeground'                         => ['value' => '#ffffff',],
        'cmtt_tooltipOpacity'                            => ['value' => 95,],
        'cmtt_tooltipZIndex'                             => ['value' => 1500,],
        'cmtt_tooltipBorderStyle'                        => ['value' => 'none',],
        'cmtt_tooltipBorderWidth'                        => ['value' => 0,],
        'cmtt_tooltipBorderColor'                        => ['value' => '#000000',],
        'cmtt_tooltipPositionTop'                        => ['value' => 5,],
        'cmtt_tooltipPositionLeft'                       => ['value' => 25,],
        'cmtt_tooltipFontSize'                           => ['value' => 13,],
        'cmtt_tooltipTitleFontSize'                      => ['value' => 13,],
        'cmtt_tooltipPadding'                            => ['value' => '2px 12px 3px 7px',],
        'cmtt_tooltipBorderRadius'                       => ['value' => 6,],
        'cmtt_glossaryParseExcludedClasses'              => ['value' => '',],
        'cmtt_glossaryParseExcludedTags'                 => ['value' => '',],
        'cmtt_tooltipParsingPriority'                    => ['value' => 20000,],
        'cmtt_glossaryRemoveExcerptParsing'              => ['value' => 0,],
        'cmtt_createGlossaryTermPages'                   => ['value' => 1,],
        'cmtt_tooltipInternalLinkColor'                  => ['value' => '#2EA3F2',],
        'cmtt_displayTermsAsFootnotes'                   => ['value' => 0,], // Key to display terms as footnotes
        'cmtt_footnoteAestheticsType'                    => ['value' => 'type1',], // type1 = [] , type2 = {}
        'cmtt_footnoteSymbolSize'                        => ['value' => '14px',], // Footnote font size
        'cmtt_footnoteSymbolColor'                       => ['value' => '#ff9fbc',],
        'cmtt_footnoteFormat'                            => ['value' => 'none',], // Footnote font style - none (inherit), bold, italic
        'cmtt_footnoteDefTitle'                          => ['value' => 'Terms definitions'] // After content terms definitions block Title
    ]
];
