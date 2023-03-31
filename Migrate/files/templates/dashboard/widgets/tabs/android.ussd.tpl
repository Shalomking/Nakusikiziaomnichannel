<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-satellite-dish"></i> {__("lang_and_droid_ussd_3")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_droid_ussd_6")}" zender-trash="android.ussd">
                <i class="la la-stream la-lg"></i>
            </button>

            <button class="btn btn-lg btn-warning" title="{__("lang_and_droid_ussd_10")}" zender-clear-pending="ussd">
                <i class="la la-exclamation-triangle la-lg"></i>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_droid_ussd_14")}" zender-toggle="zender.add.ussd">
                <i class="la la-telegram la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_droid_ussd_16")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>