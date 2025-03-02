<?php
class Helper {
    public static function check_elem_active ($val, $true_val, $text = '') {
        if ($val == $true_val) {
            return $text;
        }
        return '';
    }
}