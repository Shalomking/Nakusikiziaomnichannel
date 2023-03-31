<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-satellite-dish la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>
                        {__("lang_and_ussd_line17")} <i class="la la-info-circle" title="{__("lang_and_ussd_line17_1")}"></i>
                    </label>
                    <input type="text" name="code" class="form-control" placeholder="{__("lang_and_ussd_line19")}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_sim")} <i class="la la-info-circle" title="{__("lang_and_ussd_line24")}"></i>
                    </label>
                    <select name="sim" class="form-control">
                        <option value="1" selected>{__("lang_and_ussd_line27")}</option>
                        <option value="2">{__("lang_and_ussd_line28")}</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_device")} <i class="la la-info-circle" title="{__("lang_and_ussd_line34")}"></i>
                    </label>
                    <select name="device" class="form-control" data-live-search="true">
                        {foreach $data.devices as $device}
                        <option value="{$device@key}" data-tokens="{$device.token}" data-content="{$device.name} <span class='badge badge-{if $device.status < 2}success{else}danger{/if} device-status-{$device.id}'>{if $device.status < 2}{__("lang_form_status_online")}{else}{__("lang_form_status_offline")}{/if}</span>" {if $device@index < 1}selected{/if}>{$device.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
            <i class="la la-satellite-dish la-lg"></i> {__("lang_and_ussd_line47")}
            </button>
        </div>
    </div>
</form>