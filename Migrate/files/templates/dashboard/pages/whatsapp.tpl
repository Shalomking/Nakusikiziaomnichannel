<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-whatsapp la-lg"></i> {__("lang_and_dash_pg_whats_line10")}
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
                                    <a href="#" class="nav-link active" zender-tab="zender.{$page}.queue" zender-tab-default>
                                        <i class="la la-tasks"></i>
                                        <span>{__("lang_tabs_wapage_queuebtn")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.sent">
                                        <i class="la la-telegram"></i>
                                        <span>{__("lang_and_dash_pg_whats_line28")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.received">
                                        <i class="la la-sms"></i>
                                        <span>{__("lang_and_dash_pg_whats_line34")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.campaigns">
                                        <i class="la la-coffee"></i>
                                        <span>{__("lang_pages_whatsapp_campaignstab")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.scheduled">
                                        <i class="la la-clock"></i>
                                        <span>{__("lang_and_dash_pg_whats_line40")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.groups">
                                        <i class="la la-users"></i>
                                        <span>{__("lang_pages_whatsapp_groupstab")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.accounts">
                                        <i class="la la-user-circle"></i>
                                        <span>{__("lang_and_dash_pg_whats_line46")}</span>
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