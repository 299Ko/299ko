/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */


function resizeListener() {
    if (window.innerWidth > 768) {
        document.querySelector('#content').classList.add('withSidebar');
    } else {
        document.querySelector('#content').classList.remove('withSidebar');
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
}

/* plain JS slideToggle https://github.com/ericbutler555/plain-js-slidetoggle */
function _s(o, i, p, l) {
    void 0 === i && (i = 400), void 0 === l && (l = !1), o.style.overflow = "hidden", l && (o.style.display = "block");
    var n, t = window.getComputedStyle(o), s = parseFloat(t.getPropertyValue("height")), a = parseFloat(t.getPropertyValue("padding-top")), r = parseFloat(t.getPropertyValue("padding-bottom")), y = parseFloat(t.getPropertyValue("margin-top")), d = parseFloat(t.getPropertyValue("margin-bottom")), g = s / i, m = a / i, h = r / i, u = y / i, x = d / i;
    window.requestAnimationFrame(function t(e) {
        void 0 === n && (n = e);
        e -= n;
        l ? (o.style.height = g * e + "px", o.style.paddingTop = m * e + "px", o.style.paddingBottom = h * e + "px", o.style.marginTop = u * e + "px", o.style.marginBottom = x * e + "px") : (o.style.height = s - g * e + "px", o.style.paddingTop = a - m * e + "px", o.style.paddingBottom = r - h * e + "px", o.style.marginTop = y - u * e + "px", o.style.marginBottom = d - x * e + "px"), i <= e ? (o.style.height = "", o.style.paddingTop = "", o.style.paddingBottom = "", o.style.marginTop = "", o.style.marginBottom = "", o.style.overflow = "", l || (o.style.display = "none"), "function" == typeof p && p()) : window.requestAnimationFrame(t)
    })
}
HTMLElement.prototype.slideToggle = function (t, e) {
    0 === this.clientHeight ? _s(this, t, e, !0) : _s(this, t, e)
}, HTMLElement.prototype.slideUp = function (t, e) {
    _s(this, t, e)
}, HTMLElement.prototype.slideDown = function (t, e) {
    _s(this, t, e, !0)
};

var getNextSibling = function (elem, selector) {
    // Get the next sibling element
    var sibling = elem.nextElementSibling;
    // If there's no selector, return the first sibling
    if (!selector)
        return sibling;
    // If the sibling matches our selector, use it
    // If not, jump to the next sibling and continue the loop
    while (sibling) {
        if (sibling.matches(selector))
            return sibling;
        sibling = sibling.nextElementSibling
    }
};

document.addEventListener("DOMContentLoaded", function () {

    // For sidebar
    if (document.querySelector('#adminSidebar')) {
        window.addEventListener("resize", resizeListener);
        resizeListener();
        if (document.querySelector('#adminSidebar').closest('form')) {
            var par = document.querySelector('#adminSidebar').closest('form');
            document.querySelector('#adminSidebar').append(par.querySelector('[type="submit"]'));
        }
    }

    document.querySelectorAll('.msg').forEach(function (item, index) {
        item.querySelector('.msg-button-close').addEventListener('click', function () {
            fadeOut(item);
        });
        setTimeout(function () {
            fadeOut(item);
        }, 5000 + index * 5000);
    });
    // Login : btn Quitter redirection
    if (document.querySelector('#login input.alert')) {
        document.querySelector('#login input.alert').addEventListener('click', function () {
            document.location.href = this.getAttribute('rel');
        });
    }

    // nav
    if (document.querySelector('#open_nav')) {
        document.querySelector('#open_nav').addEventListener("click", function () {

            var sidebar = document.querySelector('#sidebar');
            if (sidebar.style.display == 'none' || sidebar.style.display == '') {
                fadeIn(sidebar, 'block');
            } else {
                fadeOut(sidebar);
            }

        });
    }

    document.querySelectorAll(".categories-list li i").forEach(function (item) {
        item.addEventListener('click', function () {
            item.classList.toggle('fa-rotate-180');
            getNextSibling(item, 'ul.categories-list-sub').slideToggle(400);
        });
    });

    document.querySelectorAll("div.categorie-list i.categories-toggle").forEach(function (item) {
        item.addEventListener('click', function () {
            item.classList.toggle('fa-rotate-180');
            getNextSibling(item.parentNode, 'div.toggle').slideToggle(400);
        });
    });

    document.querySelectorAll('.btn-add-categorie').forEach(function (item) {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            var $form = document.querySelector('#categorie-add-form-container');
            var parent_id = item.getAttribute('data-id');
            var $categorie = document.querySelector('#categorie-' + parent_id);
            $form.querySelector('h4').textContent = 'Ajouter une catégorie enfant à ' + $categorie.getAttribute('name');
            document.querySelector('#categorie-parentId').value = parent_id;
            $categorie.after($form);
            var $aRem = document.querySelector("#category-child-delete");
            $aRem.style.display = "block";
        });
    });

    if (document.querySelector('#category-child-delete')) {
        document.querySelector('#category-child-delete').addEventListener("click", function (e) {
            e.preventDefault();
            var $aRem = document.querySelector("#category-child-delete");
            $aRem.style.display = "none";
            var $form = document.querySelector('#categorie-add-form-container');
            var $list = document.querySelector('#categorie-endlist');
            $form.querySelector('h4').textContent = 'Ajouter une catégorie';
            document.querySelector('#categorie-parentId').value = 0;
            $list.after($form);
        });
    }

});