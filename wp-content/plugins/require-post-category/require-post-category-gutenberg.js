const {select, dispatch} = wp.data;

function RpcPrePublishCheck() {
    let lockPost = false;
    require_post_category.error = false;
    require_post_category.messages = [];

    let rpc_post_pre_save = Object.assign({}, select('core/editor').getCurrentPost(), select('core/editor').getPostEdits());

    if (rpc_post_pre_save.hasOwnProperty('categories')) {
        rpc_post_pre_save['categories'] = rpc_post_pre_save['categories'].filter(function (ele) {
            return ele !== 1;
        });
    }

    jQuery.each(require_post_category.taxonomies, function (taxonomy, config) {
        if (rpc_post_pre_save.hasOwnProperty(taxonomy) && rpc_post_pre_save[taxonomy].length === 0) {
            dispatch('core/notices').createNotice(
                'error',
                config.message,
                {
                    id: 'rpcNotice_' + taxonomy,
                    isDismissible: false
                }
            );
            require_post_category.error = lockPost = true;
        }else{
            dispatch('core/notices').removeNotice('rpcNotice_' + taxonomy);
        }
    });

    if (lockPost === true) {
        dispatch('core/editor').lockPostSaving();
    } else {
        dispatch('core/editor').unlockPostSaving();
    }
}

RpcPrePublishCheck();

let rpc_check_interval = setInterval(RpcPrePublishCheck, 500);
