{% IF catDisplay == "root" %}
    <!-- Categories Container -->
    {% IF categoriesManager.getNestedCategories() | empty %}
        {{ Lang.wiki.categories.none }}
    {% ELSE %}
        <select name="{{ fieldName }}" id="{{ fieldName }}">
            <option value="0">{{ Lang.wiki.categories.none }}</option>
            {% FOR cat IN categoriesManager.getNestedCategories() %}
                {{ cat.outputAsParentSelect(selectedParentId) }}
            {% ENDFOR %}
        </select>
    {% ENDIF %}
{% ELSEIF catDisplay == "sub" %}
    <!-- Categories -->
    <option value="{{ this.id }}" {% IF selectedParentId == this.id %}selected{% ENDIF %}>
        {% FOR i IN [1, this.depth * 2] %}-{% ENDFOR %} {{ this.label }}
    </option>
    {% IF this.hasChildren %}
        {% FOR child IN this.children %}
            {{ child.outputAsParentSelect(selectedParentId) }}
        {% ENDFOR %}
    {% ENDIF %}
{% ENDIF %} 