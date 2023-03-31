<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-clock la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="p-3">
                <div class="form-row">
                    <div class="input-group input-group-md col-12">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-uppercase">{__("lang_form_durationmonth")}</span>
                        </div>
                        <input type="number" class="form-control" placeholder="eg. 1" min="1" value="1" zender-duration>
                    </div>

                    <div class="form-group mt-3 mb-1 text-center col-12">
                        <label>{__("lang_form_durationpackage")} {$data.package.name}</label>
                        <label>
                            {__("lang_form_durationpay")} <span zender-duration-price>{$data.package.price}</span> {system_currency}
                        </label>
                    </div>

                    <div class="form-group mb-0 col-12">
                        <input type="hidden" name="id" class="form-control" value="{$data.package.id}">
                        <input type="hidden" name="price" class="form-control" value="{$data.package.price}">

                        <button class="btn btn-primary btn-lg btn-block" zender-toggle="zender.payment/{$data.package.id}/1" zender-loader="Processing request" zender-duration-button>
                            <i class="la la-credit-card"></i> {__("lang_btn_purchase")}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>