{#
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
#}
<!DOCTYPE html>
<html lang="fr">
    <head>
        {% HOOK.ACTION.adminHead %}
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>299ko - Administration</title>	
        <link rel="icon" href="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABb2lDQ1BpY2MAACiRdZE7SwNBFIU/EyXigxRaiCik8FUYCApiqbFIE0Sigq8mu9lNhCQuuxsk2Ao2FgEL0cZX4T/QVrBVEARFELHyB/hqJKx3EiEicZbZ+3FmzmXmDPjiWT3nNEYgl3ftRCwaWlhcCgVeaKGXAIMEk7pjTc7MxPl3fN7RoOptWPX6f1/d0ZoyHB0amoXHdMt2hSeE4+uupXhbuFPPJFPCh8LDthxQ+ErpWpWfFaer/K7YnktMgU/1DKV/sfaL9YydEx4S7stlC/rPedRN2oz8/KzUbpk9OCSIESWERoFVsriEpeYls/q+SMU3zZp4dPlbFLHFkSYj3mFRC9LVkGqKbsiXpahy/5unY46OVLu3RaHpyfPe+iGwA+WS530deV75GPyPcJGv+dckp/EP0Us1re8AgptwdlnTtF0434KuBytpJyuSX6bPNOH1FNoXoeMGWparWf2sc3IPcxvyRNewtw8Dsj+48g3mO2f+n7tX+AAAAAlwSFlzAAAOwwAADsMBx2+oZAAAAi1JREFUOMtjZEADN2/dZX3/4WPX06fPo4BcASBm+s/A8FFMRHgzGztbloWZ0XcGfODb9+8Ca9ZteWFg6vpfQ8/uvyYQ6xo5/V+8bO3nT58+S6KrZ0IXuHr1xmdzM6MeX2/Xb+/efWB48/Y9g6uL/U87G/Oes+cuvUVXz4IuYGpi+Pf///9zpKUk8llZWbj+/v3HIC0l/llOVnqmvJzML4IugALGf0BTYJx//8BMRmwKcRlg8P37d76///4xgMz59u07F1DMiIEQACl+9vxlzqSpc98Zmrn9l1Iw+i+taAQOxO6+6Z8ePHxcBVTDhNOAN2/fhdU39fyXkDf4Lylv+F9W2QSMpRQM/4vJ6v0vrWz+f/fewwycgXjg4LHKFas2fGVmYmIAYg6gzz8BPX6NhZn5HhMjk/uatVv+a2upVwCVzsBqwJmzl/RfPHmeISwidJThPwMfMAReCTAwPbxw99QfOWWTSe/evf959erNFJwuACYUhs8fPl3++OHuVSw+vA/EvJ+/fPmIMxZ4ebkZuXi5lbCFDxsj45ovnz6f4uLklMBpgL6+9g05Rbl0kHp0A+7cOf1YXkk+UFNT9T3OWHj1+k14XWP3P3Fp3blAM6SQpAT4RdXrSsqb/t++cy8LZxiIiYqsBKYDCT4+no4duw74PHv6/Agw3n9LSkqYOzvZSAUFeDWpKCvORNaDNXkCNZlcuHQ1+uKla17//v5j1dZW32lmYrCEkZHxKLpaAM7P7FOQafyUAAAAAElFTkSuQmCC"> 
        <link rel="stylesheet" href="../assets/css/pico.min.css" media="all">
        <link rel="stylesheet" href="styles.css" media="all">
        {{ show.linkTags }}
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="../assets/js/common.js"></script>
        {{ show.scriptTags }}
        {% HOOK.ACTION.endAdminHead %}	
    </head>
    <body>
        <div id="container">
            <div id="header">
                <nav id="header_content">	
                    <ul>
                        <li><a href="javascript:" id="open_nav"><i class="bi bi-list"></i></a></li>
                        <li><span id="page-title">{{ runPlugin.getInfoVal[name] }}</span></li>
                    </ul>
                    <ul id="header-site-links">
                        <li><a target="_blank" href="../">Voir le site</a></li>
                        <li><a href="index.php?action=logout&token={{ administrator.getToken }}">Déconnexion</a></li>
                    </ul>
            </div>
            <div id="alert-msg">
                {{ show.displayMsg }}
            </div>
            <div id="content_mask">
                <div id="content" class="{{ show.pluginId }}-admin">
                    <aside>
                        <nav id="sidebar">
                            <ul id="navigation">
                                {{ show.adminNavigation }}
                                <li class="site"><a href="index.php?action=logout&token={{ administrator.getToken }}">Déconnexion</a></li>
                                <li class="site"><a target="_blank" href="../">Voir le site</a></li> 
                            </ul>
                            <p class="just_using">
                                <a target="_blank" href="https://github.com/299ko/">Just using 299ko {{ VERSION }}</a>
                            </p>
                        </nav>
                    </aside>
                    <div id="links-panel">
                        {% IF runPlugin.getParamTemplate %}
                            <a title="Paramètres" data-fancybox id="param_link" href="#" data-src="#param_panel"><i class="bi bi-sliders"></i></a>
                            <div id="param_panel">
                                <div class="content">
                                    <h2>Paramètres</h2>
                                    {% INCLUDE runPlugin.getParamTemplate %}
                                </div>
                            </div>
                        {% ENDIF %}
                        {% IF runPlugin.getHelpTemplate %}
                            <a title="Aide" data-fancybox id="help_link" href="#" data-src="#help_panel"><i class="bi bi-info-lg"></i></a>
                            <div id="help_panel">   
                                <div class="content">
                                    <h2>Aide</h2>
                                    {% INCLUDE runPlugin.getHelpTemplate %}
                                </div>
                            </div>
                        {% ENDIF %}
                        {% HOOK.ACTION.adminToolsTemplates[runPlugin.getName] %}
                    </div>
                    {{ CONTENT }}
                </div>
            </div>
        </div>
        {% HOOK.ACTION.endAdminBody %}
    </body>
</html>

