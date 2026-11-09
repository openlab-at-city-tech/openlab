import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'
import StringTranslation from './string-translation.vue'
import TranslationTab from './components/string-translation/translation-tab.vue'

const trp_paths = []

for (const trp_path_index in trp_string_translation_data.string_types_config) {
    if (trp_string_translation_data.string_types_config[trp_path_index]['category_based']) {
        trp_paths.push({
            path: `/${trp_path_index}/`,
            component: TranslationTab,
            props: {
               translationTab: true,
               translationType: Object.keys(trp_string_translation_data.string_types_config[trp_path_index].categories)[0],
               currentTab: trp_string_translation_data.string_types_config[trp_path_index].categories[
                   Object.keys(trp_string_translation_data.string_types_config[trp_path_index].categories)[0]
               ],
               parentTab: trp_string_translation_data.string_types_config[trp_path_index],
               parentTranslationType: trp_path_index
            }
        })

        for (const trp_path_category_index in trp_string_translation_data.string_types_config[trp_path_index].categories) {
            trp_paths.push({
                path: `/${trp_path_index}/${trp_path_category_index}/`,
                component: TranslationTab,
                props: {
                   translationTab: true,
                   translationType: trp_path_category_index,
                   currentTab: trp_string_translation_data.string_types_config[trp_path_index].categories[trp_path_category_index],
                   parentTab: trp_string_translation_data.string_types_config[trp_path_index],
                   parentTranslationType: trp_path_index
                }
            })
        }
    } else {
        trp_paths.push({
            path: `/${trp_path_index}/`,
            component: TranslationTab,
            props: {
               translationTab: true,
               translationType: trp_path_index,
               currentTab: trp_string_translation_data.string_types_config[trp_path_index],
               parentTab: false,
               parentTranslationType: false
            }
        })
    }
}

// Redirect all unregistered paths to the first path
const first_tab = trp_paths[0]
trp_paths.push({
    path: '/:pathMatch(.*)*',
    redirect: first_tab.path
})

// Create Vue Router instance
const router = createRouter({
    history: createWebHashHistory(),
    routes: trp_paths,
    linkExactActiveClass: 'nav-tab-exact-active',
    linkActiveClass: 'nav-tab-active'
})

if (document.getElementById('trp-editor-container')) {
    const app = createApp(StringTranslation)
    app.use(router)
    app.mount('#trp-editor-container')
}
