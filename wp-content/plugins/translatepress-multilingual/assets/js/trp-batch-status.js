( function( $ ) {
    'use strict';

    if ( typeof trpBatchStatus === 'undefined' || ! trpBatchStatus.tasks || ! trpBatchStatus.tasks.length ) {
        return;
    }

    var activeTasks = {};

    function getNoticeElement( task ) {
        var $marker = $( task.selector ).first();

        if ( ! $marker.length ) {
            return $();
        }

        return $marker.closest( '.notice' );
    }

    function updateNotice( task, data ) {
        var $notice = getNoticeElement( task );

        if ( ! $notice.length || ! data || ! data.status ) {
            return;
        }

        if ( data.status === 'no' ) {
            $notice.find( '.trp-batch-message' ).html( data.message );
            return;
        }

        var noticeClass = data.status === 'failed' ? 'notice-error' : 'notice-success';
        var message     = '<p style="padding-right:30px;"><span class="trp-batch-message">' + data.message + '</span>';

        if ( data.status === 'failed' && data.retry_url ) {
            message += ' <a href="' + data.retry_url + '">' + trpBatchStatus.retry_text + '</a>';
        }

        message += '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' + trpBatchStatus.dismiss_text + '</span></button></p>';

        $notice
            .removeClass( 'notice-info notice-success notice-error' )
            .addClass( noticeClass + ' is-dismissible' )
            .html( message );

        $notice.find( '.notice-dismiss' ).on( 'click', function() {
            $notice.remove();
        } );

        delete activeTasks[ task.flag ];
    }

    function pollTask( task ) {
        if ( ! activeTasks[ task.flag ] ) {
            return;
        }

        $.ajax( {
            url: trpBatchStatus.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                action: trpBatchStatus.action,
                nonce: trpBatchStatus.nonce,
                task: task.flag
            }
        } ).done( function( response ) {
            if ( response && response.success && response.data ) {
                updateNotice( task, response.data );
            }
        } ).always( function() {
            if ( activeTasks[ task.flag ] ) {
                window.setTimeout( function() {
                    pollTask( task );
                }, trpBatchStatus.poll_interval || 5000 );
            }
        } );
    }

    $( function() {
        $.each( trpBatchStatus.tasks, function( index, task ) {
            if ( getNoticeElement( task ).length ) {
                activeTasks[ task.flag ] = true;
                pollTask( task );
            }
        } );
    } );
}( jQuery ) );
