<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-exchange-alt la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_payout_line17")} <i class="la la-info-circle" title="{__("lang_and_payout_line17_1")}"></i>
                    </label>
                    <input type="number" name="amount" class="form-control" placeholder="{__("lang_and_payout_line19")}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_payout_line24")} <i class="la la-info-circle" title="{__("lang_and_payout_line24_1")}"></i>
                    </label>
                    <select name="provider" class="form-control">
                        <option value="paypal" selected>{__("lang_and_payout_line27")}</option>
                        <option value="payoneer">{__("lang_and_payout_line28")}</option>
                    </select>
                </div>
                
                <div class="form-group col-12">
                    <label>
                        {__("lang_and_payout_line34")} <i class="la la-info-circle" title="{__("lang_and_payout_line34_1")}"></i>
                    </label>
                    <input type="text" name="address" class="form-control" placeholder="{__("lang_and_payout_line36")}">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_and_payout_line43")}
            </button>
        </div>
    </div>
</form>