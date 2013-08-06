<script language="JavaScript" type="text/javascript" src="<?php echo plugins_url('download-manager/js/jquery.dataTables.js'); ?>"></script> 
<link rel="stylesheet" href="<?php echo plugins_url('download-manager/css/jquery.dataTables.css'); ?>" type="text/css" media="all" />
<style type="text/css">
#TB_window{
    -moz-box-shadow: 0 0 5px #000;
-webkit-box-shadow: 0 0 5px#000;
box-shadow: 0 0 5px #000;
}
#TB_overlay{
    opacity:0.5;
}
</style>
<div class="wpdmpro">
<div class="container-fluid">
<table id="wpdmmydls" style="width: 100%;" class="dtable table-bordered zebra-striped">
<thead><tr>
<th class="">Title</th>
<th>File Type</th>
<th style="width: 100px;">Download</th></tr></thead>
<tbody>
<?php 
global $wpdm_download_button_class, $wpdm_login_icon, $wpdm_download_icon, $wpdm_lock_icon;
$wpdm_download_button_class = ''; 
$wpdm_login_icon = $wpdm_download_icon = $wpdm_lock_icon = '';
$myfiles = $wpdb->get_results("select * from ahm_files",ARRAY_A);
if(!is_array($myfiles)) $myfiles = array();
 
foreach($myfiles as $mfile): $filetype = end(explode('.',$mfile['file'])) ?>
<tr>
<td style="line-height: normal;height: 40px;padding-left:40px;background: url('<?php echo plugins_url('download-manager/icon/'.($mfile['icon']?$mfile['icon']:'file_extension_'.$filetype.'.png')); ?>') 2px center no-repeat"><a href='?download=<?php echo $mfile['id']; ?>' class="wpdm-pck-dl"><b><?php echo htmlspecialchars(stripcslashes($mfile['title'])); ?></b></a>
<br/>Access: <?php echo $mfile['access']=='guest'?'For EveryOne':'Members Only'; ?><?php echo $mfile['password']==''?'':' / Password Protected'; ?>
</td>
<td><?php echo strtoupper($filetype); ?></td>
<td><?php echo $mfile['download_count']; ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
<script type="text/javascript" charset="utf-8">
            /* Default class modification */
            jQuery.extend( jQuery.fn.dataTableExt.oStdClasses, {
                "sSortAsc": "header headerSortDown",
                "sSortDesc": "header headerSortUp",
                "sSortable": "header"
            } );
            
            jQuery('.wpdm-pck-dl').click(function(){
                tb_show(jQuery(this).html(),this.href+'&modal=1&width=600&height=400');
                return false;
            });

            /* API method to get paging information */
            jQuery.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
            {
                return {
                    "iStart":         oSettings._iDisplayStart,
                    "iEnd":           oSettings.fnDisplayEnd(),
                    "iLength":        oSettings._iDisplayLength,
                    "iTotal":         oSettings.fnRecordsTotal(),
                    "iFilteredTotal": oSettings.fnRecordsDisplay(),
                    "iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
                    "iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
                };
            }

            /* Bootstrap style pagination control */
            jQuery.extend( jQuery.fn.dataTableExt.oPagination, {
                "bootstrap": {
                    "fnInit": function( oSettings, nPaging, fnDraw ) {
                        var oLang = oSettings.oLanguage.oPaginate;
                        var fnClickHandler = function ( e ) {
                            if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
                                fnDraw( oSettings );
                            }
                        };

                       jQuery(nPaging).addClass('pagination').append(
                            '<ul>'+
                                '<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
                                '<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
                            '</ul>'
                        );
                        var els =jQuery('a', nPaging);
                       jQuery(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
                       jQuery(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
                    },

                    "fnUpdate": function ( oSettings, fnDraw ) {
                        var oPaging = oSettings.oInstance.fnPagingInfo();
                        var an = oSettings.aanFeatures.p;
                        var i, sClass, iStart, iEnd, iHalf=Math.floor(oPaging.iTotalPages/2);

                        if ( oPaging.iTotalPages < 5) {
                            iStart = 1;
                            iEnd = oPaging.iTotalPages;
                        }
                        else if ( oPaging.iPage <= iHalf ) {
                            iStart = 1;
                            iEnd = 5;
                        } else if ( oPaging.iPage >= (5-iHalf) ) {
                            iStart = oPaging.iTotalPages - 5 + 1;
                            iEnd = oPaging.iTotalPages;
                        } else {
                            iStart = oPaging.iPage - Math.ceil(5/2) + 1;
                            iEnd = iStart + 5 - 1;
                        }

                        for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
                            // Remove the middle elements
                           jQuery('li:gt(0)', an[i]).filter(':not(:last)').remove();

                            // Add the new list items and their event handlers
                            for ( i=iStart ; i<=iEnd ; i++ ) {
                                sClass = (i==oPaging.iPage+1) ? 'class="active"' : '';
                               jQuery('<li '+sClass+'><a href="#">'+i+'</a></li>')
                                    .insertBefore('li:last', an[i])
                                    .bind('click', function () {
                                        oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                                        fnDraw( oSettings );
                                    } );
                            }

                            // Add / remove disabled classes from the static elements
                            if ( oPaging.iPage === 0 ) {
                               jQuery('li:first', an[i]).addClass('disabled');
                            } else {
                               jQuery('li:first', an[i]).removeClass('disabled');
                            }
                             
                            if ( oPaging.iPage === oPaging.iTotalPages-1 ) {
                               jQuery('li:last', an[i]).addClass('disabled');
                            } else {
                               jQuery('li:last', an[i]).removeClass('disabled');
                            }
                        }

                    }
                }
            } );

            /* Table initialisation */
           jQuery(document).ready(function() {
               jQuery('#wpdmmydls').dataTable(   );
            } );
        </script>