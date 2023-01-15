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
            navigation.slideToggle();
        });
    }

    var pathname = window.location.href.split('#')[0];
    document.querySelectorAll('a[href^="#"]').forEach(function (item) {
        var link = item.getAttribute('href');
        item.setAttribute('href', pathname + link);
    });
});
