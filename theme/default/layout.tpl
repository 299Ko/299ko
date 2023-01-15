<!DOCTYPE html>
<html lang="fr">
    <head>
        {% HOOK.ACTION.frontHead %}
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>{{ show.titleTag }}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
        <meta name="description" content="{{ show.metaDescriptionTag }}" />
        <link rel="icon" href="{{ show.themeIcon }}" />
        <link rel="stylesheet" href="{{ show.siteUrl }}/assets/css/pico.min.css" />
        {{ show.linkTags }}
        {{ show.scriptTags }}
        {% HOOK.ACTION.endFrontHead %}
    </head>
    <body>
        <div id="container">
            <div id="header">
                <div id="header_content">
                    <div id="mobile_menu"></div>
                    <p id="siteName"><a href="{{ show.siteUrl }}">{{ show.siteName }}</a></p>
                    <ul id="navigation">
                        {{ show.mainNavigation }}
                        {% HOOK.ACTION.endMainNavigation %}
                    </ul>
                </div>
            </div>
            <div id="alert-msg">
                {{ show.displayMsg }}
            </div>
            <div id="banner"></div>
            <div id="body">
                <div id="content" class="{{ show.pluginId }}">
                    {{ show.mainTitle }}
                    {{ CONTENT }}
                </div>
                {% IF show.isPublicSidebar %}
                    <aside id="sidebar">
                        {{ show.displayPublicSidebar }}
                    </aside>
                {% ENDIF %}
            </div>
        <div id="footer">
            <div id="footer_content">
                {% HOOK.ACTION.footer %}
                <p>
                    <a target='_blank' href='https://github.com/299ko/'>Just using 299ko</a> - Thème {{ show.theme }} - <a rel="nofollow" href="{{ util.urlBuild[ , 1] }}">Administration</a>
                </p>
                {% HOOK.ACTION.endFooter %}
            </div>
        </div>
    </div>
{% HOOK.ACTION.endFrontBody %}
</body>
</html>