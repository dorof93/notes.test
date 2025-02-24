<?php
class Helper {
    public static function checkbox_value ($val, $true_val) {
        if ($val == $true_val) {
            return 'checked="cheked"';
        }
        return false;
    }
}