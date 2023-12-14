<template>
    <form @keydown.enter.prevent="" autocomplete="off" action="#" method="POST" class="mobile-settings-form">  
        <div class="mt-5 md:flex md:items-center">
        <div class="max-w-xs w-full">
            <input
                :id="name"
                class="form-input block w-full md:text-sm md:leading-5 relative rounded-md shadow-sm"
                v-on:input="$emit('input', $event.target.value)"
                :value="value"
                :placeholder="value" />
        </div>
        <span class="mt-3 inline-flex rounded-md shadow-sm md:mt-0 md:ml-3 md:w-auto mobile-settings">
            <button @click="save" type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto md:text-sm md:leading-5">
            {{ __('Save', 'ml-slider') }}
            </button>
        </span>
        </div>
    <transition name="settings-fade" mode="in-out">
        <loading-element v-if="$parent.$attrs.loading"/>
    </transition>
    </form>
</template> 
<script>
    import { default as LoadingElement } from './shimmers/_textSingleShimmer'
    export default {
        props: ['value', 'name'],
        components: {
            'loading-element' : LoadingElement
        },
        data() {
            return {}
        },
        created() {},
        mounted() {},
        methods: {
            save() {
                this.$emit("click")
            }
        }
    }
</script>
