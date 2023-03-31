<div class="container-fluid" zender-wrapper zender-authenticate-page zender-wrapper-authenticate>
    {include "../modules/analytics.block.tpl"}

    <div class="row justify-content-center align-items-center" zender-authenticate-form>
        <div class="col-md-{if isTablet}7{else}5{/if}">
            <div class="mb-4 text-center" zender-authenticate-logo>
                <a href="{site_url}"><img src="{get_image("logo_light")}"></a>
            </div>

            <div class="auth-form card">
                <div class="card-body">
                    {if !$data.confirm}
                    <div zender-register-confirm>
                        {if system_social_auth < 2}
                        {if in_array("facebook", explode(",", system_social_platforms))}
                        <a href="{site_url}/social/facebook" class="btn btn-info btn-lg btn-block text-white">
                            <i class="la la-facebook"></i> {__("lang_and_dash_pg_reg_line15")}
                        </a>
                        {/if}
                        {if in_array("google", explode(",", system_social_platforms))}
                        <a href="{site_url}/social/google" class="btn btn-danger btn-lg btn-block text-white">
                            <i class="la la-google"></i> {__("lang_and_dash_pg_reg_line20")}
                        </a>
                        {/if}
                        {if in_array("vk", explode(",", system_social_platforms))}
                        <a href="{site_url}/social/vkontakte" class="btn btn-primary btn-lg btn-block text-white">
                            <i class="la la-vk"></i> {__("lang_and_dash_pg_reg_line25")}
                        </a>
                        {/if}

                        <div class="row mt-4">
                            <div class="col"><hr></div>
                            <div class="col-auto">{__("lang_form_loginor")}</div>
                            <div class="col"><hr></div>
                        </div>
                        {/if}

                        <form zender-authenticate-register>
                            <div class="form-row">
                                <div class="form-group mb-3 col-md-6">
                                    <input type="text" name="name" class="input form-control" placeholder="{__("lang_form_fullname")}">
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <input type="text" name="email" class="input form-control" placeholder="{__("lang_form_emailaddress")}">
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <input type="password" name="password" class="input form-control" placeholder="{__("lang_form_password")}">
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <input type="password" name="cpassword" class="input form-control" placeholder="{__("lang_form_cpassword")}">
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <label class="text-uppercase">{__("lang_form_usertimezone")}</label>
                                    <select name="timezone" class="form-control" data-live-search="true">
                                        {foreach $data.timezones as $timezone}
                                        <option value="{strtolower($timezone)}" {if strtolower($timezone) eq system_default_timezone}selected{/if}>{strtoupper($timezone)}</option>
                                        {/foreach}
                                    </select>
                                </div>

                                <div class="form-group mb-3 col-md-6">
                                    <label class="text-uppercase">{__("lang_form_countrycode")}</label>
                                    <select name="country" class="form-control" data-live-search="true">
                                        {foreach $data.countries as $country}
                                        <option value="{$country@key}" data-tokens="{strtolower($country)}" {if $country@key eq system_default_country}selected{/if}>{$country} ({$country@key})</option>
                                        {/foreach}
                                    </select>
                                </div>

                                {if system_recaptcha < 2}
                                {if !empty(system_recaptcha_key) && !empty(system_recaptcha_secret)}
                                <div class="form-group text-center col-12">
                                    <div class="g-recaptcha" data-sitekey="{system_recaptcha_key}"></div>
                                </div>
                                {/if}
                                {/if}

                                <div class="btn-group-vertical btn-group-lg btn-block">
                                    <button type="submit" class="btn btn-success p-2">
                                        <i class="la la-edit"></i> {__("lang_btn_signup")}
                                    </button>

                                    <a href="{site_url("dashboard/authenticate/login")}" class="btn btn-warning p-2" zender-nav>
                                        <i class="la la-sign-in"></i> {__("lang_form_haveaccount")}
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="{site_url("dashboard/authenticate/forgot")}" class="text-uppercase" zender-nav>{__("lang_form_forgotpass")}</a>
                        </div>

                        {if system_recaptcha < 2}
                        {if !empty(system_recaptcha_key) && !empty(system_recaptcha_secret)}
                        <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
                        {/if}
                        {/if}
                    </div>
                    {else}
                    <div class="alert alert-success text-justify">
                        <p>{__("lang_form_registerconfirm_desc")}</p>
                    </div>

                    <div class="btn-group-vertical btn-group-lg btn-block">
                        <a href="{site_url("dashboard/authenticate/login")}" class="btn btn-success p-2" zender-nav>
                            <i class="la la-sign-in"></i> {__("lang_btn_signin")}
                        </a>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>