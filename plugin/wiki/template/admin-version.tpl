<section class="wiki-version-view">

    <header class="wiki-version-header">
        <h1>{{ Lang.wiki-version-title }} {{ version }} - {{ page.getName() }}</h1>
        
        <div class="wiki-version-info">
            <div class="wiki-version-meta">
                <span class="wiki-version-label">{{ Lang.wiki-version }}:</span>
                <span class="wiki-version-value">{{ page.getVersion() }}</span>
            </div>
            <div class="wiki-version-meta">
                <span class="wiki-version-label">{{ Lang.wiki-last-modified }}:</span>
                <span class="wiki-version-value">{{ util.getDate(page.getLastModified()) }}</span>
            </div>
            <div class="wiki-version-meta">
                <span class="wiki-version-label">{{ Lang.wiki-modified-by }}:</span>
                <span class="wiki-version-value">{{ page.getModifiedBy() }}</span>
            </div>
        </div>
    </header>

    <div class="wiki-version-content">
        <div class="wiki-version-diff">
            <h2>{{ Lang.wiki-content }}</h2>
            <div class="wiki-content-display">
                {{ html_entity_decode(page.getContent(), 3, "UTF-8") }}
            </div>
        </div>

        <div class="wiki-version-intro">
            <h3>{{ Lang.wiki-intro }}</h3>
            <div class="wiki-intro-display">
                {{ html_entity_decode(page.getIntro(), 3, "UTF-8") }}
            </div>
        </div>

        <div class="wiki-version-seo">
            <h3>{{ Lang.wiki-seo }}</h3>
            <div class="wiki-seo-display">
                {{ page.getSEODesc() }}
            </div>
        </div>
    </div>

    <div class="wiki-version-actions">
        {% IF currentPage and version != currentPage.getVersion() %}
            <a onclick="WikiRestoreVersion({{ currentPage.getId() }}, {{ version }})" class="button warning">
                <i class="fa-solid fa-undo"></i> {{ Lang.wiki-restore-this-version }}
            </a>
        {% ENDIF %}
        
        {% IF currentPage %}
            <a href="{{ ROUTER.generate("admin-wiki-history", {"id": currentPage.getId()}) }}" class="button">
                <i class="fa-solid fa-history"></i> {{ Lang.wiki-history }}
            </a>
            
            <a href="{{ ROUTER.generate("admin-wiki-edit-page", {"id": currentPage.getId()}) }}" class="button">
                <i class="fa-solid fa-arrow-left"></i> {{ Lang.wiki-back-to-page }}
            </a>
        {% ENDIF %}
        
        <a href="{{ ROUTER.generate("admin-wiki-list") }}" class="button">
            <i class="fa-solid fa-list"></i> {{ Lang.wiki-back-to-pages }}
        </a>
    </div>
</section> 