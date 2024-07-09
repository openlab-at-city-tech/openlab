<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="accordion" role="tablist" aria-multiselectable="true">
    <?php if ( $list ) : ?>
        <?php foreach ( $list as $plugin ) : ?>
            <div class="card bookly-collapse-with-arrow mb-0">
                <div class="card-header d-flex align-items-center bookly-js-table py-0 px-2" role="tab">
                    <button role="button" class="btn btn-link btn-block text-left text-danger py-3 shadow-none bookly-collapsed" data-toggle="bookly-collapse" href="#file-monitor-<?php echo esc_attr( $plugin['slug'] ) ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $plugin['slug'] ) ?>">
                        <div class="bookly-collapse-title">
                            <?php echo esc_html( $plugin['title'] )  ?>
                        </div>
                    </button>
                    <span class="mr-3 badge bg-danger text-white p-2"><?php echo esc_html( $plugin['mod_count'] + $slice_length )  ?></span>
                </div>
                <div class="card-body bookly-collapse pb-1" id="file-monitor-<?php echo esc_attr( $plugin['slug'] ) ?>">
                    <ul class="list-group" id="bookly-js-booking-forms">
                        <?php foreach ( $plugin['list'] as $path => $data ) : ?>
                            <li class="list-group-item py-0">
                                <div class="row align-items-center">
                                    <div class="col py-0"><?php echo esc_html( $path ) ?></div>
                                    <div class="col-auto p-1">
                                        <span class="small text-muted mr-6"><?php echo esc_html( $data ) ?></span>
                                        <a href="<?php echo add_query_arg(
                                            array(
                                                'file' => rawurlencode( $plugin['slug'] . $path ),
                                                'plugin' => rawurlencode( $plugin['plugin'] ),
                                            ),
                                            self_admin_url( 'plugin-editor.php' )
                                        ) ?>" class="m-0" target="_blank"><i class="fas fa-pencil-alt fa-fw"></i></a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                    <div class="ml-6">
                        <span class="small text-muted">&nbsp;<?php if ( $plugin['mod_count'] > 0 ) : ?>+ <?php echo esc_html( $plugin['mod_count'] ) ?> files<?php endif ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        No changes were detected
    <?php endif ?>
</div>