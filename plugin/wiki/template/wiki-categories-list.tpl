{% IF catDisplay == "root" %}
    <!-- Categories Container -->
    <div class="list-item-container">
        <div class="list-item-list">
            <div>{{ Lang.wiki.categories.name }}</div>
            <div>{{ Lang.wiki.categories.itemsNumber }}</div>
            <div>{{ Lang.wiki.categories.actions }}</div>
        </div>
        {% IF this.imbricatedCategories | empty %}
            <div class="list-item-list">{{ Lang.wiki.categories.none }}</div>
        {% ELSE %}
            {% FOR cat IN this.imbricatedCategories %}
                {{ cat.outputAsList() }}
            {% ENDFOR %}
        {% ENDIF %}
    </div>
    <footer id="list-item-endlist">
        <div id="categorie-add-form-container">
            <h4 id="head-add-cat">{{ Lang.wiki.categories.addCategory }}</h4>
            <form method="post" action="{{ ROUTER.generate('admin-wiki-add-category') }}">
                {{ SHOW.tokenField() }}
                <div class="input-field">
                    <label for="category-list-add-label">{{ Lang.wiki.categories.categoryName }}</label>
                    <input type="text" name="label" id="category-list-add-label" required/>
                    <label for="category-list-add-parentId">{{ Lang.wiki.categories.categoryParent }}</label>
                    <select name="parentId" id="category-list-add-parentId">
                        <option value="0">{{ Lang.wiki.categories.none }}</option>
                        {% IF this.imbricatedCategories %}
                            {% FOR cat IN this.imbricatedCategories %}
                                {{ cat.outputAsSelectOne(0) }}
                            {% ENDFOR %}
                        {% ENDIF %}
                    </select>
                </div>
                <button type="submit" id="list-item-add-btn">{{ Lang.wiki.categories.addCategory }}</button>
            </form>
        </div>
    </footer>
    <script>
        function CategoriesDeployChild(item) {
            nextToggle = item.parentNode.nextSibling;
            item.classList.toggle('rotate-180');
            nextToggle.slideToggle(400);
        }
    </script>
{% ELSEIF catDisplay == "sub" %}
    <!-- Categories -->
    <div id="category-{{ this.id }}" class="list-item-list {% IF this.hasChildren %}hasChildren{% ENDIF %}">
        {% IF this.hasChildren %}
            <i style="left:{{ this.depth * 15 + 5 }}px;" onclick="CategoriesDeployChild(this)" class="fa-solid fa-chevron-up list-item-toggle" title="{{ Lang.wiki.categories.collapseExpandChildren }}"></i>
        {% ENDIF %}
        <div style="padding-left:{{ this.depth * 15 + 10 }}px;">{% FOR i IN [1, this.depth * 2] %}-{% ENDFOR %} {{ this.label }}</div>
        <div>{{ this.getTotalItemsCountRecursive() }}</div>
        <div role="group">
            <a class="button small" title="{{ Lang.wiki.categories.editCategory }}" href="{{ ROUTER.generate('admin-wiki-edit-category', ['id' => this.id]) }}"><i class="fa-solid fa-pencil"></i></a>
            <a class="button alert small" title="{{ Lang.wiki.categories.deleteCategory }}" href="{{ ROUTER.generate('admin-wiki-delete-category', ['id' => this.id]) }}" onclick="return confirm('{{ Lang.confirm.deleteItem }}')"><i class="fa-solid fa-folder-minus"></i></a>
        </div>
    </div>
    {% IF this.hasChildren %}
        <div class="toggle">
            {% FOR child IN this.children %}
                {{ child.outputAsList() }}
            {% ENDFOR %}
        </div>
    {% ENDIF %}
{% ENDIF %} 