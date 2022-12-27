/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * @author ShevAbam <me@shevarezo.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */

/* Busy and disable submit button in form when submitted */
document.querySelectorAll("form").forEach(function (item) {
    item.onsubmit = function () {
        item.querySelector('[type="submit"]').setAttribute('aria-busy', true);
        item.querySelector('[type="submit"]').setAttribute('disabled', true);
    };
});

/* 
 * Fade Out an HTML element
 * 
 * var sidebar = document.querySelector('#sidebar');
 * fadeOut(sidebar);
 */
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

/* 
 * Fade In an HTML element
 * 
 * var sidebar = document.querySelector('#sidebar');
 * fadeIn(sidebar, 'block');
 */
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

/* plain JS slideToggle https://github.com/ericbutler555/plain-js-slidetoggle 
 * 
 * var sidebar = document.querySelector('#sidebar');
 * sidebar.slideToggle(400);
 */
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

/*
 * Get Next Sibling of an element, with or without selector
 * 
 * var el = getNextSibling(element, 'div.class');
 */

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

/* modal.js : https://github.com/picocss/pico/blob/master/docs/js/modal.js */
"use strict";
const isOpenClass = "modal-is-open", openingClass = "modal-is-opening", closingClass = "modal-is-closing", animationDuration = 400;
let visibleModal = null;
const toggleModal = e => {
    e.preventDefault();
    e = document.getElementById(e.currentTarget.getAttribute("data-target"));
    (void 0 !== e && null != e && isModalOpen(e) ? closeModal : openModal)(e)
}, isModalOpen = e => !(!e.hasAttribute("open") || "false" == e.getAttribute("open")), openModal = e => {
    isScrollbarVisible() && document.documentElement.style.setProperty("--scrollbar-width", getScrollbarWidth() + "px"), document.documentElement.classList.add(isOpenClass, openingClass), setTimeout(() => {
        visibleModal = e, document.documentElement.classList.remove(openingClass)
    }, animationDuration), e.setAttribute("open", !0)
}, closeModal = e => {
    visibleModal = null, document.documentElement.classList.add(closingClass), setTimeout(() => {
        document.documentElement.classList.remove(closingClass, isOpenClass), document.documentElement.style.removeProperty("--scrollbar-width"), e.removeAttribute("open")
    }, animationDuration)
}, getScrollbarWidth = (document.addEventListener("click", e => {
    null == visibleModal || visibleModal.querySelector("article").contains(e.target) || closeModal(visibleModal)
}), document.addEventListener("keydown", e => {
    "Escape" === e.key && null != visibleModal && closeModal(visibleModal)
}), () => {
    var e = document.createElement("div"), t = (e.style.visibility = "hidden", e.style.overflow = "scroll", e.style.msOverflowStyle = "scrollbar", document.body.appendChild(e), document.createElement("div")), t = (e.appendChild(t), e.offsetWidth - t.offsetWidth);
    return e.parentNode.removeChild(e), t
}), isScrollbarVisible = () => document.body.scrollHeight > screen.height;