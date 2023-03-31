<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-file-excel la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <div class="form-group">
                        <label>
                            {__("lang_forms_campagin_formname")} <i class="la la-info-circle" title="{__("lang_forms_campagin_formhelp")}"></i>
                        </label>
                        <input type="text" name="campaign" class="form-control" placeholder="{__("lang_forms_campaign_formplaceholder")}">
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_excelfile")} <i class="la la-info-circle" title="{__("lang_and_sms_exe17")}"></i>
                    </label>
                    <small class="text-danger">
                       {__("lang_and_sms_exe20")} <a href="{site_url("uploads/system/sms_sample.xlsx")}" target="_blank">{__("lang_and_sms_exe20_1")}</a>
                    </small>
                    <input type="file" name="excel" class="form-control pb-5">
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_sms_exe27")} <i class="la la-info-circle" title="{__("lang_and_sms_exe27_1")}"></i>
                    </label>
                    <select name="shortener" class="form-control">
                        <option value="0" selected>{__("lang_and_sms_exe30")}</option>
                        {foreach $data.shorteners as $shortener}
                        <option value="{$shortener@key}">{$shortener.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                       {__("lang_and_sms_exe39")} <i class="la la-info-circle" title="{__("lang_and_sms_exe39_1")}"></i>
                    </label>
                    <select name="mode" class="form-control" zender-select-mode>
                        <option value="1" selected>{__("lang_and_sms_exe42")}</option>
                        <option value="2">{__("lang_and_sms_exe43")}</option>
                    </select>

                    <label>
                        {__("lang_form_template")} <i class="la la-info-circle" title="{__("lang_and_sms_exe47")}"></i>
                    </label>
                    <select class="form-control" data-live-search="true" zender-select-template>
                        <option value="none" data-tokens="no none 0" selected>{__("lang_form_none")}</option>
                        {foreach $data.templates as $template}
                        <option value="{$template@key}" data-tokens="{$template.token}" data-format="{$template.format}">{$template.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-8">
                    <div zender-device-mode>
                        <label>
                            {__("lang_form_device")} <i class="la la-info-circle" title="{__("lang_and_sms_exe60")}"></i>
                        </label>
                        <select name="device" class="form-control" data-live-search="true" zender-device-list>
                            {foreach $data.devices as $device}
                            <option value="{$device@key}" data-tokens="{$device.token}" device-id="{$device.id}" online-id="{$device.online_id}" data-content="{$device.name} <span class='badge badge-{if $device.status < 2}success{else}danger{/if} device-status-{$device.id}'>{if $device.status < 2}{__("lang_form_status_online")}{else}{__("lang_form_status_offline")}{/if}</span>" {if $device@index < 1}selected{/if}>{$device.name}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div zender-credits-mode>
                        <label>
                           {__("lang_and_sms_exe70")} <i class="la la-info-circle" title="{__("lang_and_sms_exe70_1")}"></i>
                        </label>
                        <select name="gateway" class="form-control" data-live-search="true" zender-device-list>
                            {foreach $data.gateways as $gateway}
                            <option value="{$gateway.id}" data-tokens="{$gateway.name}">{$gateway.name}</option>
                            {/foreach}
                            {if !empty($data.devicesGlobal)}
                            {if !empty($data.gateways)}
                            <option data-divider="true"></option>
                            {/if}
                            {foreach $data.devicesGlobal as $device}
                            <option value="{$device@key}" data-tokens="{$device.token}" device-id="{$device.id}" online-id="{$device.online_id}" data-content="<i class='flag-icon flag-icon flag-icon-{strtolower($device.country)}'></i> {$device.name} (<span class='text-lowercase'>{$device.owner|truncate:15:"..."}</span>) <span class='badge badge-{if $device.status < 2}success{else}danger{/if} device-status-{$device.id}'>{if $device.status < 2}{__("lang_form_status_online")}{else}{__("lang_form_status_offline")}{/if}</span> <span class='badge badge-primary'>{__("lang_form_smsall_globalstatus")}</span>" {if empty($data.gateways) && $device@index < 1}selected{/if}>{$device.name} ({__("lang_form_smsall_globalstatus")})</option>
                            {/foreach}
                            {/if}
                        </select>
                    </div>

                    <label>
                        {__("lang_form_message")} <small class="text-muted">(<span zender-counter-view></span>{if system_message_max < 1} {__("lang_form_messagecounterchars")}{/if})</small>
                    </label>
                    <button class="btn btn-primary btn-sm" zender-action="translate">
                        <i class="la la-language" title="{__("lang_and_sms_exe92")}"></i>
                    </button>
                    <textarea name="message" class="form-control mb-3" rows="7" placeholder="{__("lang_form_message_placeholder")}" zender-counter></textarea>

                    <label>
                        {__("lang_and_sms_exe97")} <i class="la la-info-circle" title="{__("lang_and_sms_exe97_1")}"></i>
                    </label>
                    <p>
                        <small>{__("lang_and_sms_bulk_135")}</small> <code>{$data.spintax_sample.main}</code>
                    </p>
                    <p>
                        <small>{___(__("lang_form_literal_spintaxdesc2"), ["<strong>{$data.spintax_sample.good}</strong>", "<strong>{$data.spintax_sample.bad}</strong>"])}</small>
                    </p>

                    <label>
                        {__("lang_form_shortcodes")} <i class="la la-info-circle" title="{__("lang_and_sms_exe109")}"></i>
                    </label>
                    {literal}
                    <p>
                        <code><strong>{{phone}}</strong>, <strong>{{sim}}</strong>, <strong>{{priority}}</strong>, <strong>{{date.now}}</strong>, <strong>{{date.time}}</strong>, <strong>{{unsubscribe.command}}</strong>, <strong>{{unsubscribe.link}}</strong>, <strong>{{custom.YourVariable}}</strong></code>
                    </p>
                    {/literal}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-telegram la-lg"></i> {__("lang_btn_send")}
            </button>
        </div>
    </div>
</form>