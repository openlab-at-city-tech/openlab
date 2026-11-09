<script setup>
import { __ } from '@wordpress/i18n'
</script>

<template>
    <div id="trp-floater-powered-by">
    <!-- [utm3] -->
        <span v-html="__('Powered by <a href=&quot;https://translatepress.com/?utm_source=frontend-ls&amp;utm_medium=client-site&amp;utm_campaign=powered-by-tp&quot; rel=&quot;nofollow&quot; target=&quot;_blank&quot; title=&quot;WordPress Translation Plugin&quot;>TranslatePress</a>', 'translatepress-multilingual')"></span>
    </div>
</template>

<style scoped>
#trp-floater-powered-by {
    color: var(--text, #b3b3b3) !important;
    font-size: 12px;
    text-align: center;
    padding: 0 5px;
    opacity: 70%;
}

#trp-floater-powered-by :deep(a) {
    color: var(--text, #b3b3b3);
}

.trp-switcher-position-bottom #trp-floater-powered-by {
    border-top: 1px solid var(--border-color, #747474);
    padding-top: 5px;
    margin-top: 5px;
}

.trp-switcher-position-top #trp-floater-powered-by {
    border-bottom: 1px solid var(--border-color, #747474);
    padding-bottom: 5px;
    margin-bottom: 5px;
}
</style>
