/**
 * Wiki Plugin Admin JavaScript
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */

// Fonction pour déployer/replier les catégories enfants
function CategoriesDeployChild(item) {
	// Trouver l'élément parent (la catégorie)
	let categoryElement = item.parentNode;
	
	// Trouver l'élément suivant qui contient les enfants (ignorer les nœuds texte)
	let nextElement = categoryElement.nextSibling;
	while (nextElement && nextElement.nodeType !== 1) {
		nextElement = nextElement.nextSibling;
	}
	
	// Bascule la rotation de l'icône
	item.classList.toggle('rotate-180');
	
	// Vérifier si l'élément suivant est bien un conteneur d'enfants
	if (nextElement && nextElement.classList.contains('toggle')) {
		slideToggle(nextElement, 400);
	}
}

// Fonction slideToggle simple pour l'animation
function slideToggle(element, duration = 400) {
	if (element.style.display === 'none' || element.style.display === '') {
		element.style.display = 'block';
		element.style.overflow = 'hidden';
		element.style.height = '0px';
		element.style.transition = 'height ' + duration + 'ms ease';
		
		setTimeout(() => {
			element.style.height = element.scrollHeight + 'px';
		}, 10);
		
		setTimeout(() => {
			element.style.height = '';
			element.style.overflow = '';
			element.style.transition = '';
		}, duration);
	} else {
		element.style.overflow = 'hidden';
		element.style.transition = 'height ' + duration + 'ms ease';
		element.style.height = element.scrollHeight + 'px';
		
		setTimeout(() => {
			element.style.height = '0px';
		}, 10);
		
		setTimeout(() => {
			element.style.display = 'none';
			element.style.height = '';
			element.style.overflow = '';
			element.style.transition = '';
		}, duration);
	}
}

async function WikiDeletePage(id) {
	if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?') === true) {
		let url = '/admin/wiki/delete-page';
		let data = {
			id: id,
			token: document.querySelector('input[name="token"]').value
		};
		let response = await fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(data)
		});
		// See body : let result = await response.json();
		let result = await response;
		if (result.status === 204) {
			fadeOut(document.querySelector('#page' + id));
			Toastify({
				text: "Élément supprimé",
				className: "success"		
			}).showToast();
		} else {
			Toastify({
				text: "Erreur lors de la suppression",
				className: "error"		
			}).showToast();
		}				
	};
}

function WikiUpdateParentCategoryVisibility() {
	let categoriesList = document.getElementById('categories-list');
	let useParentCheckbox = document.getElementById('use-parent-category');
	let parentSection = document.getElementById('parent-category-section');
	
	// Check if there are any categories (excluding the "no categories" message)
	let hasCategories = categoriesList.querySelector('.list-item-list:not(:first-child)') !== null;
	
	if (hasCategories) {
		// Reset checkbox state when showing controls
		if (useParentCheckbox) {
			useParentCheckbox.checked = false;
		}
		if (parentSection) {
			parentSection.style.display = 'none';
		}
	}
}

async function WikiRestoreVersion(pageId, version) {
	if (confirm('Êtes-vous sûr de vouloir restaurer cette version ?')) {
		let url = '/admin/wiki/restore-version';
		let data = {
			id: pageId,
			version: version,
			token: document.querySelector('input[name="token"]').value
		};
		let response = await fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(data)
		});
		if (response.ok) {
			window.location.reload();
		} else {
			Toastify({
				text: "Erreur lors de la restauration",
				className: "error"		
			}).showToast();
		}
	}
}

// Toggle parent category section visibility
document.addEventListener('DOMContentLoaded', function() {
	let useParentCheckbox = document.getElementById('use-parent-category');
	let parentSection = document.getElementById('parent-category-section');
	
	if (useParentCheckbox && parentSection) {
		useParentCheckbox.addEventListener('change', function() {
			parentSection.style.display = this.checked ? 'block' : 'none';
		});
	}
	
	// Initial visibility check
	WikiUpdateParentCategoryVisibility();
	
	// Add click handlers for tabs
	let tabHeaders = document.querySelectorAll('.tabs-header li');
	tabHeaders.forEach((header, index) => {
		header.addEventListener('click', function() {
			// Remove active class from all tabs
			tabHeaders.forEach(h => h.classList.remove('default-tab'));
			document.querySelectorAll('.tabs li').forEach(t => t.classList.remove('tab'));
			
			// Add active class to clicked tab
			this.classList.add('default-tab');
			document.querySelectorAll('.tabs li')[index].classList.add('tab');
			
			// Save current tab to localStorage
			localStorage.setItem('wiki-admin-active-tab', index);
		});
	});
}); 