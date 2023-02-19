<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

class FilesManager {

    /**
     * JSON File
     */
    const FILE = DATA_PLUGIN . 'filesmanager/upload.json';
    
    /**
     * Path to root uploaded files
     */
    const PATH = UPLOAD;

    /**
     * Get all files metasdata, as an associative array where key is uploaded
     * file timestamp
     *
     * @return array
     */
    public static function getFilesMetas() {
        $files = util::readJsonFile(self::FILE, true);
        if (!$files) {
            $files = [];
        }
        foreach ($files as $id => &$file) {
            $file['url'] = util::urlBuild('data/upload/' . $file['year'] . '/' . $file['month'] . '/'
                    . $file['filename']);
            $file['path'] = UPLOAD . $file['year'] . '/' . $file['month'] . '/' . $file['filename'];
            $mime = mime_content_type($file['path']);
            $genMime = explode('/', $mime)[0];
            $file['isPicture'] = ($genMime === 'image');
        }
        return $files;
    }

    /**
     * Add a file into the manager
     *
     * File will be automatically saved
     * Param must be an associative array
     *
     * @param array $infos
     */
    public static function addFile($infos) {
        $metas = self::getFilesMetas();
        $metas[$infos['id']] = $infos;
        self::saveJsonFile($metas);
    }

    /**
     * Delete a file with his ID
     *
     * His ID is timestamp when he was uploaded
     *
     * @param int $id
     */
    public static function deleteFile($id) {
        $files = self::getFilesMetas();
        $fileName = $files[$id]['path'] . $files[$id]['name'] . '.' . $files[$id]['ext'];
        if (file_exists($fileName)) {
            unset($files[$id]);
            self::saveJsonFile($files);
            unlink($fileName);
        }
    }

    /**
     * Save JSON Manager file
     *
     * Param must be an associative array, where ID is timestamp
     * Return true if JSON file is correctly saved
     *
     * @param array $rawContent
     * @return bool
     */
    protected static function saveJsonFile($rawContent) {
        return file_put_contents(self::FILE, json_encode($rawContent, JSON_PRETTY_PRINT));
    }

    protected static function createFileDirectories() {
        $year = date('Y');
        $yearPath = self::PATH . $year;
        if (!is_dir($yearPath)) {
            mkdir($yearPath);
        }
        $month = date('m');
        $monthPath = $yearPath . '/' . $month;
        if (!is_dir($monthPath)) {
            mkdir($monthPath);
        }
        return $monthPath . '/';
    }

    public static function uploadFile($arrayName) {
        $file = $_FILES[$arrayName];
        $fileName = util::strToUrl(pathinfo($file['name'], PATHINFO_FILENAME));
        $fileExt  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $path = self::createFileDirectories();
        $time = time();
        if (move_uploaded_file($file['tmp_name'], $path . $fileName . '-' . $time . '.' . $fileExt)) {
            $arr = [
                'year' => (string)date('Y'),
                'month' => (string)date('m'),
                'filename' => $fileName . '-' . $time . '.' . $fileExt,
                'originalName' => $fileName . '.' . $fileExt,
                'id' => $time
            ];
            self::addFile($arr);
            return true;
        } else {
            return false;
        }
    }

}
