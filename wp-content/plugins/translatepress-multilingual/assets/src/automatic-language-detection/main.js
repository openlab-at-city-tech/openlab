import { createApp } from 'vue'
import App from './App.vue'

const mountEl = document.getElementById('tp-ald-popup-configurator-root')

if (mountEl) {
    createApp(App).mount(mountEl)
}
