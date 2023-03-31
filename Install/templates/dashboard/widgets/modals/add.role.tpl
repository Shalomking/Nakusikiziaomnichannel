<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-shield la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_role_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_role_line19")}">
                </div>
                
                <div class="form-group col-12">
                    <label>
                        {__("lang_form_permissions")} <i class="la la-info-circle" title="{__("lang_and_role_line24")}"></i>
                    </label>
                    <select name="permissions[]" class="form-control" data-live-search="true" multiple>
                        {* <option value="disallow_sms">disallow_sms</option>
                        <option value="disallow_ussd">disallow_ussd</option>
                        <option value="disallow_notifications">disallow_notifications</option>
                        <option value="disallow_devices">disallow_devices</option>
                        <option value="disallow_wa_chats">disallow_wa_chats</option>
                        <option value="disallow_wa_accounts">disallow_wa_accounts</option>
                        <option value="disallow_contacts">disallow_contacts</option>
                        <option value="disallow_groups">disallow_groups</option>
                        <option value="disallow_keys">disallow_keys</option>
                        <option value="disallow_webhooks">disallow_webhooks</option>
                        <option value="disallow_actions">disallow_actions</option>
                        <option value="disallow_templates">disallow_templates</option>
                        <option value="disallow_extensions">disallow_extensions</option>
                        <option value="disallow_redeem">disallow_redeem</option>
                        <option value="disallow_subscribe">disallow_subscribe</option>
                        <option value="disallow_topup">disallow_topup</option>
                        <option value="disallow_withdraw">disallow_withdraw</option>
                        <option value="disallow_convert">disallow_convert</option>
                        <option value="disallow_api">disallow_api</option> *}
                        <option value="manage_users" selected>manage_users</option>
                        <option value="manage_roles" selected>manage_roles</option>
                        <option value="manage_packages">manage_packages</option>
                        <option value="manage_vouchers">manage_vouchers</option>
                        <option value="manage_subscriptions">manage_subscriptions</option>
                        <option value="manage_transactions">manage_transactions</option>
                        <option value="manage_payouts">manage_payouts</option>
                        <option value="manage_widgets">manage_widgets</option>
                        <option value="manage_pages">manage_pages</option>
                        <option value="manage_marketing">manage_marketing</option>
                        <option value="manage_languages">manage_languages</option>
                        <option value="manage_gateways">manage_gateways</option>
                        <option value="manage_shorteners">manage_shorteners</option>
                        <option value="manage_plugins">manage_plugins</option>
                        <option value="manage_templates">manage_templates</option>
                        <option value="manage_api">manage_api</option>
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