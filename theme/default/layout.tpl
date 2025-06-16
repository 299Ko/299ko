<!DOCTYPE html>
<html lang="{{ lang.getLocale}}">
	<head>
		{% HOOK.frontHead %}
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>{{ SHOW.titleTag }}</title>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=5"/>
		<meta name="description" content="{{ SHOW.metaDescriptionTag }}"/>
		<link rel="icon" href="{{ SHOW.themeIcon }}"/>
		<base href="{{ SHOW.siteUrl }}/">
		{{ SHOW.linkTags }}
		{{ SHOW.scriptTags }}
		{{ SHOW.showMetas }}
		{% HOOK.endFrontHead %}
	</head>
	<body>
		<div id="container">
			<div id="header">
				<nav id="header_content">
					<button id="mobile_menu" aria-label="{{ Lang.site-menu-label }}"></button>
					<p id="siteName">
						<a href="{{ SHOW.siteUrl }}">{{ SHOW.siteName }}</a>
					</p>
					<ul id="navigation">
						{{ SHOW.mainNavigation }}
						{% HOOK.endMainNavigation %}
					</ul>
				</nav>
			</div>
			<div id="alert-msg">
				{{ SHOW.displayMsg }}
			</div>
			<div id="banner">
				<div id="siteDesc">
					{{ SHOW.siteDesc}}
				</div>
			</div>
			<main id="body">
				{% IF CORE.getConfigVal(hideTitles) == 0 %}
					<div id="pageTitle">
						{% if PAGE_TITLE %}
							<h1>{{ PAGE_TITLE }}</h1>
						{% else %}
							{{ SHOW.mainTitle }}
						{% endif %}
					</div>
				{% ENDIF %}
				<div id="body-page">
					<div id="content" class="{{ SHOW.pluginId }}">
						{{ CONTENT }}
					</div>
					{{ show.displayPublicSidebar() }}
				</div>
			</main>
			<div id="footer">
				<div id="footer_content">
					{% HOOK.footer %}
					<p>
						<a target='_blank' href='https://299ko.ovh'>{{ Lang.site-just-using( ) }}</a>
						-
						{{ Lang.site-theme }}
						{{ SHOW.theme }}
						-
						{% if IS_LOGGED %}
							<a rel="nofollow" href="{{ ROUTER.generate("logout") }}">{{ Lang.core-disconnection }}</a>
							{% if IS_ADMIN %}
								-
								<a rel="nofollow" href="{{ ROUTER.generate("admin") }}">{{ Lang.site-admin }}</a>
							{% endif %}
						{% else %}
							<a rel="nofollow" href="{{ ROUTER.generate("login") }}">{{ Lang.core-connection }}</a>
						{% endif %}
					</p>
					{% HOOK.endFooter %}
				</div>
			</div>
		</div>
		{% HOOK.endFrontBody %}
	</body>
</html>
