document.addEventListener("DOMContentLoaded", function() {

    if (document.querySelector('.galerie-admin table')) {

        document.querySelector('.galerie-admin tr.hidden').style.display = 'none';

        document.querySelector('.galerie-admin .showall').addEventListener('click', function(){
            document.querySelectorAll('tr.visible').forEach(function(item, index){
                if (item.style.display != 'none') {
                    item.style.display = 'none';
                } else {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('tr.hidden').forEach(function(item, index){
                if (item.style.display != 'none') {
                    item.style.display = 'none';
                } else {
                    item.style.display = 'block';
                }
            });
        });

    }

    if (document.querySelector('.galerie-admin .category')) {
        document.querySelector('.galerie-admin .category').addEventListener('click', function(){
            document.querySelector('.galerie-admin input#category').value = this.textContent;
        });
    }

});