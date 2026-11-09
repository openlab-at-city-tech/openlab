import { assign, has } from "lodash";
import { addFilter } from "@wordpress/hooks";
import { createHigherOrderComponent } from "@wordpress/compose";
import { useSelect } from "@wordpress/data";
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import { Notice, PanelBody } from "@wordpress/components";

import ControlsCommon from './components/ControlsCommon'

/**
 * Add the language restriction inspector controls in the editor
 */
function TrpBlockContentRestrictionControls(props) {
    const { attributes, clientId, name, setAttributes } = props;
    const { TrpContentRestriction } = attributes;

    const rootClientId = useSelect(
        (select) => select("core/block-editor").getBlockRootClientId(clientId),
        [clientId],
    );

    // WordPress serializes top-level Classic blocks as raw HTML, without a block
    // delimiter. Consequently, custom block attributes cannot be persisted on them.
    const isTopLevelClassicBlock = name === "core/freeform" && !rootClientId;

    const handlePanelToggle = isTopLevelClassicBlock
        ? undefined
        : () =>
              setAttributes({
                  TrpContentRestriction: assign(
                      { ...TrpContentRestriction },
                      { panel_open: !TrpContentRestriction.panel_open },
                  ),
              });

    // Abort if the block type does not have the TrpContentRestriction attribute registered
    if ( !has(attributes, "TrpContentRestriction") )
        return null;

    return (
        <InspectorControls>
            <PanelBody
                title={__(
                    "TranslatePress Language Restriction",
                    "translatepress-multilingual",
                )}
                className="translatepress-content-restriction-settings"
                initialOpen={TrpContentRestriction.panel_open}
                onToggle={handlePanelToggle}
            >
                {isTopLevelClassicBlock ? (
                    <Notice status="warning" isDismissible={false}>
                        {__(
                            "Language restrictions cannot be saved on a top-level Classic block. Convert it to blocks or place it inside a Group block first.",
                            "translatepress-multilingual",
                        )}
                    </Notice>
                ) : (
                    <ControlsCommon {...props} />
                )}
            </PanelBody>
        </InspectorControls>
    );
}

/**
 * Add the content restriction settings attribute
 */
function TrpContentRestrictionAttributes( settings ) {
    let contentRestrictionAttributes = {
        TrpContentRestriction: {
            type: "object",
            properties: {
                restriction_type: {
                    type: "string",
                },
                selected_languages: {
                    type: "array"
                },
                panel_open: {
                    type: "bool",
                },
            },
            default: {
                restriction_type: "exclude",
                selected_languages: [],
                panel_open: true,
            },
        },
    };

    settings.attributes = assign(
        settings.attributes,
        contentRestrictionAttributes,
    );

    return settings;
}
addFilter(
    "blocks.registerBlockType",
    "translatepress/attributes",
    TrpContentRestrictionAttributes,
);

/**
 * Filter the block edit object and add content restriction controls
 */
const blockTrpContentRestrictionControls = createHigherOrderComponent(
    (BlockEdit) => {
        return (props) => {
            return (
                <>
                    <BlockEdit {...props} />
                    <TrpBlockContentRestrictionControls {...props} />
                </>
            );
        };
    },
    "blockTrpContentRestrictionControls",
);
addFilter(
    "editor.BlockEdit",
    "translatepress/inspector-controls",
    blockTrpContentRestrictionControls,
    100, // above Advanced controls
);
