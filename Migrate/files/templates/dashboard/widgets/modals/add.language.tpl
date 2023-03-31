<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-address-book la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-4">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_dash_lang_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_dash_lang_line19")}">
                </div>

                <div class="form-group col-4">
                    <label>
                        {__("lang_form_countrycode")} <i class="la la-info-circle" title="{__("lang_and_dash_lang_line24")}"></i>
                    </label>
                    <select name="iso" class="form-control" data-live-search="true">
                        {foreach $data.countries as $country}
                        <option value="{$country@key}" data-tokens="{strtolower($country)}" {if $country@key eq logged_country}selected{/if}>{$country@key}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-4">
                    <label>
                        {__("lang_and_dash_lang_line35")} <i class="la la-info-circle" title="{__("lang_and_dash_lang_line35_1")}"></i>
                    </label>
                    <select name="rtl" class="form-control">
                        <option value="1">{__("lang_and_dash_lang_line38")}</option>
                        <option value="2" selected>{__("lang_and_dash_lang_line39")}</option>
                    </select>
                </div>
                
                <div class="form-group col-12">
                    <label>
                        {__("lang_form_translations")} <i class="la la-info-circle" title="{__("lang_and_dash_lang_line45")}"></i>
                    </label>
                    <textarea name="translations" class="form-control" cols="100" rows="10" placeholder="{__("lang_form_translations_placeholder")}">{$data.strings}</textarea>
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