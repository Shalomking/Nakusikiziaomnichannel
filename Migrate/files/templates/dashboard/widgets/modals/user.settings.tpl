<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-user-cog la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>
                        {__("lang_form_avatar")} <i class="la la-info-circle" title="{__("lang_and_uset17")}"></i>
                    </label>
                    <input type="file" name="avatar" class="form-control pb-5">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_uset24")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_uset26")}" value="{$data.user.name}">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_emailaddress")} <i class="la la-info-circle" title="{__("lang_and_uset31")}"></i>
                    </label>
                    <input type="text" name="email" class="form-control" placeholder="{__("lang_and_uset33")}" value="{$data.user.email}">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_usertimezone")} <i class="la la-info-circle" title="{__("lang_and_uset38")}"></i>
                    </label>
                    <select name="timezone" class="form-control" data-live-search="true">
                        {foreach $data.timezones as $timezone}
                        <option value="{strtolower($timezone)}" {if strtolower($timezone) eq strtolower(logged_timezone)}selected{/if}>{strtoupper($timezone)}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_forms_edituser_clockformat")} <i class="la la-info-circle" title="{__("lang_forms_edituser_clockformathelp")}"></i>
                    </label>
                    <select name="clock_format" class="form-control" data-live-search="true">
                        <option value="1" {if $data.formatting.container.clock_format < 2}selected{/if}>{__("lang_forms_edituser_clockformatselect1")}</option>
                        <option value="2" {if $data.formatting.container.clock_format > 1}selected{/if}>{__("lang_forms_edituser_clockformatselect2")}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_forms_edituser_dateformat")} <i class="la la-info-circle" title="{__("lang_forms_edituser_dateformathelp")}"></i>
                    </label>
                    <select name="date_format" class="form-control" data-live-search="true">
                        <option value="1" {if $data.formatting.container.date_format < 2}selected{/if}>MM{$data.formatting.container.separator_selected}DD{$data.formatting.container.separator_selected}YYYY</option>
                        <option value="2" {if $data.formatting.container.date_format eq 2}selected{/if}>DD{$data.formatting.container.separator_selected}MM{$data.formatting.container.separator_selected}YYYY</option>
                        <option value="3" {if $data.formatting.container.date_format eq 3}selected{/if}>YYYY{$data.formatting.container.separator_selected}MM{$data.formatting.container.separator_selected}DD</option>
                        <option value="4" {if $data.formatting.container.date_format > 3}selected{/if}>YYYY{$data.formatting.container.separator_selected}DD{$data.formatting.container.separator_selected}MM</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_forms_edituser_dateseparator")} <i class="la la-info-circle" title="{__("lang_forms_edituser_dateseparatorhelp")}"></i>
                    </label>
                    <select name="date_separator" class="form-control" data-live-search="true">
                        <option value="1" {if $data.formatting.container.date_separator < 2}selected{/if}>MM-DD-YYYY</option>
                        <option value="2" {if $data.formatting.container.date_separator eq 2}selected{/if}>MM/DD/YYYY</option>
                        <option value="3" {if $data.formatting.container.date_separator eq 3}selected{/if}>MM.DD.YYYY</option>
                        <option value="4" {if $data.formatting.container.date_separator > 3}selected{/if}>MM DD YYYY</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_uset49")} <i class="la la-info-circle" title="{__("lang_and_uset49_1")}"></i>
                    </label>
                    <select name="country" class="form-control" data-live-search="true">
                        {foreach $data.countries as $country}
                        <option value="{$country@key}" data-tokens="{strtolower($country)}" {if $country@key eq logged_country}selected{/if}>{$country} ({$country@key})</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_uset60")} <i class="la la-info-circle" title="{__("lang_and_uset60_1")}"></i>
                    </label>
                    <select name="alertsound" class="form-control">
                        <option value="1" {if $data.user.alertsound < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.user.alertsound > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_changepass")} <i class="la la-info-circle" title="{__("lang_and_uset70")}"></i>
                    </label>
                    <input type="password" name="password" class="form-control" placeholder="{__("lang_form_password_leave")}">
                </div>

                <input type="hidden" name="current_email" value="{$data.user.email}">
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_save")}
            </button>
        </div>
    </div>
</form>