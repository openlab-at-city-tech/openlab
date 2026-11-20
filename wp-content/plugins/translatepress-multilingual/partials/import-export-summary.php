<?php
// The languages table of the export card; TRP_Import_Export::render_export_summary() sets $export_summary.

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// The default language first: the others are translated from it.
$trp_ie_default   = $export_summary['default_language'];
$trp_ie_languages = $export_summary['languages'];
usort( $trp_ie_languages, function ( $a, $b ) use ( $trp_ie_default ) {
    if ( ( $a['code'] === $trp_ie_default ) !== ( $b['code'] === $trp_ie_default ) ) {
        return $a['code'] === $trp_ie_default ? -1 : 1;
    }
    return strcasecmp( $a['name'], $b['name'] );
} );
$trp_ie_show_slugs = ( null !== $export_summary['slugs_rows'] );
// Null: the language has no table of that kind.
$trp_ie_count = function ( $count ) {
    return ( null === $count ) ? '—' : number_format_i18n( (int) $count );
};
?>

<div class="trp-ie-table-wrap trp-ie-table-wrap--languages" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Languages in the export file', 'translatepress-multilingual' ); ?>">
    <table class="trp-ie-table">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e( 'Language', 'translatepress-multilingual' ); ?></th>
                <th scope="col" class="trp-ie-num"><?php esc_html_e( 'Page translations', 'translatepress-multilingual' ); ?></th>
                <th scope="col" class="trp-ie-num"><?php esc_html_e( 'Theme and plugin strings', 'translatepress-multilingual' ); ?></th>
                <?php if ( $trp_ie_show_slugs ) : ?>
                    <th scope="col" class="trp-ie-num"><?php esc_html_e( 'Translated slugs', 'translatepress-multilingual' ); ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if ( empty( $trp_ie_languages ) ) : ?>
            <tr>
                <td colspan="<?php echo $trp_ie_show_slugs ? 4 : 3; ?>"><?php esc_html_e( 'No translations yet.', 'translatepress-multilingual' ); ?></td>
            </tr>
        <?php endif; ?>
        <?php foreach ( $trp_ie_languages as $trp_ie_language ) : ?>
            <?php $trp_ie_is_default = ( $trp_ie_language['code'] === $trp_ie_default ); ?>
            <tr>
                <td>
                    <?php echo esc_html( $trp_ie_language['name'] ); ?>
                    <?php if ( $trp_ie_is_default ) : ?>
                        <span class="trp-description-text"><?php esc_html_e( '(this site\'s default language)', 'translatepress-multilingual' ); ?></span>
                    <?php endif; ?>
                </td>
                <td class="trp-ie-num"><?php echo esc_html( $trp_ie_is_default ? '—' : $trp_ie_count( $trp_ie_language['dictionary_rows'] ) ); ?></td>
                <td class="trp-ie-num"><?php echo esc_html( $trp_ie_count( $trp_ie_language['gettext_rows'] ) ); ?></td>
                <?php if ( $trp_ie_show_slugs ) : ?>
                    <td class="trp-ie-num"><?php echo esc_html( empty( $trp_ie_language['slug_rows'] ) ? '—' : number_format_i18n( $trp_ie_language['slug_rows'] ) ); ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
