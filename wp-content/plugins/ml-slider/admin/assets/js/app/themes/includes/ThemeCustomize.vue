<template>
    <div class="static-theme-customize" v-if="manifest.length">
        <table>
            <template v-for="(section_item, section_index) in manifest">
                <tr :class="section_item.name ? 'customizer-' + section_item.name : ''">
                    <td colspan="2">
                        <h4><strong>{{ section_item.label }}</strong></h4>
                    </td>
                </tr>
                <tr :class="['spacer-top', section_item.name ? 'customizer-' + section_item.name : '']">
                    <td colspan="2"></td>
                </tr>
                <template v-for="(row_item, row_index) in section_item.settings" 
                    :key="row_index">
                    <!-- Skip fields settings that has slideshow_edit as false  -->
                    <tr v-if="(row_item.type === 'color' || row_item.type === 'fields') && (typeof row_item.slideshow_edit === 'undefined' || row_item.slideshow_edit)"
                        :class="section_item.name ? 'customizer-' + section_item.name : ''">
                        <td>
                            {{ row_item.label }} 
                            <span v-if="row_item.info" 
                                class="dashicons dashicons-info tipsy-tooltip-top" 
                                style="line-height: 1.2em;" 
                                :original-title="row_item.info">
                            </span>
                        </td>
                        <td>
                            <!-- If type is 'fields', let's look for the list of fields -->
                            <template v-if="row_item.type === 'fields'">
                                <template v-for="(field_item, field_index) in row_item.fields" 
                                    :key="field_index">
                                    <template v-if="field_item.type === 'color'">
                                        <input-color :item="field_item"></input-color>
                                    </template>
                                </template>
                            </template>
                            <!-- Get the other regular settings not not grouped into 'fields' -->
                            <template v-else>
                                <template v-if="row_item.type === 'color'">
                                    <input-color :item="row_item"></input-color>
                                </template>
                            </template>
                        </td>
                    </tr>
                </template>
                <tr :class="['spacer-bottom', section_item.name ? 'customizer-' + section_item.name : '']">
                    <td colspan="2"></td>
                </tr>
            </template>
        </table>
    </div>
</template>

<script>
import { default as InputColor } from './inputs/inputColor'

export default {
    components: {
		'input-color': InputColor,
	},
	props: {
        manifest: {
            type: Object,
            required: true
        }
    },
	data() {
		return {}
	},
	created() {},
	mounted() {},
	methods: {}
}
</script>