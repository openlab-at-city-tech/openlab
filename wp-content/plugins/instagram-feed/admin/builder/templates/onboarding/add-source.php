<div class="sb-onboarding-wizard-step-wrapper sb-onboarding-wizard-step-addsource sb-fs">
    <div class="sb-onboarding-wizard-step-top sb-fs">
        <strong v-html="onboardingWizardStepContent['add-source'].smallHeading"></strong>
        <h4 v-html="onboardingWizardStepContent['add-source'].heading"></h4>
    </div>
    <div class="sb-onboarding-wizard-sources-list sb-fs">

        <div class="sb-onboarding-wizard-source-newitem sb-fs"
             @click.prevent.default="activateView('sourcePopup', 'creationRedirect')">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.66634 5.66634H5.66634V9.66634H4.33301V5.66634H0.333008V4.33301H4.33301V0.333008H5.66634V4.33301H9.66634V5.66634Z"
                      fill="#0096CC"/>
            </svg>
            <span class="sb-small-p sb-bold">{{genericText.addNew}}</span>
        </div>

        <div class="sb-onboarding-wizard-source-item sb-fs" v-for="source in sourcesList">
            <div class="sb-onboarding-wizard-source-item-avatar">
                <img :src="getSourceListAvatar(source?.header_data)" :alt="source.username"/>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="16" height="16" rx="5" fill="white"/>
                    <path d="M8 4.40625C6 4.40625 4.40625 6.03125 4.40625 8C4.40625 10 6 11.5938 8 11.5938C9.96875 11.5938 11.5938 10 11.5938 8C11.5938 6.03125 9.96875 4.40625 8 4.40625ZM8 10.3438C6.71875 10.3438 5.65625 9.3125 5.65625 8C5.65625 6.71875 6.6875 5.6875 8 5.6875C9.28125 5.6875 10.3125 6.71875 10.3125 8C10.3125 9.3125 9.28125 10.3438 8 10.3438ZM12.5625 4.28125C12.5625 3.8125 12.1875 3.4375 11.7188 3.4375C11.25 3.4375 10.875 3.8125 10.875 4.28125C10.875 4.75 11.25 5.125 11.7188 5.125C12.1875 5.125 12.5625 4.75 12.5625 4.28125ZM14.9375 5.125C14.875 4 14.625 3 13.8125 2.1875C13 1.375 12 1.125 10.875 1.0625C9.71875 1 6.25 1 5.09375 1.0625C3.96875 1.125 3 1.375 2.15625 2.1875C1.34375 3 1.09375 4 1.03125 5.125C0.96875 6.28125 0.96875 9.75 1.03125 10.9062C1.09375 12.0312 1.34375 13 2.15625 13.8438C3 14.6562 3.96875 14.9062 5.09375 14.9688C6.25 15.0312 9.71875 15.0312 10.875 14.9688C12 14.9062 13 14.6562 13.8125 13.8438C14.625 13 14.875 12.0312 14.9375 10.9062C15 9.75 15 6.28125 14.9375 5.125ZM13.4375 12.125C13.2188 12.75 12.7188 13.2188 12.125 13.4688C11.1875 13.8438 9 13.75 8 13.75C6.96875 13.75 4.78125 13.8438 3.875 13.4688C3.25 13.2188 2.78125 12.75 2.53125 12.125C2.15625 11.2188 2.25 9.03125 2.25 8C2.25 7 2.15625 4.8125 2.53125 3.875C2.78125 3.28125 3.25 2.8125 3.875 2.5625C4.78125 2.1875 6.96875 2.28125 8 2.28125C9 2.28125 11.1875 2.1875 12.125 2.5625C12.7188 2.78125 13.1875 3.28125 13.4375 3.875C13.8125 4.8125 13.7188 7 13.7188 8C13.7188 9.03125 13.8125 11.2188 13.4375 12.125Z"
                          fill="url(#paint0_linear_4085_39232)"/>
                    <defs>
                        <linearGradient id="paint0_linear_4085_39232" x1="5.97188" y1="27.7906" x2="35.4594"
                                        y2="-2.30938" gradientUnits="userSpaceOnUse">
                            <stop stop-color="white"/>
                            <stop offset="0.147864" stop-color="#F6640E"/>
                            <stop offset="0.443974" stop-color="#BA03A7"/>
                            <stop offset="0.733337" stop-color="#6A01B9"/>
                            <stop offset="1" stop-color="#6B01B9"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <strong v-html="source.username"></strong>
            <div class="sb-onboarding-wizard-source-item-delete"
                 @click.prevent.default="openDialogBox('deleteSource', source)">
                <svg width="10" height="12" viewBox="0 0 10 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.00065 10.6667C1.00065 11.4 1.60065 12 2.33398 12H7.66732C8.40065 12 9.00065 11.4 9.00065 10.6667V2.66667H1.00065V10.6667ZM2.33398 4H7.66732V10.6667H2.33398V4ZM7.33398 0.666667L6.66732 0H3.33398L2.66732 0.666667H0.333984V2H9.66732V0.666667H7.33398Z"
                          fill="#AF2121"/>
                </svg>
            </div>
        </div>

    </div>
</div>

<div class="sb-onboarding-wizard-step-pag-btns sb-fs">
    <button class="sb-btn sbi-btn-grey sb-btn-wizard-next" v-if="sourcesList.length === 0" v-html="'Skip this step'"
            @click.prevent.default="nextWizardStep"></button>
    <button class="sb-btn sbi-btn-blue sb-btn-wizard-next" v-if="sourcesList.length >= 1" v-html="'Next'"
            @click.prevent.default="nextWizardStep"></button>
</div>