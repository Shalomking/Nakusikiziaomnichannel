<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-crown la-lg"></i> {$title}
        </h3>

        <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
        <div class="p-4">
            <div class="row">
                <div class="col-md-4">
                    <ul class="text-left">
                        <li>
                            <h4 class="text-uppercase">
                                {__("lang_form_package")}
                            </h4>
                            <h5 class="text-muted">{$data.subscription.name}</h5>
                            {if $data.subscription.id > 1}
                            <h5 class="text-warning">{__("lang_and_usub23")} {$data.subscription.expire_date}</h5>
                            {/if}
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub28")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.quota.sms_send} / {if $data.subscription.send_limit > 0}{number_format($data.subscription.send_limit)} {if system_reset_mode < 2}{__("lang_form_daily")}{else}monthly{/if}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub34")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.quota.sms_receive} / {if $data.subscription.receive_limit > 0}{number_format($data.subscription.receive_limit)} {if system_reset_mode < 2}{__("lang_form_daily")}{else}monthly{/if}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub40")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.quota.wa_send} / {if $data.subscription.wa_send_limit > 0}{number_format($data.subscription.wa_send_limit)} {if system_reset_mode < 2}{__("lang_form_daily")}{else}monthly{/if}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub46")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.quota.wa_receive} / {if $data.subscription.wa_receive_limit > 0}{number_format($data.subscription.wa_receive_limit)} {if system_reset_mode < 2}{__("lang_form_daily")}{else}monthly{/if}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <ul class="text-left">
                        <li>
                            <h4 class="text-uppercase">
                                {__("lang_and_usub57")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.quota.ussd} / {if $data.subscription.ussd_limit > 0}{number_format($data.subscription.ussd_limit)} {if system_reset_mode < 2}{__("lang_form_daily")}{else}monthly{/if}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub63")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.quota.notifications} / {if $data.subscription.notification_limit > 0}{number_format($data.subscription.notification_limit)} {if system_reset_mode < 2}{__("lang_form_daily")}{else}monthly{/if}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub69")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.scheduled} / {if $data.subscription.scheduled_limit > 0}{number_format($data.subscription.scheduled_limit)}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_user_subscriptioncontacts")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.contacts} / {if $data.subscription.contact_limit > 0}{number_format($data.subscription.contact_limit)}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_form_keys")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.keys} / {if $data.subscription.key_limit > 0}{number_format($data.subscription.key_limit)}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <ul class="text-left">
                        <li>
                            <h4 class="text-uppercase">
                                {__("lang_form_hooks")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.webhooks} / {if $data.subscription.webhook_limit > 0}{number_format($data.subscription.webhook_limit)}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub98")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.actions} / {if $data.subscription.action_limit > 0}{number_format($data.subscription.action_limit)}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub104")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.devices} / {if $data.subscription.device_limit > 0}{number_format($data.subscription.device_limit)}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                        <li class="mt-3">
                            <h4 class="text-uppercase">
                                {__("lang_and_usub110")}
                            </h4>
                            <h5 class="text-muted">{$data.usage.wa_accounts} / {if $data.subscription.wa_account_limit > 0}{number_format($data.subscription.wa_account_limit)}{else}<i class="la la-infinity infinity"></i>{/if}</h5>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>