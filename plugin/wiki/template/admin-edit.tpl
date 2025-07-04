<form method="post" id="mainForm" action="{{ ROUTER.generate("admin-wiki-save-page")}}" enctype="multipart/form-data">
    {{SHOW.tokenField}}
    <input type="hidden" name="id" value="{{ wikiPage.getId() }}" />
    {% if pluginsManager.isActivePlugin("galerie") %}
        <input type="hidden" name="imgId" value="{{ wikiPage.getImg() }}" />
    {% endif %}

    <div style="margin-bottom: 20px;">
        <a class="button" href="{{ ROUTER.generate("admin-wiki-list") }}"><i class="fa-solid fa-arrow-left"></i> {{ Lang.wiki-back-to-pages }}</a>
    </div>

    <div class='tabs-container'>
        <ul class="tabs-header">
            <li class="default-tab"><i class="fa-solid fa-file-pen"></i> {{Lang.wiki-content}}</li>
            <li><i class="fa-regular fa-newspaper"></i> {{Lang.wiki-intro}}</li>
            <li><i class="fa-regular fa-thumbs-up"></i> {{Lang.wiki-seo}}</li>
            <li><i class="fa-solid fa-heading"></i> {{Lang.wiki-title}}</li>
            <li><i class="fa-solid fa-sliders"></i> {{Lang.wiki-settings}}</li>
            {% if pluginsManager.isActivePlugin("galerie") %}
                <li><i class="fa-regular fa-image"></i> {{Lang.wiki-featured-img}}</li>
            {% endif %}
            <li><i class="fa-solid fa-history"></i> {{Lang.wiki-history}}</li>
        </ul>
        <ul class="tabs">
            <li class="tab">
                {{ contentEditor }}
            </li>
            <li class="tab">
                <label for="intro">{{Lang.wiki-intro-content}}</label><br>
                <textarea name="intro" id="intro" class="editor">{%HOOK.beforeEditEditor(wikiPage.getIntro())%}</textarea><br>
                {{filemanagerDisplayManagerButton()}}
            </li>
            <li class="tab">
                <div class='form'>
                    <label for="seoDesc">{{Lang.wiki-seo-content}}</label>
                    <div class='tooltip'>
                        <span id='seoDescDesc'>{{Lang.wiki-seo-content-tooltip}}</span>
                    </div>
                    <textarea name="seoDesc" id="seoDesc" aria-describedby="seoDescDesc">{{ wikiPage.getSEODesc() }}</textarea>
                    <div id='seoDescProgress'></div>
                    <div id='seoDescCounter'></div>
                    <script>
                        function refreshSEODescCounter() {
                            var length = document.getElementById('seoDesc').value.length;
                            var progress = document.getElementById('seoDescProgress');
                            document.getElementById('seoDescCounter').innerHTML = length + ' caractère(s)';
                            if (length <= 100 || length > 250) {
                                progress.classList.remove("good", "care");
                                progress.classList.add("warning");
                            } else if (length <= 160) {
                                progress.classList.remove("good", "warning");
                                progress.classList.add("care");
                            } else {
                                progress.classList.remove("care", "warning");
                                progress.classList.add("good");
                            }
                            progress.style.width = (100 / 250 * length) + "%";
                        }

                        document.addEventListener("DOMContentLoaded", function () {
                            refreshSEODescCounter();
                        });
                        document.getElementById('seoDesc').addEventListener('keyup', function () {
                            refreshSEODescCounter();
                        });
                        document.getElementById('seoDesc').addEventListener('paste', function () {
                            refreshSEODescCounter();
                        });
                    </script>
                </div>                    
            </li>
            <li class='tab'>
                <label for="name">{{Lang.wiki-title}}</label><br>
                <input type="text" name="name" id="name" value="{{ wikiPage.getName() }}" required="required" />
                <label for="date">{{Lang.wiki-date}}</label><br>
                <input placeholder="{{Lang.wiki-date-placeh}}" type="date" name="date" id="date" value="{{wikiPage.getDate()}}" required="required" />
            </li>
            <li class='tab'>
                <h4>{{Lang.wiki-settings-page}}</h4>
                <p>
                    <input {% if wikiPage.getdraft() %}checked{% endif %} type="checkbox" name="draft" id="draft"/>
                    <label for="draft">{{Lang.wiki-do-not-publish}}</label>
                </p>

                <h4>{{Lang.wiki-categories}}</h4>
                {{ categoriesManager.outputAsCheckbox(wikiPage.getId())}}

                <h4>{{Lang.wiki-affect-new-category}}</h4>
                <div class="input-field">
                    <label class="active" for="category-add-label">{{Lang.wiki.categories.categoryName}}</label>
                    <input type="text" name="category-add-label" id="category-add-label"/>
                    <label for="category-add-parentId">{{Lang.wiki.categories.categoryParent}}</label>
                    {{ categoriesManager.outputAsSelectOne(0, "category-add-parentId")}}
                </div>
            </li>
            {% if pluginsManager.isActivePlugin("galerie") %}
            <li class='tab'>
                <h4>{{Lang.wiki-featured-img}}</h4>
                <div id="wiki-page-image-container">
                    <section id="wiki-page-image-fields">
                        <header>{{Lang.wiki-featured-img-select-title}}</header>
                        <label for="wiki-page-image">{{Lang.wiki-featured-img-url}}</label>
                        <input type="text" id="wiki-page-image" name="wiki-page-image" oninput="wikiDisplayPreview()" value="{{ wikiPage.getImg() }}">
                        {{ filemanagerDisplayManagerButton("wiki-page-image", Lang.wiki-featured-img-select) }}
                    </section>
                    <section id="wiki-page-image-preview-container">
                        <header>{{Lang.wiki-featured-img-preview}}</header>
                        <div id="wiki-page-image-preview"></div>
                    </section>
                </div>
            </li>
            {% endif %}
            <li class='tab'>
                <h4>{{Lang.wiki-history}}</h4>
                {% if wikiPage.getId() > 0 %}
                    <div class="wiki-version-info">
                        <p><strong>{{Lang.wiki-version}}:</strong> {{ wikiPage.getVersion() }}</p>
                        <p><strong>{{Lang.wiki-last-modified}}:</strong> {{ wikiPage.getLastModified() }}</p>
                        <p><strong>{{Lang.wiki-modified-by}}:</strong> {{ wikiPage.getModifiedBy() }}</p>
                    </div>
                    
                    <div class="wiki-change-description">
                        <label for="changeDescription">{{Lang.wiki-change-description}}</label>
                        <textarea name="changeDescription" id="changeDescription" placeholder="{{Lang.wiki-change-description-placeholder}}"></textarea>
                    </div>
                    
                    <div class="wiki-history-actions">
                        <a href="{{ ROUTER.generate("admin-wiki-history", {"id": wikiPage.getId()}) }}" class="btn btn-info">
                            <i class="fa fa-history"></i> {{Lang.wiki-view-history}}
                        </a>
                    </div>
                {% else %}
                    <p>{{Lang.wiki.categories.none}}</p>
                {% endif %}
            </li>
        </ul>
    </div>
    <p><button type="submit" class="button">{{ Lang.save }}</button></p>
</form>

<script>
    function wikiDisplayPreview() {
        $url = document.getElementById('wiki-page-image').value;
        document.getElementById('wiki-page-image-preview').innerHTML = '<img src="' + $url + '" alt="' + $url + '" />';
    }

    wikiDisplayPreview();
    
    // Validation du formulaire
    document.getElementById('mainForm').addEventListener('submit', function(e) {
        var pageId = document.querySelector('input[name="id"]').value;
        var changeDescription = document.getElementById('changeDescription');
        
        // Si c'est une page existante (id > 0) et que le champ changeDescription existe
        if (pageId > 0 && changeDescription) {
            if (changeDescription.value.trim() === '') {
                e.preventDefault();
                alert('{{ Lang.wiki-change-description-required }}');
                changeDescription.focus();
                return false;
            }
        }
    });
</script>

<style>
.wiki-version-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.wiki-version-info p {
    margin: 5px 0;
}

.wiki-change-description {
    margin-bottom: 20px;
}

.wiki-change-description label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.wiki-change-description textarea {
    width: 100%;
    min-height: 80px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.wiki-history-actions {
    text-align: center;
    margin-top: 20px;
}
</style>