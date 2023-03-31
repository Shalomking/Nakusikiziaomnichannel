window.zender = {
    aos: () => {
        AOS.init();
    },

    active: () => {
        $("li [zender-nav][href=\"" + "//" + location.hostname + location.pathname + "\"]").addClass("active");
    },

    cookies: () => {
        window.cookieconsent.initialise({
            "theme": "classic",
            "position": consent_position,
            "content": {
                "message": lang_cookieconsent_message,
                "link": lang_cookieconsent_link,
                "dismiss": lang_cookieconsent_dismiss
            }
        });
    },

    tooltips: () => {
        $("[title]").tooltipster({
            theme: "tooltipster-borderless",
            side: "bottom",
            debug: false,
            maxWidth: 300
        });
    },

    gototab: () => {
        if ($("[zender-gototab]").length) {
            $("[zender-gototab]").on("click", function(e) {
                var page = $(this).attr("zender-gototab");
                var action = $(this).attr("gototab-action");

                pjax.loadUrl(site_url + "/" + page);

                zender.waitElement("[zender-tab=\"" + action + "\"]").then((elm) => {
                    $("[zender-tab=\"" + action + "\"]")[0].click();
                });
            });
        }
    },

    countdown: (duration, element, seconds = false, callback = () => {}) => {
        var timer = duration,
            minutes, seconds;

        window.timerStart = setInterval(function() {
            minutes = parseInt(timer / 60, 10)
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            if (seconds) {
                element.text(seconds);
            } else {
                element.text(minutes + ":" + seconds);
            }

            if (--timer < 0) {
                timer = duration;
            }

            if (timer >= duration) {
                clearInterval(timerStart);
                callback();
            }
        }, 1000);
    },

    waitElement: (selector) => {
        return new Promise(resolve => {
            if (document.querySelector(selector)) {
                return resolve(document.querySelector(selector));
            }

            const observer = new MutationObserver(mutations => {
                if (document.querySelector(selector)) {
                    resolve(document.querySelector(selector));
                    observer.disconnect();
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    },

    loader: (msg, state = true, element = "body") => {
        if (state) {
            $(element).loading({
                stoppable: false,
                zIndex: 1100,
                message: `
                <div class="loadingio-spinner-ripple-c4xwekkbyc9">
                    <div class="ldio-k6xrhuhg6o">
                        <div></div>
                        <div></div>
                    </div>
                </div><br>
                ` + msg + `<br>
                ` + lang_js_loader_pleasewait
            });
        } else {
            $(element).loading("stop");
        }
    },

    echo: () => {
        $.get(site_url + "/requests/echo", function(http) {
            try {
                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                if (response.status == 200) {
                    window.echo = io(titansys_echo, {
                        path: "/connect",
                        transports: ["websocket"],
                        auth: {
                            token: response.data.token
                        }
                    });

                    echo.io.on("ping", () => {
                        echo.emit("pong", 0);
                    });

                    echo.on("disconnect", () => {
                        setTimeout(() => {
                            $.get(site_url + "/requests/echo", function(http) {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    echo.auth.token = response.data.token;
                                    echo.connect();
                                } else {
                                    location.reload();
                                }
                            });
                        }, 3000);
                    });

                    echo.on("connect_error", () => {
                        setTimeout(() => {
                            $.get(site_url + "/requests/echo", function(http) {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    echo.auth.token = response.data.token;
                                    echo.connect();
                                } else {
                                    location.reload();
                                }
                            });
                        }, 3000);
                    });

                    echo.on("online", (payload) => {
                        $.get(site_url + "/requests/echo/online", {
                            did: payload
                        }, function(http) {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            if (response.status == 200) {
                                zender.tables(false, true);

                                $(".device-status-" + response.data.device).text(lang_js_userstatus_online);
                                $(".device-status-" + response.data.device).attr("class", "badge badge-success device-status-" + response.data.device);
                            }
                        });
                    });

                    echo.on("offline", (payload) => {
                        $.get(site_url + "/requests/echo/offline", {
                            id: payload
                        }, function(http) {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            if (response.status == 200) {
                                zender.tables(false, true);

                                $(".device-status-" + response.data.device).text(lang_js_userstatus_offline);
                                $(".device-status-" + response.data.device).attr("class", "badge badge-danger device-status-" + response.data.device);
                            }
                        });
                    });

                    echo.on("broadcast", (payload) => {
                        if (payload.recipients.includes(response.data.id.toString())) {
                            if (alertsound) {
                                playAlert("submarine");
                            }

                            switch (payload.color) {
                                case "2":
                                    alert.success(payload.content, false, true, payload.title, true, payload.image);
                                    break;
                                case "3":
                                    alert.warning(payload.content, false, true, payload.title, true, payload.image);
                                    break;
                                case "4":
                                    alert.danger(payload.content, false, true, payload.title, true, payload.image);
                                    break;
                                default:
                                    alert.primary(payload.content, false, true, payload.title, true, payload.image);
                            }
                        }
                    });

                    echo.on(response.data.hash, (payload) => {
                        if (payload.type == "reload") {
                            $.get(site_url + "/requests/echo/reload", function(http) {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    location.reload();
                                }
                            });
                        }

                        if (payload.type == "message") {
                            if (alertsound) {
                                playAlert("submarine");
                            }

                            if (payload.status < 2) {
                                alert.success(payload.content, false, true);
                            } else {
                                alert.danger(payload.content, false, true);
                            }

                            zender.tables(false, true);
                        }

                        if (payload.type == "ussd") {
                            if (alertsound) {
                                playAlert("submarine");
                            }

                            if (payload.status < 2) {
                                alert.success(payload.content, false, true);
                            } else {
                                alert.danger(payload.content, false, true);
                            }

                            zender.tables(false, true);
                        }

                        if (payload.type == "notification") {
                            if (alertsound) {
                                playAlert("submarine");
                            }

                            if (payload.status < 2) {
                                alert.success(payload.content, false, true);
                            } else {
                                alert.danger(payload.content, false, true);
                            }

                            zender.tables(false, true);
                        }

                        if (payload.type == "whatsapp") {
                            if (alertsound) {
                                playAlert("submarine");
                            }

                            alert.success(payload.content);

                            $("zender-modal").modal("hide");
                            zender.tables(false, true);
                        }

                        if (payload.type == "table") {
                            if (!tableLoading) {
                                zender.tables(false, true);
                            }

                            if (payload.modal) {
                                $("zender-modal").modal("hide");
                            }
                        }
                    });
                }
            } catch (e) {
                alert.danger(lang_response_went_wrong);
            }
        });
    },

    whatsapp: () => {
        if ($("[zender-whatsapp-link]").length) {
            $("[zender-whatsapp-link]").on("click", function(e) {
                e.preventDefault();

                zender.disabled();
                zender.loader(lang_response_whatsapp_creatingqr);

                $.get(site_url + "/requests/whatsapp/link", function(http) {
                    zender.loader(false, false);

                    try {
                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                        if (response.status == 200) {
                            $("#wa_intro").fadeOut("fast", () => {
                                zender.disabled(false);

                                if ($("#wa_link").length) {
                                    $("#wa_link").fadeIn("fast", () => {
                                        $("#wa_qrcode").empty();

                                        new QRCode(
                                            document.getElementById("wa_qrcode"),
                                            response.data
                                        );

                                        zender.countdown(15, $("#wa_countdown"), true, () => {
                                            $("#wa_countdown").fadeOut("fast", () => {
                                                $("#wa_qrcode").fadeOut("slow", () => {
                                                    $("zender-modal").modal("hide");
                                                });
                                            });
                                        });
                                    });
                                }
                            });
                        } else {
                            zender.disabled(false);
                            $("zender-modal").modal("hide");
                            alert.danger(response.message);
                        }
                    } catch (e) {
                        alert.danger(lang_response_went_wrong);
                    }
                });
            });
        }
    },

    download: () => {
        if ($("[zender-download-gateway]").length) {
            $("[zender-download-gateway]").on("click", function(e) {
                e.preventDefault();

                $.get(site_url + "/requests/index/download/gateway", function(http) {
                    try {
                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                        if (response.status == 200) {
                            alert.success(response.message);

                            setTimeout(function() {
                                window.open(response.data.link);
                            }, 3000);
                        } else {
                            alert.warning(response.message);
                        }
                    } catch (e) {
                        alert.danger(lang_response_went_wrong);
                    }
                });
            });
        }
    },

    plugin: () => {
        $(document).on("click", "[zender-plugin-action]", function(e) {
            e.preventDefault();

            var action = $(this).attr("zender-plugin-action");

            $.get(site_url + "/" + action, function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    if (response.status == 200) {
                        alert.success(response.message);
                    } else if (response.status == 201) {
                        setTimeout(function() {
                            window.open(response.data.link);
                        }, 1000);
                    } else if (response.status == 401) {
                        location.reload();
                    } else {
                        alert.warning(response.message);
                    }
                } catch (e) {
                    alert.danger(lang_response_went_wrong);
                }
            });
        });
    },

    clear: () => {
        $(document).on("click", "[zender-clear-pending]", function(e) {
            e.preventDefault();

            var type = $(this).attr("zender-clear-pending");

            iziToast.question({
                title: lang_js_clearpending_title,
                message: lang_js_clearpending_desc,
                titleLineHeight: "25px",
                backgroundColor: "#FE9431",
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        zender.loader(lang_js_clearpending_processing);

                        $.get(site_url + "/requests/clear/" + type, function(http) {
                            zender.loader(false, false);

                            try {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    zender.tables(false, true);
                                    alert.success(response.message);
                                } else {
                                    alert.warning(response.message);
                                }
                            } catch (e) {
                                alert.danger(lang_response_went_wrong);
                            }

                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });

        $(document).on("click", "[zender-sms-start]", function(e) {
            e.preventDefault();

            var attrData = $(this).attr("zender-sms-start").split("/");

            iziToast.question({
                title: lang_campaign_popup_titleresume,
                message: lang_campaign_popup_descsmsresume,
                titleLineHeight: "25px",
                backgroundColor: "#00c52c",
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        zender.loader(lang_js_clearpending_processing);

                        $.post(site_url + "/requests/remote/start_sms", {
                            cid: attrData[0],
                            did: attrData[1],
                            name: attrData[2]
                        }, function(http) {
                            zender.loader(false, false);

                            try {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    zender.tables(false, true);
                                    alert.success(response.message);
                                } else {
                                    alert.warning(response.message);
                                }
                            } catch (e) {
                                alert.danger(lang_response_went_wrong);
                            }

                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });

        $(document).on("click", "[zender-sms-stop]", function(e) {
            e.preventDefault();

            var attrData = $(this).attr("zender-sms-stop").split("/");

            iziToast.question({
                title: lang_campaign_popup_titlestop,
                message: lang_campaign_popup_descsmsstop,
                titleLineHeight: "25px",
                backgroundColor: "#FE9431",
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        zender.loader(lang_js_clearpending_processing);

                        $.post(site_url + "/requests/remote/stop_sms", {
                            cid: attrData[0],
                            did: attrData[1],
                            name: attrData[2]
                        }, function(http) {
                            zender.loader(false, false);

                            try {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    zender.tables(false, true);
                                    alert.warning(response.message);
                                } else {
                                    alert.danger(response.message);
                                }
                            } catch (e) {
                                alert.danger(lang_response_went_wrong);
                            }

                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });

        $(document).on("click", "[zender-whatsapp-start]", function(e) {
            e.preventDefault();

            var attrData = $(this).attr("zender-whatsapp-start").split("/");

            iziToast.question({
                title: lang_campaign_popup_titleresume,
                message: lang_campaign_popup_descwaresume,
                titleLineHeight: "25px",
                backgroundColor: "#00c52c",
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        zender.loader(lang_js_clearpending_processing);

                        $.post(site_url + "/requests/remote/start_chats", {
                            cid: attrData[0]
                        }, function(http) {
                            zender.loader(false, false);

                            try {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    zender.tables(false, true);
                                    alert.success(response.message);
                                } else {
                                    alert.warning(response.message);
                                }
                            } catch (e) {
                                alert.danger(lang_response_went_wrong);
                            }

                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });

        $(document).on("click", "[zender-whatsapp-stop]", function(e) {
            e.preventDefault();

            var attrData = $(this).attr("zender-whatsapp-stop").split("/");

            iziToast.question({
                title: lang_campaign_popup_titlestop,
                message: lang_campaign_popup_descwastop,
                titleLineHeight: "25px",
                backgroundColor: "#FE9431",
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        zender.loader(lang_js_clearpending_processing);

                        $.post(site_url + "/requests/remote/stop_chats", {
                            cid: attrData[0]
                        }, function(http) {
                            zender.loader(false, false);

                            try {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    zender.tables(false, true);
                                    alert.warning(response.message);
                                } else {
                                    alert.danger(response.message);
                                }
                            } catch (e) {
                                alert.danger(lang_response_went_wrong);
                            }

                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });
    },

    tawk: () => {
        $.get(site_url + "/requests/index/livechat", function(http) {
            try {
                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                if (response.status == 200) {
                    var Tawk_API = Tawk_API || {};
                    Tawk_API.visitor = {
                        name: response.data
                    };

                    var Tawk_LoadStart = new Date();
                    (function() {
                        var s1 = document.createElement("script"),
                            s0 = document.getElementsByTagName("script")[0];
                        s1.async = true;
                        s1.src = "https://embed.tawk.to/" + tawk_id;
                        s1.charset = "UTF-8";
                        s1.setAttribute("crossorigin", "*");
                        s0.parentNode.insertBefore(s1, s0);
                    })();
                }
            } catch (e) {
                alert.danger(lang_response_went_wrong);
            }
        });
    },

    authenticate: () => {
        if ($("[zender-authenticate-login]").length) {
            $("[zender-authenticate-login]").on("submit", function(e) {
                e.preventDefault();

                var data = new FormData(this);

                var input_require = "email|" + lang_require_email + "<=>password|" + lang_require_password;

                $.ajax({
                    url: site_url + "/requests/index/login",
                    type: "POST",
                    data: data,
                    contentType: false,
                    processData: false,
                    beforeSend: () => {
                        if (input_require) {
                            var filter = input_require.split("<=>");
                            for (var i = 0; i <= filter.length; i++) {
                                if (typeof filter[i] !== "undefined") {
                                    var values = filter[i].split("|");
                                }
                                try {
                                    if (data.get(values[0]).length < 1) {
                                        alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                        return false;
                                    }
                                } catch (e) {
                                    if (data.getAll(values[0] + "[]").length < 1) {
                                        alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                        return false;
                                    }
                                }
                            }
                        }

                        zender.disabled();
                        zender.loader(lang_js_authenticate_processing);
                    },
                    success: (http) => {
                        zender.loader(false, false);

                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            switch (response.status) {
                                case 200:
                                    $("[zender-login-confirm]").fadeOut("fast", function() {
                                        $("[zender-login-confirm]").html(`
                                            <div class="alert alert-info text-justify mb-0">
                                                <p>` + response.message + `</p>
                                            </div>
                                        `);

                                        $("[zender-login-confirm]").fadeIn("slow");
                                    });

                                    break;
                                case 301:
                                    alert.success(response.message, true);

                                    break;
                                case 302:
                                    alert.warning(lang_response_session_false, true);

                                    break;
                                default:
                                    if (recaptcha_status)
                                        grecaptcha.reset();

                                    alert.danger(response.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }

                        zender.disabled(false);
                    }
                });
            });
        }

        if ($("[zender-authenticate-forgot]").length) {
            $("[zender-authenticate-forgot]").on("submit", function(e) {
                e.preventDefault();

                var data = new FormData(this);

                var input_require = "email|" + lang_require_email;

                $.ajax({
                    url: site_url + "/requests/index/forgot",
                    type: "POST",
                    data: data,
                    contentType: false,
                    processData: false,
                    beforeSend: () => {
                        if (input_require) {
                            var filter = input_require.split("<=>");
                            for (var i = 0; i <= filter.length; i++) {
                                if (typeof filter[i] !== "undefined") {
                                    var values = filter[i].split("|");
                                }
                                try {
                                    if (data.get(values[0]).length < 1) {
                                        alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                        return false;
                                    }
                                } catch (e) {
                                    if (data.getAll(values[0] + "[]").length < 1) {
                                        alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                        return false;
                                    }
                                }
                            }
                        }

                        zender.disabled();
                        zender.loader(lang_js_authenticate_processing);
                    },
                    success: (http) => {
                        zender.loader(false, false);

                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            switch (response.status) {
                                case 200:
                                    $("[zender-authenticate-forgot]").replaceWith(`
                                        <div class="alert alert-success text-center mt-3">` + response.message + `</div>
                                    `);

                                    break;
                                case 301:
                                    alert.warning(response.message, true);

                                    break;
                                default:
                                    if (recaptcha_status)
                                        grecaptcha.reset();

                                    alert.danger(response.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }

                        zender.disabled(false);
                    }
                });
            });
        }

        if ($("[zender-authenticate-register]").length) {
            $("select").selectpicker();

            $("[zender-authenticate-register]").on("submit", function(e) {
                e.preventDefault();

                var data = new FormData(this);

                var input_require = "name|" + lang_require_name + "<=>email|" + lang_require_email + "<=>password|" + lang_require_password + "<=>cpassword|" + lang_require_cpassword;

                $.ajax({
                    url: site_url + "/requests/index/register",
                    type: "POST",
                    data: data,
                    contentType: false,
                    processData: false,
                    beforeSend: () => {
                        if (input_require) {
                            var filter = input_require.split("<=>");
                            for (var i = 0; i <= filter.length; i++) {
                                if (typeof filter[i] !== "undefined") {
                                    var values = filter[i].split("|");
                                }
                                try {
                                    if (data.get(values[0]).length < 1) {
                                        alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                        return false;
                                    }
                                } catch (e) {
                                    if (data.getAll(values[0] + "[]").length < 1) {
                                        alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                        return false;
                                    }
                                }
                            }
                        }

                        zender.disabled();
                        zender.loader(lang_js_authenticate_processing);
                    },
                    success: (http) => {
                        zender.loader(false, false);

                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            switch (response.status) {
                                case 200:
                                    $("[zender-register-confirm]").fadeOut("fast", function() {
                                        $("[zender-register-confirm]").html(`
                                            <div class="alert alert-info text-justify mb-0">
                                                <p>` + response.message + `</p>
                                            </div>
                                        `);

                                        $("[zender-register-confirm]").fadeIn("slow");
                                    });
                                    break;
                                case 301:
                                    alert.success(response.message, true);
                                    break;
                                case 302:
                                    alert.warning(lang_response_session_false, true);
                                    break;
                                default:
                                    if (recaptcha_status)
                                        grecaptcha.reset();

                                    alert.danger(response.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }

                        zender.disabled(false);
                    }
                });
            });
        }
    },

    duration: () => {
        if ($("[zender-duration]").length) {
            $("[zender-duration]").on("change paste keyup cut select", function() {
                var value = $(this).val();

                if ($("[zender-duration-price]").length) {
                    var price = $("input[name=price]").val();

                    if (value < 0) {
                        $(this).val(1);
                    } else if (value < 1) {
                        $(this).val(false);
                        var value = 1;
                        $("[zender-duration-price]").text(price);
                    } else {
                        var price = $("input[name=price]").val() * value;
                        $("[zender-duration-price]").text(price);
                    }

                    $("[zender-duration-button]").attr("zender-toggle", "zender.payment/" + $("input[name=id]").val() + "/" + value);
                } else {
                    if (value < 0) {
                        $(this).val(1);
                    } else if (value < 1) {
                        $(this).val(false);
                    }
                }
            });
        }

        if ($("[zender-credits]").length) {
            $("[zender-credits]").on("change paste keyup cut select", function() {
                var value = $(this).val();

                $("[zender-credits-button]").attr("zender-toggle", "zender.payment/credits/" + value);
            });
        }
    },

    action: () => {
        $(document).on("click", "[zender-action]", function(e) {
            e.preventDefault();

            var action = $(this).attr("zender-action");

            switch (action) {
                case "trigger":
                    var triggerLink = $(this).attr("webhook-link");
                    var triggerSecret = $(this).attr("webhook-secret");
                    var triggerType = "sms";

                    iziToast.question({
                        title: lang_js_actiontrigger_title,
                        titleLineHeight: "25px",
                        backgroundColor: color_primary,
                        position: "center",
                        icon: false,
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        layout: 1,
                        zindex: 1051,
                        animateInside: false,
                        inputs: [
                            ["<select class=\"text-white\" data-live-search=\"true\" zender-trigger><option value=\"#\" selected disabled>" + lang_js_actiontrigger_typetitle + "</option><option value=\"sms\">" + lang_js_actiontrigger_typesms + "</option><option value=\"whatsapp\">" + lang_js_actiontrigger_typewa + "</option><option value=\"ussd\">" + lang_js_actiontrigger_typeussd + "</option><option value=\"notification\">" + lang_js_actiontrigger_typenoti + "</option></select>", "change", function(instance, toast, select, e) {
                                if (select.options[select.selectedIndex].value != "#") {
                                    triggerType = select.options[select.selectedIndex].value;
                                }
                            }]
                        ],
                        buttons: [
                            ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                                zender.loader(lang_js_actiontrigger_loader);

                                $.post(site_url + "/requests/index/trigger", {
                                    secret: triggerSecret,
                                    type: triggerType,
                                    url: triggerLink
                                }, (http) => {
                                    zender.loader(false, false);

                                    try {
                                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                        if (response.status == 200) {
                                            alert.success(response.message);
                                        } else {
                                            alert.warning(response.message);
                                        }
                                    } catch (e) {
                                        alert.danger(lang_response_went_wrong);
                                    }

                                    instance.hide({
                                        transitionOut: "fadeOut"
                                    }, toast, "button");
                                });
                            }, true],
                            ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ],
                        onOpening: () => {
                            $("[zender-trigger]").selectpicker();
                        }
                    });

                    break;
                case "translate":
                    var transFrom = "en";
                    var transTo = "de";

                    iziToast.question({
                        title: lang_js_actiontranslator_title,
                        titleLineHeight: "25px",
                        backgroundColor: color_primary,
                        position: "center",
                        icon: false,
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        layout: 1,
                        zindex: 1051,
                        animateInside: false,
                        maxWidth: "497px",
                        inputs: [
                            ["<select class=\"text-white\" data-live-search=\"true\" zender-translate><option value=\"#\" selected disabled>" + lang_js_actiontranslator_from + "</option><option value=\"af\" data-tokens=\"af Afrikaans\">Afrikaans</option><option value=\"sq\" data-tokens=\"sq Albanian\">Albanian</option><option value=\"am\" data-tokens=\"am Amharic\">Amharic</option><option value=\"ar\" data-tokens=\"ar Arabic\">Arabic</option><option value=\"hy\" data-tokens=\"hy Armenian\">Armenian</option><option value=\"az\" data-tokens=\"az Azerbaijani\">Azerbaijani</option><option value=\"eu\" data-tokens=\"eu Basque\">Basque</option><option value=\"be\" data-tokens=\"be Belarusian\">Belarusian</option><option value=\"bn\" data-tokens=\"bn Bengali\">Bengali</option><option value=\"bs\" data-tokens=\"bs Bosnian\">Bosnian</option><option value=\"bg\" data-tokens=\"bg Bulgarian\">Bulgarian</option><option value=\"ca\" data-tokens=\"ca Catalan\">Catalan</option><option value=\"ceb\" data-tokens=\"ceb Cebuano\">Cebuano</option><option value=\"ny\" data-tokens=\"ny Chichewa\">Chichewa</option><option value=\"zh-CN\" data-tokens=\"zh-CN Chinese\">Chinese</option><option value=\"co\" data-tokens=\"co Corsican\">Corsican</option><option value=\"hr\" data-tokens=\"hr Croatian\">Croatian</option><option value=\"cs\" data-tokens=\"cs Czech\">Czech</option><option value=\"da\" data-tokens=\"da Danish\">Danish</option><option value=\"nl\" data-tokens=\"nl Dutch\">Dutch</option><option value=\"en\" data-tokens=\"en English\">English</option><option value=\"eo\" data-tokens=\"eo Esperanto\">Esperanto</option><option value=\"et\" data-tokens=\"et Estonian\">Estonian</option><option value=\"tl\" data-tokens=\"tl Filipino\">Filipino</option><option value=\"fi\" data-tokens=\"fi Finnish\">Finnish</option><option value=\"fr\" data-tokens=\"fr French\">French</option><option value=\"fy\" data-tokens=\"fy Frisian\">Frisian</option><option value=\"gl\" data-tokens=\"gl Galician\">Galician</option><option value=\"ka\" data-tokens=\"ka Georgian\">Georgian</option><option value=\"de\" data-tokens=\"de German\">German</option><option value=\"el\" data-tokens=\"el Greek\">Greek</option><option value=\"gu\" data-tokens=\"gu Gujarati\">Gujarati</option><option value=\"ht\" data-tokens=\"ht Haitian Creole\">Haitian Creole</option><option value=\"ha\" data-tokens=\"ha Hausa\">Hausa</option><option value=\"haw\" data-tokens=\"haw Hawaiian\">Hawaiian</option><option value=\"iw\" data-tokens=\"iw Hebrew\">Hebrew</option><option value=\"hi\" data-tokens=\"hi Hindi\">Hindi</option><option value=\"hmn\" data-tokens=\"hmn Hmong\">Hmong</option><option value=\"hu\" data-tokens=\"hu Hungarian\">Hungarian</option><option value=\"is\" data-tokens=\"is Icelandic\">Icelandic</option><option value=\"ig\" data-tokens=\"ig Igbo\">Igbo</option><option value=\"id\" data-tokens=\"id Indonesian\">Indonesian</option><option value=\"ga\" data-tokens=\"ga Irish\">Irish</option><option value=\"it\" data-tokens=\"it Italian\">Italian</option><option value=\"ja\" data-tokens=\"ja Japanese\">Japanese</option><option value=\"jw\" data-tokens=\"jw Javanese\">Javanese</option><option value=\"kn\" data-tokens=\"kn Kannada\">Kannada</option><option value=\"kk\" data-tokens=\"kk Kazakh\">Kazakh</option><option value=\"km\" data-tokens=\"km Khmer\">Khmer</option><option value=\"rw\" data-tokens=\"rw Kinyarwanda\">Kinyarwanda</option><option value=\"ko\" data-tokens=\"ko Korean\">Korean</option><option value=\"ku\" data-tokens=\"ku Kurdish (Kurmanji)\">Kurdish (Kurmanji)</option><option value=\"ky\" data-tokens=\"ky Kyrgyz\">Kyrgyz</option><option value=\"lo\" data-tokens=\"lo Lao\">Lao</option><option value=\"la\" data-tokens=\"la Latin\">Latin</option><option value=\"lv\" data-tokens=\"lv Latvian\">Latvian</option><option value=\"lt\" data-tokens=\"lt Lithuanian\">Lithuanian</option><option value=\"lb\" data-tokens=\"lb Luxembourgish\">Luxembourgish</option><option value=\"mk\" data-tokens=\"mk Macedonian\">Macedonian</option><option value=\"mg\" data-tokens=\"mg Malagasy\">Malagasy</option><option value=\"ms\" data-tokens=\"ms Malay\">Malay</option><option value=\"ml\" data-tokens=\"ml Malayalam\">Malayalam</option><option value=\"mt\" data-tokens=\"mt Maltese\">Maltese</option><option value=\"mi\" data-tokens=\"mi Maori\">Maori</option><option value=\"mr\" data-tokens=\"mr Marathi\">Marathi</option><option value=\"mn\" data-tokens=\"mn Mongolian\">Mongolian</option><option value=\"my\" data-tokens=\"my Myanmar (Burmese)\">Myanmar (Burmese)</option><option value=\"ne\" data-tokens=\"ne Nepali\">Nepali</option><option value=\"no\" data-tokens=\"no Norwegian\">Norwegian</option><option value=\"or\" data-tokens=\"or Odia (Oriya)\">Odia (Oriya)</option><option value=\"ps\" data-tokens=\"ps Pashto\">Pashto</option><option value=\"fa\" data-tokens=\"fa Persian\">Persian</option><option value=\"pl\" data-tokens=\"pl Polish\">Polish</option><option value=\"pt\" data-tokens=\"pt Portuguese\">Portuguese</option><option value=\"pa\" data-tokens=\"pa Punjabi\">Punjabi</option><option value=\"ro\" data-tokens=\"ro Romanian\">Romanian</option><option value=\"ru\" data-tokens=\"ru Russian\">Russian</option><option value=\"sm\" data-tokens=\"sm Samoan\">Samoan</option><option value=\"gd\" data-tokens=\"gd Scots Gaelic\">Scots Gaelic</option><option value=\"sr\" data-tokens=\"sr Serbian\">Serbian</option><option value=\"st\" data-tokens=\"st Sesotho\">Sesotho</option><option value=\"sn\" data-tokens=\"sn Shona\">Shona</option><option value=\"sd\" data-tokens=\"sd Sindhi\">Sindhi</option><option value=\"si\" data-tokens=\"si Sinhala\">Sinhala</option><option value=\"sk\" data-tokens=\"sk Slovak\">Slovak</option><option value=\"sl\" data-tokens=\"sl Slovenian\">Slovenian</option><option value=\"so\" data-tokens=\"so Somali\">Somali</option><option value=\"es\" data-tokens=\"es Spanish\">Spanish</option><option value=\"su\" data-tokens=\"su Sundanese\">Sundanese</option><option value=\"sw\" data-tokens=\"sw Swahili\">Swahili</option><option value=\"sv\" data-tokens=\"sv Swedish\">Swedish</option><option value=\"tg\" data-tokens=\"tg Tajik\">Tajik</option><option value=\"ta\" data-tokens=\"ta Tamil\">Tamil</option><option value=\"tt\" data-tokens=\"tt Tatar\">Tatar</option><option value=\"te\" data-tokens=\"te Telugu\">Telugu</option><option value=\"th\" data-tokens=\"th Thai\">Thai</option><option value=\"tr\" data-tokens=\"tr Turkish\">Turkish</option><option value=\"tk\" data-tokens=\"tk Turkmen\">Turkmen</option><option value=\"uk\" data-tokens=\"uk Ukrainian\">Ukrainian</option><option value=\"ur\" data-tokens=\"ur Urdu\">Urdu</option><option value=\"ug\" data-tokens=\"ug Uyghur\">Uyghur</option><option value=\"uz\" data-tokens=\"uz Uzbek\">Uzbek</option><option value=\"vi\" data-tokens=\"vi Vietnamese\">Vietnamese</option><option value=\"cy\" data-tokens=\"cy Welsh\">Welsh</option><option value=\"xh\" data-tokens=\"xh Xhosa\">Xhosa</option><option value=\"yi\" data-tokens=\"yi Yiddish\">Yiddish</option><option value=\"yo\" data-tokens=\"yo Yoruba\">Yoruba</option><option value=\"zu\" data-tokens=\"zu Zulu\">Zulu</option></select>", "change", function(instance, toast, select, e) {
                                if (select.options[select.selectedIndex].value != "#") {
                                    transFrom = select.options[select.selectedIndex].value;
                                }
                            }],
                            ["<select class=\"text-white\" data-live-search=\"true\" zender-translate><option value=\"#\" selected disabled>" + lang_js_actiontranslator_to + "</option><option value=\"af\" data-tokens=\"af Afrikaans\">Afrikaans</option><option value=\"sq\" data-tokens=\"sq Albanian\">Albanian</option><option value=\"am\" data-tokens=\"am Amharic\">Amharic</option><option value=\"ar\" data-tokens=\"ar Arabic\">Arabic</option><option value=\"hy\" data-tokens=\"hy Armenian\">Armenian</option><option value=\"az\" data-tokens=\"az Azerbaijani\">Azerbaijani</option><option value=\"eu\" data-tokens=\"eu Basque\">Basque</option><option value=\"be\" data-tokens=\"be Belarusian\">Belarusian</option><option value=\"bn\" data-tokens=\"bn Bengali\">Bengali</option><option value=\"bs\" data-tokens=\"bs Bosnian\">Bosnian</option><option value=\"bg\" data-tokens=\"bg Bulgarian\">Bulgarian</option><option value=\"ca\" data-tokens=\"ca Catalan\">Catalan</option><option value=\"ceb\" data-tokens=\"ceb Cebuano\">Cebuano</option><option value=\"ny\" data-tokens=\"ny Chichewa\">Chichewa</option><option value=\"zh-CN\" data-tokens=\"zh-CN Chinese\">Chinese</option><option value=\"co\" data-tokens=\"co Corsican\">Corsican</option><option value=\"hr\" data-tokens=\"hr Croatian\">Croatian</option><option value=\"cs\" data-tokens=\"cs Czech\">Czech</option><option value=\"da\" data-tokens=\"da Danish\">Danish</option><option value=\"nl\" data-tokens=\"nl Dutch\">Dutch</option><option value=\"en\" data-tokens=\"en English\">English</option><option value=\"eo\" data-tokens=\"eo Esperanto\">Esperanto</option><option value=\"et\" data-tokens=\"et Estonian\">Estonian</option><option value=\"tl\" data-tokens=\"tl Filipino\">Filipino</option><option value=\"fi\" data-tokens=\"fi Finnish\">Finnish</option><option value=\"fr\" data-tokens=\"fr French\">French</option><option value=\"fy\" data-tokens=\"fy Frisian\">Frisian</option><option value=\"gl\" data-tokens=\"gl Galician\">Galician</option><option value=\"ka\" data-tokens=\"ka Georgian\">Georgian</option><option value=\"de\" data-tokens=\"de German\">German</option><option value=\"el\" data-tokens=\"el Greek\">Greek</option><option value=\"gu\" data-tokens=\"gu Gujarati\">Gujarati</option><option value=\"ht\" data-tokens=\"ht Haitian Creole\">Haitian Creole</option><option value=\"ha\" data-tokens=\"ha Hausa\">Hausa</option><option value=\"haw\" data-tokens=\"haw Hawaiian\">Hawaiian</option><option value=\"iw\" data-tokens=\"iw Hebrew\">Hebrew</option><option value=\"hi\" data-tokens=\"hi Hindi\">Hindi</option><option value=\"hmn\" data-tokens=\"hmn Hmong\">Hmong</option><option value=\"hu\" data-tokens=\"hu Hungarian\">Hungarian</option><option value=\"is\" data-tokens=\"is Icelandic\">Icelandic</option><option value=\"ig\" data-tokens=\"ig Igbo\">Igbo</option><option value=\"id\" data-tokens=\"id Indonesian\">Indonesian</option><option value=\"ga\" data-tokens=\"ga Irish\">Irish</option><option value=\"it\" data-tokens=\"it Italian\">Italian</option><option value=\"ja\" data-tokens=\"ja Japanese\">Japanese</option><option value=\"jw\" data-tokens=\"jw Javanese\">Javanese</option><option value=\"kn\" data-tokens=\"kn Kannada\">Kannada</option><option value=\"kk\" data-tokens=\"kk Kazakh\">Kazakh</option><option value=\"km\" data-tokens=\"km Khmer\">Khmer</option><option value=\"rw\" data-tokens=\"rw Kinyarwanda\">Kinyarwanda</option><option value=\"ko\" data-tokens=\"ko Korean\">Korean</option><option value=\"ku\" data-tokens=\"ku Kurdish (Kurmanji)\">Kurdish (Kurmanji)</option><option value=\"ky\" data-tokens=\"ky Kyrgyz\">Kyrgyz</option><option value=\"lo\" data-tokens=\"lo Lao\">Lao</option><option value=\"la\" data-tokens=\"la Latin\">Latin</option><option value=\"lv\" data-tokens=\"lv Latvian\">Latvian</option><option value=\"lt\" data-tokens=\"lt Lithuanian\">Lithuanian</option><option value=\"lb\" data-tokens=\"lb Luxembourgish\">Luxembourgish</option><option value=\"mk\" data-tokens=\"mk Macedonian\">Macedonian</option><option value=\"mg\" data-tokens=\"mg Malagasy\">Malagasy</option><option value=\"ms\" data-tokens=\"ms Malay\">Malay</option><option value=\"ml\" data-tokens=\"ml Malayalam\">Malayalam</option><option value=\"mt\" data-tokens=\"mt Maltese\">Maltese</option><option value=\"mi\" data-tokens=\"mi Maori\">Maori</option><option value=\"mr\" data-tokens=\"mr Marathi\">Marathi</option><option value=\"mn\" data-tokens=\"mn Mongolian\">Mongolian</option><option value=\"my\" data-tokens=\"my Myanmar (Burmese)\">Myanmar (Burmese)</option><option value=\"ne\" data-tokens=\"ne Nepali\">Nepali</option><option value=\"no\" data-tokens=\"no Norwegian\">Norwegian</option><option value=\"or\" data-tokens=\"or Odia (Oriya)\">Odia (Oriya)</option><option value=\"ps\" data-tokens=\"ps Pashto\">Pashto</option><option value=\"fa\" data-tokens=\"fa Persian\">Persian</option><option value=\"pl\" data-tokens=\"pl Polish\">Polish</option><option value=\"pt\" data-tokens=\"pt Portuguese\">Portuguese</option><option value=\"pa\" data-tokens=\"pa Punjabi\">Punjabi</option><option value=\"ro\" data-tokens=\"ro Romanian\">Romanian</option><option value=\"ru\" data-tokens=\"ru Russian\">Russian</option><option value=\"sm\" data-tokens=\"sm Samoan\">Samoan</option><option value=\"gd\" data-tokens=\"gd Scots Gaelic\">Scots Gaelic</option><option value=\"sr\" data-tokens=\"sr Serbian\">Serbian</option><option value=\"st\" data-tokens=\"st Sesotho\">Sesotho</option><option value=\"sn\" data-tokens=\"sn Shona\">Shona</option><option value=\"sd\" data-tokens=\"sd Sindhi\">Sindhi</option><option value=\"si\" data-tokens=\"si Sinhala\">Sinhala</option><option value=\"sk\" data-tokens=\"sk Slovak\">Slovak</option><option value=\"sl\" data-tokens=\"sl Slovenian\">Slovenian</option><option value=\"so\" data-tokens=\"so Somali\">Somali</option><option value=\"es\" data-tokens=\"es Spanish\">Spanish</option><option value=\"su\" data-tokens=\"su Sundanese\">Sundanese</option><option value=\"sw\" data-tokens=\"sw Swahili\">Swahili</option><option value=\"sv\" data-tokens=\"sv Swedish\">Swedish</option><option value=\"tg\" data-tokens=\"tg Tajik\">Tajik</option><option value=\"ta\" data-tokens=\"ta Tamil\">Tamil</option><option value=\"tt\" data-tokens=\"tt Tatar\">Tatar</option><option value=\"te\" data-tokens=\"te Telugu\">Telugu</option><option value=\"th\" data-tokens=\"th Thai\">Thai</option><option value=\"tr\" data-tokens=\"tr Turkish\">Turkish</option><option value=\"tk\" data-tokens=\"tk Turkmen\">Turkmen</option><option value=\"uk\" data-tokens=\"uk Ukrainian\">Ukrainian</option><option value=\"ur\" data-tokens=\"ur Urdu\">Urdu</option><option value=\"ug\" data-tokens=\"ug Uyghur\">Uyghur</option><option value=\"uz\" data-tokens=\"uz Uzbek\">Uzbek</option><option value=\"vi\" data-tokens=\"vi Vietnamese\">Vietnamese</option><option value=\"cy\" data-tokens=\"cy Welsh\">Welsh</option><option value=\"xh\" data-tokens=\"xh Xhosa\">Xhosa</option><option value=\"yi\" data-tokens=\"yi Yiddish\">Yiddish</option><option value=\"yo\" data-tokens=\"yo Yoruba\">Yoruba</option><option value=\"zu\" data-tokens=\"zu Zulu\">Zulu</option></select>", "change", function(instance, toast, select, e) {
                                if (select.options[select.selectedIndex].value != "#") {
                                    transTo = select.options[select.selectedIndex].value;
                                }
                            }]
                        ],
                        buttons: [
                            ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                                var transMessage = $("textarea[name=message]").val();

                                zender.loader(lang_js_actiontranslator_loader);

                                $.post(site_url + "/requests/translate/" + transFrom + "/" + transTo, {
                                    message: transMessage
                                }, function(http) {
                                    zender.loader(false, false);

                                    try {
                                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                        if (response.status == 200) {
                                            $("textarea[name=message]").val(response.data);
                                            $("textarea[name=message]").trigger("change.counter");
                                        } else {
                                            alert.danger(response.message);
                                        }
                                    } catch (e) {
                                        alert.danger(lang_response_went_wrong);
                                    }

                                    instance.hide({
                                        transitionOut: "fadeOut"
                                    }, toast, "button");
                                });
                            }, true],
                            ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ],
                        onOpening: () => {
                            $("[zender-translate]").selectpicker();
                        }
                    });

                    break;
                case "regenerate":
                    iziToast.question({
                        title: lang_js_actionregen_title,
                        message: lang_js_actionregen_desc,
                        titleLineHeight: "25px",
                        backgroundColor: "#E82753",
                        position: "center",
                        icon: false,
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        layout: 1,
                        zindex: 1051,
                        buttons: [
                            ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                                zender.loader(lang_js_actionregen_loader);

                                $.get(site_url + "/requests/index/regenerate", function(http) {
                                    zender.loader(false, false);

                                    try {
                                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                        if (response.status == 200) {
                                            $("[zender-token]").text(response.data);

                                            alert.success(response.message);
                                        } else {
                                            alert.danger(response.message);
                                        }
                                    } catch (e) {
                                        alert.danger(lang_response_went_wrong);
                                    }

                                    instance.hide({
                                        transitionOut: "fadeOut"
                                    }, toast, "button");
                                });
                            }, true],
                            ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ]
                    });

                    break;
                case "token":
                    iziToast.question({
                        title: "Titan Echo",
                        message: lang_js_response_titanechorefreshdesc,
                        titleLineHeight: "25px",
                        backgroundColor: "#FE9431",
                        position: "center",
                        icon: false,
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        layout: 1,
                        zindex: 1024,
                        buttons: [
                            ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                                zender.loader(lang_js_processing_dataloader);

                                $.get(site_url + "/requests/clear/token", function(http) {
                                    zender.loader(false, false);

                                    try {
                                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                        if (response.status == 200) {
                                            alert.success(response.message);
                                        } else {
                                            alert.danger(response.message);
                                        }
                                    } catch (e) {
                                        alert.danger(lang_response_went_wrong);
                                    }

                                    instance.hide({
                                        transitionOut: "fadeOut"
                                    }, toast, "button");
                                });
                            }, true],
                            ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ]
                    });

                    break;
                case "clear":
                    iziToast.question({
                        title: lang_js_actionclear_title,
                        message: lang_js_actionclear_desc,
                        titleLineHeight: "25px",
                        backgroundColor: "#FE9431",
                        position: "center",
                        icon: false,
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        layout: 1,
                        zindex: 1024,
                        buttons: [
                            ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                                zender.loader(lang_js_processing_dataloader);

                                $.get(site_url + "/requests/clear/cache", function(http) {
                                    zender.loader(false, false);

                                    try {
                                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                        if (response.status == 200) {
                                            alert.success(response.message);
                                        } else {
                                            alert.danger(response.message);
                                        }
                                    } catch (e) {
                                        alert.danger(lang_response_went_wrong);
                                    }

                                    instance.hide({
                                        transitionOut: "fadeOut"
                                    }, toast, "button");
                                });
                            }, true],
                            ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ]
                    });

                    break;
                case "mollie":
                    zender.loader(lang_js_processing_dataloader);

                    $.get(site_url + "/requests/payment/mollie", (http) => {
                        zender.loader(false, false);

                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            if (response.status == 200) {
                                alert.success(response.message);

                                setTimeout(() => {
                                    zender.redirect(response.data);
                                }, 3000);
                            } else {
                                alert.danger(response.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }
                    });

                    break;
                case "payout_confirm":
                    zender.loader(lang_js_processing_dataloader);

                    var payoutId = $(this).attr("payout-id");

                    $.get(site_url + "/requests/payout/confirm/" + payoutId, (http) => {
                        setTimeout(() => {
                            zender.loader(false, false);
                            alert.success(response.message);
                        }, 2000);

                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            if (response.status == 200) {
                                zender.tables(false, true);
                            } else {
                                alert.danger(response.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }
                    });

                    break;
                case "payout_reject":
                    zender.loader(lang_js_processing_dataloader);

                    var payoutId = $(this).attr("payout-id");

                    $.get(site_url + "/requests/payout/reject/" + payoutId, (http) => {
                        setTimeout(() => {
                            zender.loader(false, false);
                            alert.success(response.message);
                        }, 2000);

                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            if (response.status == 200) {
                                zender.tables(false, true);
                            } else {
                                alert.danger(response.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }
                    });

                    break;
                case "resend":
                    zender.loader(lang_js_processing_dataloader);

                    var resendId = $(this).attr("resend-id");

                    $.get(site_url + "/requests/resend/sms/" + resendId, (http) => {
                        zender.loader(false, false);

                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            if (response.status == 200) {
                                zender.tables(false, true);
                                alert.success(response.message);
                            } else {
                                alert.danger(response.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }
                    });

                    break;
                case "suspend":
                    var uid = $(this).attr("user-id");

                    iziToast.question({
                        title: lang_suspend_user_title,
                        message: lang_suspend_user_desc,
                        titleLineHeight: "25px",
                        backgroundColor: "#FE9431",
                        position: "center",
                        icon: false,
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        layout: 1,
                        zindex: 1024,
                        buttons: [
                            ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                                $.ajax({
                                    url: site_url + "/requests/update/admin.suspend",
                                    type: "POST",
                                    data: "id=" + uid,
                                    beforeSend: function() {
                                        instance.hide({
                                            transitionOut: "fadeOut"
                                        }, toast, "button");
                                    },
                                    success: function(http) {
                                        try {
                                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));
                                            if (response.status == 200) {
                                                zender.tables(false, true);
                                                alert.success(response.message);
                                            } else {
                                                alert.danger(response.message);
                                            }
                                        } catch (e) {
                                            alert.danger(lang_response_went_wrong);
                                        }
                                    }
                                });
                            }, true],
                            ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ]
                    });

                    break;
                case "unsuspend":
                    var uid = $(this).attr("user-id");

                    iziToast.question({
                        title: lang_unsuspend_user_title,
                        message: lang_unsuspend_user_desc,
                        titleLineHeight: "25px",
                        backgroundColor: color_primary,
                        position: "center",
                        icon: false,
                        drag: false,
                        timeout: false,
                        close: false,
                        overlay: true,
                        layout: 1,
                        zindex: 1024,
                        buttons: [
                            ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                                $.ajax({
                                    url: site_url + "/requests/update/admin.unsuspend",
                                    type: "POST",
                                    data: "id=" + uid,
                                    beforeSend: function() {
                                        instance.hide({
                                            transitionOut: "fadeOut"
                                        }, toast, "button");
                                    },
                                    success: function(http) {
                                        try {
                                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));
                                            if (response.status == 200) {
                                                zender.tables(false, true);
                                                alert.success(response.message);
                                            } else {
                                                alert.danger(response.message);
                                            }
                                        } catch (e) {
                                            alert.danger(lang_response_went_wrong);
                                        }
                                    }
                                });
                            }, true],
                            ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");
                            }]
                        ]
                    });

                    break;
                case "logout":
                    $.get(site_url + "/requests/index/logout", (http) => {
                        try {
                            var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            if (response.status == 200) {
                                alert.success(response.message, true);
                            } else {
                                location.reload();
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }
                    });

                    break;
                default:
                    alert.danger(lang_unknown_action_method);
            }
        });
    },

    translate: (homepage = false) => {
        $("body").append(
            `<ul class="mfb-component--br mfb-slidein-spring" data-mfb-toggle="hover">
                <li class="mfb-component__wrap">
                    <a href="#" class="mfb-component__button--main" zender-scroll-top>
                        <i class="mfb-component__main-icon--resting la la-language la-lg text-white"></i>
                        <i class="mfb-component__main-icon--active la la-arrow-up la-lg text-white"></i>
                    </a>
                    <ul class="mfb-component__list" zender-languages></ul>
                </li>
            </ul>`
        ).ready(() => {
            $.get(site_url + "/requests/languages/list", function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    $("[zender-languages]").html(response.data);

                    if (homepage) {
                        $(document).on("click", "[zender-toggle=\"zender.languages\"]", function(e) {
                            e.preventDefault();

                            $.get(site_url + "/widget/modal/default/zender.languages", function(http) {
                                try {
                                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                    $("body").append(response.data.tpl);

                                    var langModal = new bootstrap.Modal(document.getElementById("lang-modal"), {
                                        keyboard: false
                                    });

                                    langModal.show();
                                } catch (e) {
                                    alert.danger(lang_response_went_wrong);
                                }
                            });
                        });
                    }

                    $(document).on("click", "[zender-language]", function(e) {
                        e.preventDefault();
                        zender.loader(lang_js_processing_dataloader);

                        $.get(site_url + "/requests/languages/change/" + $(this).attr("zender-language"), function(http) {
                            zender.loader(false, false);

                            try {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    alert.success(response.message, true);
                                } else {
                                    alert.warning(response.message);
                                }
                            } catch (e) {
                                alert.danger(lang_response_went_wrong);
                            }
                        });
                    });
                } catch (e) {
                    alert.danger(lang_response_went_wrong);
                }
            });

            zender.scroll();
        });
    },

    codeflask: () => {
        if ($("[zender-codeflask]").length) {
            window.codeflask = new CodeFlask("[zender-codeflask]", {
                language: "html",
                lineNumbers: true
            });

            codeflask.updateCode(he.decode(codeflask.getCode()));
        }
    },

    jsoneditor: () => {
        if ($("#zender-jsoneditor-list").length) {
            var container = document.getElementById("zender-jsoneditor-list");
            var options = {
                mode: "code"
            };

            window.jsonEditorList = new JSONEditor(container, options);

            var sampleList = [{
                "title": "Section 1",
                "rows": [{
                    "title": "Option 1"
                }, {
                    "title": "Option 2"
                }]
            }, {
                "title": "Section 2",
                "rows": [{
                    "title": "Option 1"
                }, {
                    "title": "Option 2",
                    "description": "This is a description of option"
                }]
            }];

            jsonEditorList.set(sampleList);
        }

        if ($("#zender-jsoneditor-template").length) {
            var container = document.getElementById("zender-jsoneditor-template");
            var options = {
                mode: "code"
            };

            window.jsonEditorTemplate = new JSONEditor(container, options);

            var sampleTemplate = [{
                "urlButton": {
                    "index": 1,
                    "displayText": "Visit our Website!",
                    "url": "http:" + site_url
                }
            }, {
                "callButton": {
                    "index": 2,
                    "displayText": "Call us!",
                    "phoneNumber": "+1 (234) 5678-910"
                }
            }];

            jsonEditorTemplate.set(sampleTemplate);
        }
    },

    clipboard: (destroy = false) => {
        if (!destroy) {
            if ($("[zender-clipboard]").length) {
                window.clipboard = new ClipboardJS("[zender-clipboard]");

                clipboard.on("success", (e) => {
                    e.clearSelection();
                    alert.primary(lang_copy_data);
                });
            }
        } else {
            if (window.clipboard)
                clipboard.destroy();
        }
    },

    disabled: (disabled = true) => {
        if (disabled)
            $(".form-control, .input, button[type=submit]").attr("disabled", "");
        else
            $(".form-control, .input, button[type=submit]").removeAttr("disabled");
    },

    scroll: () => {
        $("[zender-scroll-top]").on("click", function(e) {
            e.preventDefault();
            $("html, body").animate({
                scrollTop: "0px"
            }, 300);
        });

        $("[zender-scroll]").on("click", function(e) {
            e.preventDefault();
            $(window).stop(true).scrollTo(this.hash);
        });
    },

    qrcode: (data, width, height, element = "zender-qrcode") => {
        new QRCode(element, {
            text: data,
            width: width,
            height: height
        });
    },

    redirect: (path) => {
        window.location.href = path;
    },

    ripple: () => {
        Waves.init();
        Waves.attach(".btn");
    },

    iframe: () => {
        if ($("[zender-iframe]").length) {
            $("[zender-iframe]").each(function(i) {
                $(this).attr("src", $(this).attr("zender-iframe"));
                $(this).on("load", function() {
                    setTimeout(() => {
                        $(this).iFrameResize({
                            log: false,
                            resizeFrom: "child",
                            warningTimeout: 0,
                            heightCalculationMethod: "bodyScroll"
                        });
                    }, 500);
                });
            });
        }
    },

    datepicker: () => {
        if ($("[zender-datepicker]").length) {
            var ranges = [];
            ranges[lang_js_calendarall_option] = ["1/1/1975", moment()];
            ranges[lang_date_today] = [moment(), moment()];
            ranges[lang_date_yesterday] = [moment().subtract(1, "days"), moment().subtract(1, "days")];
            ranges[lang_date_7days] = [moment().subtract(6, "days"), moment()];
            ranges[lang_date_30days] = [moment().subtract(29, "days"), moment()];
            ranges[lang_date_month] = [moment().startOf("month"), moment().endOf("month")];
            ranges[lang_date_lmonth] = [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")];

            $(".daterangepicker").remove();
            $("[zender-datepicker]").daterangepicker({
                opens: "left",
                startDate: "1/1/1975",
                endDate: moment(),
                ranges: ranges,
                locale: {
                    applyLabel: lang_js_calendarall_apply,
                    cancelLabel: lang_js_calendarall_cancel,
                    fromLabel: lang_js_calendarall_from,
                    toLabel: lang_js_calendarall_to,
                    customRangeLabel: lang_date_custom,
                    weekLabel: lang_js_calendarall_week,
                    daysOfWeek: [
                        lang_js_calendarall_daysu,
                        lang_js_calendarall_daymo,
                        lang_js_calendarall_daytu,
                        lang_js_calendarall_daywe,
                        lang_js_calendarall_dayth,
                        lang_js_calendarall_dayfr,
                        lang_js_calendarall_daysa
                    ],
                    monthNames: [
                        lang_js_calendarall_monjan,
                        lang_js_calendarall_monfeb,
                        lang_js_calendarall_monmar,
                        lang_js_calendarall_monapr,
                        lang_js_calendarall_monmay,
                        lang_js_calendarall_monjun,
                        lang_js_calendarall_monjul,
                        lang_js_calendarall_monaug,
                        lang_js_calendarall_monsep,
                        lang_js_calendarall_monoct,
                        lang_js_calendarall_monnov,
                        lang_js_calendarall_mondec
                    ]
                }
            });
        }

        if ($("[zender-datepicker-schedule]").length) {
            $(".daterangepicker").remove();

            $("[zender-datepicker-schedule]").daterangepicker({
                opens: "left",
                timePicker: true,
                singleDatePicker: true,
                locale: {
                    format: "MM/DD/YYYY hh:mm A",
                    applyLabel: lang_js_calendarall_apply,
                    cancelLabel: lang_js_calendarall_cancel,
                    fromLabel: lang_js_calendarall_from,
                    toLabel: lang_js_calendarall_to,
                    customRangeLabel: lang_date_custom,
                    weekLabel: lang_js_calendarall_week,
                    daysOfWeek: [
                        lang_js_calendarall_daysu,
                        lang_js_calendarall_daymo,
                        lang_js_calendarall_daytu,
                        lang_js_calendarall_daywe,
                        lang_js_calendarall_dayth,
                        lang_js_calendarall_dayfr,
                        lang_js_calendarall_daysa
                    ],
                    monthNames: [
                        lang_js_calendarall_monjan,
                        lang_js_calendarall_monfeb,
                        lang_js_calendarall_monmar,
                        lang_js_calendarall_monapr,
                        lang_js_calendarall_monmay,
                        lang_js_calendarall_monjun,
                        lang_js_calendarall_monjul,
                        lang_js_calendarall_monaug,
                        lang_js_calendarall_monsep,
                        lang_js_calendarall_monoct,
                        lang_js_calendarall_monnov,
                        lang_js_calendarall_mondec
                    ]
                }
            });
        }
    },

    autocomplete: () => {
        if ($("[zender-autocomplete]").length) {
            $(".autocomplete-suggestions").remove();

            $.get(site_url + "/requests/autocomplete/" + ($("[zender-autocomplete]").attr("zender-autocomplete") ? $("[zender-autocomplete]").attr("zender-autocomplete") : false), function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    switch (response.status) {
                        case 200:
                            $("[zender-autocomplete]").autocomplete({
                                lookup: response.data,
                                onSelect: function(suggestion) {
                                    $(this).val(suggestion.data);
                                }
                            });
                            break;
                        case 302:
                            alert.warning(lang_response_session_false, true);
                            break;
                        default:
                            alert.danger(response.message);
                    }
                } catch (e) {
                    alert.danger(lang_response_went_wrong);
                }
            });
        }

        if ($("[zender-whatsapp-autocomplete]").length) {
            $(".autocomplete-suggestions").remove();

            $.post(site_url + "/requests/autocomplete/wa.contacts", {
                account: $("[zender-wa-account-select]").find(":selected").val()
            }, function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    switch (response.status) {
                        case 200:
                            $("[zender-whatsapp-autocomplete]").autocomplete({
                                lookup: response.data,
                                onSelect: function(suggestion) {
                                    $(this).val(suggestion.data);
                                }
                            });
                            break;
                        case 302:
                            alert.warning(lang_response_session_false, true);
                            break;
                        default:
                            alert.danger(response.message);
                    }
                } catch (e) {
                    alert.danger(lang_response_went_wrong);
                }
            });
        }
    },

    counter: () => {
        if ($("[zender-counter]").length) {
            $("[zender-counter]").counter({
                goal: message_max > 0 ? message_max : "sky",
                target: "[zender-counter-view]"
            });
        }
    },

    select: () => {
        if ($("[zender-form] select").length) {
            $("[zender-form] select").selectpicker();
        }

        if ($("[zender-wa-account-select]").length) {
            $("[zender-wa-account-select]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                $(".autocomplete-suggestions").remove();

                $.post(site_url + "/requests/autocomplete/wa.contacts", {
                    account: $(this).find(":selected").val()
                }, function(http) {
                    try {
                        var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                        switch (response.status) {
                            case 200:
                                $("[zender-whatsapp-autocomplete]").autocomplete({
                                    lookup: response.data,
                                    onSelect: function(suggestion) {
                                        $(this).val(suggestion.data);
                                    }
                                });
                                break;
                            case 302:
                                alert.warning(lang_response_session_false, true);
                                break;
                            default:
                                alert.danger(response.message);
                        }
                    } catch (e) {
                        alert.danger(lang_response_went_wrong);
                    }
                });
            });
        }

        if ($("[zender-select-template]").length) {
            $("[zender-select-template]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                var temp = $(this).find("option:selected");
                var format = (temp.data("format") ? temp.data("format") : false);

                if (format)
                    $("textarea[name=message]").val(format);
                else
                    $("textarea[name=message]").val("");
            });
        }

        if ($("[zender-select-groups]").length) {
            $("[zender-select-groups]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                var groups = $(this).find("option:selected"),
                    i;

                if (clickedIndex > 0) {
                    for (i = 0; i < groups.length; i++) {
                        if (groups[i].value == "0")
                            groups[i].selected = false;
                    }
                } else {
                    for (i = 0; i < groups.length; i++) {
                        if (groups[i].value != "0")
                            groups[i].selected = false;
                    }
                }

                $("[zender-select-groups]").selectpicker("refresh");
            });
        }

        if ($("[zender-select-users]").length) {
            $("[zender-select-users]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                var users = $(this).find("option:selected"),
                    i;

                if (clickedIndex > 0) {
                    for (i = 0; i < users.length; i++) {
                        if (users[i].value == "0")
                            users[i].selected = false;
                    }
                } else {
                    for (i = 0; i < users.length; i++) {
                        if (users[i].value != "0")
                            users[i].selected = false;
                    }
                }

                $("[zender-select-users]").selectpicker("refresh");
            });
        }

        if ($("[zender-select-roles]").length) {
            $("[zender-select-roles]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                var roles = $(this).find("option:selected"),
                    i;

                if (clickedIndex > 0) {
                    for (i = 0; i < roles.length; i++) {
                        if (roles[i].value == "0")
                            roles[i].selected = false;
                    }
                } else {
                    for (i = 0; i < roles.length; i++) {
                        if (roles[i].value != "0")
                            roles[i].selected = false;
                    }
                }

                $("[zender-select-roles]").selectpicker("refresh");
            });
        }

        if ($("[zender-select-adminapi]").length) {
            $("[zender-select-adminapi]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                var adminapi = $(this).find("option:selected"),
                    i;

                if (clickedIndex > 0) {
                    for (i = 0; i < adminapi.length; i++) {
                        if (adminapi[i].value == "0")
                            adminapi[i].selected = false;
                    }
                } else {
                    for (i = 0; i < adminapi.length; i++) {
                        if (adminapi[i].value != "0")
                            adminapi[i].selected = false;
                    }
                }

                $("[zender-select-adminapi]").selectpicker("refresh");
            });
        }

        if ($("[zender-select-mailing]").length) {
            $("[zender-select-mailing]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                var mailing = $(this).find("option:selected"),
                    i;

                if (clickedIndex > 0) {
                    for (i = 0; i < mailing.length; i++) {
                        if (mailing[i].value == "0")
                            mailing[i].selected = false;
                    }
                } else {
                    for (i = 0; i < mailing.length; i++) {
                        if (mailing[i].value != "0")
                            mailing[i].selected = false;
                    }
                }

                $("[zender-select-mailing]").selectpicker("refresh");
            });
        }

        if ($("[zender-select-mode]").length) {
            $("[zender-credits-mode]").hide();

            $("[zender-select-mode]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                var methodMode = $(this).find("option:selected");

                if (methodMode[0].value > 1) {
                    $("[zender-device-mode]").hide();
                    $("[zender-credits-mode]").show();
                } else {
                    $("[zender-device-mode]").show();
                    $("[zender-credits-mode]").hide();
                }
            });
        }

        if ($("[zender-wa-type]").length) {
            $("[zender-wa-type-media]").hide();
            $("[zender-wa-type-document]").hide();
            $("[zender-wa-type-button]").hide();
            $("[zender-wa-type-template]").hide();
            $("[zender-wa-type-list]").hide();

            window.typeMode = [{
                value: "text"
            }];

            $("[zender-wa-type]").on("changed.bs.select", function(e, clickedIndex, isSelected, previousValue) {
                typeMode = $(this).find("option:selected");

                if (typeMode[0].value == "text") {
                    $("[zender-wa-type-media]").hide();
                    $("[zender-wa-type-document]").hide();
                    $("[zender-wa-type-button]").hide();
                    $("[zender-wa-type-template]").hide();
                    $("[zender-wa-type-list]").hide();
                } else if (typeMode[0].value == "media") {
                    $("[zender-wa-type-media]").show();
                    $("[zender-wa-type-document]").hide();
                    $("[zender-wa-type-button]").hide();
                    $("[zender-wa-type-template]").hide();
                    $("[zender-wa-type-list]").hide();
                } else if (typeMode[0].value == "document") {
                    $("[zender-wa-type-document]").show();
                    $("[zender-wa-type-media]").hide();
                    $("[zender-wa-type-button]").hide();
                    $("[zender-wa-type-template]").hide();
                    $("[zender-wa-type-list]").hide();
                } else if (typeMode[0].value == "button") {
                    $("[zender-wa-type-button]").show();
                    $("[zender-wa-type-document]").hide();
                    $("[zender-wa-type-media]").hide();
                    $("[zender-wa-type-template]").hide();
                    $("[zender-wa-type-list]").hide();
                } else if (typeMode[0].value == "list") {
                    $("[zender-wa-type-list]").show();
                    $("[zender-wa-type-document]").hide();
                    $("[zender-wa-type-button]").hide();
                    $("[zender-wa-type-template]").hide();
                    $("[zender-wa-type-media]").hide();
                } else if (typeMode[0].value == "template") {
                    $("[zender-wa-type-template]").show();
                    $("[zender-wa-type-list]").hide();
                    $("[zender-wa-type-button]").hide();
                    $("[zender-wa-type-media]").hide();
                    $("[zender-wa-type-document]").hide();
                } else {
                    $("[zender-wa-type-document]").hide();
                    $("[zender-wa-type-media]").hide();
                    $("[zender-wa-type-button]").hide();
                    $("[zender-wa-type-template]").hide();
                    $("[zender-wa-type-list]").hide();
                }
            });
        }

        if ($("[zender-device-list]").length) {
            $("[zender-device-list] option").each((index, data) => {
                if ((data["attributes"]["online-id"] !== undefined) && data["attributes"]["online-id"].value.length > 0) {
                    echo.emit("status", data["attributes"]["online-id"].value, function(status) {
                        if (status) {
                            $(".device-status-" + data["attributes"]["device-id"].value).text(lang_js_userstatus_online);
                            $(".device-status-" + data["attributes"]["device-id"].value).attr("class", "badge badge-success device-status-" + data["attributes"]["device-id"].value);
                        } else {
                            $.get(site_url + "/requests/echo/offline", {
                                id: data["attributes"]["online-id"].value
                            }, function(http) {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                if (response.status == 200) {
                                    $(".device-status-" + response.data.device).text(lang_js_userstatus_offline);
                                    $(".device-status-" + response.data.device).attr("class", "badge badge-danger device-status-" + response.data.device);
                                }
                            });
                        }
                    });
                }
            });
        }
    },

    visitors: () => {
        window.visitorTracking = false;

        setTimeout(() => {
            if (!visitorTracking) {
                visitorTracking = true;
                $.get(site_url + "/requests/visitors");
            }
        }, 5000);
    },

    pages: () => {
        $(document).on("click", "[zender-page]", function(e) {
            var page = $(this).attr("zender-page").split("/");

            if (page.length > 1)
                pjax.loadUrl(site_url + "/pages/" + page[0] + "/" + page[1]);
        });
    },

    build: () => {
        $(document).on("click", "[zender-build]", function(e) {
            iziToast.question({
                title: lang_js_build_title,
                message: lang_js_build_desc,
                titleLineHeight: "25px",
                backgroundColor: color_primary,
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_js_build_btnbuild + "</button>", function(instance, toast, button, e, inputs) {
                        zender.loader(lang_requests_build_submitrequest);

                        $.get(site_url + "/requests/build", function(http) {
                            zender.loader(false, false);

                            try {
                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                switch (response.status) {
                                    case 200:
                                        alert.success(response.message);
                                        pjax.loadUrl(window.location.href);

                                        break;
                                    case 302:
                                        alert.warning(lang_response_session_false, true);
                                        break;
                                    default:
                                        alert.danger(response.message);
                                }
                            } catch (e) {
                                alert.danger(lang_response_went_wrong);
                            }

                            instance.hide({
                                transitionOut: "fadeOut"
                            }, toast, "button");
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });
    },

    reorder: () => {
        $(document).on("click", "[zender-reorder]", function(e) {
            e.preventDefault();

            iziToast.question({
                title: lang_js_reorder_title,
                message: lang_js_reorder_desc,
                titleLineHeight: "25px",
                backgroundColor: color_primary,
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        $.ajax({
                            url: site_url + "/requests/reorder",
                            type: "POST",
                            data: {
                                rows: tableSelected
                            },
                            beforeSend: function() {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");

                                zender.loader(lang_requests_reorder_loader);
                            },
                            success: function(http) {
                                zender.loader(false, false);
                                $("[zender-reorder]").attr("disabled", true);

                                try {
                                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                    switch (response.status) {
                                        case 200:
                                            tableSelected = [];
                                            zender.tables(false, true);
                                            alert.success(response.message, true);
                                            break;
                                        default:
                                            alert.danger(response.message);
                                    }
                                } catch (e) {
                                    alert.danger(lang_response_went_wrong);
                                }
                            }
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });
    },

    delete: () => {
        $(document).on("click", "[zender-delete]", function(e) {
            e.preventDefault();
            var param = ($(this).attr("zender-delete") ? $(this).attr("zender-delete") : false);
            iziToast.question({
                title: lang_delete_title,
                message: lang_delete_tagline,
                titleLineHeight: "25px",
                backgroundColor: "#E82753",
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        $.ajax({
                            url: site_url + "/requests/delete/" + param,
                            type: "GET",
                            beforeSend: function() {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");

                                zender.loader(lang_requests_deleteitem_loader);
                            },
                            success: function(http) {
                                zender.loader(false, false);

                                try {
                                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                    switch (response.status) {
                                        case 200:
                                            if (response.data.vars.table)
                                                zender.tables(false, true);

                                            alert.success(response.message);
                                            break;
                                        case 302:
                                            alert.warning(lang_response_session_false, true);
                                            break;
                                        default:
                                            alert.danger(response.message);
                                    }
                                } catch (e) {
                                    alert.danger(lang_response_went_wrong);
                                }
                            }
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });

        $(document).on("click", "[zender-trash]", function(e) {
            e.preventDefault();

            var type = $(this).attr("zender-trash");

            iziToast.question({
                title: lang_js_deletetrash_title,
                message: lang_js_deletetrash_desc,
                titleLineHeight: "25px",
                backgroundColor: "#E82753",
                position: "center",
                icon: false,
                drag: false,
                timeout: false,
                close: false,
                overlay: true,
                layout: 1,
                zindex: 1024,
                buttons: [
                    ["<button class=\"text-white\">" + lang_btn_confirm + "</button>", function(instance, toast, button, e, inputs) {
                        $.ajax({
                            url: site_url + "/requests/trash/" + type,
                            type: "POST",
                            data: {
                                rows: tableSelected
                            },
                            beforeSend: function() {
                                instance.hide({
                                    transitionOut: "fadeOut"
                                }, toast, "button");

                                zender.loader(lang_requests_deleteitem_loader);
                            },
                            success: function(http) {
                                zender.loader(false, false);
                                $("[zender-trash]").attr("disabled", true);

                                try {
                                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                    switch (response.status) {
                                        case 200:
                                            tableSelected = [];
                                            zender.tables(false, true);
                                            alert.success(response.message);
                                            break;
                                        default:
                                            alert.danger(response.message);
                                    }
                                } catch (e) {
                                    alert.danger(lang_response_went_wrong);
                                }
                            }
                        });
                    }, true],
                    ["<button class=\"text-white\">" + lang_btn_cancel + "</button>", function(instance, toast, button, e) {
                        instance.hide({
                            transitionOut: "fadeOut"
                        }, toast, "button");
                    }]
                ]
            });
        });
    },

    charts: (chart, update = false) => {
        if (update) {
            parent.jQuery.get(parent.site_url + "/requests/chart/" + chart, function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    apex.updateSeries(response.data.vars.series);
                } catch (e) {
                    parent.alert.danger(parent.lang_response_went_wrong);
                }
            });
        } else {
            parent.jQuery.get(parent.site_url + "/requests/chart/" + chart, function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    window.apex = new ApexCharts(
                        document.querySelector("#chart"), {
                            chart: {
                                type: "area",
                                height: 300,
                                foreColor: "#999",
                                stacked: true,
                                dropShadow: {
                                    enabled: true,
                                    enabledSeries: [0],
                                    top: -2,
                                    left: 2,
                                    blur: 5,
                                    opacity: 0.06
                                },
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: false
                                    }
                                }
                            },
                            colors: response.data.vars.colors,
                            stroke: {
                                curve: "smooth",
                                width: 4
                            },
                            dataLabels: {
                                enabled: false
                            },
                            series: response.data.vars.series,
                            markers: {
                                size: 0,
                                strokeColor: "#fff",
                                strokeWidth: 3,
                                strokeOpacity: 1,
                                fillOpacity: 1,
                                hover: {
                                    size: 6
                                }
                            },
                            xaxis: {
                                type: "datetime",
                                axisBorder: {
                                    show: false
                                },
                                axisTicks: {
                                    show: false
                                },
                                labels: {
                                    datetimeUTC: false
                                }
                            },
                            yaxis: {
                                labels: {
                                    offsetX: -10,
                                    offsetY: 0
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            grid: {
                                padding: {
                                    left: -5,
                                    right: 5
                                }
                            },
                            tooltip: {
                                x: {
                                    format: "MMMM dd yyyy"
                                }
                            },
                            legend: {
                                position: "top",
                                horizontalAlign: "left"
                            },
                            fill: {
                                type: "gradient",
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.7,
                                    opacityTo: 0.9,
                                    stops: [0, 90, 100]
                                }
                            },
                        });

                    apex.render();
                } catch (e) {
                    parent.alert.danger(parent.lang_response_went_wrong);
                }
            });
        }
    },

    tables: (table = false, reload = false) => {
        if (reload) {
            if ($("[zender-table]").length && typeof zenderTable !== "undefined") {
                zenderTable.ajax.reload(null, false);
            }
        } else {
            $.post(site_url + "/table/" + table, {
                structure: true
            }, function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    window.tableSelected = [];

                    zender.clipboard(true);

                    if ($("[zender-trash]").length) {
                        $("[zender-trash]").attr("disabled", true);
                    }

                    if ($("[zender-reorder]").length) {
                        $("[zender-reorder]").attr("disabled", true);
                    }

                    if ($.fn.DataTable.isDataTable("[zender-table]")) {
                        $("[zender-table]").DataTable().destroy();
                    }

                    if (response.data.export) {
                        var tableDom = "l<\"dtrangesearch\">fBrtip";
                    } else {
                        var tableDom = "l<\"dtrangesearch\">frtip";
                    }

                    switch (response.status) {
                        case 200:
                            window.zenderTable = $("[zender-table]").DataTable({
                                processing: true,
                                serverSide: true,
                                ajax: {
                                    url: site_url + "/table/" + table,
                                    type: "POST",
                                    data: (d) => {
                                        if ($("[zender-datepicker]").length && response.data.history) {
                                            d.history_date = $("[zender-datepicker]").val();
                                            d.history_column = response.data.history.column;
                                        }
                                    }
                                },
                                responsive: true,
                                dom: tableDom,
                                searching: response.data.search.status,
                                lengthMenu: [
                                    parseInt(response.data.limit),
                                    parseInt(response.data.limit) + 10,
                                    parseInt(response.data.limit) + 25,
                                    parseInt(response.data.limit) + 50,
                                    parseInt(response.data.limit) + 75,
                                    parseInt(response.data.limit) + 100
                                ],
                                pageLength: parseInt(response.data.limit),
                                sPaginationType: "full_numbers",
                                oLanguage: {
                                    sSearch: "<i class=\"la la-search\"></i> " + response.data.search.text,
                                    sSearchPlaceholder: response.data.search.placeholder
                                },
                                aaSorting: [
                                    [0, "desc"]
                                ],
                                columns: response.data.columns,
                                language: {
                                    infoPostFix: "",
                                    processing: lang_datatable_processing,
                                    lengthMenu: lang_datatable_length,
                                    info: lang_datatable_info,
                                    infoEmpty: lang_datatable_empty,
                                    infoFiltered: lang_datatable_filtered,
                                    loadingRecords: lang_datatable_loading,
                                    zeroRecords: lang_datatable_zero,
                                    emptyTable: lang_datatable_null,
                                    paginate: {
                                        first: lang_datatable_first,
                                        previous: lang_datatable_prev,
                                        next: lang_datatable_next,
                                        last: lang_datatable_last
                                    },
                                    buttons: {
                                        copyTitle: lang_datatable_addedtoclipboard,
                                        copyKeys: lang_datatable_addedtoclipboardkeys,
                                        copySuccess: {
                                            _: lang_datatable_addedtoclipboardmulti + " %d",
                                            1: lang_datatable_addedtoclipboardsingle
                                        }
                                    },
                                    processing: `<div class="loadingio-spinner-magnify-tjzjco4pgun">
                                        <div class="ldio-kqy42eczvc9">
                                            <div>
                                                <div>
                                                    <div></div>
                                                    <div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    `
                                },
                                buttons: [{
                                    extend: "copy",
                                    className: "btn-sm btn-primary",
                                    text: "<i class=\"la la-copy\"></i> " + lang_js_tablesexport_copy,
                                    exportOptions: {
                                        columns: response.data.export.export_columns,
                                        format: {
                                            body: function(data, row, column, node) {
                                                return $(node).find("[export-data]").attr("export-data") ? $(node).find("[export-data]").attr("export-data").replace(/[\r\n]+/g, " ") : $(node).text().trim().replace(/[\r\n]+/g, " ");
                                            }
                                        }
                                    },
                                    title: response.data.export.copy_title
                                }, {
                                    extend: "excel",
                                    className: "btn-sm btn-primary",
                                    text: "<i class=\"la la-table\"></i> " + lang_js_tablesexport_excel,
                                    exportOptions: {
                                        columns: response.data.export.export_columns,
                                        format: {
                                            body: function(data, row, column, node) {
                                                return $(node).find("[export-data]").attr("export-data") ? $(node).find("[export-data]").attr("export-data") : $(node).text().trim();
                                            }
                                        }
                                    },
                                    title: response.data.export.excel_filename
                                }, {
                                    extend: "pdf",
                                    className: "btn-sm btn-primary",
                                    text: "<i class=\"la la-file\"></i> " + lang_js_tablesexport_pdf,
                                    exportOptions: {
                                        columns: response.data.export.export_columns,
                                        format: {
                                            body: function(data, row, column, node) {
                                                return $(node).find("[export-data]").attr("export-data") ? $(node).find("[export-data]").attr("export-data") : $(node).text().trim();
                                            }
                                        }
                                    },
                                    title: response.data.export.pdf_filename
                                }],
                                createdRow: (row, data, index) => {
                                    if (response.data.multiselect) {
                                        if (tableSelected.includes(data.id)) {
                                            $(row).removeClass("dtrow-selected");
                                            $(row).addClass("dtrow-selected");
                                        }
                                    }
                                },
                                preDrawCallback: (settings) => {
                                    tableLoading = true;
                                },
                                drawCallback: (settings) => {
                                    tableLoading = false;
                                    zender.tooltips();
                                    zender.clipboard();
                                }
                            });

                            if ($("[zender-table]").length) {
                                zenderTable.on("error.dt", () => {
                                    // Ignore
                                });

                                zenderTable.on("processing.dt", (e, settings, processing) => {
                                    if (processing) {
                                        $("table.dataTable").css("opacity", "0.5");
                                        $("div.dataTables_wrapper div.dataTables_info").css("opacity", "0.5");
                                        $("div.dataTables_wrapper div.dataTables_paginate").css("opacity", "0.5");
                                    } else {
                                        $("table.dataTable").css("opacity", "1");
                                        $("div.dataTables_wrapper div.dataTables_info").css("opacity", "1");
                                        $("div.dataTables_wrapper div.dataTables_paginate").css("opacity", "1");
                                    }
                                });
                            }

                            if ($("[zender-table]").length && response.data.multiselect) {
                                $("[zender-table] tbody").on("click", "tr", function(e) {
                                    var targetSelected = e.target.outerHTML.toString();

                                    if (targetSelected.startsWith("<a") || targetSelected.startsWith("<i") || targetSelected.startsWith("<button")) {
                                        return;
                                    }

                                    var rowId = $("[zender-table]").DataTable().row(this).data().id;

                                    if ($(this).hasClass("dtrow-selected")) {
                                        const tableSelectedIndex = tableSelected.indexOf(rowId);
                                        if (tableSelectedIndex > -1) {
                                            tableSelected.splice(tableSelectedIndex, 1);
                                        }
                                        $(this).removeClass("dtrow-selected");
                                    } else {
                                        tableSelected.push(rowId);
                                        $(this).addClass("dtrow-selected");
                                    }

                                    if (tableSelected.length < 1) {
                                        $("[zender-trash]").attr("disabled", "");
                                    } else {
                                        $("[zender-trash]").removeAttr("disabled", "");
                                    }

                                    if (tableSelected.length < 1) {
                                        $("[zender-reorder]").attr("disabled", "");
                                    } else {
                                        $("[zender-reorder]").removeAttr("disabled", "");
                                    }
                                });
                            }

                            if ($(".dtrangesearch").length && response.data.history) {
                                $(".dtrangesearch").html(`<input type="text" class="form-control form-control-sm" placeholder="Date range" zender-datepicker>`);
                                zender.datepicker();
                            }

                            if ($("[zender-datepicker]").length && response.data.history) {
                                $("[zender-datepicker]").on("input change paste keyup cut select", function() {
                                    $("[zender-table]").DataTable().draw();
                                });
                            }

                            break;
                        default:
                            alert.warning(response.message, true);
                    }
                } catch (e) {
                    alert.danger(lang_response_went_wrong);
                }
            });
        }
    },

    tabs: (initial = true, def = true) => {
        if (initial) {
            $(document).on("click", "[zender-tab]", function(e) {
                e.preventDefault();
                NProgress.start();;

                $.get(site_url + "/widget/tab/dashboard/" + ($(this).attr("zender-tab") ? $(this).attr("zender-tab") : false), (http) => {
                    try {
                        var tab = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                        switch (tab.status) {
                            case 200:
                                $("[zender-tab]").removeClass("active");
                                $("zender-tab-content").html(tab.data.tpl);
                                $("zender-tab-content").children(":first").addClass("animated fadeIn");
                                $(this).addClass("active");

                                if ($(this).attr("zender-tab-default") != "false") {
                                    if (tab.data.vars.table)
                                        zender.tables(tab.data.vars.table);
                                }

                                zender.ripple();
                                zender.select();
                                zender.iframe();
                                zender.gototab();
                                zender.tooltips();
                                break;
                            case 302:
                                alert.warning(lang_response_session_false, true);
                                break;
                            default:
                                alert.danger(tab.message);
                        }
                    } catch (e) {
                        alert.danger(lang_response_went_wrong);
                    }

                    NProgress.done();;
                });
            });
        }

        if (def) {
            $("[zender-tab-default]").each(function(i) {
                if (i < 1) {
                    $.get(site_url + "/widget/tab/dashboard/" + ($(this).attr("zender-tab") ? $(this).attr("zender-tab") : false), (http) => {
                        try {
                            var tab = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                            switch (tab.status) {
                                case 200:
                                    $("zender-tab-content").html(tab.data.tpl);
                                    $("zender-tab-content").children(":first").addClass("animated fadeIn");

                                    if ($(this).attr("zender-tab-default") != "false") {
                                        if (tab.data.vars.table)
                                            zender.tables(tab.data.vars.table);
                                    }

                                    zender.select();
                                    zender.ripple();
                                    zender.iframe();
                                    zender.gototab();
                                    zender.tooltips();
                                    break;
                                case 302:
                                    alert.warning(lang_response_session_false, true);
                                    break;
                                default:
                                    alert.danger(tab.message);
                            }
                        } catch (e) {
                            alert.danger(lang_response_went_wrong);
                        }
                    });
                }
            });
        }
    },

    modals: () => {
        $("body").append(
            `<zender-modal class="modal fade" data-backdrop="static">
                <div class="modal-dialog" role="document">
                    <zender-modal-content></zender-modal-content>
                </div>
            </zender-modal>`
        ).ready(() => {
            $(document).on("click", "[zender-toggle]", function(e) {
                e.preventDefault();
                NProgress.start();;

                var loaderModal = $(this).attr("zender-loader");

                if (loaderModal)
                    zender.loader(loaderModal);

                if ($("[zender-reply]").length) {
                    var replyNumber = $(this).attr("zender-reply");
                }

                $.get(site_url + "/widget/modal/" + template + "/" + ($(this).attr("zender-toggle") ? $(this).attr("zender-toggle") : false) + "?_=" + new Date().getTime(), (http) => {
                    try {
                        var modal = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                        switch (modal.status) {
                            case 200:
                                if (loaderModal)
                                    zender.loader(false, false);

                                $("zender-modal").removeClass("left right");
                                $("zender-modal .modal-dialog").removeClass("modal-dialog-centered modal-sm modal-md modal-lg modal-xl");

                                if (modal.data.vars.position) {
                                    $("zender-modal").addClass(modal.data.vars.position);
                                } else {
                                    $("zender-modal .modal-dialog").addClass("modal-dialog-centered");
                                }

                                if (modal.data.vars.size) {
                                    $("zender-modal .modal-dialog").addClass("modal-" + modal.data.vars.size);
                                } else {
                                    $("zender-modal .modal-dialog").addClass("modal-sm");
                                }

                                $("zender-modal-content").html(modal.data.tpl);

                                if ($("[zender-reply]").length) {
                                    $("input[name=phone]").val(replyNumber);
                                }

                                $("zender-modal").modal({
                                    keyboard: false
                                });

                                if (modal.data.vars.iframe)
                                    zender.iframe();

                                zender.whatsapp();
                                zender.download();
                                zender.duration();
                                zender.ripple();
                                zender.tooltips();
                                zender.select();

                                if (modal.data.vars.type) {
                                    zender.autocomplete();
                                    zender.datepicker();
                                    zender.jsoneditor();
                                    zender.codeflask();
                                    zender.counter();

                                    $("[zender-form]").on("submit", function(e) {
                                        e.preventDefault();

                                        var data = new FormData(this);

                                        if (modal.data.vars.id)
                                            data.append("id", modal.data.vars.id);

                                        if (window.codeflask) {
                                            data.append("content", codeflask.getCode());
                                        }

                                        if (window.typeMode) {
                                            if (typeMode[0].value == "list" || typeMode[0].value == "template") {
                                                try {
                                                    if (typeMode[0].value == "list") {
                                                        data.append("list_format", JSON.stringify(jsonEditorList.get()));
                                                    } else {
                                                        data.append("template_format", JSON.stringify(jsonEditorTemplate.get()));
                                                    }
                                                } catch (e) {
                                                    alert.danger("List format is invalid!");

                                                    return false;
                                                }
                                            }
                                        }

                                        if (modal.data.vars.stripe) {
                                            zender.stripe(data);
                                        } else {
                                            $.ajax({
                                                url: site_url + "/requests/" + modal.data.vars.type + "/" + modal.data.vars.tpl,
                                                type: "POST",
                                                data: data,
                                                contentType: false,
                                                processData: false,
                                                beforeSend: () => {
                                                    if (modal.data.vars.require) {
                                                        var filter = modal.data.vars.require.split("<=>");
                                                        for (var i = 0; i <= filter.length; i++) {
                                                            if (typeof filter[i] !== "undefined") {
                                                                var values = filter[i].split("|");
                                                            }
                                                            try {
                                                                if (data.get(values[0]).length < 1) {
                                                                    alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                                                    return false;
                                                                }
                                                            } catch (e) {
                                                                if (data.getAll(values[0] + "[]").length < 1) {
                                                                    alert.warning(values[1] + ", " + lang_validate_cannotemp);
                                                                    return false;
                                                                }
                                                            }
                                                        }
                                                    }

                                                    zender.disabled();

                                                    if (modal.data.vars.loader)
                                                        zender.loader(modal.data.vars.loader);
                                                },
                                                success: (http) => {
                                                    if (modal.data.vars.loader) {
                                                        setTimeout(() => {
                                                            zender.loader(false, false);

                                                            try {
                                                                var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                                                                switch (response.status) {
                                                                    case 200:
                                                                        if (modal.data.vars.table)
                                                                            zender.tables(false, true);

                                                                        $("zender-modal").modal("hide");
                                                                        alert.success(response.message);
                                                                        break;
                                                                    case 301:
                                                                        $("zender-modal").modal("hide");
                                                                        alert.success(response.message, true);
                                                                        break;
                                                                    case 302:
                                                                        $("zender-modal").modal("hide");
                                                                        alert.warning(lang_response_session_false, true);
                                                                        break;
                                                                    case 303:
                                                                        $("zender-modal").modal("hide");
                                                                        alert.warning(response.message);

                                                                        setTimeout(() => {
                                                                            zender.redirect(response.data);
                                                                        }, 3000);
                                                                        break;
                                                                    default:
                                                                        if (modal.data.vars.recaptcha)
                                                                            grecaptcha.reset();

                                                                        alert.danger(response.message);
                                                                }
                                                            } catch (e) {
                                                                alert.danger(lang_response_went_wrong);
                                                            }
                                                        }, 1000);
                                                    }

                                                    zender.disabled(false);
                                                }
                                            });
                                        }
                                    });
                                }

                                break;
                            case 302:
                                alert.warning(lang_response_session_false, true);
                                break;
                            default:
                                alert.danger(modal.message);
                        }
                    } catch (e) {
                        alert.danger(lang_response_went_wrong);
                    }

                    NProgress.done();
                });
            });
        });
    }
}

window.alert = {
    setup: () => {
        iziToast.settings({
            title: lang_alert_attention,
            titleSize: "18px",
            titleLineHeight: "25px",
            messageSize: "17px",
            messageLineHeight: "20px",
            icon: false,
            timeout: 3000,
            animateInside: true,
            titleColor: "#f5f5f5",
            messageColor: "#f5f5f5",
            iconColor: "#f5f5f5",
            transitionIn: "fadeInRight",
            transitionOut: "fadeOutRight",
            position: alert_position,
            displayMode: "replace",
            layout: 2,
            maxWidth: "300px",
            close: false
        });
    },

    primary: (message, redirect = false, overlap = false, title = false, notify = false, image = false) => {
        if (overlap) {
            iziToast.info({
                title: !title ? false : title,
                backgroundColor: color_primary,
                displayMode: 0,
                image: !image || image == "0" ? false : image,
                imageWidth: 85,
                timeout: !notify ? 3000 : 8000,
                close: notify ? true : false,
                maxWidth: notify ? "600px" : "300px",
                message: message,
                position: overlap_alert_position,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        } else {
            iziToast.info({
                backgroundColor: color_primary,
                message: message,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        }
    },

    success: (message, redirect = false, overlap = false, title = false, notify = false, image = false) => {
        if (overlap) {
            iziToast.success({
                title: !title ? false : title,
                backgroundColor: "#00c52c",
                displayMode: 0,
                image: !image || image == "0" ? false : image,
                imageWidth: 85,
                timeout: !notify ? 3000 : false,
                close: notify ? true : false,
                maxWidth: notify ? "600px" : "300px",
                message: message,
                position: overlap_alert_position,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        } else {
            iziToast.success({
                backgroundColor: "#00c52c",
                message: message,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        }
    },

    warning: (message, redirect = false, overlap = false, title = false, notify = false, image = false) => {
        if (overlap) {
            iziToast.warning({
                title: !title ? false : title,
                backgroundColor: "#FE9431",
                displayMode: 0,
                image: !image || image == "0" ? false : image,
                imageWidth: 85,
                timeout: !notify ? 3000 : false,
                close: notify ? true : false,
                maxWidth: notify ? "600px" : "300px",
                message: message,
                position: overlap_alert_position,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        } else {
            iziToast.warning({
                backgroundColor: "#FE9431",
                message: message,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        }
    },

    danger: (message, redirect = false, overlap = false, title = false, notify = false, image = false) => {
        if (overlap) {
            iziToast.error({
                title: !title ? false : title,
                backgroundColor: "#E82753",
                displayMode: 0,
                image: !image || image == "0" ? false : image,
                imageWidth: 85,
                timeout: !notify ? 3000 : false,
                close: notify ? true : false,
                maxWidth: notify ? "600px" : "300px",
                message: message,
                position: overlap_alert_position,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        } else {
            iziToast.error({
                backgroundColor: "#E82753",
                message: message,
                onClosed: () => {
                    if (redirect)
                        location.reload();
                }
            });
        }
    }
}

window.titansys = {
    support: () => {
        $(document).on("click", "[zender-support]", function(e) {
            $.get(site_url + "/requests/index/support", function(http) {
                try {
                    var response = (typeof http === "string") ? JSON.parse(http) : JSON.parse(JSON.stringify(http));

                    switch (response.status) {
                        case 200:
                            setTimeout(() => {
                                zender.redirect(response.data);
                            }, 1500);
                            break;
                        default:
                            alert.danger(response.message);
                    }
                } catch (e) {
                    alert.danger(lang_response_went_wrong);
                }
            });
        });
    }
}