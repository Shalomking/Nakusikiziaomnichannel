(function($) {
    "use strict";
    $(function() {
        /**
         * Initial Load
         */

        alert.setup();
        zender.aos();
        zender.pages();
        zender.ripple();
        zender.action();
        zender.cookies();

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
                "[zender-wrapper]"
            ]
        });

        document.addEventListener("pjax:send", function() {
            NProgress.start();

            if (typeof _template !== "undefined") {
                _template.hookOnload();
            }
        });

        document.addEventListener("pjax:complete", function() {
            zender.ripple();
            NProgress.done();

            if (typeof _template !== "undefined") {
                _template.hookOnloaded();
            }

            if (typeof _customZender !== "undefined") {
                _customZender.hookOnloaded();
            }
        });

        $("[zender-preloader]").fadeOut("fast", () => {
            zender.translate(true);
            zender.echo();

            if (typeof _customZender !== "undefined") {
                _customZender.hookOnload();
            }
        });
    });
})(jQuery);