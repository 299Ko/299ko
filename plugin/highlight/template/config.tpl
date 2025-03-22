<section>
	<header>{{Lang.core-settings}}</header>
	<form method="post" action="{{ ROUTER.generate("highlight-saveconf") }}" enctype="multipart/form-data">
		{{SHOW.tokenField()}}
		<p>
			<label for="theme-select">{{ Lang.highlight.theme-choose }}</label><br>
			<select name="theme" id="theme-select">
                {% for k, v in highlightGetThemes() %}
                    <option value='{{k}}'
                    {% if runPlugin.getConfigVal("theme") == k %} selected {% endif %}
                    >{{v}}</option>
                {% endfor %}
			</select>
		</p>
		<p>
			<button type="submit" class="button">{{Lang.submit}}</button>
		</p>
	</form>
</section>
