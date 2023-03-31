<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-tools la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label>
                        {__("lang_and_theme_line17")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line17_1")}"></i>
                        {if $data.assets.logo_light}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="logo_light_img" class="form-control pb-5 mb-3">

                    <label>
                        {__("lang_and_theme_line23")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line223_1")}"></i>
                        {if $data.assets.logo_dark}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="logo_dark_img" class="form-control pb-5 mb-3">

                    <label>
                        {__("lang_form_theme_favicon")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line29")}"></i>
                        {if $data.assets.favicon}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="favicon_img" class="form-control pb-5 mb-3">

                    <label>
                        {__("lang_and_theme_line35")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line35_1")}"></i>
                        {if $data.assets.background}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="bg_img" class="form-control pb-5 mb-3">

                    <label>
                        {__("lang_form_themebg")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line41")}"></i>
                    </label>
                    <input type="color" name="theme_background" class="form-control mb-3" value="{$data.system.theme_background}">

                    <label>
                        {__("lang_form_themetext")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line46")}"></i>
                    </label>
                    <input type="color" name="theme_highlight" class="form-control mb-3" value="{$data.system.theme_highlight}">

                    <label>
                        {__("lang_and_theme_line51")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line51_1")}"></i>
                    </label>
                    <input type="color" name="theme_spinner" class="form-control mb-3" value="{$data.system.theme_spinner}">
                </div>

                <div class="form-group col-md-7">
                    <label>
                        {__("lang_and_theme_line58")} <i class="la la-info-circle la-lg" title="{__("lang-and_theme_line58_1")}"></i>
                    </label>
                    <textarea name="script" class="form-control mb-3" rows="13" placeholder="eg., alert.success('{__("lang_and_theme_line60")}');">{$data.script}</textarea>

                    <label>
                        {__("lang_and_theme_line63")} <i class="la la-info-circle la-lg" title="{__("lang_and_theme_line63_1")}"></i>
                    </label>
                    <textarea name="css" class="form-control" rows="12" placeholder="eg., body { display: none; }">{$data.css}</textarea>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_save")}
            </button>
        </div>
    </div>
</form>