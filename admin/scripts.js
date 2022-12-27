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


function resizeListener() {
    if (window.innerWidth > 768) {
        document.querySelector('#content').classList.add('withSidebar');
    } else {
        document.querySelector('#content').classList.remove('withSidebar');
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("input[required='required']").forEach(function (input) {
        input.addEventListener('change', (e) => {
            const isValid = e.target.reportValidity();
            // other code from before
            e.target.setAttribute('aria-invalid', !isValid);
        });
    });

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

    document.querySelectorAll(".list-item-list li i").forEach(function (item) {
        item.addEventListener('click', function () {
            item.classList.toggle('rotate-180');
            getNextSibling(item, 'ul.list-item-list-sub').slideToggle(400);
        });
    });

    document.querySelectorAll("div.list-item-list i.list-item-toggle").forEach(function (item) {
        item.addEventListener('click', function () {
            item.classList.toggle('rotate-180');
            getNextSibling(item.parentNode, 'div.toggle').slideToggle(400);
        });
    });

    document.querySelectorAll('.btn-add-categorie').forEach(function (item) {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            var $form = document.querySelector('#categorie-add-form-container');
            var parent_id = item.getAttribute('data-id');
            var $categorie = document.querySelector('#categorie-' + parent_id);
            document.querySelector("#head-add-cat").textContent = 'Ajouter une catégorie enfant à ' + $categorie.getAttribute('name');
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
            var $list = document.querySelector('#list-item-endlist');
            document.querySelector("#head-add-cat").textContent = 'Ajouter une catégorie';
            document.querySelector('#categorie-parentId').value = 0;
            $list.after($form);
        });
    }

});