<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-money-bill-wave"></i> {__("lang_dashboard_vouchers_title")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_admin_vouch_6")}" zender-trash="administration.vouchers">
                <i class="la la-stream la-lg"></i>
            </button>
            
            <button class="btn btn-lg btn-primary" zender-toggle="zender.add.voucher">
                <i class="la la-money-bill-wave la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_dashboard_vouchers_addrole")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>