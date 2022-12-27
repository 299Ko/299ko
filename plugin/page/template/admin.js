document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".page-admin div.toggle").forEach(function (item) {
        var els = item.querySelectorAll(":scope > .list-item-list");
        let firstElement = els[0];
        let lastElement = els[els.length - 1];
        firstElement.classList.add('first-item');
        lastElement.classList.add('last-item');
    });
    
    document.querySelectorAll(".page-admin div.list-item-container").forEach(function (item) {
        var els = item.querySelectorAll(":scope  article > .list-item-list");
        let firstElement = els[0];
        let lastElement = els[els.length - 1];
        firstElement.classList.add('first-item');
        lastElement.classList.add('last-item');
        firstElement.querySelector("a.up").remove();
        lastElement.querySelector("a.down").remove();
    });
});