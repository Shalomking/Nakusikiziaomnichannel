<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="{if !isMobile}float-left{else}text-center{/if}">
                        {if system_freemodel < 2}
                        <button class="btn btn-lg btn-primary" zender-toggle="zender.user.subscription">
                            <i class="la la-crown la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_dashboard_nav_menusubscription")}</span>
                        </button>
                        {else}
                        {if !empty($data.package)}
                        <button class="btn btn-lg btn-primary" zender-toggle="zender.user.subscription">
                            <i class="la la-crown la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_dashboard_nav_menusubscription")}</span>
                        </button>
                        {/if}
                        {/if}

                        <button class="btn btn-lg btn-primary" zender-toggle="zender.packages">
                            <i class="la la-cubes la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_btn_packages")}</span>
                        </button>

                        <button class="btn btn-lg btn-primary" zender-toggle="zender.redeem">
                            <i class="la la-ticket la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_btn_redeem")}</span>
                        </button>
                    </div>

                    <div class="{if !isMobile}float-right{else}text-center mt-2{/if}">
                        <a href="{site_url("dashboard/rates")}" class="btn btn-lg btn-primary" zender-nav>
                            <i class="la la-comments-dollar la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_and_dashboard_pages_defailt_line37")}</span>
                        </a>

                        <a href="{site_url("dashboard/docs")}" class="btn btn-lg btn-primary" zender-nav>
                            <i class="la la-book la-lg"></i>
                            <span class="d-none d-sm-inline">{__("lang_and_dashboard_pages_default_line42")}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            {if !isMobile}
            <div class="col-9">
                <div class="card animated fadeIn">
                    <div class="card-header border-0">
                        <h2>
                            <i class="la la-mail-bulk la-lg"></i> 
                            {__("lang_and_dashboard_pages_default_line58")}
                        </h2>
                        <h4 class="text-primary">{__("lang_and_dashboard_pages_default_line60")}</h4>
                    </div>

                    <div class="card-body pt-0 pb-0">
                        <div class="embed-responsive">
                            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/dashboard.messages"></iframe>
                        </div>
                    </div>
                </div>

                <div class="card animated fadeIn">
                    <div class="card-header border-0">
                        <h2>
                            <i class="la la-chart-line la-lg"></i> 
                            {__("lang_and_dashboard_pages_default_line74")}
                        </h2>
                        <h4 class="text-primary">{__("lang_and_dashboard_pages_default_line76")}</h4>
                    </div>

                    <div class="card-body pt-0 pb-0">
                        <div class="embed-responsive">
                            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/dashboard.events"></iframe>
                        </div>
                    </div>
                </div>

                <div class="card animated fadeIn">
                    <div class="card-header border-0">
                        <h2>
                            <i class="la la-tools la-lg"></i> 
                            {__("lang_and_dashboard_pages_default_line90")}
                        </h2>
                        <h4 class="text-primary">{__("lang_and_dashboard_pages_default_line92")}</h4>
                    </div>

                    <div class="card-body pt-0 pb-0">
                        <div class="embed-responsive">
                            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/dashboard.utilities"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            {/if}

            <div class="{if !isMobile}col-3{else}col-12{/if}">
                {if $data.partner < 2}
                <div class="card right-widget">
                    <div class="card-body">
                        <h4 class="text-uppercase">
                            <i class="la la-handshake la-lg"></i> {__("lang_and_dashboard_pages_default_line109")}
                        </h4>

                        <ul class="list-unstyled">
                            <li class="media">
                                <i class="la la-hand-holding-usd mr-2"></i>
                                <div class="media-body">
                                    <h6 class="m-0 text-uppercase">
                                        {__("lang_and_dashboard_pages_default_line117")}
                                    </h6>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">
                                        <span title="{$data.balance.earnings} {strtoupper(system_currency)}">{number_format($data.balance.earnings, 2)} {strtoupper(system_currency)}</span>
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="card-footer text-center">
                        <button class="btn btn-primary btn-lg" zender-toggle="zender.add.payout">
                            <i class="la la-money-check-alt"></i> {__("lang_and_dashboard_pages_default_line131")}
                        </button>
                    </div>
                </div>
                {/if}

                <div class="card right-widget">
                    <div class="card-body">
                        <h4 class="text-uppercase">
                            <i class="la la-digital-tachograph la-lg"></i> {__("lang_and_dashboard_pages_default_line140")}
                        </h4>

                        <ul class="list-unstyled">
                            <li class="media">
                                <i class="la la-coins mr-2"></i>
                                <div class="media-body">
                                    <h6 class="m-0 text-uppercase">
                                        {__("lang_and_dashboard_pages_default_line148")}
                                    </h6>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">
                                        <span title="{$data.balance.credits} {strtoupper(system_currency)}">{number_format($data.balance.credits, 2)} {strtoupper(system_currency)}</span>
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="card-footer text-center">
                        <button class="btn btn-primary btn-lg" zender-toggle="zender.add.credits">
                            <i class="la la-coins"></i> {__("lang_and_dashboard_pages_default_line162")}
                        </button>
                    </div>
                </div>

                <div class="card right-widget">
                    <div class="card-body">
                        <h4 class="text-uppercase">
                            <i class="la la-chart-bar la-lg"></i> {__("lang_and_dashboard_pages_default_line170")}
                        </h4>

                        <ul class="list-unstyled">
                            <li class="media">
                                <i class="la la-arrow-circle-up text-success mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {__("lang_and_dash_pg_def_line178")}
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <h5 class="text-uppercase text-success">{$data.ratio.success}%</h5>
                                </div>
                            </li>

                            <li class="media">
                                <i class="la la-arrow-circle-down text-danger mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {__("lang_and_dash_pg_def_line190")}
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <h5 class="text-uppercase text-danger">{$data.ratio.failed}%</h5>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card right-widget">
                    <div class="card-body">
                        <h4 class="text-uppercase">
                            <i class="la la-link la-lg"></i> {__("lang_and_dash_pg_def_line204")}
                        </h4>

                        {if empty($data.package)}
                        <div class="alert alert-danger mt-3 mb-0">
                            {__("lang_and_dash_pg_def_line209")}
                        </div>
                        {else}
                        <ul class="list-unstyled">
                            <li class="media">
                                <i class="la la-mobile mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {__("lang_and_dash_pg_def_line217")}
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">
                                        {$data.count.devices} / {if $data.package.device_limit > 0}{$data.package.device_limit}{else}<i class="la la-infinity infinity"></i>{/if}
                                    </span>
                                    <h5 class="text-uppercase">{__("lang_and_dash_pg_def_line224")}</h5>
                                </div>
                            </li>

                            <li class="media">
                                <i class="la la-whatsapp mr-2"></i>
                                <div class="media-body">
                                    <h5 class="m-0 text-uppercase">
                                        {__("lang_and_dash_pg_def_line232")}
                                    </h5>
                                </div>
                                <div class="text-right">
                                    <span class="text-warning">
                                        {$data.count.accounts} / {if $data.package.wa_account_limit > 0}{$data.package.wa_account_limit}{else}<i class="la la-infinity infinity"></i>{/if}
                                    </span>
                                    <h5 class="text-uppercase">{__("lang_and_dash_pg_def_line239")}</h5>
                                </div>
                            </li>
                        </ul>
                        {/if}
                    </div>
                </div>

                {* <div class="card app-download-widget">
                    <div class="card-body">
                        <div class="app-download-body text-uppercase text-center">
                            <h4>{__("lang_and_dash_pg_def_line250")}</h4>
                            <div class="mt-4 text-center">
                                <a href="#" class="btn btn-primary py-3"><img src="{_assets("images/android.svg")}"></a>
                                <a href="#" class="btn btn-success py-3"><img src="{_assets("images/apple.svg")}"></a>
                            </div>
                        </div>
                    </div>
                </div> *}
            </div>
        </div>
    </div>

    {include "../modules/footer.block.tpl"}
</div>