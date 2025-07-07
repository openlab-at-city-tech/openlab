<template v-if="item.length">
    <div class="ms-select">
        <select 
            :name="`settings[theme_customize][${item.name}]`">
            <option v-for="(option_item, option_index) in item.options" 
                :key="option_index"  
                :value="option_item.value"
                :selected="option_item.value == item.value">
                {{ option_item.label }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    props: {
        item: {
            type: Object,
            required: true
        }
    },
	data() {
		return {}
	}
}
</script>