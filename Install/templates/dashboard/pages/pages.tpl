<div class="container-fluid" zender-wrapper>
    {include "../modules/analytics.block.tpl"}

    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="float-left">
                        <h1>
                            <i class="la la-stream la-lg"></i>
                            <span class="d-none d-sm-inline">{$data.page.name}</span>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card animated fadeIn">
                    <div class="card-body">
                        {$data.content}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {include "../modules/footer.block.tpl"}
</div>