<template>
	<div class="preview-sidebar">
		<div class="preview-sidebar__container">
			<h5 class="site-title ellipsis">{{site_data.title}}</h5>
			<div class="buttons-wrap">
				<button class="button button-secondary" v-on:click="cancelPreview()">
					{{strings.cancel_btn}}
				</button>
				<button class="button button-primary" v-on:click="site_data.in_pro ? buyPro() : showModal()">
					{{ site_data.in_pro ? strings.pro_btn : strings.import_btn}}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
  /* jshint esversion: 6 */

  export default {
    name: 'preview-sidebar',
    data: function () {
      return {
        strings: this.$store.state.strings
      }
    },
    props: {
      site_data: {
        default: {},
        type: Object
      }
    },
    methods: {
      cancelPreview: function () {
        this.$store.commit('resetStates')
      },
      showModal: function () {
        this.$store.commit('showImportModal', true)
      },
      buyPro: function () {
        var win = window.open(this.$store.state.sitesData.pro_link, '_blank')
        win.focus()
      }
    }
  }
</script>

<style scoped>
	.preview-sidebar {
		border-top: 1px solid #ccc;
	}

	.site-title {
		margin: 0;
		font-size: 15px;
	}

	.buttons-wrap {
		align-self: flex-end;
		margin-left: auto;
	}

	.ellipsis {
		max-width: 50%;
	}
</style>