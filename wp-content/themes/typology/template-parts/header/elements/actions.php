<ul class="typology-nav typology-actions-list">
    <?php
    $elements = typology_get_header_elements();
    foreach ($elements as $element) {
        get_template_part('template-parts/header/elements/' . $element);
    } ?>
</ul>