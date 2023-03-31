<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <nav class="navbar navbar-expand-lg navbar-light px-0">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="la la-bars"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent" zender-navbar>
                        <ul class="navbar-nav show">
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard")}" zender-nav>
                                    <i class="la la-chart-bar la-lg"></i> {__("lang_dashboard_nav_default")}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/sms")}" zender-nav>
                                    <i class="la la-comment la-lg"></i> {__("lang_dashnav_sms_navname")}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/whatsapp")}" zender-nav>
                                    <i class="la la-whatsapp la-lg"></i> {__("lang_and_dashboard_modules_header_line25")}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/android")}" zender-nav>
                                    <i class="la la-android la-lg"></i> {__("lang_and_dashboard_modules_header_line20")}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/contacts")}" zender-nav>
                                    <i class="la la-address-book la-lg"></i> {__("lang_dashboard_nav_contacts")}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/tools")}" zender-nav>
                                    <i class="la la-toolbox la-lg"></i> {__("lang_dashboard_nav_tools")}
                                </a>
                            </li>

                            {if is_admin}
                            <li class="nav-item">
                                <a class="nav-link" href="{site_url("dashboard/administration")}" zender-nav>
                                    <i class="la la-tools la-lg"></i> {__("lang_and_dashboard_modules_header_line45")}
                                </a>
                            </li>
                            {/if}
                        </ul>
                    </div>

                    <div class="user-nav" zender-usernav>
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <div class="user" data-toggle="dropdown">
                                    <span class="thumb">
                                        <img src="{logged_avatar}" class="rounded-circle">
                                    </span>
                                    <span class="name">{logged_name}</span>
                                    <span class="arrow">
                                        <i class="la la-angle-down"></i>
                                    </span>
                                </div>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item" zender-toggle="zender.user.settings">
                                        <i class="la la-user-cog"></i> {__("lang_dashboard_nav_menusettings")}
                                    </a>

                                    <a href="#" class="dropdown-item" zender-action="logout">
                                        <i class="la la-sign-out"></i> {__("lang_dashboard_nav_menulogout")}
                                    </a>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>