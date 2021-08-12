<?php

$config = [
    'abbrev'  => 'cmtt',
    'tabs'    => [
        '0'  => 'Installation Guide',
        '1'  => 'General Settings',
        '2'  => 'Glossary Index Page',
        '3'  => 'Glossary Term',
        '4'  => 'Tooltip',
        '50' => 'Labels',
        '99' => 'Upgrade',
    ],
    'default' => [
        'cmtt_glossaryOnMainQuery'                       => 1, //Show on Main Query only
        'cmtt_glossaryID'                                => - 1, //The ID of the main Glossary Page
        'cmtt_glossaryPermalink'                         => 'glossary', //Set permalink name
        'cmtt_glossaryOnlySingle'                        => 0, //Show on Home and Category Pages or just single post pages?
        'cmtt_glossaryFirstOnly'                         => 0, //Search for all occurances in a post or only one?
        'cmtt_removeGlossaryCreateListFilter'            => 0, //Remove the Glossary Index List after first run
        'cmtt_glossaryOnlySpaceSeparated'                => 1, //Search only for words separated by spaces
        'cmtt_script_in_footer'                          => 1, //Place the scripts in the footer not the header
        'cmtt_add_structured_data_term_page'             => 1, // Add Structured data to the Term page
        'cmtt_show_desc_inlist_table'                    => 0, // Limit number of char for text in the description column=> if set to 0=> don't show
        'cmtt_glossaryOnPosttypes'                       => ['post', 'page', 'glossary'], //Default post types where the terms are highlighted
        'cmtt_disable_metabox_all_post_types'            => 0, //show disable metabox for all post types
        'cmtt_glossaryTurnOnAmp'                         => 0, // Turn on AMP version of the plugin
        'cmtt_glossaryEnableAjaxComplete'                => 1, // Turn on AMP version of the plugin
        'cmtt_glossaryDoubleclickEnabled'                => 0,
        'cmtt_glossaryDoubleclickService'                => 0,
        'cmtt_glossaryShowShareBox'                      => 0, //Show/hide the Share This box on top of the Glossary Index Page
        'cmtt_glossaryShowShareBoxTermPage'              => 0, //Show/hide the Share This box on top of the Glossary Term Page
        'cmtt_glossaryShowShareBoxLabel'                 => 'Share This', //Label of the Sharing Box on the Glossary Index Page
        'cmtt_glossaryTooltipDescLength'                 => 300, //Limit the length of the definision shown on the Glossary Index Page
        'cmtt_glossaryDiffLinkClass'                     => 0, //Use different class to style glossary list
        'cmtt_glossaryListTiles'                         => 0, // Display glossary terms list as tiles
        'cmtt_glossaryListTermLink'                      => 0, //Remove links from glossary index to glossary page
        'cmtt_index_letters'                             => [
            'a',
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
        ],
        'cmtt_glossaryTooltipDesc'                       => 0, // Display description in glossary list
        'cmtt_glossaryTooltipDescExcerpt'                => 0, // Display excerpt in glossary list
        'cmtt_glossaryServerSidePagination'              => 0, //paginate server side or client side (with alphabetical index)
        'cmtt_glossaryPaginationRound'                   => 0,
        'cmtt_glossaryPaginationPosition'                => 'bottom',
        'cmtt_glossaryScrollTop'                         => 0,
        'cmtt_perPage'                                   => 0, //pagination on "glossary page" withing alphabetical navigation
        'cmtt_glossaryRunApiCalls'                       => 0, //exclude the API calls from the glossary main page
        'cmtt_index_includeNum'                          => 1,
        'cmtt_limitNum'                                  => 0,
        'cmtt_letter_width'                              => 0,
        'cmtt_index_includeAll'                          => 1,
        'cmtt_index_showEmpty'                           => 1,
        'cmtt_index_allLabel'                            => 'ALL',
        'cmtt_glossary_addBackLink'                      => 1,
        'cmtt_addBacklinksOnce'                          => 0,
        'cmtt_glossary_addBackLinkBottom'                => 1,
        'cmtt_glossary_backLinkText'                     => '&laquo; Back to Glossary Index',
        'cmtt_glossary_backLinkBottomText'               => '&laquo; Back to Glossary Index',
        'cmtt_glossary_showRelatedArticles'              => 1,
        'cmtt_glossary_showRelatedArticlesCount'         => 5,
        'cmtt_glossary_showCustomRelatedArticlesCount'   => 5,
        'cmtt_glossary_showRelatedArticlesGlossaryCount' => 5,
        'cmtt_glossary_showRelatedArticlesTitle'         => 'Related Articles:',
        'cmtt_glossary_showRelatedArticlesPostTypesArr'  => array('post', 'page', 'glossary'),
        'cmtt_glossary_relatedArticlesPrefix'            => 'Glossary: ',
        'cmtt_glossary_relatedArticlesPagination'        => 0,
        'cmtt_glossary_addSynonyms'                      => 1,
        'cmtt_glossary_addSynonymsTitle'                 => 'Synonyms: ',
        'cmtt_glossary_addSynonymsTooltip'               => 0,
        'cmtt_glossaryReferral'                          => false,
        'cmtt_glossaryAffiliateCode'                     => '',
        'cmtt_glossaryBeforeTitle'                       => '', //Text which shows up before the title on the term page
        'cmtt_glossaryTooltip'                           => 1, //Use tooltips on glossary items?
        'cmtt_glossaryAddTermTitle'                      => 1, //Add the term title to the glossary?
        'cmtt_glossaryTooltipStripShortcode'             => 0, //Strip the shortcodes from glossary page before placing the tooltip?
        'cmtt_glossaryFilterTooltip'                     => 1, //Clean the tooltip text from uneeded chars?
        'cmtt_glossaryFilterTooltipA'                    => 0, //Clean the tooltip anchor tags
        'cmtt_glossaryLimitTooltip'                      => 0, // Limit the tooltip length  ?
        'cmtt_glossaryTermDetailsLink'                   => 'Term details', // Label of the link to term's details
        'cmtt_glossaryExcerptHover'                      => 0, //Search for all occurances in a post or only one?
        'cmtt_glossaryProtectedTags'                     => 1, //Aviod the use of Glossary in Protected tags?
        'cmtt_glossaryCaseSensitive'                     => 0, //Case sensitive?
        'cmtt_glossaryRemoveCommentsTermPage'            => 1, //Remove the comments from term page
        'cmtt_glossaryInNewPage'                         => 0, //In New Page?
        'cmtt_glossaryTermLink'                          => 0, //Remove links to glossary page
        'cmtt_showTitleAttribute'                        => 0, //show HTML title attribute
        'cmtt_tooltipIsClickable'                        => 0,
        'cmtt_tooltipLinkUnderlineStyle'                 => 'dotted',
        'cmtt_tooltipLinkUnderlineWidth'                 => 1,
        'cmtt_tooltipLinkUnderlineColor'                 => '#000000',
        'cmtt_tooltipLinkColor'                          => '#000000',
        'cmtt_tooltipLinkHoverUnderlineStyle'            => 'solid',
        'cmtt_tooltipLinkHoverUnderlineWidth'            => '1',
        'cmtt_tooltipLinkHoverUnderlineColor'            => '#333333',
        'cmtt_tooltipLinkHoverColor'                     => '#333333',
        'cmtt_tooltipBackground'                         => '#666666',
        'cmtt_tooltipForeground'                         => '#ffffff',
        'cmtt_tooltipOpacity'                            => 95,
        'cmtt_tooltipZIndex'                             => 1500,
        'cmtt_tooltipBorderStyle'                        => 'none',
        'cmtt_tooltipBorderWidth'                        => 0,
        'cmtt_tooltipBorderColor'                        => '#000000',
        'cmtt_tooltipPositionTop'                        => 5,
        'cmtt_tooltipPositionLeft'                       => 25,
        'cmtt_tooltipFontSize'                           => 13,
        'cmtt_tooltipTitleFontSize'                      => 13,
        'cmtt_tooltipPadding'                            => '2px 12px 3px 7px',
        'cmtt_tooltipBorderRadius'                       => 6,
        'cmtt_glossaryParseExcludedClasses'              => '',
        'cmtt_glossaryParseExcludedTags'                 => '',
        'cmtt_tooltipParsingPriority'                    => 20000,
        'cmtt_glossaryRemoveExcerptParsing'              => 0,
        'cmtt_createGlossaryTermPages'                   => 1,
        'cmtt_tooltipInternalLinkColor'                  => '#2EA3F2'
    ],
    'presets' => [
        'default' => [
            '0'  => [
                'labels' => [
                    'label'    => '',
                    'before'   => '[cminds_free_guide id="cmtt"]',
                    'settings' => []
                ],
            ],
//            '1'  => [
//                [
//                    'label'    => 'General Settings',
//                    'settings' => [
//                        [
//                            'label' => 'Glossary Index Page ID',
//                            'html'  => \CM\CMTT_Glossary_Index_Page_ID::render()
//                        ]
//                    ]
//                ],
//                [
//                    'label'    => 'Advanced Custom Fields Settings',
//                    'settings' => []
//                ],
//            ],
            '99' => [
                'labels' => [
                    'label'  => '',
                    'before' => '[cminds_upgrade_box id="cmtt"]'
                ]
            ]
        ]
    ]
];
