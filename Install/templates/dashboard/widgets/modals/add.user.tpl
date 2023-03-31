<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-user-plus la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_user_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_user_line19")}">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_emailaddress")} <i class="la la-info-circle" title="{__("lang_and_user_line24")}"></i>
                    </label>
                    <input type="text" name="email" class="form-control" placeholder="{__("lang_and_user_line26")}">
                </div>
                
                <div class="form-group col-12">
                    <label>
                        {__("lang_form_password")} <i class="la la-info-circle" title="{__("lang_and_user_line31")}"></i>
                    </label>
                    <input type="text" name="password" class="form-control" placeholder="eg. {__("lang_form_password")}">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_usertimezone")} <i class="la la-info-circle" title="{__("lang_and_user_line38")}"></i>
                    </label>
                    <select name="timezone" class="form-control" data-live-search="true">
                        {foreach $data.timezones as $timezone}
                        <option value="{strtolower($timezone)}">{strtoupper($timezone)}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_user_line49")} <i class="la la-info-circle" title="{__("lang_and_user_line49_1")}"></i>
                    </label>
                    <select name="country" class="form-control" data-live-search="true">
                        {foreach $data.countries as $country}
                        <option value="{$country@key}" data-tokens="{strtolower($country)}">{$country} ({$country@key})</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_user_line60")} <i class="la la-info-circle" title="{__("lang_and_user_line60_1")}"></i>
                    </label>
                    <select name="alertsound" class="form-control">
                        <option value="1" selected>{__("lang_form_enable")}</option>
                        <option value="2">{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_adduser_role")} <i class="la la-info-circle" title="{__("lang_and_user_line70")}"></i>
                    </label>
                    <select name="role" class="form-control">
                        {foreach $data.roles as $role}
                        <option value="{$role@key}">{$role.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_language")} <i class="la la-info-circle" title="{__("lang_and_user_line81")}"></i>
                    </label>
                    <select name="language" class="form-control" data-live-search="true">
                        {foreach $data.languages as $language}
                        <option value="{$language@key}" data-tokens="{$language.token}">{$language.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_addusertpl_credits")} <i class="la la-info-circle" title="{__("lang_form_addusertpl_creditsdesc")}"></i>
                    </label>
                    <input type="text" name="credits" class="form-control" placeholder="eg. 10">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_user_line92")} <i class="la la-info-circle" title="{__("lang_and_user_line92_1")}"></i>
                    </label>
                    <select name="partner" class="form-control">
                        <option value="1">{__("lang_form_enable")}</option>
                        <option value="2" selected>{__("lang_form_disable")}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_submit")}
            </button>
        </div>
    </div>
</form>