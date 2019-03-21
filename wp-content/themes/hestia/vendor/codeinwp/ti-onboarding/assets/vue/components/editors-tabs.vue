<template>
	<tabs>
		<!-- Uncomment the following lines if you want to enable the tab with all the templates -->
		<!--<tab name="all">
			<div class="template-search-wrapper">
				<input type="text" v-model="search" v-bind:placeholder="strings.search + '...'" class="template-search">
			</div>
			<div class="templates-wrapper">
				<template v-for="(data, index) in sites">
					<template v-for="(editor_sites, site_editor) in data">
						<div v-for="site in filterTemplates(editor_sites)">
							<SiteItem :site-data="site"></SiteItem>
						</div>
					</template>
				</template>
			</div>
		</tab>-->
		<template v-for="editor in editors">
			<tab v-bind:name="editor">
				<!-- To enable search in tab, uncomment the following lines -->
				<!--<div class="template-search-wrapper">-->
				<!--<input type="text" v-model="search" v-bind:placeholder="strings.search + '...'"-->
				<!--class="template-search">-->
				<!--</div>-->

				<div class="listing-demo templates-wrapper" v-if="listingDemo[editor]">
					<SiteItem :site-data="listingDemo[editor]"></SiteItem>
				</div>
				<div class="templates-wrapper">
					<template v-for="(data, index) in sites">
						<template v-for="(editor_sites, site_editor) in data">
							<div v-if="site_editor===editor" v-for="site in filterTemplates(editor_sites)">
								<SiteItem :site-data="site"></SiteItem>
							</div>
						</template>
					</template>
				</div>
			</tab>
		</template>
	</tabs>
</template>

<script>
  import Tab from './tab.vue'
  import Tabs from './tabs.vue'
  import SiteItem from './site-item.vue'

  export default {
    name: 'editors-tabs',
    data: function () {
      return {
        strings: this.$store.state.strings,
        search: ''
      }
    },
    computed: {
      editors: function () {
        return this.$store.state.sitesData.editors
      },
      sites: function () {
        let local = this.$store.state.sitesData.local
        let remote = this.$store.state.sitesData.remote
        let upsell = this.$store.state.sitesData.upsell
        return {local, remote, upsell}
      },
      onboard: function () {
        return this.$store.state.onboard
      },
      listingDemo: function () {
        return this.$store.state.sitesData.listing_demo
      }
    },
    methods: {
      filterTemplates: function (sites) {
        let result = {}
        Object.keys(sites).forEach(key => {
          const item = sites[key]
          if (item.title.toLowerCase().indexOf(this.search.toLowerCase()) > -1) {
            result[key] = item
          }
        })
        return result
      }
    },
    components: {
      Tab,
      Tabs,
      SiteItem
    }
  }
</script>