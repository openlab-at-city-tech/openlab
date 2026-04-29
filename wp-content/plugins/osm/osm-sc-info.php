<?php
/*  (c) Copyright 2026 MiKa (wp-osm-plugin.HyuMiKa.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    global $post;
    $post_org = $post;
    $CustomFieldName = get_option('osm_custom_field', 'OSM_geo_data');
    $pluginDir       = OSM_PRIV_WP_PLUGIN_DIR; // plugin_dir_path() already points to the osm/ plugin folder

    // helper: yes/no badge
    $yes = '<span style="color:green;font-weight:bold">yes</span>';
    $no  = '<span style="color:red;font-weight:bold">no</span>';

    $td1 = 'style="padding:3px 10px 3px 0;font-weight:bold;white-space:nowrap;vertical-align:top"';
    $td2 = 'style="padding:3px 0;vertical-align:top"';

    echo '<style>.osm-info-table{border-collapse:collapse;width:100%;max-width:800px}'
        .'.osm-info-table td{padding:4px 8px;border-bottom:1px solid #ddd}'
        .'.osm-info-table th{text-align:left;background:#f0f0f0;padding:6px 8px}'
        .'.osm-info-section{margin:1em 0 0.3em;font-size:1.1em;font-weight:bold;border-bottom:2px solid #aaa}'
        .'</style>';

    echo '<h2>OpenStreetMap Plugin ' . PLUGIN_VER . '</h2>';

    // ── 1. Environment ───────────────────────────────────────────────────────
    echo '<p class="osm-info-section">Environment</p>';
    echo '<table class="osm-info-table">';
    echo '<tr><td ' . $td1 . '>PHP version</td><td ' . $td2 . '>' . PHP_VERSION . '</td></tr>';
    echo '<tr><td ' . $td1 . '>WordPress version</td><td ' . $td2 . '>' . get_bloginfo('version') . '</td></tr>';
    echo '<tr><td ' . $td1 . '>PHP memory limit</td><td ' . $td2 . '>' . ini_get('memory_limit') . '</td></tr>';
    echo '<tr><td ' . $td1 . '>PHP max execution time</td><td ' . $td2 . '>' . ini_get('max_execution_time') . ' s</td></tr>';
    echo '<tr><td ' . $td1 . '>PHP EXIF extension</td><td ' . $td2 . '>' . (function_exists('exif_read_data') ? $yes : $no) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>PHP cURL extension</td><td ' . $td2 . '>' . (function_exists('curl_init') ? $yes : $no) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>allow_url_fopen</td><td ' . $td2 . '>' . (ini_get('allow_url_fopen') ? $yes : $no) . '</td></tr>';
    echo '</table>';

    // ── 2. Plugin configuration ──────────────────────────────────────────────
    echo '<p class="osm-info-section">Plugin Configuration</p>';
    echo '<table class="osm-info-table">';
    echo '<tr><td ' . $td1 . '>AJAX (Metabox) enabled</td><td ' . $td2 . '>' . (OSM_enable_Ajax ? $yes : $no) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>Custom field name (geo)</td><td ' . $td2 . '>' . esc_html($CustomFieldName) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>Default lat / lon</td><td ' . $td2 . '>' . esc_html(OSM_default_lat) . ' / ' . esc_html(OSM_default_lon) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>Default zoom</td><td ' . $td2 . '>' . esc_html(OSM_default_zoom) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>Plugin URL</td><td ' . $td2 . '>' . esc_html(OSM_PLUGIN_URL) . '</td></tr>';
    echo '</table>';

    // ── 3. JavaScript / CSS library files ───────────────────────────────────
    echo '<p class="osm-info-section">JavaScript &amp; CSS Library Files</p>';

    $filesToCheck = array(
        'js/OL/10.9.0/ol.js'                 => 'OL v10 (ol.js)',
        'js/OL/10.9.0/ol.css'                => 'OL v10 (ol.css)',
        'js/OL/2.13.1/OpenLayers.js'         => 'OL v2 (OpenLayers.js)',
        'js/OSM/openlayers/OpenStreetMap.js' => 'OSM integration (OpenStreetMap.js)',
        'js/osm-v3-plugin-lib.js'            => 'osm-v3-plugin-lib.js',
        'js/osm-startup-lib.js'              => 'osm-startup-lib.js',
        'js/osm-metabox-events.js'           => 'osm-metabox-events.js',
        'js/osm-metabox.js'                  => 'osm-metabox.js',
        'js/osm-plugin-lib.js'               => 'osm-plugin-lib.js',
        'css/osm_map_v3.css'                 => 'osm_map_v3.css',
        'css/osm_map.css'                    => 'osm_map.css',
    );

    $missingFiles = array();
    foreach ($filesToCheck as $rel => $label) {
        if ( ! file_exists($pluginDir . $rel) ) {
            $missingFiles[$rel] = $label;
        }
    }

    if ( empty($missingFiles) ) {
        echo '<p style="color:green;font-weight:bold">&#10003; All ' . count($filesToCheck) . ' library files are present.</p>';
    } else {
        echo '<table class="osm-info-table">';
        echo '<tr><th>Missing file (relative to plugin dir)</th><th>Full path</th></tr>';
        foreach ($missingFiles as $rel => $label) {
            echo '<tr>'
                . '<td ' . $td1 . ' style="color:red">' . esc_html($label) . '</td>'
                . '<td ' . $td2 . '>' . esc_html($pluginDir . $rel) . '</td>'
                . '</tr>';
        }
        echo '</table>';
    }

    // ── 5. Geotagged posts / pages ───────────────────────────────────────────
    echo '<p class="osm-info-section">Geotagged Content</p>';
    echo '<table class="osm-info-table">';
    echo '<tr><th>Type</th><th>Published total</th><th>Geo-tagged</th><th>DB query time</th></tr>';

    foreach (array('post', 'page') as $postType) {
        $Counter   = 0;
        $starttime = microtime(true);
        $recentPosts = new WP_Query();
        $recentPosts->query('meta_key=' . $CustomFieldName . '&post_status=publish&showposts=-1&post_type=' . $postType);
        while ($recentPosts->have_posts()) : $recentPosts->the_post();
            $Data = get_post_meta($post->ID, $CustomFieldName, true);
            $Data = preg_replace('/\s*,\s*/', ',', $Data);
            $parts = explode(',', $Data);
            if (count($parts) >= 2 && $parts[0] !== '' && $parts[1] !== '') {
                list($temp_lat, $temp_lon) = Osm::checkLatLongRange('sc_info', (float)$parts[0], (float)$parts[1], 'no');
                if ($temp_lat != 0 || $temp_lon != 0) {
                    $Counter++;
                }
            }
        endwhile;
        wp_reset_postdata();

        $diff  = microtime(true) - $starttime;
        $count = wp_count_posts($postType);

        echo '<tr>'
            . '<td ' . $td1 . '>' . esc_html($postType) . '</td>'
            . '<td ' . $td2 . '>' . (int) $count->publish . '</td>'
            . '<td ' . $td2 . '>' . $Counter . '</td>'
            . '<td ' . $td2 . '>' . number_format($diff, 4) . ' s</td>'
            . '</tr>';
    }
    echo '</table>';

    // ── 6. Upload directory (GPX / KML) ──────────────────────────────────────
    echo '<p class="osm-info-section">Upload Directory</p>';
    $upload = wp_upload_dir();
    $osmThemeDir = $upload['basedir'] . '/osm/theme/';
    echo '<table class="osm-info-table">';
    echo '<tr><td ' . $td1 . '>Upload base dir</td></tr>'
        . '<tr><td colspan="2" style="padding:0 8px 6px 8px;word-break:break-all;font-family:monospace">' . esc_html($upload['basedir']) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>Upload base URL</td></tr>'
        . '<tr><td colspan="2" style="padding:0 8px 6px 8px;word-break:break-all;font-family:monospace">' . esc_html($upload['baseurl']) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>Upload dir writable</td><td ' . $td2 . '>' . (wp_is_writable($upload['basedir']) ? $yes : $no) . '</td></tr>';
    echo '<tr><td ' . $td1 . '>OSM theme dir exists</td><td ' . $td2 . '>' . (is_dir($osmThemeDir) ? $yes : $no) . '</td></tr>'
        . '<tr><td colspan="2" style="padding:0 8px 6px 8px;word-break:break-all;font-family:monospace">' . esc_html($osmThemeDir) . '</td></tr>';
    echo '</table>';

    $post = $post_org;
?>
