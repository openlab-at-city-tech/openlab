// Dependencies
import { createNggEditComponent } from './edit.min'
import icons from './icons.min';

const { __ } 					            = wp.i18n
const { RawHTML } 	                        = wp.element
const { registerBlockType }                 = wp.blocks
const { withSelect}                         = wp.data
const { omit }                              = lodash
const { memo }                              = React

// Provides a higher order component that is aware when the freeform block becomes available
const withFreeform = withSelect((select, props) => {
    return {
        Freeform: select('core/blocks').getBlockType('core/freeform')
    }
})

// When the freeform block is available, we create our edit component for NGG and render it
const edit = withFreeform(memo(props => {
    const editProps  = omit(props, ['Freeform'])
    const NggEdit = createNggEditComponent(jQuery, props.Freeform.edit)
    return <NggEdit {...editProps} isSelected={true}/>
}))

// Register our block
registerBlockType('imagely/nextgen-gallery', {

    title: __('NextGEN Gallery'),

    desription: __('A block for adding NextGEN Galleries.'),

    icon: icons.nextgen,

    category: 'common',

    attributes: {
        content: {
            type: 'string',
            source: 'html',
        },
    },

    supports: {
        className: false,
        customClassName: false,
    },

    edit,

    save( { attributes } ) {
        const { content } = attributes;
        return <RawHTML>{ content }</RawHTML>
    },
});


