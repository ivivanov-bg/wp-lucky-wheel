"use strict";
jQuery(document).ready(function ($) {
    let font_size = _wplwl_get_email_params.font_size;
    let wheel_size = _wplwl_get_email_params.wheel_size;
    let custom_field_name_enable = _wplwl_get_email_params.custom_field_name_enable;
    let custom_field_name_required = _wplwl_get_email_params.custom_field_name_required;
    let wplwl_hide_popup = _wplwl_get_email_params.hide_popup;
    let color = _wplwl_get_email_params.bg_color;
    let slices_text_color = _wplwl_get_email_params.slices_text_color;
    let label = _wplwl_get_email_params.label;
    let piece_coupons = _wplwl_get_email_params.prize_type;
    let wplwl_auto_close = parseInt(_wplwl_get_email_params.auto_close);
    let wplwl_show_again = _wplwl_get_email_params.show_again;
    let wplwl_show_again_unit = _wplwl_get_email_params.show_again_unit;
    let time_if_close = _wplwl_get_email_params.time_if_close;
    switch (wplwl_show_again_unit) {
        case 'm':
            wplwl_show_again *= 60;
            break;
        case 'h':
            wplwl_show_again *= 60 * 60;
            break;
        case 'd':
            wplwl_show_again *= 60 * 60 * 24;
            break;
        default:
    }
    let intent_type = _wplwl_get_email_params.intent;
    let initial_time = _wplwl_get_email_params.show_wheel;
    let wplwl_center_color = _wplwl_get_email_params.wheel_center_color;
    let wplwl_border_color = _wplwl_get_email_params.wheel_border_color;
    let wplwl_dot_color = _wplwl_get_email_params.wheel_dot_color;
    let gdpr_checkbox = _wplwl_get_email_params.gdpr;
    let wplwl_spinning_time = _wplwl_get_email_params.spinning_time;
    let wheel_speed = _wplwl_get_email_params.wheel_speed;
    let slices = piece_coupons.length;
    let sliceDeg = 360 / slices;
    let deg = -(sliceDeg / 2);
    let cv = document.getElementById('wplwl_canvas');
    let ctx = cv.getContext('2d');

    let canvas_width;
    let wd_width, wd_height;
    wd_width = window.innerWidth;
    wd_height = window.innerHeight;
    if (wd_width > wd_height) {
        canvas_width = wd_height;
    } else {
        canvas_width = wd_width;
    }
    let width = parseInt(wheel_size * (canvas_width * 0.55 + 16) / 100);// size
    cv.width = width;
    cv.height = width;
    if (window.devicePixelRatio) {
        let hidefCanvasWidth = $(cv).attr('width');
        let hidefCanvasHeight = $(cv).attr('height');
        let hidefCanvasCssWidth = hidefCanvasWidth;
        let hidefCanvasCssHeight = hidefCanvasHeight;

        $(cv).attr('width', hidefCanvasWidth * window.devicePixelRatio);
        $(cv).attr('height', hidefCanvasHeight * window.devicePixelRatio);
        $(cv).css('width', hidefCanvasCssWidth);
        $(cv).css('height', hidefCanvasCssHeight);
        ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    }

    let center = (width) / 2; // center
    $('.wplwl_wheel_spin').css({'width': width + 'px', 'height': width + 'px'});
    if ('on' === _wplwl_get_email_params.show_full_wheel) {
        $('.wplwl_lucky_wheel_content').css({'max-width': (width + 600) + 'px'});
    } else {
        $('.wplwl_lucky_wheel_content').css({'max-width': (0.6 * width + 600) + 'px'});
    }
    let inline_css = '.wplwl_lucky_wheel_content:not(.wplwl_lucky_wheel_content_mobile) .wheel-content-wrapper .wheel_content_left{min-width:' + (width + 35) + 'px}';

        inline_css += '.wplwl_pointer:before{font-size:' + parseInt(width / 4) + 'px !important; }';

    $('head').append('<style id="123123" type="text/css">' + inline_css + '</style>');
    inline_css = $('#wp-lucky-wheel-frontend-style-inline-css').html();
    $('#wp-lucky-wheel-frontend-style-inline-css').html(inline_css);
    let wheel_text_size;
    wheel_text_size = parseInt(width / 28) * parseInt(font_size) / 100;

    function deg2rad(deg) {
        return deg * Math.PI / 180;
    }

    function drawSlice(deg, color) {
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.moveTo(center, center);
        let r;
        if (width <= 480) {
            r = width / 2 - 10;
        } else {
            r = width / 2 - 14;
        }
        ctx.arc(center, center, r, deg2rad(deg), deg2rad(deg + sliceDeg));
        ctx.lineTo(center, center);
        ctx.fill();
    }

    function drawPoint(deg, color) {
        ctx.save();
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.shadowBlur = 1;
        ctx.shadowOffsetX = 8;
        ctx.shadowOffsetY = 8;
        ctx.shadowColor = 'rgba(0,0,0,0.2)';
        ctx.arc(center, center, width / 8, 0, 2 * Math.PI);
        ctx.fill();

        ctx.clip();
        ctx.restore();
    }

    function drawBorder(borderC, dotC, lineW, dotR, des, shadColor) {
        ctx.beginPath();
        ctx.strokeStyle = borderC;
        ctx.lineWidth = lineW;
        ctx.shadowBlur = 1;
        ctx.shadowOffsetX = 8;
        ctx.shadowOffsetY = 8;
        ctx.shadowColor = shadColor;
        ctx.arc(center, center, center, 0, 2 * Math.PI);
        ctx.stroke();
        let x_val, y_val, deg;
        deg = sliceDeg / 2;
        let center1 = center - des;
        for (let i = 0; i < slices; i++) {
            ctx.beginPath();
            ctx.fillStyle = dotC;
            x_val = center + center1 * Math.cos(deg * Math.PI / 180);
            y_val = center - center1 * Math.sin(deg * Math.PI / 180);
            ctx.arc(x_val, y_val, dotR, 0, 2 * Math.PI);
            ctx.fill();
            deg += sliceDeg;
        }
    }

    function drawText(deg, text, color) {
        ctx.save();
        ctx.translate(center, center);
        ctx.rotate(deg2rad(deg));
        ctx.textAlign = "right";
        ctx.fillStyle = color;
        ctx.font = '200 ' + wheel_text_size + 'px Helvetica';
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 0;
        text = text.replace(/&#(\d{1,4});/g, function (fullStr, code) {
            return String.fromCharCode(code);
        });
        text = text.replace(/&nbsp;/g, ' ');
        let reText = text.split('\/n'), text1 = '', text2 = '';
        if (reText.length > 1) {
            text1 = reText[0];
            text2 = reText.splice(1, reText.length - 1);
            text2 = text2.join('');
        }
        if (text1.trim() !== "" && text2.trim() !== "") {
            ctx.fillText(text1.trim(), 7 * center / 8, -(wheel_text_size * 1 / 4));
            ctx.fillText(text2.trim(), 7 * center / 8, wheel_text_size * 3 / 4);
        } else {
            ctx.fillText(text, 7 * center / 8, wheel_text_size / 2 - 2);
        }
        ctx.restore();
    }
//cookie
    function setCookie(cname, cvalue, expire) {
        let d = new Date();
        d.setTime(d.getTime() + (expire * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function overlay_function() {
        $('.wplwl-overlay').on('click', function () {
            $('html').removeClass('wplwl-html');
            $(this).hide();
            $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
            setCookie('wplwl_cookie', 'closed', time_if_close);
            if (wplwl_hide_popup != 'on') {
                $('.wplwl_wheel_icon').addClass('wplwl_show');
            }
        });
    }

    function spins_wheel(stop_position, result_notification, result) {
        let canvas_1 = $('#wplwl_canvas');
        let canvas_3 = $('#wplwl_canvas2');
        let default_css = '';
        if (window.devicePixelRatio) {
            default_css = 'width:' + width + 'px;height:' + width + 'px;';
        }
        canvas_1.attr('style', default_css);
        canvas_3.attr('style', default_css);
        let stop_deg = 360 - sliceDeg * stop_position;
        let wheel_stop = wheel_speed * 360 * wplwl_spinning_time + stop_deg;
        let css = default_css + '-moz-transform: rotate(' + wheel_stop + 'deg);-webkit-transform: rotate(' + wheel_stop + 'deg);-o-transform: rotate(' + wheel_stop + 'deg);-ms-transform: rotate(' + wheel_stop + 'deg);transform: rotate(' + wheel_stop + 'deg);';
        css += '-webkit-transition: transform ' + wplwl_spinning_time + 's ease-out;-moz-transition: transform ' + wplwl_spinning_time + 's ease-out;-ms-transition: transform ' + wplwl_spinning_time + 's ease-out;-o-transition: transform ' + wplwl_spinning_time + 's ease-out;transition: transform ' + wplwl_spinning_time + 's ease-out;';
        canvas_1.attr('style', css);
        canvas_3.attr('style', css);
        setTimeout(function () {
            css = default_css + 'transform: rotate(' + stop_deg + 'deg);';
            canvas_1.attr('style', css);
            canvas_3.attr('style', css);

            $('.wplwl_lucky_wheel_content').addClass('wplwl-finish-spinning');
            $('.wplwl-overlay').unbind();
            $('.wplwl-overlay').on('click', function () {
                $('html').removeClass('wplwl-html');
                $(this).hide();

                $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
                $('.wplwl_wheel_spin').css({'margin-left': '0', 'transition': '2s'});
            });
            $('.wplwl_user_lucky').html('<div class="wplwl-frontend-result">' + result_notification + '</div>');
            $('.wplwl_user_lucky').fadeIn(300);
            if (wplwl_auto_close > 0) {
                setTimeout(function () {
                    $('.wplwl-overlay').hide();
                    $('html').removeClass('wplwl-html');
                    $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
                    $('.wplwl_wheel_spin').css({'margin-left': '0', 'transition': '2s'});
                }, wplwl_auto_close * 1000);
            }
        }, parseInt(wplwl_spinning_time * 1000))
    }

    function isValidEmailAddress(emailAddress) {
        let pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
        return pattern.test(emailAddress);
    }

    function check_email() {
        $(document).on('keypress', function (e) {
            if ($('.wplwl_lucky_wheel_content').hasClass('lucky_wheel_content_show') && e.keyCode === 13) {
                $('#wplwl_chek_mail').click();
            }
        });
        $('#wplwl_chek_mail').on('click', function () {
            $('#wplwl_error_mail').html('');
            $('#wplwl_error_name').html('');
            $('.wplwl_field_name').removeClass('wplwl-required-field');
            $('.wplwl_field_email').removeClass('wplwl-required-field');
            if ('on' === gdpr_checkbox && !$('.wplwl-gdpr-checkbox-wrap input[type="checkbox"]').prop('checked')) {
                alert(_wplwl_get_email_params.gdpr_warning);
                return false;
            }
            let wplwl_email = $('#wplwl_player_mail').val();
            let wplwl_name = $('#wplwl_player_name').val();
            let qualified = true;
            if (custom_field_name_enable == 'on' && custom_field_name_required == 'on' && !wplwl_name) {
                $('#wplwl_error_name').html(_wplwl_get_email_params.custom_field_name_message);
                $('.wplwl_field_name').addClass('wplwl-required-field');
                qualified = false;
            }
            if (!wplwl_email) {
                $('#wplwl_player_mail').prop('disabled', false).focus();
                $('#wplwl_error_mail').html(_wplwl_get_email_params.empty_email_warning);
                $('.wplwl_field_email').addClass('wplwl-required-field');
                qualified = false;
            }
            if (qualified == false) {
                return false;
            }
            $(this).unbind();
            $('.wplwl-overlay').unbind();
            $('#wplwl_player_mail').prop('disabled', true);
            if (getCookie('wplwl_cookie') === "" || getCookie('wplwl_cookie') === 'closed') {
                if (isValidEmailAddress($('#wplwl_player_mail').val())) {
                    $('#wplwl_error_mail').html('');
                    $('#wplwl_chek_mail').addClass('wplwl-adding');

                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: _wplwl_get_email_params.ajaxurl,
                        data: {
                            user_email: wplwl_email,
                            user_name: wplwl_name,
                            language: _wplwl_get_email_params.language,
                            _wordpress_lucky_wheel_nonce: $('#_wordpress_lucky_wheel_nonce').val(),
                        },
                        success: function (response) {
                            if (response.allow_spin === 'yes') {
                                $('.wplwl-show-again-option').hide();
                                $('.wplwl-close-wheel').hide();
                                $('.wplwl-hide-after-spin').show();
                                spins_wheel(response.stop_position, response.result_notification, response.result);

                                setCookie('wplwl_cookie', wplwl_email, wplwl_show_again);
                            } else {
                                $('#wplwl_chek_mail').removeClass('wplwl-adding');
                                $('#wplwl_player_mail').prop('disabled', false);
                                check_email();
                                overlay_function();
                                alert(response.allow_spin);
                            }
                        }
                    });

                } else {
                    $('#wplwl_player_mail').prop('disabled', false).focus();
                    check_email();
                    overlay_function();
                    $('#wplwl_error_mail').html(_wplwl_get_email_params.invalid_email_warning);
                    $('.wplwl_field_email').addClass('wplwl-required-field');
                }

            } else {
                alert(_wplwl_get_email_params.limit_time_warning);
                $('#wplwl_player_mail').prop('disabled', false);
                check_email();
                overlay_function();
            }
        });
    }
    overlay_function();
    check_email();
    let center1 = 32;

    if (!getCookie('wplwl_cookie') || getCookie('wplwl_cookie') == "") {
        $('.wplwl-hide-after-spin').bind('click', function () {
            $('.wplwl-overlay').hide();
            $('html').removeClass('wplwl-html');
            $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
            $('.wplwl_wheel_spin').css({'margin-left': '0', 'transition': '2s'});
            // setTimeout(function () {
            //     $('.wplwl_lucky_wheel_content').hide();
            // }, 2000);
        });

        $('.wplwl-reminder-later-a').unbind();
        $('.wplwl-reminder-later-a').bind('click', function () {
            setCookie('wplwl_cookie', 'reminder_later', 24 * 60 * 60);

            $('.wplwl_wheel_icon').addClass('wplwl_show');

            $('.wplwl-overlay').hide();
            $('html').removeClass('wplwl-html');
            $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
        });
        $('.wplwl-never-again span').unbind();
        $('.wplwl-never-again span').bind('click', function () {
            setCookie('wplwl_cookie', 'never_show_again', 30 * 24 * 60 * 60);

            $('.wplwl_wheel_icon').addClass('wplwl_show');

            $('.wplwl-overlay').hide();
            $('html').removeClass('wplwl-html');
            $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
        });
        $('.wplwl-close span').on('click', function () {
            $('.wplwl-overlay').hide();
            setCookie('wplwl_cookie', 'closed', time_if_close);
            $('html').removeClass('wplwl-html');
            $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
            if (wplwl_hide_popup != 'on') {
                $('.wplwl_wheel_icon').addClass('wplwl_show');
            }
        });
        $('.wplwl-close-wheel span').on('click', function () {
            $('.wplwl-overlay').hide();
            $('html').removeClass('wplwl-html');
            $('.wplwl_lucky_wheel_content').removeClass('lucky_wheel_content_show');
            setCookie('wplwl_cookie', 'closed', time_if_close);
            if (wplwl_hide_popup != 'on') {
                $('.wplwl_wheel_icon').addClass('wplwl_show');
            }
        });

        $('.wp-lucky-wheel-popup-icon').on('click', function () {
            $('.wplwl_wheel_icon').removeClass('wplwl_show');
            $('.wplwl-overlay').show();
            $('html').addClass('wplwl-html');
            $('.wplwl_lucky_wheel_content').addClass('lucky_wheel_content_show');
        });

        for (let i = 0; i < slices; i++) {
            drawSlice(deg, color[i]);
            drawText(deg + sliceDeg / 2, label[i], slices_text_color[i]);
            deg += sliceDeg;

        }
        cv = document.getElementById('wplwl_canvas1');
        ctx = cv.getContext('2d');
        cv.width = width;
        cv.height = width;
        if (window.devicePixelRatio) {
            let hidefCanvasWidth = $(cv).attr('width');
            let hidefCanvasHeight = $(cv).attr('height');
            let hidefCanvasCssWidth = hidefCanvasWidth;
            let hidefCanvasCssHeight = hidefCanvasHeight;

            $(cv).attr('width', hidefCanvasWidth * window.devicePixelRatio);
            $(cv).attr('height', hidefCanvasHeight * window.devicePixelRatio);
            $(cv).css('width', hidefCanvasCssWidth);
            $(cv).css('height', hidefCanvasCssHeight);
            ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
        }
        drawPoint(deg, wplwl_center_color);
        if (width <= 480) {
            drawBorder(wplwl_border_color, 'rgba(0,0,0,0)', 20, 4, 5, 'rgba(0,0,0,0.2)');

        } else {
            drawBorder(wplwl_border_color, 'rgba(0,0,0,0)', 30, 6, 7, 'rgba(0,0,0,0.2)');
        }

        cv = document.getElementById('wplwl_canvas2');
        ctx = cv.getContext('2d');

        cv.width = width;
        cv.height = width;
        if (window.devicePixelRatio) {
            let hidefCanvasWidth = $(cv).attr('width');
            let hidefCanvasHeight = $(cv).attr('height');
            let hidefCanvasCssWidth = hidefCanvasWidth;
            let hidefCanvasCssHeight = hidefCanvasHeight;

            $(cv).attr('width', hidefCanvasWidth * window.devicePixelRatio);
            $(cv).attr('height', hidefCanvasHeight * window.devicePixelRatio);
            $(cv).css('width', hidefCanvasCssWidth);
            $(cv).css('height', hidefCanvasCssHeight);
            ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
        }
        if (width <= 480) {
            drawBorder('rgba(0,0,0,0)', wplwl_dot_color, 20, 4, 5, 'rgba(0,0,0,0)');

        } else {
            drawBorder('rgba(0,0,0,0)', wplwl_dot_color, 30, 6, 7, 'rgba(0,0,0,0)');
        }

        if (intent_type === 'popup_icon') {
            let notify_time_out = setTimeout(function () {
                $('.wplwl_wheel_icon').addClass('wplwl_show');

            }, initial_time * 1000);
        } else if (intent_type === 'show_wheel') {
            setTimeout(function () {
                $('.wplwl-overlay').show();
                $('html').addClass('wplwl-html');
                $('.wplwl_lucky_wheel_content').addClass('lucky_wheel_content_show');
            }, initial_time * 1000);
        }
    }

    function drawPopupIcon() {
        cv = document.getElementById('wplwl_popup_canvas');
        if(cv){
            ctx = cv.getContext('2d');

            for (let k = 0; k < slices; k++) {
                drawSlice1(deg, color[k]);
                deg += sliceDeg;
            }
            drawPoint1(wplwl_center_color);
            drawBorder1(wplwl_border_color, wplwl_dot_color, 4, 1, 0);
        }
    }

    drawPopupIcon();

    function drawSlice1(deg, color) {
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.moveTo(center1, center1);
        ctx.arc(center1, center1, 32, deg2rad(deg), deg2rad(deg + sliceDeg));
        ctx.lineTo(center1, center1);
        ctx.fill();
    }

    function drawPoint1(color) {
        ctx.save();
        ctx.beginPath();
        ctx.fillStyle = color;
        ctx.arc(center1, center1, 8, 0, 2 * Math.PI);
        ctx.fill();
        ctx.restore();
    }

    function drawBorder1(borderC, dotC, lineW, dotR, des) {
        ctx.beginPath();
        ctx.strokeStyle = borderC;
        ctx.lineWidth = lineW;
        ctx.arc(center1, center1, center1, 0, 2 * Math.PI);
        ctx.stroke();
        let x_val, y_val, deg;
        deg = sliceDeg / 2;
        let center2 = center1 - des;
        for (let i = 0; i < slices; i++) {
            ctx.beginPath();
            ctx.fillStyle = dotC;
            x_val = center1 + center2 * Math.cos(deg * Math.PI / 180);
            y_val = center1 - center2 * Math.sin(deg * Math.PI / 180);
            ctx.arc(x_val, y_val, dotR, 0, 2 * Math.PI);
            ctx.fill();
            deg += sliceDeg;
        }
    }

});
