<template>
  <div>
    <div v-if="needsSlideshows">
      <alert-warning-small :link="metasliderPage">
        <template slot="description">{{ __('No slideshows found.', 'ml-slider') }}</template>
        <template slot="link-text">{{ __('Create a new slideshow now', 'ml-slider') }}</template>
      </alert-warning-small>
    </div>
    <split-layout :loading="loading">
      <template slot="header">{{ __('Export', 'ml-slider') }}</template>
      <template slot="description">
        <ul class="export-instruction">
          <li>{{ __('If you have only a few slideshows, we will prepare them automatically. If you have many slideshows, click the "Load" button.', 'ml-slider') }}</li>
          <li>{{ __('Choose the slideshows you wish to export.', 'ml-slider') }}</li>
          <li>{{ __('Click the "Export" button.', 'ml-slider') }}</li>
          <li>{{ __('A .json file will automatically be downloaded with all your slideshow data.', 'ml-slider') }}</li>
        </ul>
        <b>{{ __('A few notes on exporting slideshow images:', 'ml-slider') }}</b>
        <ul class="export-instruction">
          <li>{{ __('Your images need to be exported manually. Please upload them to the new website before importing these slideshows.', 'ml-slider') }}</li>
          <li>{{ __('Images will need to keep the same file names on the new site.', 'ml-slider') }} <a href="https://www.metaslider.com/how-to-export-and-import-slideshows-from-one-website-to-another"
           target="_blank">{{ this.__('Read our guide on exporting images.', 'ml-slider') }} <span class="dashicons dashicons-external"></span></a> </li>
        </ul>
      </template>
      <template slot="fields">
        <div v-if="needsSlideshows">
          <textbox-with-link link="" :new-tab="false">
            <template slot="header">{{ __('No slideshows found', 'ml-slider') }}</template>
          </textbox-with-link>
        </div>
        <action-button v-else @click="fetchAllSlideshows" :disabled="processing">
          <template slot="header">{{ __('Load slideshows', 'ml-slider') }}</template>
          <template slot="description">{{
              sprintf(__('You have %s slideshows that can be exported. Information about each slideshow will be presented below.', 'ml-slider'), totalSlideshows)
            }}
          </template>
          <template v-if="exporting" slot="button">{{ __('Processing...', 'ml-slider') }}</template>
          <template v-else-if="Object.keys(slideshowsList).length" slot="button">{{
              __('Refresh', 'ml-slider')
            }}
          </template>
          <template v-else-if="processing" slot="button">{{ __('Loading...', 'ml-slider') }}</template>
          <template v-else slot="button">{{ __('Load', 'ml-slider') }}</template>
        </action-button>
        <template v-if="Object.keys(slideshowsList).length || processing"
        :loading="processing">
          <action-button
              @click="exportSlideshows"
              :disabled="!slideshowsToExport.length">
            <template slot="header">{{
                sprintf(__('Export %s slideshows', 'ml-slider'), slideshowsToExport.length)
              }}
            </template>
            <template slot="description">
              {{ __('Select the slideshows you wish to export below, then press here to export them.', 'ml-slider') }}
            </template>
            <template slot="button">{{ __('Export', 'ml-slider') }}</template>
          </action-button>
          <switch-single-input
              v-if="Object.keys(slideshows).length > 10"
              :value="slideshowsToExport.length > 0"
              @change="toggleSlideshowsToExport($event)">
            <template slot="header">{{ __('Toggle all slideshows') }}</template>
            <template slot="description">{{ __('Select or deselect all slideshows') }}</template>
          </switch-single-input>
          <div v-for="slideshow in slideshows" :key="slideshow.id">
            <switch-single-input
                v-model.lazy="slideshowsList[slideshow.id]">
              <template slot="header">{{ slideshow.title }}</template>
              <template slot="subheader">
                <span :title="slideshow.modified_at_gmt">{{
                    sprintf(__('last updated: %s', 'ml-slider'), modifiedAt(slideshow))
                  }}</span>
              </template>
              <template slot="description">
                <div class="pl-3 inline-flex flex-row-reverse justify-end relative z-0 overflow-hidden">
                  <div
                      v-for="slide in slideshow.slides"
                      :key="slide.id"
                      class="relative -ml-3 z-30 inline-block h-12 w-12 text-white border border-gray-light shadow-solid rounded-full">
                    <div
                        v-if="'post_feed' === slide.meta['ml-slider_type']"
                        class="bg-blue border border-blue flex items-center justify-center text-lg text-white rounded-full h-full tipsy-tooltip-top"
                        :original-title="__('Post Feed slide', 'ml-slider')"
                        :title="__('Post Feed slide', 'ml-slider')">
                      P
                    </div>
                    <div
                        v-else-if="'external' === slide.meta['ml-slider_type']"
                        class="bg-blue-light border border-blue-light flex items-center justify-center text-lg text-white rounded-full h-full tipsy-tooltip-top"
                        :original-title="__('External slide', 'ml-slider')"
                        :title="__('External slide', 'ml-slider')">
                      E
                    </div>
                    <img
                        v-else :src="slide.thumbnail"
                        class="gradient border border-white rounded-full h-full inline-block"
                        alt="">
                  </div>
                  <div
                      class="relative -ml-3 z-50 inline-block bg-gray-lighter flex items-center justify-center text-lg text-gray-dark h-12 w-12 rounded-full shadow-solid border border-gray-light">
                    {{ slideshow.slides.length }}
                  </div>
                </div>
              </template>
            </switch-single-input>
          </div>
        </template>
      </template>
    </split-layout>
  </div>
</template>

<script>
import {Slideshow} from '../../api'
import {default as SplitLayout} from '../layouts/_split'
import {default as SwitchSingle} from '../inputs/_switchSingle'
import {default as ActionButton} from '../inputs/_actionButton'
import {default as TextBoxWithLink} from '../inputs/_textBoxWithLink'
import {default as WarningAlert} from '../inputs/alerts/_warningSmall'
import {default as fileDownload} from 'js-file-download'
import {mapState} from 'vuex'
import {DateTime} from "luxon"

export default {
  components: {
    'split-layout': SplitLayout,
    'switch-single-input': SwitchSingle,
    'action-button': ActionButton,
    'textbox-with-link': TextBoxWithLink,
    'alert-warning-small': WarningAlert
  },
  computed: {
    ...mapState({
      slideshows: state => state.slideshows.all,
      totalSlideshows: state => state.slideshows.totalSlideshows
    }),
    loading() {
      return this.totalSlideshows < 1
    },
    slideshowsToExport() {
      if (!Object.keys(this.slideshowsList).length) return []
      let ids = []
      Object.keys(this.slideshowsList).forEach(slideshowId => {
        this.slideshowsList[slideshowId] && ids.push(slideshowId)
      })
      return ids
    }
  },
  watch: {
    totalSlideshows: {
      immediate: true,
      handler: function (total) {
        // Auto load slideshows if there are less than 25.
        this.$nextTick(() => {
          total && total < 25 && this.fetchAllSlideshows()
        })
      }
    },
  },
  props: {
    needsSlideshows: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      slideshowsList: {},
      processing: false,
      exporting: false,
    }
  },
  created() {
  },
  mounted() {
  },
  methods: {
    fetchAllSlideshows() {
      this.processing = true
      this.notifyInfo(
          'metaslider-loading-all-slideshows',
          this.sprintf(this.__('Loading %s slideshows...', 'ml-slider'), this.totalSlideshows, true)
      )

      this.$store.dispatch('slideshows/getAllSlideshows').then(() => {
        this.slideshowsList = {}
        const slideshowsList = {}
        this.slideshows.forEach(slideshow => {
          slideshowsList[slideshow.id] = true
        })
        this.slideshowsList = slideshowsList
        this.processing = false
        this.notifySuccess('metaslider/all-slideshows-loaded', this.__('All Slideshows loaded', 'ml-slider'), true)
      })
    },
    exportSlideshows() {
      if (!this.slideshowsToExport.length) {
        this.notifyWarning(
            'metaslider-exporting-slideshows-no-slideshows',
            this.__('You have no slideshows to export', 'ml-slider'), true)
      }
      this.exporting = true
      this.processing = true
      this.notifyInfo(
          'metaslider-exporting-slideshows',
          this.sprintf(this.__('Exporting %s slideshows...', 'ml-slider'), this.slideshowsToExport.length), true)

      Slideshow.export(this.slideshowsToExport).then(response => {
        const site = window.location.hostname.replace('.', '-')
        const time = new Date().toJSON()
        fileDownload(
            response.data,
            `metaslider-export-${site}-${time}.json`
        )
        this.notifySuccess(
            'metaslider-exporting-slideshows-success',
            this.__('Export successful', 'ml-slider'), true)
      }).catch(error => {
        this.notifyError('metaslider/export-error', error, true)
      }).finally(() => {
        this.exporting = false
        this.processing = false
      })
    },
    modifiedAt(slideshow) {
      return DateTime
          .fromSQL(slideshow.modified_at_gmt, {zone: 'utc'})
          .setLocale(metaslider.locale)
          .toRelative()
    },
    toggleSlideshowsToExport(state) {
      Object.keys(this.slideshowsList).forEach(slideshow => this.slideshowsList[slideshow] = state)
    },
  }
}
</script>
