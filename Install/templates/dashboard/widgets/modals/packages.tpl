<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-cubes la-lg"></i> {$title}
        </h3>

        <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
        <div class="row">
            {foreach $data.packages as $package}
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header bg-prime">
                        <h2 class="card-title text-white">
                            <i class="la la-cube la-lg"></i> {$package.name}
                        </h2>
                        <small class="text-white">
                            {if $package.id < 2}
                            {__("lang_form_freefor")}
                            {else}
                            {$package.price} <small>{strtoupper(system_currency)} {__("lang_form_monthly")}</small>
                            {/if}
                        </small>
                    </div>

                    <div class="card-body">
                        <h4 class="text-uppercase">{__("lang_dashboard_packagesmodal_smssend")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-telegram la-lg"></i> {if $package.send_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.send_limit)} {if system_reset_mode < 2}{__("lang_default_pricecolumns_dailylabel")}{else}{__("lang_default_pricecolumns_monthlylabel")}{/if}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line36")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-sms la-lg"></i> {if $package.receive_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.receive_limit)} {if system_reset_mode < 2}{__("lang_default_pricecolumns_dailylabel")}{else}{__("lang_default_pricecolumns_monthlylabel")}{/if}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line41")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-telegram la-lg"></i> {if $package.wa_send_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.wa_send_limit)} {if system_reset_mode < 2}{__("lang_default_pricecolumns_dailylabel")}{else}{__("lang_default_pricecolumns_monthlylabel")}{/if}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line46")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-sms la-lg"></i> {if $package.wa_receive_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.wa_receive_limit)} {if system_reset_mode < 2}{__("lang_default_pricecolumns_dailylabel")}{else}{__("lang_default_pricecolumns_monthlylabel")}{/if}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line51")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-phone-volume la-lg"></i> {if $package.ussd_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.ussd_limit)} {if system_reset_mode < 2}{__("lang_default_pricecolumns_dailylabel")}{else}{__("lang_default_pricecolumns_monthlylabel")}{/if}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line56")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-bell la-lg"></i> {if $package.notification_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.notification_limit)} {if system_reset_mode < 2}{__("lang_default_pricecolumns_dailylabel")}{else}{__("lang_default_pricecolumns_monthlylabel")}{/if}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line61")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-clock la-lg"></i> {if $package.scheduled_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.scheduled_limit)}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line66")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-address-book la-lg"></i> {if $package.contact_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.contact_limit)}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line71")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-key la-lg"></i> {if $package.key_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.key_limit)}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line76")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-code-branch la-lg"></i> {if $package.webhook_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.webhook_limit)}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line81")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-bolt la-lg"></i> {if $package.action_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.action_limit)}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line86")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-android la-lg"></i> {if $package.device_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.device_limit)}{/if}
                        </h4>

                        <h4 class="text-uppercase">{__("lang_and_package_line91")}</h4>
                        <h4 class="text-muted">
                            <i class="la la-whatsapp la-lg"></i> {if $package.wa_account_limit < 1}{__("lang_default_pricecolumns_unlimitedlabel")}{else}{number_format($package.wa_account_limit)}{/if}
                        </h4>

                        <button class="btn btn-{if $package.id < 2}primary{else}secondary{/if} btn-lg btn-block mt-3" {if $package.id > 1}zender-toggle="zender.add.duration/{$package.id}"{/if} {if $package.id < 2}disabled{/if}>
                            {if $package.id < 2}
                                <i class="la la-bolt"></i> {__("lang_btn_free")}
                            {else}
                                <i class="la la-credit-card"></i> {__("lang_btn_purchase")}
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
</div>