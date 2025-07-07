<template v-if="item.length">
    <div class="ms-color-tooltip-wrapper">
        <input 
            type="text" 
            class="colorpicker"
            :name="`settings[theme_customize][${item.name}]`" 
            v-model="item.value"
            data-alpha-enabled="true"
        >
        <span class="ms-color-tooltip" v-if="item.label">
            {{ item.label }}
        </span>
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