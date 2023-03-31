<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-tools la-lg"></i> {__("lang_dashboard_admin_title")}
                        </h1>
                    </div>

                    {if super_admin}
                    <div class="float-right">
                        <button class="btn btn-lg btn-primary" title="{__("lang_pages_admin_tokenrefreshbutthelp")}" zender-action="token">
                            <i class="la la-refresh la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_pages_admin_tokenrefreshbutt")}</span>
                        </button>

                        <button class="btn btn-lg btn-danger" title="{__("lang_pages_admin_cachehelp")}" zender-action="clear">
                            <i class="la la-trash la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_administration_landing_btncache")}</span>
                        </button>

                        <button class="btn btn-lg btn-primary" title="{__("lang_pages_admin_themehelp")}" zender-toggle="zender.admin.theme">
                            <i class="la la-palette la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_dashboard_btn_theme")}</span>
                        </button>

                        <button class="btn btn-lg btn-primary" title="{__("lang_pages_admin_systemsettingshelp")}" zender-toggle="zender.admin.settings">
                            <i class="la la-cog la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_dashboard_btn_settings")}</span>
                        </button>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-md-4">
                <div class="tabs-menu">
                    <div class="card">
                        <div class="card-body">
                            <ul>
                                <li class="nav-item">
                                    <a href="#" class="nav-link active" zender-tab="zender.{$page}.default" zender-tab-default="false">
                                        <i class="la la-chart-area"></i>
                                        <span>{__("lang_dashboard_admin_menustats")}</span>
                                    </a>
                                </li>
                                {if permission("manage_users")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.users">
                                        <i class="la la-users"></i>
                                        <span>{__("lang_dashboard_admin_menuusers")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_roles")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.roles">
                                        <i class="la la-shield"></i>
                                        <span>{__("lang_dashboard_admin_menuroles")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_packages")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.packages">
                                        <i class="la la-cubes"></i>
                                        <span>{__("lang_dashboard_admin_menupackages")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_vouchers")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.vouchers">
                                        <i class="la la-money-bill-wave"></i>
                                        <span>{__("lang_dashboard_admin_menuvouchers")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_subscriptions")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.subscriptions">
                                        <i class="la la-crown"></i>
                                        <span>{__("lang_dashboard_admin_menusubscriptions")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_transactions")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.transactions">
                                        <i class="la la-coins"></i>
                                        <span>{__("lang_dashboard_admin_menutransactions")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_payouts")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.payouts">
                                        <i class="la la-money-check-alt"></i>
                                        <span>{__("lang_administration_landing_payouts")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_widgets")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.widgets">
                                        <i class="la la-puzzle-piece"></i>
                                        <span>{__("lang_dashboard_admin_menuwidgets")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_pages")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.pages">
                                        <i class="la la-stream"></i>
                                        <span>{__("lang_dashboard_admin_menupages")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_marketing")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.marketing">
                                        <i class="la la-bullhorn"></i>
                                        <span>{__("lang_administration_landing_marketing")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_languages")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.languages">
                                        <i class="la la-language"></i>
                                        <span>{__("lang_dashboard_admin_menulanguages")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_gateways")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.gateways">
                                        <i class="la la-code"></i>
                                        <span>{__("lang_administration_landing_gateways")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_shorteners")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.shorteners">
                                        <i class="la la-link"></i>
                                        <span>{__("lang_administration_landing_shorteners")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_plugins")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.plugins">
                                        <i class="la la-cogs"></i>
                                        <span>{__("lang_dashboard_admin_menuplugins")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if permission("manage_api")}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.api">
                                        <i class="la la-terminal"></i>
                                        <span>{__("lang_administration_landing_api")}</span>
                                    </a>
                                </li>
                                {/if}
                                {if super_admin}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.filemanager">
                                        <i class="la la-file-code"></i>
                                        <span>{__("lang_administration_landing_editor")}</span>
                                    </a>
                                </li>
                                {/if}
                            </ul>
                        </div>
                    </div>
                </div>

                {if super_admin}
                <div class="card text-center">
                    <div class="card-header d-block pt-3 pb-2">
                        <h3 class="text-uppercase">
                            <i class="la la-android la-lg"></i> {__("lang_admin_gateway_title")}
                        </h3>
                    </div>

                    <div class="card-body">
                        <h4 class="text-uppercase">{__("lang_admin_gateway_status")}: {if $data.gateway}<span class="badge badge-success">{__("lang_admin_gateway_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_admin_gateway_notuploaded")}</span>{/if}</h4>
                        <h4 class="text-uppercase">{__("lang_administration_landing_build")}: <span class="badge badge-success">v{system_apk_version}</span></h4>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-md btn-primary" zender-build>
                            <i class="la la-hammer la-lg"></i> {__("lang_dashboard_btn_build")}
                        </button>

                        <button class="btn btn-md btn-primary" zender-toggle="zender.admin.builder">
                            <i class="la la-tools la-lg"></i> {__("lang_dashboard_btn_buildsettings")}
                        </button>
                    </div>
                </div>

                <div class="card text-center">
                    <div class="card-header d-block pt-3 pb-2">
                        <h3 class="text-uppercase">
                            <i class="la la-whatsapp la-lg"></i> {__("lang_template_administration_wablocktitle")}
                        </h3>
                    </div>

                    <div class="card-body">
                        {if empty(system_wa_server) || empty(system_wa_port)}
                        <h4 class="text-uppercase"><span class="badge badge-danger">{__("lang_template_administration_wablocknotconfig")}</span></h4>
                        {else}
                        <h4 class="text-uppercase">{__("lang_template_administration_wablockstatus")} {if $data.whatsapp}<span class="badge badge-success">{__("lang_template_administration_wablockonline")}</span>{else}<span class="badge badge-danger">{__("lang_template_administration_wablockoffline")}</span>{/if}</h4>
                        {/if}
                    </div>
                </div>

                <div class="card text-center">
                    <div class="card-header d-block pt-3 pb-2">
                        <h3 class="text-uppercase">
                            <i class="la la-cogs la-lg"></i> {__("lang_administration_landing_system")}
                        </h3>
                    </div>

                    <div class="card-body">
                        <h4 class="text-uppercase">{__("lang_admin_update_installed")}: <span class="badge badge-success">v{version}</span></h4>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-md btn-primary" zender-toggle="zender.system.update">
                            <i class="la la-terminal la-lg"></i> {__("lang_admin_update_btn")}
                        </button>

                        <button class="btn btn-md btn-primary" zender-support>
                            <i class="la la-headset la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_dashboard_btn_support")}</span>
                        </button>
                    </div>
                </div>
                {/if}
            </div>

            <div class="col-xl-9 col-md-8">
                <zender-tab-content></zender-tab-content>
            </div>
        </div>
    </div>
    
    {include "../modules/footer.block.tpl"}
</div>