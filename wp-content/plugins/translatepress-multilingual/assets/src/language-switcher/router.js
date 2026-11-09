import { createRouter, createWebHashHistory } from 'vue-router'

import FloaterConfig   from './views/FloaterConfig.vue'
import ShortcodeConfig from './views/ShortcodeConfig.vue'
import MenuConfig      from './views/MenuConfig.vue'

export default createRouter({
    history: createWebHashHistory(),
    routes: [
        { path: '/',          redirect: '/floater' },
        { path: '/floater',   component: FloaterConfig },
        { path: '/shortcode', component: ShortcodeConfig },
        { path: '/menu-item', component: MenuConfig },
    ],
})
