<div class="content">
	<header>
		<h1>{{ Lang.wiki.categories.editCategory }}</h1>
	</header>
	
	<div style="margin-bottom: 20px;">
		<a class="button" href="{{ ROUTER.generate("admin-wiki-list") }}"><i class="fa-solid fa-arrow-left"></i> {{ Lang.wiki-back-to-pages }}</a>
	</div>
	
	<form method="post" action="{{ ROUTER.generate("admin-wiki-save-category") }}">
		{{ SHOW.tokenField }}
		<input type="hidden" name="id" value="{{ category.id }}"/>
		
		<div class="form">
			<label for="label">{{ Lang.wiki.categories.categoryName }}</label>
			<input type="text" name="label" id="label" value="{{ category.label }}" required="required"/>
		</div>
		
		{% if categoriesManager.getCategories() %}
		<div class="form">
			<label for="parentId">{{ Lang.wiki.categories.categoryParent }}</label>
			{{ categoriesManager.outputAsParentSelect(category.parentId, "parentId") }}
		</div>
		{% endif %}
		
		<p>
			<button type="submit" class="button">{{ Lang.save }}</button>
			<a href="{{ ROUTER.generate("admin-wiki-list") }}" class="button">{{ Lang.cancel }}</a>
		</p>
	</form>
</div> 