<div class="card">
    <div class="card-header border-0">
        <h2>
            <i class="la la-chart-area"></i> {__("lang_administration_default_visitors")}
        </h2>
        <h4>{__("lang_dashboard_admin_tabdefaultmessageslast")}</h4>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-globe la-lg"></i> {__("lang_administration_default_countries")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.countries"></iframe>
        </div>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-chrome la-lg"></i> {__("lang_administration_default_browsers")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.browsers"></iframe>
        </div>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-laptop la-lg"></i> {__("lang_administration_default_os")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.os"></iframe>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-0">
        <h2>
            <i class="la la-chart-pie"></i> {__("lang_administration_default_usages")}
        </h2>
        <h4>{__("lang_dashboard_admin_tabdefaultmessageslast")}</h4>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-mail-bulk la-lg"></i> {__("lang_administration_default_messages")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.messages"></iframe>
        </div>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-wrench la-lg"></i> {__("lang_administration_default_utilities")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.utilities"></iframe>
        </div>
    </div>
</div>

{if permission("manage_transactions")}
<div class="card">
    <div class="card-header border-0">
        <h2>
            <i class="la la-coins"></i> {__("lang_dashboard_admin_tabdefaultearningstitle")} 
            <span class="badge badge-primary">{strtoupper(system_currency)}</span>
        </h2>
        <h4>{__("lang_dashboard_admin_tabdefaultmessageslast")}</h4>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-cubes la-lg"></i> {__("lang_administration_default_subscriptions")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.subscriptions"></iframe>
        </div>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-coins la-lg"></i> {__("lang_administration_default_credits")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.credits"></iframe>
        </div>
    </div>

    <div class="card-body pt-0">
        <h4 class="text-uppercase">
            <i class="la la-handshake la-lg"></i> {__("lang_pages_administration_commissionstitle")}
        </h4>

        <div class="embed-responsive">
            <iframe class="embed-responsive-item position-relative" zender-iframe="{site_url}/widget/chart/admin.commissions"></iframe>
        </div>
    </div>
</div>
{/if}