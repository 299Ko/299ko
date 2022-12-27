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
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="robots" content="noindex"><meta name="googlebot" content="noindex">
        <title>299ko - Mot de passe perdu</title>	
        <?php show::linkTags(); ?>
        <link rel="stylesheet" href="<?php show::siteUrl(); ?>/assets/css/pico.min.css" />
        <link rel="stylesheet" href="styles.css" media="all">
        <?php show::scriptTags(); ?>
        <script type="text/javascript" src="scripts.js"></script>
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