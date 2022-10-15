/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * @author ShevAbam <me@shevarezo.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */

document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector('#mobile_menu')) {
        document.querySelector('#mobile_menu').addEventListener("click", function () {
            var navigation = document.querySelector('#header #navigation');
            toggleSlide(navigation);
        });
    }

    var pathname = window.location.href.split('#')[0];
    document.querySelectorAll('a[href^="#"]').forEach(function (item) {
        var link = item.getAttribute('href');
        item.setAttribute('href', pathname + link);
    });


    document.querySelectorAll('.msg').forEach(function (item, index) {
        item.querySelector('.msg-button-close').addEventListener('click', function () {
            fadeOut(item);
        });

        setTimeout(function () {
            fadeOut(item);
        }, 5000 + index * 5000);

    });

});

function toggleSlide(el) {
    if (el.clientHeight > 0) {
        el.classList.add('active');
    } else {
        el.classList.remove('active');
    }
    if (!el.classList.contains('active')) {
        // slideDown
        el.classList.add('active');
        el.style.height = "auto";
        var height = el.clientHeight + "px";
        el.style.height = "0px";
        setTimeout(function () {
            el.style.height = height;
        }, 0);
    } else {
        // slideUp
        el.style.height = "0px";
        el.addEventListener('transitionend', function () {
            el.classList.remove('active');
        }, {once: true});
    }
}

function fadeOut(el) {
    el.style.opacity = 1;
    (function fade() {
        if ((el.style.opacity -= .03) < 0) {
            el.style.display = "none";
        } else {
            requestAnimationFrame(fade);
        }
    })();
}
;
function fadeIn(el, display) {
    el.style.opacity = 0;
    el.style.display = display || "block";
    (function fade() {
        var val = parseFloat(el.style.opacity);
        if (!((val += .03) > 1)) {
            el.style.opacity = val;
            requestAnimationFrame(fade);
        }
    })();
};
