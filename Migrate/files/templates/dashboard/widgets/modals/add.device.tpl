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
        <p class="text-justify">{__("lang_form_adddevice_one")}</p>

        <h5 class="text-uppercase">{__("lang_form_adddevice_two")}</h5>
        <p class="pl-3 text-justify">{__("lang_form_adddevice_three")}</p>
        <p class="text-center">
            <a href="#" class="btn btn-lg btn-primary" zender-download-gateway>
                <i class="la la-android la-lg text-success"></i> {__("lang_btn_download")}<br>
            </a>

            <div class="row">
                <div class="col"><hr></div>
                <div class="col-auto">{__("lang_form_adddevicescandl")}</div>
                <div class="col"><hr></div>
            </div>

            <div id="zender-qrcode-download">
                <script>zender.qrcode("{$data.site_url}/uploads/builder/{system_package_name}.apk", 150, 150, "zender-qrcode-download");</script>
            </div>
        </p>

        <h5 class="text-uppercase">{__("lang_form_adddevice_four")}</h5>
        <p class="pl-3 text-justify">
            {__("lang_form_adddevice_five")}

            <div id="zender-qrcode">
                <script>zender.qrcode("{$data.hash}", 220, 220);</script>
            </div>
        </p>

        <h5 class="text-uppercase">{__("lang_form_adddevice_six")}</h5>
        <p class="pl-3 text-justify">{__("lang_form_adddevice_seven")}</p>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-primary" data-dismiss="modal">
            <i class="la la-check-circle la-lg"></i> {__("lang_btn_done")}
        </button>
    </div>
</div>