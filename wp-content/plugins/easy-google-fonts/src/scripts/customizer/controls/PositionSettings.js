import MarginControl from './position/MarginControl';
import PaddingControl from './position/PaddingControl';
import BorderControl from './position/BorderControl';
import BorderRadiusControl from './position/BorderRadiusControl';
import DisplayControl from './position/DisplayControl';

const PositionSettings = ({
  control,
  marginBottom,
  setMarginBottom,
  marginLeft,
  setMarginLeft,
  marginRight,
  setMarginRight,
  marginTop,
  setMarginTop,
  paddingBottom,
  setPaddingBottom,
  paddingLeft,
  setPaddingLeft,
  paddingRight,
  setPaddingRight,
  paddingTop,
  setPaddingTop,
  borderTopColor,
  setBorderTopColor,
  borderTopStyle,
  setBorderTopStyle,
  borderTopWidth,
  setBorderTopWidth,
  borderBottomColor,
  setBorderBottomColor,
  borderBottomStyle,
  setBorderBottomStyle,
  borderBottomWidth,
  setBorderBottomWidth,
  borderLeftColor,
  setBorderLeftColor,
  borderLeftStyle,
  setBorderLeftStyle,
  borderLeftWidth,
  setBorderLeftWidth,
  borderRightColor,
  setBorderRightColor,
  borderRightStyle,
  setBorderRightStyle,
  borderRightWidth,
  setBorderRightWidth,
  borderRadiusBottomLeft,
  setBorderRadiusBottomLeft,
  borderRadiusBottomRight,
  setBorderRadiusBottomRight,
  borderRadiusTopLeft,
  setBorderRadiusTopLeft,
  borderRadiusTopRight,
  setBorderRadiusTopRight,
  display,
  setDisplay
}) => {
  return (
    <div className="egf-position-settings__settings">
      <MarginControl
        className="mb-2"
        control={control}
        marginBottom={marginBottom}
        setMarginBottom={setMarginBottom}
        marginLeft={marginLeft}
        setMarginLeft={setMarginLeft}
        marginRight={marginRight}
        setMarginRight={setMarginRight}
        marginTop={marginTop}
        setMarginTop={setMarginTop}
      />

      <PaddingControl
        className="mb-2"
        control={control}
        paddingBottom={paddingBottom}
        setPaddingBottom={setPaddingBottom}
        paddingLeft={paddingLeft}
        setPaddingLeft={setPaddingLeft}
        paddingRight={paddingRight}
        setPaddingRight={setPaddingRight}
        paddingTop={paddingTop}
        setPaddingTop={setPaddingTop}
      />

      <BorderControl
        className="mb-2"
        control={control}
        borderTopColor={borderTopColor}
        setBorderTopColor={setBorderTopColor}
        borderTopStyle={borderTopStyle}
        setBorderTopStyle={setBorderTopStyle}
        borderTopWidth={borderTopWidth}
        setBorderTopWidth={setBorderTopWidth}
        borderBottomColor={borderBottomColor}
        setBorderBottomColor={setBorderBottomColor}
        borderBottomStyle={borderBottomStyle}
        setBorderBottomStyle={setBorderBottomStyle}
        borderBottomWidth={borderBottomWidth}
        setBorderBottomWidth={setBorderBottomWidth}
        borderLeftColor={borderLeftColor}
        setBorderLeftColor={setBorderLeftColor}
        borderLeftStyle={borderLeftStyle}
        setBorderLeftStyle={setBorderLeftStyle}
        borderLeftWidth={borderLeftWidth}
        setBorderLeftWidth={setBorderLeftWidth}
        borderRightColor={borderRightColor}
        setBorderRightColor={setBorderRightColor}
        borderRightStyle={borderRightStyle}
        setBorderRightStyle={setBorderRightStyle}
        borderRightWidth={borderRightWidth}
        setBorderRightWidth={setBorderRightWidth}
      />

      <BorderRadiusControl
        className="mb-2"
        control={control}
        borderRadiusBottomLeft={borderRadiusBottomLeft}
        setBorderRadiusBottomLeft={setBorderRadiusBottomLeft}
        borderRadiusBottomRight={borderRadiusBottomRight}
        setBorderRadiusBottomRight={setBorderRadiusBottomRight}
        borderRadiusTopLeft={borderRadiusTopLeft}
        setBorderRadiusTopLeft={setBorderRadiusTopLeft}
        borderRadiusTopRight={borderRadiusTopRight}
        setBorderRadiusTopRight={setBorderRadiusTopRight}
      />

      <DisplayControl className="mb-3" display={display} setDisplay={setDisplay} />
    </div>
  );
};

export default PositionSettings;
