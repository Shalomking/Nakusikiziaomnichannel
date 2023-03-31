(function($) {
    "use strict";
    $(function() {
        /**
         * Initial Load
         */

        alert.setup();
        zender.active();
        zender.modals();
        zender.delete();
        zender.reorder();
        zender.action();
        zender.pages();
        zender.build();
        zender.clear();
        zender.plugin();
        zender.cookies();
        zender.visitors();

        window.tableLoading = false;

        $(window).scroll(() => {
            if ($(this).scrollTop() > 1)
                $(".header").addClass("animated slideInDown fixed");
            else
                $(".header").removeClass("animated slideInDown fixed");
        });

        /**
         * PJAX Handler
         */

        window.pjax = new Pjax({
            scrollTo: false,
            cacheBust: false,
            elements: "[zender-nav]",
            selectors: [
                "title",
                "[zender-navbar]",
                "[zender-usernav]",
                "[zender-wrapper]"
            ]
        });

        document.addEventListener("pjax:send", function() {
            NProgress.start();
        });

        document.addEventListener("pjax:complete", function() {
            zender.active();
            zender.ripple();
            zender.iframe();
            zender.tooltips();
            zender.visitors();
            zender.tabs(false);
            zender.authenticate();
            NProgress.done();

            if (typeof _customZender !== "undefined") {
                _customZender.hookOnloaded();
            }
        });

        /**
         * Preloader
         */

        $("[zender-preloader]").fadeOut("fast", () => {
            titansys.support();
            zender.echo();
            zender.tawk();
            zender.tabs();
            zender.ripple();
            zender.iframe();
            zender.tooltips();
            zender.translate();
            zender.authenticate();

            if (typeof _customZender !== "undefined") {
                _customZender.hookOnload();
            }
        });
    });
})(jQuery);