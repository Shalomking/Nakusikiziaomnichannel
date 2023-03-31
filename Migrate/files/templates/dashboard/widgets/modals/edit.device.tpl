<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-android la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <h4 class="text-uppercase">{__("lang_and_edit_dev_line16")}</h4>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_editdevice_devicename")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line21")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_edit_dev_line23")}" value="{$data.device.name}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_editdevice_receiveoptiontitle")} <i class="la la-info-circle la-lg" title="{__("lang_form_editdevice_receiveoptiondesc")}"></i>
                    </label>
                    <select name="receive_sms" class="form-control">
                        <option value="1" {if $data.device.receive_sms < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.device.receive_sms > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>
                
                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_editdevice_randomsend")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line28")}"></i>
                    </label>
                    <select name="random_send" class="form-control">
                        <option value="1" {if $data.device.random_send < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.device.random_send > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_editdevice_randommin")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line38")}"></i>
                    </label>

                    <input type="number" name="random_min" class="form-control" placeholder="{__("lang_and_edit_dev_line41")}" value="{$data.device.random_min}">
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_editdevice_randommax")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line46")}"></i>
                    </label>
                    <input type="number" name="random_max" class="form-control" placeholder="{__("lang_and_edit_dev_line49")}" value="{$data.device.random_max}">
                </div>

                <div class="form-group col-12">
                    <h4 class="text-uppercase">{__("lang_forms_editdevice_sendinglimittitle")}</h4>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_forms_editdevice_limitstatus")} <i class="la la-info-circle la-lg" title="{__("lang_forms_editdevice_limitstatushelp")}"></i>
                    </label>
                    <select name="limit_status" class="form-control">
                        <option value="1" {if $data.device.limit_status < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.device.limit_status > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_forms_editdevice_limitinterv")} <i class="la la-info-circle la-lg" title="{__("lang_forms_editdevice_limitintervhelp")}"></i>
                    </label>
                    <select name="limit_interval" class="form-control">
                        <option value="1" {if $data.device.limit_interval < 2}selected{/if}>{__("lang_forms_editdevice_limitintervselect1")}</option>
                        <option value="2" {if $data.device.limit_interval > 1}selected{/if}>{__("lang_forms_editdevice_limitintervselect2")}</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_forms_editdevice_messagecount")} <i class="la la-info-circle la-lg" title="{__("lang_forms_editdevice_messagecounthelp")}"></i>
                    </label>
                    <input type="number" name="limit_number" class="form-control" placeholder="eg. 100" value="{$data.device.limit_number}">
                </div>

                <div class="form-group col-12">
                    <h4 class="text-uppercase">{__("lang_and_edit_dev_line53")}</h4>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_edit_dev_line58")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line58_1")}"></i>
                    </label>
                    <textarea name="packages" class="form-control" placeholder="com.facebook.orca&#10;com.facebook.katana&#10;com.instagram.android" rows="5">{$data.device.packages}</textarea>
                </div>

                {if $data.partner < 2}
                <div class="form-group col-12">
                    <h4 class="text-uppercase">{__("lang_and_edit_dev_line65")}</h4>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_forms_editdevice_partnerstatus")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line78_1")}"></i>
                    </label>
                    <select name="global_device" class="form-control">
                        <option value="1" {if $data.device.global_device < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.device.global_device > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_edit_dev_line89")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line89_1")}"></i>
                    </label>
                    <select name="country" class="form-control" data-live-search="true">
                        {foreach $data.countries as $country}
                            <option value="{$country@key}" data-tokens="{$country@key} {$country}" {if $country@key eq $data.device.country}selected{/if}>{$country}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_edit_dev_line70")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line70_1_new")}"></i>
                    </label>
                    <input type="text" name="rate" class="form-control" placeholder="{__("lang_and_edit_dev_line73")}" value="{$data.device.rate}">
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_edit_dev_line101")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line101_1")}"></i>
                    </label>
                    <select name="global_priority" class="form-control">
                        <option value="1" {if $data.device.global_priority < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.device.global_priority > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_edit_dev_line112")} <i class="la la-info-circle la-lg" title="{__("lang_and_edit_dev_line112_1")}"></i>
                    </label>
                    <select name="global_slots[]" class="form-control" multiple>
                        <option value="1" {if in_array(1, explode(",", $data.device.global_slots))}selected{/if}>SIM 1</option>
                        <option value="2" {if in_array(2, explode(",", $data.device.global_slots))}selected{/if}>SIM 2</option>
                    </select>
                </div>
                {/if}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_submit")}
            </button>
        </div>
    </div>
</form>