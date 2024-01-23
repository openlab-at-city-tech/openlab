<?php
namespace Bookly\Backend\Modules\News;

use Bookly\Lib;

class Ajax extends Lib\Base\Ajax
{
    /**
     * Get news
     */
    public static function getNews()
    {
        $query = Lib\Entities\News::query( 'n' );

        if ( ! $query->count() ) {
            Lib\Routines::handleDailyInfo();
        }

        $news_per_page = 30;
        $page = self::parameter( 'page', 1 );
        $total = $query->count();

        $query->sortBy( 'n.created_at' )->order( 'DESC' );

        $query->limit( $news_per_page )->offset( ( $page - 1 ) * $news_per_page );

        $data = $query->fetchArray();
        foreach ( $data as &$row ) {
            $row['created_at'] = Lib\Utils\DateTime::formatDate( $row['created_at'] );
        }

        Lib\Entities\News::query( 'n' )
            ->update()
            ->set( 'seen', 1 )
            ->execute();

        wp_send_json( array(
            'more' => $news_per_page * ( $page - 1 ) + count( $data ) < $total,
            'data' => $data,
        ) );
    }
}