<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-coffee"></i> {__("lang_pages_wacampaigns_header")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-primary"  title="{__("lang_and_what_sent_15")}" zender-toggle="zender.whatsapp.bulk">
                <i class="la la-mail-bulk la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_what_sent_17")}</span>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_what_sent_20")}" zender-toggle="zender.whatsapp.excel">
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