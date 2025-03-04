<?php
class Helper {
    public static function check_elem_active ($val, $true_val, $text = '') {
        if ($val == $true_val) {
            return $text;
        }
        return '';
    }
    public static function validate_path ($new_path, $old_path = '') {
        if ( $old_path != $new_path && file_exists($new_path) ) {
            $_SESSION['errors'][] = 'Элемент с таким названием уже существует';
        }
        $symbols = [
            '?', ':', '..', '//',
        ];
        foreach ($symbols as $symbol) {
            if ( strpos($new_path, $symbol) !== false ) {
                $_SESSION['errors'][] = 'Путь к файлу содержит недопустимые символы';
            }
        }
        if ( ! empty($_SESSION['errors']) ) {
            return false;
        }
        return true;
    }
    public static function get_short_path ($path) {
        $short_path = '';
        $pathinfo = pathinfo($path);
        if ( ! empty($pathinfo['dirname']) ) {
            $short_path = str_replace([MAIN_DIR . '/', MAIN_DIR], '', $pathinfo['dirname']);
        }
        if ( ! empty($pathinfo['filename']) ) {
            $short_path = $short_path . DIRECTORY_SEPARATOR . $pathinfo['filename'];
        }
        return $short_path;
    }
}