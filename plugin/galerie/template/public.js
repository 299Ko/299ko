
document.addEventListener("DOMContentLoaded", function() {

    if (document.querySelector('.galerie .categories a')) {

        document.querySelectorAll('.galerie .categories a').forEach(function(item, index) {
            item.addEventListener('click', function(){
                var rel = this.getAttribute('rel');

                document.querySelectorAll('.galerie #list li').forEach(function(item, index) {
                    item.style.display = 'none';
                });

                document.querySelectorAll('.galerie #list li.'+rel).forEach(function(item, index) {
                    fadeIn(item, 'block');
                });

            });
        });

    }

});

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
