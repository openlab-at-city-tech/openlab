export function AdvColorControl(props) {
    const {ColorIndicator, BaseControl} = wp.components;
    const {ColorPalette} = wp.blockEditor || wp.editor;
    const BaseLabel = BaseControl.VisualLabel ? BaseControl.VisualLabel : "span";

    const {label, value, onChange} = props;
    return (
        <BaseControl
            className="editor-color-palette-control block-editor-color-palette-control"
        >
            <BaseLabel className="components-base-control__label">
                {label}
                {value && (
                    <ColorIndicator colorValue={value} />
                )}
            </BaseLabel>
            <ColorPalette
                className="editor-color-palette-control__color-palette block-editor-color-palette-control__color-palette"
                value={value}
                onChange={onChange}
            />
        </BaseControl>
    )
}