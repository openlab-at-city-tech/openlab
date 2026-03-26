<div class="sbi-fb-types-ctn sbi-fb-fs sb-box-shadow" v-if="viewsActive.selectedFeedSection == 'feedsType'">
    <div class="sbi-fb-types sbi-fb-fs">
        <h4>{{selectFeedTypeScreen.feedTypeHeading}}</h4>
        <span class="sbi-fb-types-desc">{{selectFeedTypeScreen.mainDescription}}</span>
        <div class="sbi-fb-types-list sbi-fb-types-list-free">
            <div class="sbi-fb-type-el" v-for="(feedTypeEl, feedTypeIn) in feedTypes"
                 :data-active="selectedFeed.includes(feedTypeEl.type) && feedTypeEl.type != 'socialwall'"
                 :data-type="feedTypeEl.type" @click.prevent.default="chooseFeedType(feedTypeEl)">
                <div class="sbi-fb-type-el-img sbi-fb-fs" v-html="svgIcons[feedTypeEl.icon]"></div>
                <div class="sbi-fb-type-el-info sbi-fb-fs">
                    <p class="sb-small-p sb-bold sb-dark-text">
                        {{feedTypeEl.title}}
                        <span v-if="feedTypeEl.type != 'user'" v-html="svgIcons.rocketPremiumBlue"></span>
                    </p>
                    <a href="" v-if="feedTypeEl.businessRequired != undefined && feedTypeEl.businessRequired">
                        <span v-html="genericText.businessRequired"></span>
                        <div class="sb-control-elem-tltp" v-if="feedTypeEl.tooltip != undefined"
                             @mouseover.prevent.default="toggleElementTooltip(feedTypeEl.tooltip, 'show', feedTypeEl.tooltipAlign ? feedTypeEl.tooltipAlign : 'center' )"
                             @mouseleave.prevent.default="toggleElementTooltip('', 'hide')">
                            <div class="sb-control-elem-tltp-icon" v-html="svgIcons['tooltipHelpSvg']"></div>
                        </div>
                    </a>
                    <span class="sb-caption sb-lightest sb-small-text">{{feedTypeEl.description}}</span>
                </div>
            </div>
        </div>
    </div>
</div>