<?php
namespace Bookly\Backend\Modules\Diagnostics\Tools;

class Info extends Tool
{
    protected $slug = 'info';
    protected $hidden = true;

    public function __construct()
    {
        $this->title = 'PHP Info';
    }

    public function render()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        ob_start();
        if ( function_exists( 'phpinfo' ) ) {
            phpinfo();
            $info = ob_get_clean();
        } else {
            $info = '<div class="text-danger w-100 mt-2">undefined function phpinfo</div>';
        }
        ob_start();
        ?>
        <style type="text/css">
            #info table {
                border-collapse: collapse;
                border: 0;
                width: 934px;
                box-shadow: 1px 2px 3px #ccc;
            }

            #info .center {
                text-align: center;
            }

            #info .center table {
                margin: 1em auto;
                text-align: left;
            }

            #info .center th {
                text-align: center !important;
            }

            #info td, #info th {
                border: 1px solid #666;
                font-size: 75%;
                vertical-align: baseline;
                padding: 4px 5px;
            }

            #info h1 {
                font-size: 150%;
            }

            #info h2 {
                font-size: 125%;
            }

            #info .p {
                text-align: left;
            }

            #info .e {
                background-color: #ccf;
                width: 300px;
                font-weight: bold;
            }

            #info .h {
                background-color: #99c;
                font-weight: bold;
            }

            #info .v {
                background-color: #ddd;
                max-width: 300px;
                overflow-x: auto;
                word-wrap: break-word;
            }

            #info .v i {
                color: #999;
            }

            #info img {
                float: right;
                border: 0;
            }

            #info hr {
                width: 934px;
                background-color: #ccc;
                border: 0;
                height: 1px;
            }

        </style>
        <?php
        $styles = ob_get_clean();
        $db = $wpdb->get_var( 'SELECT version() AS version' );

        return self::renderTemplate( '_info', array( 'db' => $db, 'php_info' => $styles . preg_replace( '/(<(style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $info ) ), false );
    }
}