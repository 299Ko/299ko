document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.btn-add-respond').forEach(function (item) {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            var $form = document.querySelector('#comments-add-respond');
            var parent_id = item.getAttribute('data-id');
            var $comment = document.querySelector('#comment' + parent_id);
            document.querySelector("#comments-title").textContent = document.querySelector('#comment' + parent_id + "Infos").getAttribute('data-author');
            document.querySelector('#commentParentId').value = parent_id;
            $comment.after($form);
            var $aRem = document.querySelector("#comments-cancel-respond");
            $aRem.style.display = "block";
        });
    });

    if (document.querySelector('#comments-cancel-respond')) {
        document.querySelector('#comments-cancel-respond').addEventListener("click", function (e) {
            e.preventDefault();
            var $aRem = document.querySelector("#comments-cancel-respond");
            $aRem.style.display = "none";
            var $form = document.querySelector('#comments-add-respond');
            var $container = document.querySelector('#comments-add-container');
            document.querySelector("#comments-title").textContent = document.querySelector("#comments-title").getAttribute('data-title');
            document.querySelector('#commentParentId').value = 0;
            $container.after($form);
        });
    }

});

/**
 * JavaScript pour l'arbre des catégories du wiki
 */
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des clics sur les en-têtes de catégories
    const categoryHeaders = document.querySelectorAll('.category-header');
    
    categoryHeaders.forEach(header => {
        header.addEventListener('click', function(e) {
            // Si pas de flèche, ne rien faire
            const toggleIcon = this.querySelector('.toggle-icon');
            if (!toggleIcon) return;
            // Empêcher le clic si on clique sur le lien
            if (e.target.tagName === 'A') {
                return;
            }
            const targetId = this.getAttribute('data-target');
            const targetContent = document.querySelector(targetId);
            if (targetContent) {
                // Toggle de l'affichage
                if (targetContent.classList.contains('show')) {
                    targetContent.classList.remove('show');
                    toggleIcon.classList.remove('expanded');
                } else {
                    targetContent.classList.add('show');
                    toggleIcon.classList.add('expanded');
                }
            }
        });
    });
    
    // Expansion automatique de la première catégorie au chargement (si elle a une flèche)
    const firstCategory = document.querySelector('.category-header .toggle-icon');
    if (firstCategory) {
        const header = firstCategory.closest('.category-header');
        const targetId = header.getAttribute('data-target');
        const targetContent = document.querySelector(targetId);
        if (targetContent) {
            targetContent.classList.add('show');
            firstCategory.classList.add('expanded');
        }
    }
    
    // Animation smooth pour les transitions
    const style = document.createElement('style');
    style.textContent = `
        .category-content {
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .category-content:not(.show) {
            max-height: 0;
            opacity: 0;
        }
        .category-content.show {
            max-height: 1000px;
            opacity: 1;
        }
        .category-header {
            cursor: default;
        }
        .category-header .toggle-icon {
            cursor: pointer;
        }
        .category-header.has-toggle {
            cursor: pointer;
        }
    `;
    document.head.appendChild(style);
    // Ajout d'une classe pour le curseur pointer si flèche
    document.querySelectorAll('.category-header').forEach(header => {
        if (header.querySelector('.toggle-icon')) {
            header.classList.add('has-toggle');
        }
    });
});

/**
 * JavaScript pour la TOC interactive du plugin Wiki
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la TOC interactive
    initWikiTOC();
});

function initWikiTOC() {
    const tocContainer = document.querySelector('.wiki-toc');
    if (!tocContainer) return;

    // Trouver tous les éléments li qui ont des enfants (ol)
    const listItems = tocContainer.querySelectorAll('li');
    
    listItems.forEach(function(li) {
        const children = li.querySelector('ol');
        if (children) {
            // Ajouter la classe has-children
            li.classList.add('has-children');
            
            // Cacher les enfants par défaut
            children.style.display = 'none';
            
            // Ajouter l'événement de clic sur le lien
            const link = li.querySelector('a');
            if (link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleTOCItem(li, children);
                });
            }
        }
    });
}

function toggleTOCItem(parentElement, childrenElement) {
    const isExpanded = childrenElement.style.display !== 'none';
    
    if (isExpanded) {
        // Réduire
        childrenElement.style.display = 'none';
        parentElement.classList.remove('expanded');
    } else {
        // Étendre
        childrenElement.style.display = 'block';
        parentElement.classList.add('expanded');
    }
}

// Fonction pour étendre/réduire tout
function toggleAllWikiTOC() {
    const tocContainer = document.querySelector('.wiki-toc');
    if (!tocContainer) return;
    
    const allListItems = tocContainer.querySelectorAll('li.has-children');
    const allChildren = tocContainer.querySelectorAll('li.has-children ol');
    const isAnyExpanded = Array.from(allChildren).some(child => child.style.display !== 'none');
    
    allListItems.forEach(function(li, index) {
        const children = allChildren[index];
        if (children) {
            if (isAnyExpanded) {
                // Réduire tout
                children.style.display = 'none';
                li.classList.remove('expanded');
            } else {
                // Étendre tout
                children.style.display = 'block';
                li.classList.add('expanded');
            }
        }
    });
}

// Exposer la fonction globalement pour pouvoir l'appeler depuis l'admin si nécessaire
window.toggleAllWikiTOC = toggleAllWikiTOC;