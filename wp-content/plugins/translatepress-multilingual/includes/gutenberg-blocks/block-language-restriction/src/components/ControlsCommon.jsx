import { assign } from 'lodash';
import { __ } from '@wordpress/i18n';
import {
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption
} from '@wordpress/components'

import { FormTokenField } from '@wordpress/components';

export default function ControlsCommon({ attributes, setAttributes }) {
    const { TrpContentRestriction } = attributes;

    const allLanguages = trpBlockEditorData.all_languages;

    const languageNames = Object.keys( allLanguages ).map( key => allLanguages[key] )

    const handleLanguagePick = (newSelectedLanguages) => {
        setAttributes({
              TrpContentRestriction: assign(
                  { ...TrpContentRestriction },
                  { selected_languages: newSelectedLanguages }
              ),
        });
    };

    const helpText = TrpContentRestriction.restriction_type === 'include'
        ? __("Choose in which languages to show the block.", "translatepress-multilingual")
        : __("Choose from which languages the block is excluded.", "translatepress-multilingual");

    const validateInput = ( token ) => {
        return languageNames.includes(token);
    };

    const renderItem = ({ item }) => {
        const languageCode = Object.keys(allLanguages).find( (key) => allLanguages[key] === item );
        const flag =  trpBlockEditorData.plugin_url + '/assets/images/flags/' + languageCode + '.png';

        return (
           <span style={{ display: 'flex', alignItems: 'center' }}>
               <img alt={`Flag for ${flag}`} src={flag} style={{ marginRight: 8 }}/>
               {item}
           </span>
        );
    };

    return (
        <>
            <p>{helpText}</p>
            <ToggleGroupControl
                isBlock
                label={__("Content Restriction Mode", "translatepress-multilingual")}
                value={TrpContentRestriction.restriction_type}
                onChange={(value) =>
                    setAttributes({
                          TrpContentRestriction: assign(
                              { ...TrpContentRestriction },
                              { restriction_type: value } // Set "include" or "exclude"
                          ),
                    })
                }
            >
                <ToggleGroupControlOption
                    value="include"
                    label={__("Include", "translatepress-multilingual")}
                />
                <ToggleGroupControlOption
                    value="exclude"
                    label={__("Exclude", "translatepress-multilingual")}
                />
            </ToggleGroupControl>
            <FormTokenField
                label={__("Select language(s)", 'translatepress-multilingual')}
                suggestions={languageNames}
                value={TrpContentRestriction.selected_languages}
                onChange={handleLanguagePick}
                __experimentalValidateInput={validateInput}
                __experimentalExpandOnFocus
                __experimentalShowHowTo={false}
                __experimentalRenderItem={renderItem}
            />
        </>
    );
}
