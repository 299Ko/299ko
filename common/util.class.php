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

class util {
    ## Tri un tableau à 2 dimenssions
    ## $data => tableau
    ## $key => index du tableau sur lequel doit se faire le tri
    ## $mode => type de tri ('desc', 'asc', 'num')

    public static function sort2DimArray($data, $key, $mode) {
        if ($mode == 'desc')
            $mode = SORT_DESC;
        elseif ($mode == 'asc')
            $mode = SORT_ASC;
        elseif ($mode == 'num')
            $mode = SORT_NUMERIC;
        $temp = array();
        foreach ($data as $k => $v) {
            $temp[$k] = $v[$key];
        }
        array_multisort($temp, $mode, $data);
        return $data;
    }

    /**
     * Truncate an HTML content and keep only the <p> and <br> tags
     * It save the new lines and dont cut on a word
     * 
     * @param  string $str      Content to truncate
     * @param  int    $length   Number of characters to keep
     * @param  string $add      Text to add after the content if truncated
     * @return string
     */
    public static function cutStr($str, $length, $add = '...') {
        $str = str_replace("<br />", "<br>", $str);
        $no_tags_content = strip_tags($str, '<p><br>');
        $no_tags_content = str_replace("<p>", "<br>", $no_tags_content);
        $no_tags_content = str_replace("</p>", "", $no_tags_content);
        if (strlen($no_tags_content) > $length) {
            return substr($no_tags_content, 0, strpos($no_tags_content, ' ', $length)) . $add;
        } else {
            return $no_tags_content;
        }
    }

    /**
     * Return current page URL
     * 
     * @return string
     */
    public static function getCurrentURL() {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https";
        } else {
            $url = "http";
        }
        $url .= "://";
        $url .= $_SERVER['HTTP_HOST'];
        $url .= $_SERVER['REQUEST_URI'];
        return $url;
    }

## Transforme une chaîne en URL

    public static function strToUrl($str) {
        $str = str_replace('&', 'et', $str);
        if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
            $str = mb_convert_encoding($str, 'UTF-8');
        $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
        $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '$1', $str);
        $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
        return strtolower(trim($str, '-'));
    }

    ## Vérifie si la chaîne est un email valide

    public static function isEmail($email) {
        if (preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}$/", $email))
            return true;
        return false;
    }

    ## Envoie un email

    public static function sendEmail($from, $reply, $to, $subject, $msg) {
        $headers = "From: " . $from . "\r\n";
        $headers .= "Reply-To: " . $reply . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= 'Content-Type: text/plain; charset="utf-8"' . "\r\n";
        $headers .= 'Content-Transfer-Encoding: 8bit';
        if (@mail($to, $subject, $msg, $headers))
            return true;
        return false;
    }

    ## Retourne l'extension d'un fichier présent sur le serveur

    public static function getFileExtension($file) {
        return substr(strtolower(strrchr(basename($file), ".")), 1);
    }

    ## Liste un répertoire et retourne un tableau contenant les fichiers et les dossiers (séparés)

    public static function scanDir($folder, $not = array()) {
        $data['dir'] = array();
        $data['file'] = array();
        foreach (scandir($folder) as $file) {
            if ($file[0] != '.' && !in_array($file, $not)) {
                if (is_file($folder . $file))
                    $data['file'][] = $file;
                elseif (is_dir($folder . $file))
                    $data['dir'][] = $file;
            }
        }
        return $data;
    }

    ## Sauvegarde un tableau dans un fichier au format json

    public static function writeJsonFile($file, $data) {
        if (@file_put_contents($file, json_encode($data), LOCK_EX))
            return true;
        return false;
    }

    ## Retourne un tableau provenant d'un fichier au format json

    public static function readJsonFile($file, $assoc = true) {
        return json_decode(@file_get_contents($file), $assoc);
    }

    ## Upload

    public static function uploadFile($k, $dir, $name, $validations = array()) {
        if (isset($_FILES[$k]) && $_FILES[$k]['name'] != '') {
            $extension = mb_strtolower(util::getFileExtension($_FILES[$k]['name']));
            if (isset($validations['extensions']) && !in_array($extension, $validations['extensions']))
                return 'extension error';
            $size = filesize($_FILES[$k]['tmp_name']);
            if (isset($validations['size']) && $size > $validations['size'])
                return 'size error';
            if (move_uploaded_file($_FILES[$k]['tmp_name'], $dir . $name . '.' . $extension))
                return 'success';
            else
                return 'upload error';
        }
        return 'undefined';
    }

    ## Formate une date

    public static function formatDate($date, $langFrom = 'en', $langTo = 'en') {
        $date = substr($date, 0, 10);
        $temp = preg_split('#[-_;\. \/]#', $date);
        if ($langFrom == 'en') {
            $year = $temp[0];
            $month = $temp[1];
            $day = $temp[2];
        } elseif ($langFrom == 'fr') {
            $year = $temp[2];
            $month = $temp[1];
            $day = $temp[0];
        }
        if ($langTo == 'en')
            $data = $year . '-' . $month . '-' . $day;
        elseif ($langTo == 'fr')
            $data = $day . '/' . $month . '/' . $year;
        return $data;
    }

    /**
     * Build absolute URL with siteURL saved in config.json
     * 
     * @param  string URI
     * @param  bool   is Admin location
     * @return string URL
     */
    public static function urlBuild($uri, $admin = false) {
        if (strpos(strtolower($uri), 'http') === 0 ) {
            // Absolute URL, no need to modify it
            return $uri;
        }
        $base = core::getInstance()->getConfigVal('siteUrl') . '/';
        if ($admin) {
            $base .= 'admin/';
        }
        return $base . ltrim($uri, '/');
    }

}


