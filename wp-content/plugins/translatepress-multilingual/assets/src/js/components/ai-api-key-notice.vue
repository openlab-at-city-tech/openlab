<template>
    <div class="trp-license-notice">
        <div class="trp-notice trp-notice-warning trp-license-notice-inner">
            <div v-html="licenseNoticeContent">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : [
            'licenseNoticeContent',
        ],
    }
</script>