<header id="home" class="wrapper bg-soft-primary">
  <nav class="navbar navbar-expand-lg classic transparent position-absolute navbar-dark">
    <div class="container flex-lg-row flex-nowrap align-items-center">
      <div class="navbar-brand w-100">
        <a href="{site_url}" zender-nav>
          <img class="logo-light" src="{get_image("logo_light")}" />
          <img class="logo-dark" src="{get_image("logo_dark")}" />
        </a>
      </div>
      
      <div class="navbar-collapse offcanvas-nav">
        <div class="offcanvas-header d-lg-none d-xl-none">
          <a href="{site_url}">
            <img src="{get_image("logo_light")}" alt="{system_site_name}" />
          </a>
          <button type="button" class="btn-close btn-close-white offcanvas-close offcanvas-nav-close" aria-label="Close"></button>
        </div>

        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link scroll" href="#home">{__("lang_and_head_blck_21")}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link scroll" href="#features">{__("lang_and_head_blck_24")}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link scroll" href="#pricing">{__("lang_and_head_blck_27")}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link scroll" href="#clients">{__("lang_and_head_blck_30")}</a>
          </li> 
        </ul>
      </div>

      <div class="navbar-other ms-lg-4">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            {if logged_id} 
            <li class="nav-item d-md-block">
                <a href="{site_url("dashboard")}" class="btn btn-sm btn-primary text-capitalize">{__("lang_and_head_blck_39")}</a>
            </li> 
            <li class="nav-item d-none d-md-block">
                <button class="btn btn-sm btn-danger text-capitalize" zender-action="logout">{__("lang_and_head_blck_42")}</button>
            </li>
            {else}
            <li class="nav-item d-md-block">
                <a href="{site_url("dashboard/authenticate/login")}" class="btn btn-sm btn-white text-capitalize">{__("lang_landing_nav_login")}</a>
            </li> 
            {/if} 

            <li class="nav-item d-lg-none">
                <div class="navbar-hamburger">
                    <button class="hamburger animate plain" data-toggle="offcanvas-nav">
                        <span></span>
                    </button>
                </div>
            </li>
        </ul>
      </div>
    </div>
  </nav>
</header>