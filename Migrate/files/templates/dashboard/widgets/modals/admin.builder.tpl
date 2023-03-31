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
                <div class="form-group mb-0 col-12">
                    <h4 class="text-uppercase">{__("lang_and_admin_build_line16")}</h4>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_builderpackagename")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line21")}"></i>
                    </label>
                    <input type="text" name="package_name" class="form-control" placeholder="{__("lang_and_admin_build_line23")}" value="{$data.builder.package_name}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_builderappname")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line28")}"></i>
                    </label>
                    <input type="text" name="app_name" class="form-control" placeholder="{__("lang_and_admin_build_line30")}" value="{$data.builder.app_name}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_builderappdesc")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line35")}"></i>
                    </label>
                    <input type="text" name="app_desc" class="form-control" placeholder="eg. {__("lang_form_builderappdesc_placeholder")}" value="{$data.builder.app_desc}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line42")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line42_1")}"></i>
                    </label>
                    <input type="color" name="app_color" class="form-control" placeholder="{__("lang_and_admin_build_line44")}" value="{$data.builder.app_color}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line49")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line_49_1")}"></i>
                    </label>
                    <input type="text" name="apk_version" class="form-control" placeholder="{__("lang_and_admin_build_line51")}" value="{$data.builder.apk_version}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line56")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line_56_1")}"></i>
                    </label>
                    <input type="text" name="build_email" class="form-control" placeholder="{__("lang_and_admin_build_line58")}" value="{$data.builder.build_email}">
                </div>

                <div class="form-group mb-0 col-12">
                    <h4 class="text-uppercase">{__("lang_and_admin_build_line62")}</h4>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line67")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line67_1")}"></i>
                        {if $data.assets.google}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="google" class="form-control pb-5">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line75")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line75_1")}"></i>
                        {if $data.assets.firebase}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="firebase" class="form-control pb-5">
                </div>

                <div class="form-group mb-0 col-12">
                    <h4 class="text-uppercase">{__("lang_and_admin_build_line82")}</h4>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_builderapplogo")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line87")}"></i>
                        {if $data.assets.logo}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="app_logo" class="form-control pb-5">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line95")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line95_1")}"></i>
                        {if $data.assets.logo_login}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="app_logo_login" class="form-control pb-5">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_builderappicon")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line103")}"></i>
                        {if $data.assets.icon}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="app_icon" class="form-control pb-5">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_builderappsplash")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line111")}"></i>
                        {if $data.assets.splash}<span class="badge badge-success">{__("lang_form_uploaded")}</span>{else}<span class="badge badge-danger">{__("lang_form_notuploaded")}</span>{/if}
                    </label>
                    <input type="file" name="app_splash" class="form-control pb-5">
                </div>

                <div class="form-group mb-0 col-12">
                    <h4 class="text-uppercase">{__("lang_and_admin_build_line118")}</h4>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line123")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line123_1")}"></i>
                    </label>
                    <textarea name="script" class="form-control mb-3" rows="13" placeholder="{__("lang_and_admin_build_line125")}">{system_app_js}</textarea>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_admin_build_line130")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line130_1")}"></i>
                    </label>
                    <textarea name="css" class="form-control mb-3" rows="13" placeholder="eg., body { display: none; }">{system_app_css}</textarea>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_admin_build_line137")} <i class="la la-info-circle la-lg" title="{__("lang_and_admin_build_line137_1")}"></i>
                    </label>
                    <textarea name="layout" class="form-control mb-3" rows="20" placeholder="eg., alert.success('{__("lang_and_admin_build_line139")}');">{$data.layout}</textarea>
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