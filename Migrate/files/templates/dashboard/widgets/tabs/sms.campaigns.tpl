<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-coffee"></i> {__("lang_pages_smscampaigns_header")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-primary"  title="{__("lang_and_droid_sent_19")}" zender-toggle="zender.sms.bulk">
                <i class="la la-mail-bulk la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_droid_sent_21")}</span>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_droid_sent_24")}" zender-toggle="zender.sms.excel">
                <i class="la la-file-excel la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_btn_bulkexcel")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>