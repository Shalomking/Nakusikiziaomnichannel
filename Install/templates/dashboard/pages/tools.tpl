<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-toolbox la-lg"></i> {__("lang_dashboard_tools_title")}
                        </h1>
                    </div>
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
                                    <a href="#" class="nav-link active" zender-tab="zender.{$page}.keys" zender-tab-default>
                                        <i class="la la-key"></i>
                                        <span>{__("lang_dashboard_tools_menukeys")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.webhooks">
                                        <i class="la la-code-branch"></i>
                                        <span>{__("lang_dashboard_tools_menuhooks")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.actions">
                                        <i class="la la-robot"></i>
                                        <span>{__("lang_dashboard_tools_menuactions")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.templates">
                                        <i class="la la-wrench"></i>
                                        <span>{__("lang_dashboard_messages_menutemplates")}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-md-8">
                <zender-tab-content></zender-tab-content>
            </div>
        </div>
    </div>

    {include "../modules/footer.block.tpl"}
</div>