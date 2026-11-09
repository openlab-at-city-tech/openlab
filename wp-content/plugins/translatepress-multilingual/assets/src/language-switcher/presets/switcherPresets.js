import { __ } from '@wordpress/i18n'

export const switcherPresets = [
    {
        id: 'default',
        name: __('Default', 'translatepress-multilingual'),
        settings: {
            bgColor: '#ffffff',
            bgHoverColor: '#0000000D',
            textColor: '#143852',
            textHoverColor: '#1D2327',
            borderColor: '#1438521A',
        },
    },
    {
        id: 'dark',
        name: __('Dark', 'translatepress-multilingual'),
        settings: {
            bgColor: '#000000',
            bgHoverColor: '#444444',
            textColor: '#ffffff',
            textHoverColor: '#eeeeee',
            borderColor: 'transparent',
        },
    },
    {
        id: 'border',
        name: __('Border', 'translatepress-multilingual'),
        settings: {
            bgColor: '#FFFFFF',
            bgHoverColor: '#000000',
            textColor: '#143852',
            textHoverColor: '#ffffff',
            borderColor: '#143852',
        },
    },
    {
        id: 'transparent',
        name: __('Transparent', 'translatepress-multilingual'),
        previewBackground: 'linear-gradient(145.41deg, #2271B1 20.41%, #D3B4DA 96.59%)',
        settings: {
            bgColor: '#FFFFFFB2',
            bgHoverColor: '#0000000D',
            textColor: '#000000',
            textHoverColor: '#000000',
            borderColor: 'transparent',
        },
    },
]

export function switcherPresetCssVars(settings) {
    return {
        '--bg': settings.bgColor,
        '--bg-hover': settings.bgHoverColor,
        '--text': settings.textColor,
        '--text-hover': settings.textHoverColor,
        '--border-color': settings.borderColor,
    }
}
