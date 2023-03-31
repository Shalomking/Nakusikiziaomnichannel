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
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_edit_sms_line19")}" value="{$data.scheduled.name}">
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_schedule_schedule")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line24")}"></i>
                    </label>
                    <input type="text" name="schedule" class="form-control" placeholder="{__("lang_and_edit_sms_line26")}" value="{date("m/d/Y h:i A", $data.scheduled.send_date)}" zender-datepicker-schedule>
                </div>

                <div class="form-group col-md-4">
                    <div zender-device-mode>
                        <label>
                            {__("lang_form_device")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line88")}"></i>
                        </label>
                        <select name="device" class="form-control" data-live-search="true">
                            {foreach $data.devices as $device}
                            <option value="{$device@key}" data-tokens="{$device.token}" data-content="{$device.name} <span class='badge badge-{if $device.status < 2}success{else}danger{/if} device-status-{$device.id}'>{if $device.status < 2}{__("lang_form_status_online")}{else}{__("lang_form_status_offline")}{/if}</span>" {if $device@key eq $data.scheduled.did}selected{/if}>{$device.name}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div zender-credits-mode>
                        <label>
                            {__("lang_and_edit_sms_line99")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line99_1")}"></i>
                        </label>
                        <select name="gateway" class="form-control" data-live-search="true">
                            {foreach $data.gateways as $gateway}
                            <option value="{$gateway.id}" data-tokens="{$gateway.name}" {if $gateway@key eq $data.scheduled.gateway}selected{/if}>{$gateway.name}</option>
                            {/foreach}
                            {if !empty($data.devicesGlobal)}
                            {if !empty($data.gateways)}
                            <option data-divider="true"></option>
                            {/if}
                            {foreach $data.devicesGlobal as $device}
                            <option value="{$device@key}" data-tokens="{$device.token}" data-content="<i class='flag-icon flag-icon flag-icon-{strtolower($device.country)}'></i> {$device.name} <span class='badge badge-{if $device.status < 2}success{else}danger{/if} device-status-{$device.id}'>{if $device.status < 2}{__("lang_form_status_online")}{else}{__("lang_form_status_offline")}{/if}</span> <span class='badge badge-primary'>{__("lang_form_smsall_globalstatus")}</span> ({$device.rate} PHP)" {if $device@key eq $data.scheduled.did}selected{/if}>{$device.name} ({__("lang_form_smsall_globalstatus")})</option>
                            {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <div class="form-group">
                        <label>
                            {__("lang_form_schedule_numbers")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line42")}"></i>
                        </label>
                        <textarea name="numbers" class="form-control" rows="3" placeholder="{$data.number}
{$data.number}
{$data.number}
{$data.number}
{$data.number}
">{$data.scheduled.numbers}</textarea>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_groups")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line55")}"></i>
                    </label>
                    <select name="groups[]" class="form-control" data-live-search="true" zender-select-groups multiple>
                        <option value="0" data-tokens="None {__("lang_form_select_multinone")}" {if in_array(0, explode(",", $data.scheduled.groups))}selected{/if}>{__("lang_form_select_multinone")}</option>
                        {foreach $data.groups as $group}
                        <option value="{$group@key}" data-tokens="{$group.token}" {if in_array($group@key, explode(",", $data.scheduled.groups))}selected{/if}>{$group.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_forms_repeatdays_title")} <i class="la la-info-circle" title="{__("lang_forms_repeatdays_tagline")}"></i>
                    </label>
                    <input type="number" name="repeat" class="form-control" placeholder="eg. 7" value="{$data.scheduled.repeat}">

                    <label class="mt-3">
                        {__("lang_and_edit_sms_line67")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line67_1")}"></i>
                    </label>
                    <select name="mode" class="form-control" zender-select-mode>
                        <option value="1" {if $data.scheduled.mode < 2}selected{/if}>{__("lang_and_edit_sms_line70")}</option>
                        <option value="2" {if $data.scheduled.mode > 1}selected{/if}>{__("lang_and_edit_sms_line71")}</option>
                    </select>

                    <div zender-device-mode>
                        <label>
                            {__("lang_form_sim")} <i class="la la-info-circle" title="{__("lang_and_edit_sms_line76")}"></i>
                        </label>
                        <select name="sim" class="form-control">
                            <option value="1" {if $data.scheduled.sim < 2}selected{/if}>{__("lang_and_edit_sms_line79")}</option>
                            <option value="2" {if $data.scheduled.sim > 1}selected{/if}>{__("lang_and_edit_sms_line80")}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-8">
                    <label>{__("lang_form_message")} <small class="text-muted">(<span zender-counter-view></span>{if system_message_max < 1} {__("lang_form_messagecounterchars")}{/if})</small></label>
                    <textarea name="message" class="form-control mb-3" rows="7" placeholder="{__("lang_form_message_placeholder")}" zender-counter>{$data.scheduled.message}</textarea>

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