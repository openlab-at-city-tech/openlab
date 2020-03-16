<script>
    var nonce = '<?php echo wp_create_nonce(TidioLiveChat::TIDIO_XHR_NONCE_NAME); ?>';
</script>
<div id="tidio-wrapper">
    <div class="tidio-box-wrapper">
        <div class="tidio-box tidio-box-actions">
        <div class="logos">
            <div class="logo tidio-logo"></div>
            <div class="logo wp-logo"></div>
        </div>
            <form novalidate id="tidio-start">
                <h1>Start using Tidio</h1>
                <label>
                    Email Address
                    <input type="email" id="email" placeholder="e.g. tidius@tidio.com" required/>
                </label>

                <div class="error"></div>
                <button>Let’s go</button>
            </form>
            <form novalidate id="tidio-login">
                <h1>Log into your account</h1>
                <label>
                    Email Address
                    <input type="email" id="email" placeholder="e.g. tidius@tidio.com" required/>
                </label>

                <label>
                    Password
                    <input type="password" id="password" placeholder="Type your password&hellip;" required/>
                </label>

                <div class="error"></div>
                <button>Go to Tidio panel</button>
                <a class="button btn-link" href="https://www.tidio.com/panel/forgot-password" target="_blank">Forgot password?</a>
            </form>
            <form novalidate id="tidio-project">
                <h1>Choose your project</h1>
                <label>
                    Choose your project
                    <div class="custom-select">
                        <select name="select-tidio-project" id="select-tidio-project">
                            <option selected="selected" disabled>Pick one from the list&hellip;</option>
                        </select>
                    </div>
                </label>

                <div class="error"></div>
                <button>Go to Tidio panel</button>
                <button type="button" id="start-over" class="btn-link">Start all over again</button>
            </form>
            <form id="after-install-text">
                <h1>Your site is already integrated with Tidio Chat</h1>
                <p>All you need to do now is select the “Tidio Chat” tab on the left - that will take you to your Tidio panel. You can also open the panel by using the link below.</p>
                <a href="#" id="open-panel-link" class="button" target="_blank">Go to Panel</a>
            </form>
        </div>
        <div class="tidio-box tidio-box-george">
            <h2>Join 300 000+ websites using Tidio - Live Chat boosted with Bots</h2>
            <p>Increase sales by skyrocketing communication with customers.</p>
            <div class="tidio-box-george-image"></div>
        </div>
    </div>
</div>
