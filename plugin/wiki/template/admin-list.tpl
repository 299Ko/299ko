<div class="tabs-container">
	<ul class="tabs-header">
		<li class="default-tab"><i class="fa-solid fa-list"></i> {{ Lang.wiki-pages-list }}</li>
		<li><i class="fa-solid fa-folder"></i> {{ Lang.wiki-categories }}</li>
		<li><i class="fa-solid fa-sliders"></i> {{ Lang.wiki-settings }}</li>
	</ul>
	<ul class="tabs">
		<li class="tab">
			<section class="overflow-auto">
									<div class="button-group">
				<a class="button" href="{{ ROUTER.generate("admin-wiki-edit-page") }}"><i class="fa-solid fa-plus"></i> {{ Lang.wiki-add }}</a>
					</div>
				<table class="small">
					<tr>
						<th>{{ Lang.wiki-title }}</th>
						<th>{{ Lang.wiki-date }}</th>
						<th>{{ Lang.wiki-version }}</th>

						<th>{{ Lang.wiki-see }}</th>
						<th>{{ Lang.wiki-categories }}</th>
						<th>{{ Lang.delete }}</th>
					</tr>
					{% for item in wikiPageManager.getItems() %}
						<tr id="page{{ item.getId() }}">
							<td>
								<a title="{{ Lang.wiki-edit }}" href="{{ ROUTER.generate("admin-wiki-edit-page", ["id" => item.getId()]) }}">{{ item.getName() }}</a>
							</td>
							<td>
								{{ util.getDate(item.getDate()) }}
							</td>
							<td style="text-align: center">
								<span class="wiki-version">{{ item.getVersion() }}</span>
							</td>

							<td style="text-align: center">
								<a title="{{ Lang.wiki-see }}" target="_blank" href="{{ item.getUrl }}" class="button">
									<i class="fa-solid fa-eye"></i>
								</a>
							</td>
							<td>
								{% if item.categories %}
									{% for cat in item.categories %}
										<span class="wiki-category">{{ cat.label }}</span>
									{% endfor %}
								{% else %}
									{{ Lang.wiki.categories.none}}
								{% endif %}
							</td>
							<td style="text-align: center">
								<a title="{{ Lang.delete }}" onclick="WikiDeletePage('{{ item.getId() }}')" class="button alert">
									<i class="fa-regular fa-trash-can"></i>
								</a>
							</td>
						</tr>
					{% endfor %}
				</table>
			</section>
		</li>
		<li class="tab">
			<section>
				<!-- Section Liste des catégories -->
				<div class="section-header">
					<h3>{{ Lang.wiki.categories.list }}</h3>
				</div>
				<div id="categories-list">
					<div class="list-item-container">
						<div class="list-item-list">
							<div>{{ Lang.wiki.categories.name }}</div>
							<div>{{ Lang.wiki.categories.itemsNumber }}</div>
							<div>{{ Lang.wiki.categories.actions }}</div>
						</div>
						{% if categoriesManager.getNestedCategories() %}
							{% for cat in categoriesManager.getNestedCategories() %}
								<div id="category-{{ cat.id }}" class="list-item-list {% if cat.hasChildren %}hasChildren{% endif %}">
									{% if cat.hasChildren %}
										<i onclick="CategoriesDeployChild(this)" class="fa-solid fa-chevron-up list-item-toggle" title="{{ Lang.wiki.categories.collapseExpandChildren }}"></i>
									{% endif %}
									<div style="padding-left:{% if cat.hasChildren %}{{ cat.depth * 15 + 25 }}{% else %}{{ cat.depth * 15 + 10 }}{% endif %}px;">
										{{ str_repeat("-", cat.depth * 2) }} {{ cat.label }}
									</div>
									<div>{{ categoriesManager.getTotalItemsCount(cat) }}</div>
									<div role="group">
										<form method="post" action="{{ ROUTER.generate("admin-wiki-edit-category") }}" style="display: inline;">
											<input type="hidden" name="id" value="{{ cat.id }}">
											<input type="hidden" name="token" value="{{ token }}">
											<button type="submit" class="button small" title="{{ Lang.wiki.categories.editCategory }}"><i class="fa-solid fa-pencil"></i></button>
										</form>
										<form method="post" action="{{ ROUTER.generate("admin-wiki-delete-category") }}" style="display: inline;">
											<input type="hidden" name="id" value="{{ cat.id }}">
											<input type="hidden" name="token" value="{{ token }}">
											<button type="submit" class="button alert small" title="{{ Lang.wiki.categories.deleteCategory }}" onclick="return confirm('{{ Lang.confirm.deleteItem }}')"><i class="fa-solid fa-folder-minus"></i></button>
										</form>
									</div>
								</div>
								{% if cat.hasChildren %}
									<div class="toggle">
										{% for child in cat.children %}
											<div id="category-{{ child.id }}" class="list-item-list {% if child.hasChildren %}hasChildren{% endif %}">
												{% if child.hasChildren %}
													<i onclick="CategoriesDeployChild(this)" class="fa-solid fa-chevron-up list-item-toggle" title="{{ Lang.wiki.categories.collapseExpandChildren }}"></i>
												{% endif %}
												<div style="padding-left:{% if child.hasChildren %}{{ child.depth * 15 + 25 }}{% else %}{{ child.depth * 15 + 10 }}{% endif %}px;">
													{{ str_repeat("-", child.depth * 2) }} {{ child.label }}
												</div>
												<div>{{ categoriesManager.getTotalItemsCount(child) }}</div>
												<div role="group">
													<form method="post" action="{{ ROUTER.generate("admin-wiki-edit-category") }}" style="display: inline;">
														<input type="hidden" name="id" value="{{ child.id }}">
														<input type="hidden" name="token" value="{{ token }}">
														<button type="submit" class="button small" title="{{ Lang.wiki.categories.editCategory }}"><i class="fa-solid fa-pencil"></i></button>
													</form>
													<form method="post" action="{{ ROUTER.generate("admin-wiki-delete-category") }}" style="display: inline;">
														<input type="hidden" name="id" value="{{ child.id }}">
														<input type="hidden" name="token" value="{{ token }}">
														<button type="submit" class="button alert small" title="{{ Lang.wiki.categories.deleteCategory }}" onclick="return confirm('{{ Lang.confirm.deleteItem }}')"><i class="fa-solid fa-folder-minus"></i></button>
													</form>
												</div>
											</div>
											{% if child.hasChildren %}
												<div class="toggle">
													{% for grandchild in child.children %}
														<div id="category-{{ grandchild.id }}" class="list-item-list">
															<div style="padding-left:{{ grandchild.depth * 15 + 10 }}px;">
																{{ str_repeat("-", grandchild.depth * 2) }} {{ grandchild.label }}
															</div>
															<div>{{ categoriesManager.getTotalItemsCount(grandchild) }}</div>
															<div role="group">
																<form method="post" action="{{ ROUTER.generate("admin-wiki-edit-category") }}" style="display: inline;">
																	<input type="hidden" name="id" value="{{ grandchild.id }}">
																	<input type="hidden" name="token" value="{{ token }}">
																	<button type="submit" class="button small" title="{{ Lang.wiki.categories.editCategory }}"><i class="fa-solid fa-pencil"></i></button>
																</form>
																<form method="post" action="{{ ROUTER.generate("admin-wiki-delete-category") }}" style="display: inline;">
																	<input type="hidden" name="id" value="{{ grandchild.id }}">
																	<input type="hidden" name="token" value="{{ token }}">
																	<button type="submit" class="button alert small" title="{{ Lang.wiki.categories.deleteCategory }}" onclick="return confirm('{{ Lang.confirm.deleteItem }}')"><i class="fa-solid fa-folder-minus"></i></button>
																</form>
															</div>
														</div>
													{% endfor %}
												</div>
											{% endif %}
										{% endfor %}
									</div>
								{% endif %}
							{% endfor %}
						{% else %}
							<div class="list-item-list">{{ Lang.wiki.categories.none }}</div>
						{% endif %}
					</div>
				</div>

				<!-- Section Ajout de catégorie -->
				<div class="section-header">
					<h3>{{ Lang.wiki.categories.addCategory }}</h3>
				</div>
				<div class="form-section">
					<form method="post" action="{{ ROUTER.generate("admin-wiki-add-category") }}">
						{{ SHOW.tokenField }}
					<div class="form">
						<label for="category-add-label">{{ Lang.wiki.categories.categoryName }}</label>
							<input type="text" id="category-add-label" name="label" placeholder="{{ Lang.wiki.categories.categoryNamePlaceholder }}" required/>
					</div>
					
					{% if categoriesManager.getCategories() %}
						<div class="form">
							<input type="checkbox" id="use-parent-category" name="use-parent-category"/>
							<label for="use-parent-category">{{ Lang.wiki.categories.useParentCategory }}</label>
						</div>
						<div id="parent-category-section" style="display: none;">
							<div class="form">
								<label for="category-add-parentId">{{ Lang.wiki.categories.categoryParent }}</label>
								<div id="parent-category-select-container">
										{{ categoriesManager.outputAsSelectOne(0, "parentId") }}
								</div>
							</div>
						</div>
					{% endif %}
					
					<div class="form">
							<button type="submit" class="button">
							<i class="fa-solid fa-plus"></i> {{ Lang.wiki.categories.addCategory }}
						</button>
					</div>
					</form>
				</div>
			</section>
		</li>
		<li class="tab">
			<section>
				<form method="post" action="{{ ROUTER.generate("admin-wiki-save-config") }}">
					{{ SHOW.tokenField }}
					
					<!-- Section Général -->
					<div class="section-header">
						<h3>Configuration générale</h3>
					</div>
					<div class="form-section">
						<div class="form">
							<label for="label">Titre de la page d'accueil</label>
							<input type="text" name="label" id="label" value="{{ runPlugin.getConfigVal("label") }}" placeholder="Ex: Documentation, Wiki, Guide" required="required"/>
							<small>Titre affiché sur la page d'accueil publique</small>
						</div>
						<div class="form">
							<label for="homeText">Texte de la page d'accueil</label>
							<textarea name="homeText" id="homeText" rows="4" placeholder="Texte descriptif affiché sur la page d'accueil">{{ runPlugin.getConfigVal("homeText") }}</textarea>
							<small>Description ou introduction affichée sur la page d'accueil</small>
					</div>
					<div class="form">
							<input {% if runPlugin.getConfigVal("showLastActivity") %}checked{% endif %} type="checkbox" name="showLastActivity" id="showLastActivity"/>
							<label for="showLastActivity">Afficher la dernière activité</label>
							<small>Affiche un bloc avec la dernière action effectuée sur le wiki</small>
					</div>
					</div>
					
					<!-- Section Affichage -->
					<div class="section-header">
						<h3>Affichage</h3>
					</div>
					<div class="form-section">
					<div class="form">
							<label for="displayTOC">Affichage de la table des matières</label>
						<select name="displayTOC" id="displayTOC">
								<option value="no" {% if runPlugin.getConfigVal("displayTOC") == "no" %}selected{% endif %}>Désactivé</option>
								<option value="content" {% if runPlugin.getConfigVal("displayTOC") == "content" %}selected{% endif %}>Dans le contenu</option>
								<option value="sidebar" {% if runPlugin.getConfigVal("displayTOC") == "sidebar" %}selected{% endif %}>Dans la sidebar</option>
						</select>
					</div>
					<div class="form">
							<input {% if runPlugin.getConfigVal("hideContent") %}checked{% endif %} type="checkbox" name="hideContent" id="hideContent"/>
							<label for="hideContent">Masquer le contenu sur la liste</label>
							<small>N'affiche que le titre et l'intro sur la liste des pages</small>
					</div>
					</div>
					
					<!-- Section Fonctionnalités -->
					<div class="section-header">
						<h3>Fonctionnalités</h3>
					</div>
					<div class="form-section">

					<div class="form">
						<input {% if runPlugin.getConfigVal("enableVersioning") %}checked{% endif %} type="checkbox" name="enableVersioning" id="enableVersioning"/>
							<label for="enableVersioning">Activer le versioning</label>
							<small>Permet de sauvegarder l'historique des modifications</small>
					</div>
					<div class="form">
						<input {% if runPlugin.getConfigVal("enableInternalLinks") %}checked{% endif %} type="checkbox" name="enableInternalLinks" id="enableInternalLinks"/>
							<label for="enableInternalLinks">Activer les liens internes</label>
							<small>Permet de créer des liens automatiques entre les pages</small>
						</div>
					</div>
					
					<p><button type="submit" class="button">{{ Lang.save }}</button></p>
				</form>
			</section>
		</li>
	</ul>
</div>

<script>
// Restore active tab from localStorage immediately
(function() {
	let activeTabIndex = localStorage.getItem('wiki-admin-active-tab');
	if (activeTabIndex === null) {
		// Si aucune valeur n'est sauvegardée, on reste sur l'onglet par défaut (0 = liste des pages)
		activeTabIndex = 0;
		localStorage.setItem('wiki-admin-active-tab', activeTabIndex);
	}
	
	// Apply the saved tab immediately
	activeTabIndex = parseInt(activeTabIndex);
	let tabHeaders = document.querySelectorAll('.tabs-header li');
	let tabContents = document.querySelectorAll('.tabs li');
	
	if (activeTabIndex >= 0 && activeTabIndex < tabHeaders.length && tabContents.length > activeTabIndex) {
		// Remove active class from all tabs
		tabHeaders.forEach(h => h.classList.remove('default-tab'));
		tabContents.forEach(t => t.classList.remove('tab'));
		
		// Add active class to saved tab
		tabHeaders[activeTabIndex].classList.add('default-tab');
		tabContents[activeTabIndex].classList.add('tab');
	}
})();
</script>