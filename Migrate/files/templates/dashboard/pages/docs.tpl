<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-book la-lg"></i> {__("lang_and_dash_pg_doc_line10")}
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
                                    <a href="#" class="nav-link active" zender-tab="zender.{$page}.api" zender-tab-default="false">
                                        <i class="la la-terminal"></i>
                                        <span>{__("lang_and_dash_pg_doc_line28")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.webhooks">
                                        <i class="la la-code-branch"></i>
                                        <span>{__("lang_and_dash_pg_doc_line34")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.actions">
                                        <i class="la la-robot"></i>
                                        <span>{__("lang_and_dash_pg_doc_line40")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.devices">
                                        <i class="la la-android"></i>
                                        <span>{__("lang_and_dash_pg_doc_line46")}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" zender-tab="zender.{$page}.partnership">
                                        <i class="la la-handshake"></i>
                                        <span>{__("lang_and_dash_pg_doc_line52")}</span>
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