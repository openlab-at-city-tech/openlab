<template>
	<div class="site-box" :class="siteData.pricing">
		<div class="preview-image" :class="{ 'demo-pro' : siteData.in_pro }">
			<img :src="siteData.screenshot" :alt="siteData.title">
		</div>
		<div class="footer">
			<h4>{{siteData.title}}</h4>
			<div class="theme-actions">
				<button class="button button-secondary" v-on:click="showPreview()">
					{{this.$store.state.strings.preview_btn}}
				</button>
				<button class="button button-primary" v-if="! siteData.in_pro" v-on:click="importSite()">
					{{strings.import_btn}}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
  /* jshint esversion: 6 */

  import { getInstallablePlugins } from '../common/common.js'

  export default {
    name: 'site-item',
    data: function () {
      return {
        strings: this.$store.state.strings
      }
    },
    props: {
      siteData: {
        default: {},
        type: Object,
        required: true
      }
    },
    methods: {
      setupImportData: function () {
        let recommended = this.siteData.recommended_plugins ? this.siteData.recommended_plugins : {}
        let mandatory = this.siteData.mandatory_plugins ? this.siteData.mandatory_plugins : {}
        let plugins = getInstallablePlugins(mandatory, recommended)
        this.$store.commit('updatePlugins', plugins)
      },
      importSite: function () {
        this.setupImportData()
        this.$store.commit('populatePreview', this.siteData)
        this.$store.commit('showImportModal', true)
      },
      showPreview: function () {
        this.setupImportData()
        this.$store.commit('showPreview', true)
        this.$store.commit('populatePreview', this.siteData)
      }
    }
  }
</script>