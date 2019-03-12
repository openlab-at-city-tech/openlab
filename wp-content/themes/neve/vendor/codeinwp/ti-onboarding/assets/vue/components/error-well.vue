<template>
	<div class="error-well">
		<div class="message">
			<p v-html="strings.error_report"></p>
		</div>
		<div class="buttons">
			<button v-clipboard="errorMessage"
					v-clipboard:success="handleCopy"
					class="button button-primary copy-button">
				<i class="dashicons" :class="{'dashicons-yes' : copied, 'dashicons-admin-page': !copied}"></i>
				<span class="copy-confirm" :class="{copied}">{{strings.copy_error_code}}</span>
			</button>
		</div>
		<pre :class="{collapsed}">{{errorMessage}}
			<span v-if="collapsed" class="expand" @click="togglePre"><i>+</i></span>
		</pre>
	</div>
</template>

<script>
  export default {
    name: 'error-well',
    data () {
      return {
        copied: false,
        collapsed: true
      }
    },
    computed: {
      errorMessage () {
        return this.$store.state.errorToast
      },
      strings () {
        return this.$store.state.strings
      }
    },
    methods: {
      handleCopy () {
        this.copied = true
      },
      togglePre () {
        this.collapsed = !this.collapsed
      }
    }
  }
</script>