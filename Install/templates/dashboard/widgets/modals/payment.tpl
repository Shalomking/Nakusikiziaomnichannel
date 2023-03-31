<div class="modal-content">
    <div class="modal-header">
        <h3 class="modal-title">
            <i class="la la-credit-card la-lg"></i> {$title}
        </h3>

        <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    
    <div class="modal-body">
        {if $data.providers.paypal}
        <form action="{$data.paypal_url}" method="post" class="mb-2">
            <input type="hidden" name="business" value="{system_paypal_email}">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="item_name" value="{$data.paypal_itemname}">
            <input type="hidden" name="item_number" value="{$data.paypal_itemid}">
            <input type="hidden" name="amount" value="{$data.paypal_amount}">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="return" value="{$data.paypal_return_url}">
            <input type="hidden" name="cancel_return" value="{$data.paypal_cancel_url}">
            <input type="hidden" name="notify_url" value="{$data.paypal_notify_url}">

            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-paypal la-lg"></i> {__("lang_pay_with_paypal")}
            </button>
        </form>
        {/if}

        {if $data.providers.mollie}
        <button class="btn btn-lg btn-primary btn-block" zender-action="mollie">
            <i class="la la-euro-sign la-lg"></i> {__("lang_pay_with_mollie")}
        </button>
        {/if}

        {if $data.providers.bank}
        <button class="btn btn-lg btn-primary btn-block" zender-toggle="zender.view/bank-{$data.original_price}">
            <i class="la la-money-check la-lg"></i> {__("lang_modal_paymentbtn_banktransfer")}
        </button>
        {/if}
        
        {if defined("plugin_payment")}
        <a href="{site_url}/paypage?hash={logged_hash}&salt={uniqid(logged_id)}" class="btn btn-lg btn-primary btn-block">
            <i class="la la-angle-double-right la-lg"></i> {__("lang_btn_other_paymentmethods")}
        </a>
        {/if}
    </div>
</div>