<article>
	<header>
		{% if item.getImg() %}
			<img class="featured" src="{{ item.getImgUrl }}" alt="{{ item.getName }}"/>
		{% endif %}
		<div class="item-head">
			<p class="date">
			{{ Lang.wiki.posted-date(item.getReadableDate())}}

				{% if count(categories) == 0 %}
					{{ Lang.wiki.categories.none}}
				{% else %}
					dans
					{% for cat in categories %}
						<span class="wiki-label-category"><a href="{{ cat.url }}">{{ cat.label }}</a></span> 
					{% endfor %}
				{% endif %}
				| <a href="{{ runPlugin.getPublicUrl }}">{{ Lang.wiki.back-to-list }}</a>
			</p>
		</div>
		
	</header>
	{{ TOC }}
	{{ generatedHtml }}
	{% if runPlugin.getConfigVal("displayAuthor") %}
		<footer>
			<div class='wiki-author'>
				<div class='wiki-avatar'>
					<img src='{{runPlugin.getConfigVal("authorAvatar")}}' alt='{{runPlugin.getConfigVal("authorName")}}'/>
				</div>
				<div class='wiki-infos'>
					<div class='wiki-infos-name'>
						<span>{{runPlugin.getConfigVal("authorName")}}</span>
					</div>
					<div class='wiki-infos-bio'>
						{{htmlspecialchars_decode(runPlugin.getConfigVal("authorBio"))}}
					</div>
				</div>
			</div>
		</footer>
	{% endif %}
</article>
