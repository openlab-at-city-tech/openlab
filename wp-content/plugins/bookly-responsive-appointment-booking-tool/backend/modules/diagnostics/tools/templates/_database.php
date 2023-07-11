<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
/** @var array $troubles */
/** @var bool $fixable */
global $wpdb;
$charset = $wpdb->charset;
$collate = $wpdb->collate;
$charset_collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : 'DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci';
?>
<?php if ( $fixable ) : ?>
    <div class="text-right mb-3">
        <?php Buttons::render( 'bookly-fix-all-silent', 'btn-success', 'Fix database schema…' ) ?>
    </div>
<?php endif ?>
<div id="accordion" class="accordion" role="tablist" aria-multiselectable="true">
    <?php if ( $troubles ) : ?>
        <?php foreach ( $troubles as $table_name => $table ) : ?>
            <div class="card bookly-collapse-with-arrow mb-0" data-table="<?php echo esc_attr( $table_name ) ?>">
                <div class="card-header d-flex align-items-center bookly-js-table py-0 px-2" role="tab">
                    <button role="button" class="btn btn-link btn-block text-left text-danger py-3 shadow-none bookly-collapsed" data-toggle="bookly-collapse" href="#table-<?php echo esc_attr( $table_name ) ?>" aria-expanded="false" aria-controls="<?php echo esc_attr( $table_name ) ?>">
                        <div class="bookly-collapse-title">
                            <?php echo esc_html( $table_name ) ?>
                        </div>
                    </button>
                </div>
                <div class="card-body bookly-collapse pb-1" id="table-<?php echo esc_attr( $table_name ) ?>">
                    <?php if ( isset( $table['missing'] ) ) : ?>
                        <div class="mb-3">Table does not exist</div>
                    <?php else: ?>
                        <?php if ( isset( $table['tables']['character'] ) ) : ?>
                            <div class="text-right mb-3">
                                <?php if ( in_array( 'character_set', $table['tables']['character'][0]['data'] ) &&  in_array( 'collation', $table['tables']['character'][0]['data'] ) ) : ?>
                                    <button class="btn btn-success ml-auto" type="button" data-charset="<?php echo esc_attr( $charset ) ?>" data-collate="<?php echo esc_attr( $collate ) ?>" data-action="fix-charset_collate-table" data-fix='["character_set","collate"]' data-job="<?php echo esc_attr( $table_name . '~tables~character~character_set' ) ?>">Fix character set and collation…</button>
                                <?php else : ?>
                                    <button class="btn btn-success ml-auto" type="button" data-charset="<?php echo esc_attr( $charset ) ?>" data-action="fix-charset_collate-table" data-fix='["character_set"]' data-job="<?php echo esc_attr( $table_name . '~tables~character~character_set' ) ?>">Fix character set…</button>
                                <?php endif ?>
                            </div>
                        <?php endif ?>
                        <?php if ( isset( $table['fields']['missing'] ) ) : ?>
                            <div>Not found columns:
                                <ul class="list-group mb-3">
                                    <?php foreach ( $table['fields']['missing'] as $field ) : ?>
                                        <li class="list-group-item d-flex align-items-center">
                                            <?php echo esc_html( $field['title'] ) ?>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                        <?php if ( isset( $table['fields']['diff'] ) ) : ?>
                            <div>Incorrect columns:
                                <ul class="list-group mb-3">
                                    <?php foreach ( $table['fields']['diff'] as $field ) : ?>
                                        <li class="list-group-item d-flex align-items-center">
                                            <div class="flex-fill">
                                                <?php echo esc_html( $field['title'] ) ?>
                                            </div>
                                            <div>
                                                <?php foreach ( $field['data']['diff'] as $key ) : ?>
                                                    <button class="btn btn-warning disabled" disabled><?php echo esc_html( $key ) ?></button>
                                                <?php endforeach ?>
                                            </div>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                        <?php if ( isset( $table['fields']['unknown'] ) ) : ?>
                            <div>Unknown columns:
                                <ul class="list-group mb-3">
                                    <?php foreach ( $table['fields']['unknown'] as $field ) : ?>
                                        <li class="list-group-item d-flex align-items-center" data-column="<?php echo esc_html( $field['title'] ) ?>" data-job="<?php echo esc_attr( $table_name . '~fields~unknown~' . $field['title'] ) ?>">
                                            <div class="flex-fill">
                                                <?php echo esc_html( $field['title'] ) ?>
                                            </div>
                                            <div>
                                                <button class="btn btn-success" type="button" data-action="drop-column">DROP…</button>
                                            </div>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                        <?php if ( isset( $table['constraints']['missing'] ) ) : ?>
                            <div>Not found constraints:
                                <ul class="list-group mb-3">
                                    <?php foreach ( $table['constraints']['missing'] as $constraint ) : ?>
                                        <li class="list-group-item d-flex align-items-center" data-data='<?php echo json_encode( $constraint['data'] ) ?>'
                                            data-job="<?php echo esc_attr( $table_name . '~constraints~missing~' . $constraint['title'] ) ?>">
                                            <div class="flex-fill">
                                                <?php echo esc_html( $constraint['title'] ) ?>
                                            </div>
                                            <div>
                                                <button class="btn btn-success" type="button" data-action="add-constraint">ADD…</button>
                                            </div>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                        <?php if ( isset( $table['constraints']['unknown'] ) ) : ?>
                            <div>Unknown constraints:
                                <ul class="list-group mb-3">
                                    <?php foreach ( $table['constraints']['unknown'] as $constraint ) : ?>
                                        <li class="list-group-item d-flex align-items-center" data-key="<?php echo esc_html( $constraint['data']['key'] ) ?>"
                                            data-job="<?php echo esc_attr( $table_name . '~constraints~unknown~' . $constraint['title'] ) ?>">
                                            <div class="flex-fill">
                                                <?php echo esc_html( $constraint['title'] ) ?>
                                            </div>
                                            <div>
                                                <button class="btn btn-success" type="button" data-action="drop-constraint">DROP…</button>
                                            </div>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        Database is ok
    <?php endif ?>
</div>
