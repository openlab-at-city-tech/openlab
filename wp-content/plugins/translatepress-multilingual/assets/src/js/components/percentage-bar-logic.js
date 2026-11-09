import utils from "../utils"

const computeTotalTranslationPercentage = function( dictionary, languagesArray ){
    let translationPercentage = {};

    if ( !dictionary || dictionary.length === 0 )
        return 0;

    const getDefaultLanguagePercentage = ( percentageObject ) => {
        let cumulatedPercentages, defaultLanguagePercentage;

        cumulatedPercentages      = Object.values( percentageObject ).reduce( ( acc, percentage ) => acc + percentage, 0 );
        defaultLanguagePercentage = cumulatedPercentages / Object.keys( percentageObject ).length;

        return parseInt( defaultLanguagePercentage );
    }

    languagesArray.forEach( language => {
        translationPercentage[language] = computeLanguageTranslationPercentage( dictionary, language );
    });

    translationPercentage.defaultLanguage = getDefaultLanguagePercentage( translationPercentage ) ;

    return translationPercentage;
}

const computeLanguageTranslationPercentage = function( dictionary, languageCode ){
    let translationPercentage, nrTotalStrings = 0, nrTranslatedStrings = 0;

    const isGettextStringInEnglish = ( stringObject ) => {
        return utils.isEnglishLanguage( languageCode ) && stringObject.type && stringObject.type === 'gettext';
    }

    for ( const dictionaryKey in dictionary ){
        const translationsArray            = dictionary[dictionaryKey] ? dictionary[dictionaryKey].translationsArray : '';
        const currentLangTranslationsArray = translationsArray ? translationsArray[languageCode] : '';

        const shouldSkip = !currentLangTranslationsArray || !currentLangTranslationsArray.status || dictionary[dictionaryKey].attribute === 'href' || dictionary[dictionaryKey].attribute === 'src';

        if ( shouldSkip )
            continue;

        const isTranslated = currentLangTranslationsArray.status !== '0' || isGettextStringInEnglish( dictionary[dictionaryKey] );

        if ( isTranslated )
            nrTranslatedStrings++;

        nrTotalStrings++;
    }

    translationPercentage = ( nrTranslatedStrings / nrTotalStrings ) * 100;

    return parseInt( translationPercentage );
}


const percentageBarText = function( props ){
    const getTooltipText = () => {
        const { defaultLanguage, percentage, currentLanguage, languageNames, percentageBarStrings } = props;

        let tooltipText = '';

        // Get localized strings and fill dynamic values
        let defaultLanguageText = percentageBarStrings['tooltip_text_default'].replace( '%s', percentage.defaultLanguage || '0' );
        let generalLanguageText = percentageBarStrings['tooltip_text_general'].replace(/%1\$s|%2\$s/g, function( match ){
            const replacements = {
                '%1$s': percentage[currentLanguage] || '0',
                '%2$s': languageNames[currentLanguage]
            }

            return replacements[match];
        });

        if ( currentLanguage === defaultLanguage )
            tooltipText = defaultLanguageText;

        else
            tooltipText = generalLanguageText;

        return tooltipText;
    }

    const getStringStatus = () => {
        const { currentLanguage, defaultLanguage, stringObject } = props;

        let stringStatus = '';
        const objectHasStatus = stringObject.translationsArray && stringObject.translationsArray[currentLanguage] && stringObject.translationsArray[currentLanguage].status;

        if ( currentLanguage !== defaultLanguage && objectHasStatus )
            stringStatus = stringObject.translationsArray[currentLanguage].status;

        return stringStatus;
    }

    return { getTooltipText, getStringStatus };
}

const miniBar = function( props ){
    const getMinibarHTML = () => {
        const { option, percentage, defaultLanguage, percentageBarStrings } = props;

        const isDefaultLanguage   = option.id === defaultLanguage;
        const displayedPercentage = !isDefaultLanguage ? percentage[option.id] : percentage.defaultLanguage;
        const languageName        = !isDefaultLanguage ? option.text : 'all languages';

        const titleText           = percentageBarStrings['minibar_text'].replace(/%1\$s|%2\$s/g, function( match ){
            const replacements = {
                '%1$s': displayedPercentage,
                '%2$s': languageName
            }

            return replacements[match];
        });

        return `<span class="trp-mini-bar-wrapper" title="${titleText}">${option.text}<div class="trp-percentage-mini-bar"><div class="trp-percentage-bar-inner" style="width:${displayedPercentage}%"></div></div>`;
    }

    return { getMinibarHTML };
}

const PercentageBarLogic = {
    calculateTranslationPercentage : computeTotalTranslationPercentage,
    percentageBarText,
    miniBar
}

export default PercentageBarLogic;