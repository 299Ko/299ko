<div class="wiki-home">
    <!-- Texte de présentation -->
    {% IF runPlugin.getConfigVal("homeText") %}
        <div class="wiki-intro">
            {{ runPlugin.getConfigVal("homeText") }}
        </div>
    {% ENDIF %}

    <!-- Dernière activité -->
    {% IF lastActivity && lastActivity.text %}
        <div class="wiki-last-activity">
            <h3><i class="fa-solid fa-clock"></i> Dernière activité</h3>
            <div class="activity-content">
                {{ lastActivity.text }}
            </div>
        </div>
    {% ENDIF %}

    <!-- Table des matières -->
    <div class="wiki-toc">
        {% IF toc %}
            {{ toc }}
        {% ELSE %}
            <div class="wiki-toc-empty">
                <h3><i class="fa-solid fa-sitemap"></i> {{ Lang.wiki-toc-empty-title }}</h3>
                <p>{{ Lang.wiki-toc-empty-message }}</p>
            </div>
        {% ENDIF %}
    </div>
</div>

 