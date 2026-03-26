<div v-if="selected === 'app-2'">
    <div class="sb-tab-box sb-caching-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{feedsTab.cachingBox.title}}</h3>
        </div>
        <div class="sbi-tab-form-field">
            <div class="sb-form-field">
                <div class="mb-10 caching-form-fields-group">
                    <select id="sbi-caching-intervals" class="sbi-select size-sm mr-3"
                            v-model="model.feeds.cronInterval">
                        <option v-for="(name, key) in feedsTab.cachingBox.inTheBackgroundOptions" :value="key">
                            {{name}}
                        </option>
                    </select>
                    <select id="sbi-caching-cron-time" class="sbi-select size-xs mr-3" v-model="model.feeds.cronTime"
                            v-if="model.feeds.cachingType === 'background' && model.feeds.cronInterval !== '30mins' && model.feeds.cronInterval !== '1hour'">
                        <option v-for="index in 12" :value="index">{{index}}:00</option>
                    </select>
                    <select id="sbi-caching-cron-am-pm" class="sbi-select size-xs mr-3" v-model="model.feeds.cronAmPm"
                            v-if="model.feeds.cachingType === 'background' && model.feeds.cronInterval !== '30mins' && model.feeds.cronInterval !== '1hour'">
                        <option value="am">{{feedsTab.cachingBox.am}}</option>
                        <option value="pm">{{feedsTab.cachingBox.pm}}</option>
                    </select>
                    <button type="button" class="sbi-btn sb-btn-lg sbi-caching-btn" @click="clearCache"
                            :disabled="clearCacheStatus !== null">
                        <span v-html="clearCacheIcon()" :class="clearCacheStatus"></span>
                        {{feedsTab.cachingBox.clearCache}}
                    </button>
                </div>
                <div class="help-text help-text-green" v-html="cronNextCheck"></div>
            </div>
        </div>
    </div>
    <div :class="{'sb-reset-box-style': !model.wpconsentScreen.isPluginActive}" class="sb-tab-box sb-gdpr-box clearfix">
        <div class="tab-label">
            <h3>
                {{feedsTab.gdprBox.title}}
                <span class="sb-tooltip-info gdpr-tooltip" id="sbi-tooltip" v-html="tooltipHelpSvg"
                      @mouseover.prevent.default="toggleElementTooltip(feedsTab.gdprBox.tooltip, 'show', 'left')"
                      @mouseleave.prevent.default="toggleElementTooltip('', 'hide')"></span>
            </h3>
        </div>
        <div class="sbi-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex mb-10">
                    <select id="sbi-gdpr-options" class="sbi-select size-md" v-model="model.feeds.gdpr"
                            @change="gdprOptions">
                        <option value="auto">{{feedsTab.gdprBox.automatic}}</option>
                        <option value="yes">{{feedsTab.gdprBox.yes}}</option>
                        <option value="no">{{feedsTab.gdprBox.no}}</option>
                    </select>
                </div>
                <div class="help-text" v-if="model.feeds.gdpr == 'auto'"
                     :class="['gdpr-help-text-' + model.feeds.gdpr, {'sb-gdpr-active': model.feeds.gdprPlugin}]">
                    <span class="gdpr-active-icon" v-if="model.feeds.gdprPlugin">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.0003 1.66667C5.41699 1.66667 1.66699 5.41667 1.66699 10C1.66699 14.5833 5.41699 18.3333 10.0003 18.3333C14.5837 18.3333 18.3337 14.5833 18.3337 10C18.3337 5.41667 14.5837 1.66667 10.0003 1.66667ZM8.33366 14.1667L4.16699 10L5.34199 8.82501L8.33366 11.8083L14.6587 5.48334L15.8337 6.66667L8.33366 14.1667Z"
                                  fill="#59AB46"/>
                        </svg>
                    </span>
                    <div v-html="feedsTab.gdprBox.infoAuto" :class="{'sb-text-bold': model.feeds.gdprPlugin}"></div>
                    <span v-html="feedsTab.gdprBox.someFacebook" v-if="model.feeds.gdprPlugin"></span>
                    <span v-html="feedsTab.gdprBox.whatLimited" @click="gdprLimited" class="sb-text-bold sb-gdpr-bold"
                          v-if="model.feeds.gdprPlugin"></span>
                </div>
                <div class="help-text" v-if="model.feeds.gdpr == 'yes'" :class="'gdpr-help-text-' + model.feeds.gdpr">
                    <span v-html="feedsTab.gdprBox.infoYes"></span>
                    <span v-html="feedsTab.gdprBox.whatLimited" @click="gdprLimited"
                          class="sb-text-bold sb-gdpr-bold"></span>
                </div>
                <div class="help-text" v-html="feedsTab.gdprBox.infoNo" v-if="model.feeds.gdpr == 'no'"
                     :class="'gdpr-help-text-' + model.feeds.gdpr"></div>
                <div class="sb-gdpr-info-tooltip" v-if="gdprInfoTooltip !== null">
                    <span class="sb-gdpr-info-headline">{{feedsTab.gdprBox.gdprTooltipFeatureInfo.headline}}</span>
                    <ul class="sb-gdpr-info-list">
                        <li v-for="feature in feedsTab.gdprBox.gdprTooltipFeatureInfo.features">{{feature}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-wpconsent-box clearfix sbi-uo-install-notice" v-if="!model.wpconsentScreen.isPluginActive">
        <div class="sbi-tab-notice" >
            <div class="sbi-notice-left">
                <span class="icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.44963 4.41484C9.44963 4.02063 9.1338 3.70034 8.74185 3.69138H4.578C4.31818 3.69138 4.07853 3.83248 3.95084 4.05871L0.653828 9.9024C0.382808 10.384 0.72998 10.9775 1.28322 10.9775H5.44257C5.70239 10.9731 5.94207 10.8342 6.06973 10.608L9.34659 4.79337C9.34659 4.79337 9.34434 4.79337 9.34211 4.79337C9.40929 4.68362 9.44963 4.55595 9.44963 4.41708V4.41484Z" fill="#663D00"/>
                        <path d="M23.9998 11.9989C23.9998 11.8646 23.9595 11.7391 23.8968 11.6316L19.9972 4.71503L17.0877 9.86214L18.1023 11.663C18.1583 11.766 18.1897 11.8847 18.1897 12.0124C18.1897 12.1289 18.1583 12.2364 18.1091 12.3349L14.2677 19.1529C14.1826 19.2716 14.1311 19.4172 14.1311 19.5763C14.1311 19.9772 14.4537 20.2997 14.8523 20.3065H19.005C19.2648 20.3065 19.5045 20.1653 19.6321 19.9391L23.9303 12.3147C23.9303 12.3147 23.9281 12.3125 23.9259 12.3103C23.9707 12.2162 23.9998 12.1109 23.9998 11.9967V11.9989Z" fill="#663D00"/>
                        <path d="M19.9993 4.71278L19.4215 3.68918L16.5836 8.67727L16.503 8.82063L15.8713 9.93157C15.9968 9.71431 16.232 9.56648 16.5007 9.56648C16.7426 9.56648 16.9554 9.68519 17.0876 9.86439V9.85989L19.9971 4.71278H19.9993Z" fill="#FFF7E5"/>
                        <path d="M14.0709 3.68912C14.0709 3.68912 14.0709 3.68912 14.0709 3.69136C14.0619 3.69136 14.053 3.68912 14.0462 3.68912C13.7595 3.68912 13.5131 3.8571 13.3967 4.09901L7.74784 14.0864C7.62465 14.3104 7.38052 14.4627 7.10726 14.4627C6.83399 14.4627 6.57867 14.3037 6.4577 14.0685L5.49907 12.3684C5.37138 12.14 5.1295 12.0011 4.86968 12.0011L0.719276 12.0146C0.168279 12.0146 -0.176654 12.6103 0.0943649 13.0897L3.95806 19.9435C4.08574 20.1697 4.3254 20.3109 4.58522 20.3109H9.52627C9.533 20.3109 9.53747 20.3109 9.54419 20.3109C9.81969 20.3109 10.0571 20.1563 10.1803 19.9278C10.1803 19.9278 10.1803 19.9279 10.1825 19.9301L13.7259 13.7079L15.8739 9.93376L16.5056 8.82279L16.5862 8.67946L19.4218 3.68912H14.0709Z" fill="#663D00"/>
                    </svg>
                </span>
                <div class="sbi-notice-text sbi-notice-text-stacked">
                    <p>
                        <strong>{{feedsTab.wpconsentBox.title}}</strong>
                        {{feedsTab.wpconsentBox.description}}<br>
                        {{feedsTab.wpconsentBox.description2}}
                    </p>
                </div>
            </div>
            <div class="sbi-notice-right">
                <button class="sbi-btn sbi-notice-learn-more"
                        @click="handleWPConsentAction"
                        :disabled="disableWPConsentBtn">
                    <span v-html="wpconsentInstallBtnIcon()"></span>
                    {{model.wpconsentScreen.isPluginInstalled ? 'Activate WPConsent' : 'Install WPConsent'}}
                </button>
            </div>
        </div>
    </div>

    <div class="sb-tab-box sb-custom-css-box sb-reset-box-style clearfix">
        <div class="tab-label">
            <h3>{{feedsTab.customCSSBox.title}}</h3>
        </div>
        <div class="sbi-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex mb-15">
                    <textarea name="" class="sbi-textarea" v-model="model.feeds.customCSS"
                              :placeholder="feedsTab.customCSSBox.placeholder"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="sb-tab-box sb-custom-js-box clearfix">
        <div class="tab-label">
            <h3>{{feedsTab.customJSBox.title}}</h3>
        </div>
        <div class="sbi-tab-form-field">
            <div class="sb-form-field">
                <div class="d-flex mb-15">
                    <textarea name="" class="sbi-textarea" v-model="model.feeds.customJS"
                              :placeholder="feedsTab.customJSBox.placeholder"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- todo: this is just demo content and will be replaced once I work on this -->