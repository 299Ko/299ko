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
        <title>299ko - Connexion</title>	
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
                <header><h1>Connexion</h1></header>
                    <form method="post" action="index.php?action=login">   
                        <?php show::adminTokenField(); ?>          
                        <p>
                            <label for="adminEmail">Email</label><br>
                            <input style="display:none;" type="text" name="_email" value="" />
                            <input type="email" id="adminEmail" name="adminEmail" required>
                        </p>
                        <p><label for="adminPwd">Mot de passe</label>
                            <input type="password" id="adminPwd" name="adminPwd" required></p>
                        <p>
                            <input type="button" class="button alert" value="Quitter" rel="<?php echo $core->getConfigVal('siteUrl'); ?>" />
                            <input type="submit" class="button" value="Valider" />
                        </p>
                        </p>
                    </form>
                <footer>
                    <p><a href="index.php?action=lostpwd&token=<?php echo $administrator->getToken(); ?>">Mot de passe perdu ?</a></p>
                    <p class="just_using"><a target="_blank" href="https://github.com/299ko/">Just using 299ko</a>
                </footer>
            </article>
        </div>
    </body>
</html>