<?php
class Tree {
    public $current_path = '';
    public $dir_output = '';
    public $file_output = '';
    public $dir_list_before = '';
    public $dir_list_after = '';

    public function show_dir ($dir) {
        $cdir = scandir($dir);
        natcasesort($cdir);
        echo $this->dir_list_before;
        foreach ($cdir as $key => $value) {
            if ( ! in_array($value, array(".","..")) ) {
                $path = $dir . DIRECTORY_SEPARATOR . $value;
                $short_path = $this->get_short_path($path);
                $checked = Helper::check_elem_active($path, $this->current_path, 'checked="cheked"');
                $curr_active = Helper::check_elem_active($path, $this->current_path, 'active');
                if ( is_dir($path) ) {
                    $output = $this->dir_output;
                } else {
                    $output = $this->file_output;
                }
                $output = str_replace('%%path%%', $path, $output);
                $output = str_replace('%%short_path%%', $short_path, $output);
                $output = str_replace('%%checked%%', $checked, $output);
                $output = str_replace('%%curr_active%%', $curr_active, $output);
                echo $output;
                if ( is_dir($path) ) {
                    $this->show_dir($path);
                }
            }
        }
        echo $this->dir_list_after;
    }

    public function get_short_path ($path) {
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