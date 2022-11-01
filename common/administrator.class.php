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

/**
 * Administrator is the class relative to the admin
 * Use it as a singleton
 */
class administrator {

    /**
     * @var self Instance
     */
    private static $instance = null;
    
    /**
     * @var string Admin email
     */
    private $email;
    
    /**
     * @var string Encrypted Admin Password
     */
    private $pwd;
    
    /**
     * @var string Token (stored in session) to verify user is the Admin
     */
    private $token;
    
    /**
     * @var string new password if Admin ask to reset him
     */
    private $newPwd;

    /**
     * Build the Administrator instance
     * @param string Admin email
     * @param string Admin password
     */
    public function __construct($email = '', $pwd = '') {
        $this->email = ($email != '') ? $email : @$_SESSION['adminEmail'];
        $this->pwd = ($pwd != '') ? $pwd : @$_SESSION['adminPwd'];
        $this->token = $_SESSION['adminToken'] ?? sha1(uniqid(mt_rand(), true));
        $_SESSION['adminToken'] = $this->token;
        $this->newPwd = $_SESSION['newPwd'] ?? '';
        $_SESSION['newPwd'] = $this->newPwd;
    }

    /**
     * Return the Admin Email
     * @return string Admin email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Return the new password admin
     * @return string New password
     */
    public function getNewPwd() {
        return $this->newPwd;
    }

    /**
     * Return the administrator singleton instance
     * @return self Object instance
     */
    public static function getInstance() {
        if (is_null(self::$instance))
            self::$instance = new administrator();
        return self::$instance;
    }

    /**
     * Return the current Admin Token
     * @return string Admin token
     */
    public static function getToken() {
        $instance = self::getInstance();
        return $instance->token;
    }

    /**
     * Start the admin login and store datas in session
     * @param string admin email
     * @param string admin password
     * @return boolean true if logged in
     */
    public function login($email, $pwd) {
        if ($this->encrypt($pwd) == $this->pwd && $email == $this->email) {
            $_SESSION['admin'] = $this->pwd;
            $_SESSION['adminEmail'] = $email;
            $_SESSION['adminPwd'] = $this->pwd;
            return true;
        } else
            return false;
    }

    /**
     * Logout admin by destroying the session
     */
    public function logout() {
        session_destroy();
    }

    /**
     * Check if current user is the admin by checking session datas
     * @return boolean true if logged in
     */
    public function isLogged() {
        if (!isset($_SESSION['admin']) || $_SESSION['admin'] != $this->pwd)
            return false;
        else
            return true;
    }

    /**
     * Check token in POST and GET
     * @return boolean true if authorized
     */
    public function isAuthorized() {
        if (!isset($_REQUEST['token']))
            return false;
        if ($_REQUEST['token'] != $this->token)
            return false;
        return true;
    }

    /**
     * Used to encrypt admin password
     * @param string Content to encrypt
     * @return string Encrypted content
     */
    public function encrypt($data) {
        return hash_hmac('sha1', $data, KEY);
    }

    /**
     * Create a new password for the admin user and send it by the admin email
     * @param int size of password
     */
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