<template v-if="item.type === 'range'">
    <div class="ms-range-dual">
        <input 
            type="number"
            :name="`settings[theme_customize][${item.name}]`" 
            v-model="item.value"
            :min="item.min"
            :max="item.max"
        > <span class="after" v-if="item.metric">
            {{ item.metric }}
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