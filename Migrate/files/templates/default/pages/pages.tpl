<div class="content-wrapper accent-header" zender-wrapper>
	{include "../modules/analytics.block.tpl"}

    <section class="wrapper accent-overlay">
      <div class="container py-18 text-center">
        <div class="row">
          <div class="col-sm-10 col-md-8 col-lg-6 col-xl-6 col-xxl-5 mx-auto">
            <h1 class="display-1 mb-3 text-white">{$data.page.name}</h1>
          </div>
          <!-- /column -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container -->
    </section>
    <!-- /section -->

    <section class="wrapper bg-gray angled upper-end">
      <div class="container py-14 py-md-16">
        <div class="gy-10 gx-lg-8 gx-xl-12 mb-16 align-items-center">
        	{$data.content}
        </div>
        <!--/.row -->
      </div>
      <!-- /.container -->
    </section>
    <!-- /section -->
</div>
<!-- /.content-wrapper -->
