<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-calendar la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_sms_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_sms_line19")}">
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_schedule_schedule")} <i class="la la-info-circle" title="{__("lang_and_sms_line24")}"></i>
                    </label>
                    <input type="text" name="schedule" class="form-control" placeholder="{__("lang_and_sms_line26")}" zender-datepicker-schedule>
                </div>

                <div class="form-group col-md-4">
                    <div zender-device-mode>
                        <label>
                            {__("lang_form_device")} <i class="la la-info-circle" title="{__("lang_and_sms_line110")}"></i>
                        </label>
                        <select name="device" class="form-control" data-live-search="true">
                            {foreach $data.devices as $device}
                            <option value="{$device@key}" data-tokens="{$device.token}" data-content="{$device.name} <span class='badge badge-{if $device.status < 2}success{else}danger{/if} device-status-{$device.id}'>{if $device.status < 2}{__("lang_form_status_online")}{else}{__("lang_form_status_offline")}{/if}</span>" {if $device@index < 1}selected{/if}>{$device.name}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div zender-credits-mode>
                        <label>
                            {__("lang_and_sms_line121")} <i class="la la-info-circle" title="{__("lang_and_sms_line121_1")}"></i>
                        </label>
                        <select name="gateway" class="form-control" data-live-search="true">
                            {foreach $data.gateways as $gateway}
                            <option value="{$gateway.id}" data-tokens="{$gateway.name}">{$gateway.name}</option>
                            {/foreach}
                            {if !empty($data.devicesGlobal)}
                            {if !empty($data.gateways)}
                            <option data-divider="true"></option>
                            {/if}
                            {foreach $data.devicesGlobal as $device}
                            <option value="{$device@key}" data-tokens="{$device.token}" data-content="<i class='flag-icon flag-icon flag-icon-{strtolower($device.country)}'></i> {$device.name} <span class='badge badge-{if $device.status < 2}success{else}danger{/if} device-status-{$device.id}'>{if $device.status < 2}{__("lang_form_status_online")}{else}{__("lang_form_status_offline")}{/if}</span> <span class='badge badge-primary'>{__("lang_form_smsall_globalstatus")}</span> ({$device.rate} PHP)" {if empty($data.gateways) && $device@index < 1}selected{/if}>{$device.name} ({__("lang_form_smsall_globalstatus")})</option>
                            {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <div class="form-group">
                        <label>
                            {__("lang_form_schedule_numbers")} <i class="la la-info-circle" title="{__("lang_and_sms_line42")}"></i>
                        </label>
                        <textarea name="numbers" class="form-control" rows="3" placeholder="{$data.number}
{$data.number}
{$data.number}
{$data.number}
{$data.number}
"></textarea>
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_groups")} <i class="la la-info-circle" title="{__("lang_and_sms_line55")}"></i>
                    </label>
                    <select name="groups[]" class="form-control" data-live-search="true" zender-select-groups multiple>
                        <option value="0" data-tokens="None {__("lang_form_select_multinone")}" selected>{__("lang_form_select_multinone")}</option>
                        {foreach $data.groups as $group}
                        <option value="{$group@key}" data-tokens="{$group.token}">{$group.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_sms_line67")} <i class="la la-info-circle" title="{__("lang_and_sms_line67_1")}"></i>
                    </label>
                    <select name="shortener" class="form-control">
                        <option value="0" selected>{__("lang_and_sms_line70")}</option>
                        {foreach $data.shorteners as $shortener}
                        <option value="{$shortener@key}">{$shortener.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_forms_repeatdays_title")} <i class="la la-info-circle" title="{__("lang_forms_repeatdays_tagline")}"></i>
                    </label>
                    <input type="number" name="repeat" class="form-control" placeholder="ex. 7" value="0">

                    <label class="mt-3">
                        {__("lang_and_sms_line79")} <i class="la la-info-circle" title="{__("lang_and_sms_line79_1")}"></i>
                    </label>
                    <select name="mode" class="form-control" zender-select-mode>
                        <option value="1" selected>{__("lang_and_sms_line82")}</option>
                        <option value="2">{__("lang_and_sms_line83")}</option>
                    </select>

                    <div zender-device-mode>
                        <label>
                            {__("lang_form_sim")} <i class="la la-info-circle" title="{__("lang_and_sms_line88")}"></i>
                        </label>
                        <select name="sim" class="form-control">
                            <option value="1" selected>{__("lang_and_sms_line91")}</option>
                            <option value="2">{__("lang_and_sms_line92")}</option>
                        </select>
                    </div>

                    <label>
                        {__("lang_form_template")} <i class="la la-info-circle" title="{__("lang_and_sms_line97")}"></i>
                    </label>
                    <select class="form-control" data-live-search="true" zender-select-template>
                        <option value="none" data-tokens="no none 0" selected>{__("lang_form_none")}</option>
                        {foreach $data.templates as $template}
                        <option value="{$template@key}" data-tokens="{$template.token}" data-format="{$template.format}">{$template.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-8">
                    <label>{__("lang_form_message")} <small class="text-muted">(<span zender-counter-view></span>{if system_message_max < 1} {__("lang_form_messagecounterchars")}{/if})</small></label>
                    <textarea name="message" class="form-control mb-3" rows="7" placeholder="{__("lang_form_message_placeholder")}" zender-counter></textarea>

                    <label>
                        {__("lang_and_sms_bulk_131")} <i class="la la-info-circle" title="{__("lang_and_sms_bulk_131_1")}"></i>
                    </label>
                    <p>
                        <small>{__("lang_and_sms_bulk_135")}</small> <code>Tom is a {literal}<strong>{good|bad}</strong>{/literal} cat</code>
                    </p>
                    <p>
                        <small>{___(__("lang_form_literal_spintaxdesc2"), ["<strong>good</strong>", "<strong>bad</strong>"])}</small>
                    </p>

                    <label>{__("lang_form_shortcodes")}</label>
                    {literal}
                    <p>
                        <code><strong>{{contact.name}}</strong>, <strong>{{contact.number}}</strong>, <strong>{{group.name}}</strong>, <strong>{{date.now}}</strong>, <strong>{{date.time}}</strong></code>
                    </p>
                    {/literal}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-clock la-lg"></i> {__("lang_form_smsschedule_submit")}
            </button>
        </div>
    </div>
</form>