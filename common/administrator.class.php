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

class administrator {

    private static $instance = null;
    private $email;
    private $pwd;
    private $token;
    private $newPwd;

    ## Constructeur

    public function __construct($email = '', $pwd = '') {
        $this->email = ($email != '') ? $email : @$_SESSION['adminEmail'];
        $this->pwd = ($pwd != '') ? $pwd : @$_SESSION['adminPwd'];
        $this->token = (isset($_SESSION['adminToken'])) ? $_SESSION['adminToken'] : sha1(uniqid(mt_rand(), true));
        $_SESSION['adminToken'] = $this->token;
        $this->newPwd = (isset($_SESSION['newPwd'])) ? $_SESSION['newPwd'] : '';
        $_SESSION['newPwd'] = $this->newPwd;
    }

    ## Retourne l'email

    public function getEmail() {
        return $this->email;
    }

    ## Retourne le nouveau mot de passe

    public function getNewPwd() {
        return $this->newPwd;
    }

    ## Retourne l'instance de l'objet administrator

    public static function getInstance() {
        if (is_null(self::$instance))
            self::$instance = new administrator();
        return self::$instance;
    }

    ## Retourne le jeton

    public static function getToken() {
        $instance = self::getInstance();
        return $instance->token;
    }

    ## Démmare la session

    public function login($email, $pwd) {
        if ($this->encrypt($pwd) == $this->pwd && $email == $this->email) {
            $_SESSION['admin'] = $this->pwd;
            $_SESSION['adminEmail'] = $email;
            $_SESSION['adminPwd'] = $this->pwd;
            return true;
        } else
            return false;
    }

    ## Détruit la session

    public function logout() {
        session_destroy();
    }

    ## Teste l'état de la session

    public function isLogged() {
        if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $this->pwd)
            return false;
        else
            return true;
    }

    ## Teste le statut du jeton

    public function isAuthorized() {
        if (!isset($_REQUEST['token']))
            return false;
        if ($_REQUEST['token'] != $this->token)
            return false;
        return true;
    }

    ## Fonction de cryptage

    public function encrypt($data) {
        return hash_hmac('sha1', $data, KEY);
    }

    ## Regeneration / envoi pwd

    public function makePwd($size = 8) {
        $core = core::getInstance();
        $password = '';
        $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        for ($i = 0; $i < $size; $i++) {
            $password .= ($i % 2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
        }
        $this->newPwd = $password;
        $_SESSION['newPwd'] = $this->newPwd;
        $to = $this->email;
        $from = '299ko@' . $_SERVER['SERVER_NAME'];
        $reply = $from;
        $subject = 'Demande de mot de passe administrateur pour le site ' . $core->getConfigVal('siteName');
        $msg = "Vous venez de faire une demande de changement de mot de passe administrateur.
        
Si vous n'êtes pas l'auteur de cette demande, veuillez ignorer l'étape ci-dessous et supprimer cet email !
Si vous êtes l'auteur de cette demande, veuillez confirmer le changement de mot de passe en cliquant sur le lien ci-dessous :
        
Votre nouveau mot de passe : " . $password . "
Lien de confirmation : " . $core->getConfigVal('siteUrl') . "/admin/index.php?action=lostpwd&step=confirm&token=" . $this->token;
        util::sendEmail($from, $reply, $to, $subject, $msg);
    }

}

?>