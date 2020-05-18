<ul class="ngg-breadcrumbs">
    <?php
    $end = end($breadcrumbs);
    reset($breadcrumbs);
    foreach ($breadcrumbs as $crumb) { ?>
        <li class="ngg-breadcrumb">
            <?php if (!is_null($crumb['url'])) { ?>
                <a href="<?php echo $crumb['url']; ?>"><?php print wp_kses($crumb['name'], M_I18N::get_kses_allowed_html()); ?></a>
            <?php } else { ?>
                <?php print wp_kses($crumb['name'], M_I18N::get_kses_allowed_html()); ?>
            <?php } ?>
            <?php if ($crumb !== $end) { ?>
                <span class="ngg-breadcrumb-divisor"><?php echo $divisor; ?></span>
            <?php } ?>
        </li>
    <?php } ?>
</ul>