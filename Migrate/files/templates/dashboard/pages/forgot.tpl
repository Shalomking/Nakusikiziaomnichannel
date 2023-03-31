<div class="container-fluid" zender-wrapper zender-authenticate-page zender-wrapper-authenticate>
    {include "../modules/analytics.block.tpl"}

    <div class="row justify-content-center align-items-center" zender-authenticate-form>
        <div class="col-md-{if isTablet}6{else}4{/if}">
            <div class="mb-4 text-center" zender-authenticate-logo>
                <a href="{site_url}"><img src="{get_image("logo_light")}"></a>
            </div>

            <div class="auth-form card">
                <div class="card-body">
                    <form zender-authenticate-forgot>
                        <div class="form-group mb-3">
                            <input type="text" name="email" class="input form-control" placeholder="{__("lang_form_emailaddress")}">
                        </div>

                        {if system_recaptcha < 2}
                        {if !empty(system_recaptcha_key) || !empty(system_recaptcha_secret)}
                        <div class="form-group text-center mb-3">
                            <div class="g-recaptcha" data-sitekey="{system_recaptcha_key}"></div>
                        </div>
                        {/if}
                        {/if}

                        <div class="btn-group-vertical btn-group-lg btn-block">
                            <button type="submit" class="btn btn-success p-2">
                                <i class="la la-shield"></i> {__("lang_btn_retrieve")}
                            </button>

                            <a href="{site_url("dashboard/authenticate/login")}" class="btn btn-warning btn-lg p-2" zender-nav>
                                <i class="la la-close"></i> {__("lang_and_dash_pg_for_line29")}
                            </a>
                        </div>
                    </form>

                    {if system_recaptcha < 2}
                    {if !empty(system_recaptcha_key) || !empty(system_recaptcha_secret)}
                    <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
                    {/if}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>