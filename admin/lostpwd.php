<?php
/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');
?>
<!doctype html>
<html lang="fr">
    <head>
        <?php $core->callHook('adminHead'); ?>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>299ko - Administration</title>	
        <link rel="icon" href="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABb2lDQ1BpY2MAACiRdZE7SwNBFIU/EyXigxRaiCik8FUYCApiqbFIE0Sigq8mu9lNhCQuuxsk2Ao2FgEL0cZX4T/QVrBVEARFELHyB/hqJKx3EiEicZbZ+3FmzmXmDPjiWT3nNEYgl3ftRCwaWlhcCgVeaKGXAIMEk7pjTc7MxPl3fN7RoOptWPX6f1/d0ZoyHB0amoXHdMt2hSeE4+uupXhbuFPPJFPCh8LDthxQ+ErpWpWfFaer/K7YnktMgU/1DKV/sfaL9YydEx4S7stlC/rPedRN2oz8/KzUbpk9OCSIESWERoFVsriEpeYls/q+SMU3zZp4dPlbFLHFkSYj3mFRC9LVkGqKbsiXpahy/5unY46OVLu3RaHpyfPe+iGwA+WS530deV75GPyPcJGv+dckp/EP0Us1re8AgptwdlnTtF0434KuBytpJyuSX6bPNOH1FNoXoeMGWparWf2sc3IPcxvyRNewtw8Dsj+48g3mO2f+n7tX+AAAAAlwSFlzAAAOwwAADsMBx2+oZAAAAi1JREFUOMtjZEADN2/dZX3/4WPX06fPo4BcASBm+s/A8FFMRHgzGztbloWZ0XcGfODb9+8Ca9ZteWFg6vpfQ8/uvyYQ6xo5/V+8bO3nT58+S6KrZ0IXuHr1xmdzM6MeX2/Xb+/efWB48/Y9g6uL/U87G/Oes+cuvUVXz4IuYGpi+Pf///9zpKUk8llZWbj+/v3HIC0l/llOVnqmvJzML4IugALGf0BTYJx//8BMRmwKcRlg8P37d76///4xgMz59u07F1DMiIEQACl+9vxlzqSpc98Zmrn9l1Iw+i+taAQOxO6+6Z8ePHxcBVTDhNOAN2/fhdU39fyXkDf4Lylv+F9W2QSMpRQM/4vJ6v0vrWz+f/fewwycgXjg4LHKFas2fGVmYmIAYg6gzz8BPX6NhZn5HhMjk/uatVv+a2upVwCVzsBqwJmzl/RfPHmeISwidJThPwMfMAReCTAwPbxw99QfOWWTSe/evf959erNFJwuACYUhs8fPl3++OHuVSw+vA/EvJ+/fPmIMxZ4ebkZuXi5lbCFDxsj45ovnz6f4uLklMBpgL6+9g05Rbl0kHp0A+7cOf1YXkk+UFNT9T3OWHj1+k14XWP3P3Fp3blAM6SQpAT4RdXrSsqb/t++cy8LZxiIiYqsBKYDCT4+no4duw74PHv6/Agw3n9LSkqYOzvZSAUFeDWpKCvORNaDNXkCNZlcuHQ1+uKla17//v5j1dZW32lmYrCEkZHxKLpaAM7P7FOQafyUAAAAAElFTkSuQmCC">
        <link rel="stylesheet" href="<?php show::siteUrl(); ?>/assets/css/pico.min.css" />
        <link rel="stylesheet" href="styles.css" media="all">
        <?php show::linkTags(); ?>
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="<?php show::siteUrl(); ?>/assets/js/common.js"></script>
        <?php show::scriptTags(); ?>
        <?php $core->callHook('endAdminHead'); ?>	
    </head>
    <body class="login">
        <div class="container">
            <div id="alert-msg">
                <?php show::displayMsg(); ?>
            </div>
            <article id="login">
                <header><h1>Changement de mot de passe</h1></header>
                <?php if ($step == 'form') { ?>
                    <form method="post" action="index.php?action=lostpwd&step=send">   
                        <?php show::adminTokenField(); ?>
                        <p>Entrez l'email administrateur et validez. Si celui-ci est correct, vous recevrez un nouveau mot de passe qu'il faudra confirmer immédiatement via le lien de validation.</p>
                        <p>
                            <label for="adminEmail">Email administrateur</label><br>
                            <input style="display:none;" type="text" name="_email" value="" />
                            <input type="email" id="adminEmail" name="adminEmail" required>
                        </p>
                        <input type="submit" class="button" value="Valider" />
                        </p>
                    </form>
                <?php } elseif ($step == 'send') { ?>
                    <p>Un mot de passe vient d'être envoyé par email, voici les étapes permettant de valider son changement :</p>
                    <ul>
                        <li>Ne quittez pas cette page et ne la rechargez pas</li>
                        <li>Ouvrez l'email reçu, toujours sans quitter cette page (dans un autre onglet)</li>
                        <li>Cliquez sur le lien de validation</li>
                        <li>Connectez-vous avec le nouveau mot de passe</li>
                        <li>Vous pourrez changer le mot de passe dans la section configuration</li>
                    </ul>
                <?php } elseif ($step == 'confirm') { ?>
                    <p>Le mot de passe administrateur a bien été modifié. Vous pouvez maintenant vous connecter.</p>
                    <p><a class="button" href="index.php">Me connecter</a></p>
                <?php } ?>
                <footer>
                    <p class="just_using"><a target="_blank" href="https://github.com/299ko/">Just using 299ko</a>
                    </p></footer>
            </article>
        </div>
    </body>
</html>