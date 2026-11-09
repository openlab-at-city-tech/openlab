import { createApp } from 'vue'
import Editor from './editor.vue'

if (document.getElementById('trp-editor-container')) {
    const app = createApp(Editor);
    app.mount('#trp-editor-container');
    window.tpEditorApp = app;
}
