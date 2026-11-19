// @codingStandardsIgnoreFile
/**
 * Slideshow Selector
 *
 * Renders a searchable combobox with the slideshows list.
 */

/**
 * WordPress dependencies
 */
const wp = window.wp
const {__} = wp.i18n
const {ComboboxControl} = wp.components

/**
 * SlideshowSelector
 *
 * @param {object} props
 * @return {object}
 */
export default function SlideshowSelector({props}) {
    let slideshowId = props.attributes.slideshowId
    let {slideshows} = props

    return <ComboboxControl
        label={__('Select a slideshow', 'ml-slider')}
        value={slideshowId || null}
        options={slideshows.items.map(function (slider) {
            return {
                label: wp.htmlEntities.decodeEntities(slider.title) + ' (#' + slider.id + ')',
                value: slider.id
            }
        })}
        onChange={(newId) => {
            newId = parseInt(newId) || 0
            props.setAttributes({slideshowId: newId})
        }}
    />
}
