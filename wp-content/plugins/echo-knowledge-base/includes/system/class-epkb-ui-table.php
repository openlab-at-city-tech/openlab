<?php
/**
 * Custom_Table class to generate a dynamic HTML table in WordPress.
 * 
 * // Example usage:
 *
 *   $data = [
 *       ['id' => 1, 'name' => 'John', 'date' => '2023-01-01'],
 *       ['id' => 2, 'name' => 'Jane', 'date' => '2023-01-02'],
 *   ];
 *   $headings = ['name' => 'Name', 'date' => 'Date'];
 *   $sortable = ['name', 'date'];
 *   $table = new Custom_Table( 'my-table', 10, $headings, $data, $sortable, true);
 *   $table->render();
 */
class EPKB_UI_Table {

    private $table_id;          // Unique identifier for the table
    private $rows_per_page;     // Number of rows per page for pagination
    private $headings;          // Array of column headings (key => label)
    private $data;              // Array of row data
    private $sortable_columns;  // Array of column keys that can be sorted
    private $has_checkboxes;    // Boolean flag for checkboxes in first column
    private $id_column_key;     // Key for the unique row identifier
    private $total_pages;       // Total number of pages
    private $current_page;      // Current page number
    private $total_rows;        // Total number of rows
    private $filter_fields;     // Array of column keys that can be filtered
    private $has_actions;       // Boolean flag for actions column

    /**
     * Constructor to initialize table parameters.
     *
     * @param string $table_id        Unique ID for the table.
     * @param int    $rows_per_page   Number of rows per page.
     * @param array  $headings        Associative array of column keys and labels.
     * @param array  $data            Array of row data.
     * @param array  $sortable_columns Array of sortable column keys.
     * @param bool   $has_checkboxes  Whether to include checkboxes.
     * @param string $id_column_key   The array key used for the unique row ID.
     * @param int    $total_pages     Total number of pages.
     * @param int    $current_page    Current page number.
     * @param int    $total_rows      Total number of rows.
     * @param array  $filter_fields   Array of fields that can be filtered.
     * @param bool   $has_actions     Whether to include actions column.
     */
    public function __construct( $table_id, $rows_per_page, $headings, $data, $sortable_columns = [], $has_checkboxes = false, $id_column_key = 'id', $total_pages = null, $current_page = 1, $total_rows = null, $filter_fields = [], $has_actions = true ) {
        $this->table_id = sanitize_key( $table_id );
        $this->rows_per_page = absint( $rows_per_page );
        $this->headings = array_map( 'sanitize_text_field', $headings );
        $this->data = $data; // Data assumed to be pre-sanitized array
        $this->sortable_columns = array_map( 'sanitize_key', $sortable_columns );
        $this->has_checkboxes = (bool) $has_checkboxes;
        $this->id_column_key = sanitize_key( $id_column_key );
        $this->filter_fields = array_map( 'sanitize_key', $filter_fields );
        $this->has_actions = (bool) $has_actions;
        
        // Initialize pagination information
        $this->total_pages = $total_pages;
        $this->current_page = $current_page;
        $this->total_rows = $total_rows;
    }

    /**
     * Render the HTML table with initial data for the first page.
     */
    public function render() {
        // If pagination details were provided in constructor, use those
        $rows = $this->data;
        $total_rows = $this->total_rows;
        $total_pages = $this->total_pages;
        $current_page = $this->current_page;
        $sortColumn = '';
        $sortOrder = 'asc';
        $filter = '';

        // Output container div for styling        ?>

        <div class="epkb-submissions-table-container">
            <!-- Filter input -->
            <div class="epkb-table-filter-container">
                <div class="epkb-table-total-submissions">
                    <?php esc_html_e( 'Total Submissions:', 'echo-knowledge-base' ); ?>
                    <span class="epkb-total-count"><?php echo esc_html( $total_rows ); ?></span>
                </div>
                <input type="text" id="filter-<?php echo esc_attr( $this->table_id ); ?>" placeholder="<?php esc_attr_e( 'Filter...', 'echo-knowledge-base' ); ?>">
            </div>

            <!-- Table with data attributes for JS -->
            <table id="<?php echo esc_attr( $this->table_id ); ?>"
                   data-rows-per-page="<?php echo esc_attr( $this->rows_per_page ); ?>"
                   data-total-rows="<?php echo esc_attr( $total_rows ); ?>"
                   data-total-pages="<?php echo esc_attr( $total_pages ); ?>"
                   data-current-page="<?php echo esc_attr( $current_page ); ?>"
                   data-sort-column="<?php echo esc_attr( $sortColumn ?? '' ); ?>"
                   data-sort-order="<?php echo esc_attr( $sortOrder ?? 'asc' ); ?>"
                   data-filter="<?php echo esc_attr( $filter ?? '' ); ?>"
                   data-has-checkboxes="<?php echo esc_attr( $this->has_checkboxes ? 'true' : 'false' ); ?>"
                   data-filter-fields="<?php echo esc_attr( implode( ',', $this->filter_fields ) ); ?>">
                <thead>
                    <tr>                        <?php
	                    if ( $this->has_checkboxes ) { ?>
                            <th><input type="checkbox" id="select-all-<?php echo esc_attr( $this->table_id ); ?>"></th>                        <?php
	                    }

						foreach ( $this->headings as $key => $label ) { ?>
                            <th class="<?php echo in_array( $key, $this->sortable_columns ) ? 'sortable' : ''; ?>"
                                data-column="<?php echo esc_attr( $key ); ?>"                                <?php
                                if ( in_array( $key, $this->sortable_columns ) ) { ?>
                                    data-sortable="true"
                                    data-sort-order="<?php echo isset( $sortColumn ) && $sortColumn === $key ? esc_attr( $sortOrder ) : ''; ?>" <?php
                                } ?>>                                <?php
                                echo esc_html( $label );
								if ( in_array( $key, $this->sortable_columns ) ) { ?>
                                    <span class="epkb-sort-icon"></span>                                <?php
								} ?>
                            </th>                        <?php
						}
						if ( $this->has_actions ) { ?>
                        <th><?php esc_html_e( 'Actions', 'echo-knowledge-base' ); ?></th>                        <?php
						}   ?>
                    </tr>
                </thead>
                <tbody>                    <?php
                    foreach ( $rows as $row ) { ?>
                        <tr data-id="<?php echo isset( $row[$this->id_column_key] ) ? esc_attr( $row[$this->id_column_key] ) : ''; ?>">                            <?php
	                        if ( $this->has_checkboxes ){ ?>
                                <td><input type="checkbox" class="select-row" data-id="<?php echo isset( $row[$this->id_column_key] ) ? esc_attr( $row[$this->id_column_key] ) : ''; ?>"></td>                            <?php
	                        }
							foreach ( $this->headings as $key => $label ) { 
								// Allow HTML for 'name' field to support user profile links
								if ( $key === 'name' && isset( $row[$key] ) ) {
									echo '<td>' . wp_kses( $row[$key], array( 'a' => array( 'href' => array() ) ) ) . '</td>';
								} else { ?>
                                <td><?php echo isset( $row[$key] ) ? esc_html( $row[$key] ) : ''; ?></td>                            <?php
								}
							}
							if ( $this->has_actions ) { ?>
                            <td><button class="delete-row" data-id="<?php echo isset( $row[$this->id_column_key] ) ? esc_attr( $row[$this->id_column_key] ) : ''; ?>"><?php esc_html_e( 'Delete', 'echo-knowledge-base' ); ?></button></td>                            <?php
							}   ?>
                        </tr>                        <?php

                        // Make sure we have the required fields before trying to display row info
                        if ( isset( $row['subject'] ) && isset( $row['comment'] ) ) {
                            echo $this->display_row_info( $row['subject'], $row['comment'] );
                        }
                    } ?>
                </tbody>
            </table>

            <!-- Pagination and Action buttons in one row -->
            <div class="pagination-and-actions">
                <!-- Action buttons -->
                <div class="bulk-actions">                    <?php
	                if ( $this->has_checkboxes ) { ?>
                        <button id="delete-selected"><?php esc_html_e( 'Delete Selected', 'echo-knowledge-base' ); ?></button>                    <?php
	                }   ?>
                    <button id="delete-all"><?php esc_html_e( 'Delete All', 'echo-knowledge-base' ); ?></button>
                </div>
                
                <!-- Pagination controls -->
                <div class="pagination">
                    <button id="first-page" <?php echo $current_page <= 1 ? 'disabled' : ''; ?>><?php esc_html_e( 'First', 'echo-knowledge-base' ); ?></button>
                    <button id="prev-page" <?php echo $current_page <= 1 ? 'disabled' : ''; ?>><?php esc_html_e( 'Previous', 'echo-knowledge-base' ); ?></button>
                    <span><?php esc_html_e( 'Page', 'echo-knowledge-base' ); ?> <span id="current-page"><?php echo esc_html( $current_page ); ?></span> <?php esc_html_e( 'of', 'echo-knowledge-base' ); ?> <span id="total-pages"><?php echo esc_html( $total_pages ); ?></span></span>
                    <button id="next-page" <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>><?php esc_html_e( 'Next', 'echo-knowledge-base' ); ?></button>
                    <button id="last-page" <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>><?php esc_html_e( 'Last', 'echo-knowledge-base' ); ?></button>
                </div>
            </div>
        </div>        <?php
    }

    /**
     * Generate HTML for table rows only (used for AJAX responses)
     * 
     * @param array  $rows          Array of row data
     * @return string               HTML for the table rows
     */
    public function generate_rows_html( $rows ) {
        ob_start();
        
        if ( empty( $rows ) ) {

            // If no rows, add a "no data" row
            $colspan = count( $this->headings );
            if ( $this->has_actions ) {
                $colspan++; // +1 for actions column
            }
            if ( $this->has_checkboxes ) {
                $colspan++;
            }            ?>
            <tr>
                <td colspan="<?php echo esc_attr( $colspan ); ?>"><?php esc_html_e( 'No submissions found', 'echo-knowledge-base' ); ?></td>
            </tr>            <?php

        } else {
            foreach ( $rows as $row ) { ?>
                <tr data-id="<?php echo isset( $row[$this->id_column_key] ) ? esc_attr( $row[$this->id_column_key] ) : ''; ?>">
                    <?php if ( $this->has_checkboxes ): ?>
                        <td><input type="checkbox" class="select-row" data-id="<?php echo isset( $row[$this->id_column_key] ) ? esc_attr( $row[$this->id_column_key] ) : ''; ?>"></td>
                    <?php endif; ?>
                    <?php foreach ( $this->headings as $key => $label ): ?>
                        <?php 
                        // Allow HTML for 'name' field to support user profile links
                        if ( $key === 'name' && isset( $row[$key] ) ) {
                            echo '<td>' . wp_kses( $row[$key], array( 'a' => array( 'href' => array() ) ) ) . '</td>';
                        } else { ?>
                            <td><?php echo isset( $row[$key] ) ? esc_html( $row[$key] ) : ''; ?></td>
                        <?php } ?>
                    <?php endforeach; ?>
                    <?php if ( $this->has_actions ): ?>
                    <td><button class="delete-row" data-id="<?php echo isset( $row[$this->id_column_key] ) ? esc_attr( $row[$this->id_column_key] ) : ''; ?>"><?php esc_html_e( 'Delete', 'echo-knowledge-base' ); ?></button></td>
                    <?php endif; ?>
                </tr>
                <?php 
                // Make sure we have the required fields before trying to display row info
                if ( isset( $row['subject'] ) && isset( $row['comment'] ) ) {
                    echo $this->display_row_info( $row['subject'], $row['comment'] );
                }
            }
        }
        
        return ob_get_clean();
    }

    private function display_row_info( $value1, $value2 ) {
        ob_start(); 
        
        // Calculate correct colspan that includes all columns
        $colspan = count( $this->headings ) + 1; // +1 for actions column
        if ( $this->has_checkboxes ) {
            $colspan++;
        }        ?>

        <tr class="epkb-row-info">
            <td colspan="<?php echo esc_attr( $colspan ); ?>">
                <div class="epkb-row-info-content">
                    <div class="epkb-row-info-item">
                        <div class="epkb-row-info-header"><?php esc_html_e( 'Subject:', 'echo-knowledge-base' ); ?></div>
                        <div class="epkb-row-info-value"><?php echo esc_html( $value1 ); ?></div>
                    </div>
                    <div class="epkb-row-info-item">
                        <div class="epkb-row-info-header"><?php esc_html_e( 'Comment:', 'echo-knowledge-base' ); ?></div>
                        <div class="epkb-row-info-value"><?php echo nl2br( esc_html( $value2 ) ); ?></div>
                    </div>
                </div>
            </td>
        </tr><?php
        return ob_get_clean();
    }

}   ?>