<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-address-book la-lg"></i> {__("lang_dashboard_contacts_title")}
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
                                    <a href="#" class="nav-link active" zender-tab="zender.{$page}.saved" zender-tab-default>
                                        <i class="la la-telegram"></i>
                                        <span>{__("lang_dashboard_contacts_menusaved")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.groups">
                                        <i class="la la-list"></i>
                                        <span>{__("lang_dashboard_contacts_menugroups")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.unsubscribed">
                                        <i class="la la-unlink"></i>
                                        <span>{__("lang_and_dashboard_pages_contacts_line40")}</span>
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