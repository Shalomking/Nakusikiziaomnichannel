<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-whatsapp la-lg"></i> {$title}
        </h3>
    </div>
    
    <div class="modal-body mb-2">
        <div class="text-center">
            <div id="wa_intro">
                <p class="px-5">{___(__("lang_widgets_addwhatsapp_newmodal"), [$data.linkbtn])}</p>
                <div class="btn-group">
                    <button class="btn btn-primary btn-lg" zender-whatsapp-link>
                        <i class="la la-chain"></i> {__("lang_and_whatsapp_line16")}
                    </button>
                    <button class="btn btn-danger btn-lg" data-dismiss="modal">
                        <i class="la la-close"></i> {__("lang_widgets_addwhatsapp_newmodal4")}
                    </button>
                </div>
            </div>

            <div id="wa_link">
                <div class="mt-2 mb-3" id="wa_qrcode"></div>
                <h1 id="wa_countdown"></h1>
            </div>
        </div>
    </div>
</div>