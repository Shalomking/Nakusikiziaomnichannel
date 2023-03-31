<div class="container-fluid" zender-wrapper zender-authenticate-page zender-wrapper-authenticate>
    {include "../modules/analytics.block.tpl"}

    <div class="row justify-content-center align-items-center" zender-authenticate-form>
        <div class="col-md-{if isTablet}6{else}4{/if}">
            <div class="mb-4 text-center" zender-authenticate-logo>
                <a href="{site_url}"><img src="{get_image("logo_light")}"></a>
            </div>

            <div class="auth-form card">
                <div class="card-body">
                    <div zender-login-confirm>
                        {if system_social_auth < 2}
                        {if in_array("facebook", explode(",", system_social_platforms))}
                        <a href="{site_url}/social/facebook" class="btn btn-info btn-lg btn-block text-white">
                            <i class="la la-facebook"></i> {__("lang_and_dash_pg_log_line14")}
                        </a>
                        {/if}
                        {if in_array("google", explode(",", system_social_platforms))}
                        <a href="{site_url}/social/google" class="btn btn-danger btn-lg btn-block text-white">
                            <i class="la la-google"></i> {__("lang_and_dash_pg_log_line17")}
                        </a>
                        {/if}
                        {if in_array("vk", explode(",", system_social_platforms))}
                        <a href="{site_url}/social/vkontakte" class="btn btn-primary btn-lg btn-block text-white"><i class="la la-vk"></i> {__("lang_and_dash_pg_log_line20")}</a>
                        {/if}

                        <div class="row mt-4">
                            <div class="col"><hr></div>
                            <div class="col-auto">{__("lang_form_loginor")}</div>
                            <div class="col"><hr></div>
                        </div>
                        {/if}

                        <form zender-authenticate-login>
                            <div class="form-group mb-3">
                                <input type="text" name="email" class="input form-control" placeholder="{__("lang_form_emailaddress")}">
                            </div>

                            <div class="form-group mb-3">
                                <input type="password" name="password" class="input form-control" placeholder="{__("lang_form_password")}">
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
                                    <i class="la la-sign-in"></i> {__("lang_btn_signin")}
                                </button>

                                {if system_registrations < 2}
                                <a href="{site_url("dashboard/authenticate/register")}" class="btn btn-warning p-2" zender-nav>
                                    <i class="la la-edit"></i> {__("lang_and_dash_pg_log_line54")}
                                </a>
                                {/if}
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="{site_url("dashboard/authenticate/forgot")}" class="text-uppercase" zender-nav>{__("lang_form_forgotpass")}</a>
                        </div>

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
</div>