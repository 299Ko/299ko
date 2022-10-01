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

$(document).ready(function () {
    $(".msg").each(function (index) {
        $(this).children(".msg-button-close").click(function () {
            $(this).parent().dequeue();
        });
        $(this).delay(5000 + index * 5000).slideUp();
    });


    // tri menu
    var elem = $('#navigation').find('li').sort(sortMe);
    function sortMe(a, b) {
        return a.className > b.className;
    }
    $('#navigation').append(elem);
    // login
    $('#login input.alert').click(function () {
        document.location.href = $(this).attr('rel');
    });
    // nav
    $('#open_nav').click(function () {
        if ($('#sidebar').css('display') == 'none') {
            $('#sidebar').fadeIn();
        } else {
            $('#sidebar').hide();
        }
    });

    $(".categories-list li i").click(function () {
        $(this).toggleClass('fa-rotate-180');
        $(this).next('ul.categories-list-sub').slideToggle();
    });

    $('div.categorie-list i.categories-toggle').click(function () {
        $(this).toggleClass('fa-rotate-180');
        $(this).parent('div.categorie-list').nextAll('div.categories-toggle').first().slideToggle();
    });

    $('.btn-add-categorie').click(function (e) {
        e.preventDefault();
        var $form = $('#categorie-add-form-container');
        var $this = $(this);
        var parent_id = $this.data('id');
        var $categorie = $('#categorie-' + parent_id);
        $form.find('h4').text('Ajouter une catégorie enfant à ' + $categorie.attr('name'));
        $('#categorie-parentId').val(parent_id);
        $categorie.after($form);
        //$('#comment-reply-delete').style.opacity = "100";
        var $aRem = document.getElementById("category-child-delete");
        $aRem.style.display = "block";
    });

    $('#category-child-delete').click(function (e) {
        e.preventDefault();
        var $aRem = document.getElementById("category-child-delete");
        $aRem.style.display = "none";
        var $form = $('#categorie-add-form-container');
        var $list = $('#categorie-endlist');
        $form.find('h4').text('Ajouter une catégorie');
        $('#comment-parentId').val(0);
        $list.after($form);
    });
});

