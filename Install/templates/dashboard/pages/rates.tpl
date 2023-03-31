<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-comments-dollar la-lg"></i> {__("lang_and_dash_pg_rates_line10")}
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
                                {if !empty($data.gateways)}
                                {foreach $data.gateways as $gateway}
                                <li class="nav-item">
                                    <a href="#" class="nav-link {if $gateway@index < 1}active{/if}" zender-tab="zender.{$page}.{$gateway@key}" {if $gateway@index < 1}zender-tab-default{/if}>
                                        <i class="la la-telegram"></i>
                                        <span>{$gateway.name}</span>
                                    </a>
                                </li>
                                {/foreach}
                                {/if}

                                <li class="nav-item">
                                    <a href="#" class="nav-link {if empty($data.gateways)}active{/if}" zender-tab="zender.{$page}.partners" {if empty($data.gateways)}zender-tab-default{/if}>
                                        <i class="la la-handshake"></i>
                                        <span>{__("lang_and_dash_pg_rates_line39")}</span>
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