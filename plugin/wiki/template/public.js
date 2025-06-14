document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons de catégorie
    const categoryButtons = document.querySelectorAll('.wiki-menu-category-button');
    
    // Ajouter les écouteurs d'événements pour chaque bouton
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Trouver la catégorie parente
            const category = this.closest('.wiki-menu-category');
            
            // Basculer la classe active sur la catégorie
            category.classList.toggle('active');
            
            // Désactiver les autres catégories au même niveau
            const siblings = category.parentElement.querySelectorAll('.wiki-menu-category');
            siblings.forEach(sibling => {
                if (sibling !== category) {
                    sibling.classList.remove('active');
                }
            });
        });
    });
    
    // Activer la catégorie courante au chargement
    const currentCategory = document.querySelector('.wiki-menu-category-button[data-category-id="{{ currentCategory }}"]');
    if (currentCategory) {
        currentCategory.closest('.wiki-menu-category').classList.add('active');
    }
}); 