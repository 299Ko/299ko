<div class="wiki-container">
    <div class="wiki-content">
        <h1>{{ category.name }}</h1>
        
        {% if category.description %}
            <div class="wiki-category-description">
                {{ category.description|markdown }}
            </div>
        {% endif %}

        {% if subcategories %}
            <div class="wiki-subcategories">
                <h2>Sous-catégories</h2>
                <div class="wiki-subcategories-grid">
                    {% for subcat in subcategories %}
                        <div class="wiki-subcategory-card">
                            <a href="{{ baseUrl }}?cat={{ subcat.id }}" class="wiki-subcategory-link">
                                <i class="fa-solid fa-folder"></i>
                                <span class="wiki-subcategory-name">{{ subcat.name }}</span>
                                {% if subcat.description %}
                                    <p class="wiki-subcategory-description">{{ subcat.description }}</p>
                                {% endif %}
                            </a>
                        </div>
                    {% endfor %}
                </div>
            </div>
        {% endif %}

        {% if pages %}
            <div class="wiki-category-pages">
                <h2>Pages</h2>
                <div class="wiki-pages-list">
                    {% for page in pages %}
                        <div class="wiki-page-item">
                            <a href="{{ baseUrl }}?page={{ page.filename }}" class="wiki-page-link">
                                <i class="fa-solid fa-file-alt"></i>
                                <span class="wiki-page-title">{{ page.title }}</span>
                            </a>
                        </div>
                    {% endfor %}
                </div>
            </div>
        {% endif %}
    </div>
</div>