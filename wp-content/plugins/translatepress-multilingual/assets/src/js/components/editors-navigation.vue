<template>
    <div id="trp-editors-navigation-tabs" v-if="editorsNavigation.show">
        <table id="trp-editors-tab-table">
            <tbody>
                <tr>
                    <td class="trp-editors-tab" :class="{'trp-selected-tab': editorsNavigation.tabs[0].handle === selectedTab }">
                        <span class="trp-tooltip-toggle trp-translation-editor-tooltip" :data-tooltip="editorsNavigation.tabs[0].tooltip">
                            <a :href="editorsNavigation.tabs[0].path" class="link_editors_navigation">{{editorsNavigation.tabs[0].label}}</a>
                        </span>
                    </td>
                    <td class="trp-editors-tab" :class="{'trp-selected-tab': editorsNavigation.tabs[1].handle === selectedTab }">
                        <span class="trp-tooltip-toggle" :data-tooltip="editorsNavigation.tabs[1].tooltip">
                            <a :href="editorsNavigation.tabs[1].path" class="link_editors_navigation">{{editorsNavigation.tabs[1].label}}</a>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import Tooltip from "./tooltip.vue"
import axios   from 'axios'
import utils from '../utils'

export default {
    components: {Tooltip},
    props: [
        'editorsNavigation',
        'selectedTab'
    ],
    methods: {

    }
}
</script>
