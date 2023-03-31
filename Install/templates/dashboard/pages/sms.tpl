<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-comment la-lg"></i> {__("lang_dashpages_sms_headertitle")}
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
                                        <span>{__("lang_tabs_smspage_queuebtn")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.sent">
                                        <i class="la la-telegram"></i>
                                        <span>{__("lang_dashboard_messages_menusent")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.received">
                                        <i class="la la-sms"></i>
                                        <span>{__("lang_dashboard_messages_menureceived")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.campaigns">
                                        <i class="la la-coffee"></i>
                                        <span>{__("lang_pages_sms_campaignstab")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.scheduled">
                                        <i class="la la-clock"></i>
                                        <span>{__("lang_and_dashboard_pages_android_line40")}</span>
                                    </a>
                                </li>
                                {if $partner}
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.transactions">
                                        <i class="la la-handshake"></i>
                                        <span>{__("lang_pages_sms_partnertransactions")}</span>
                                    </a>
                                </li>
                                {/if}
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