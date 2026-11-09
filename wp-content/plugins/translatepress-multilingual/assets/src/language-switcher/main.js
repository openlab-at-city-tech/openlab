import { createApp } from 'vue'
import router from './router'
import App from './App.vue'

const mountEl = document.getElementById('tp-language-switcher-root')

createApp(App).use(router).mount(mountEl)
