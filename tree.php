<?php
class Tree {
    public $current_path = '';
    public $dir_output = '';
    public $file_output = '';
    public $dir_list_before = '';
    public $dir_list_after = '';

    public function show ($dir, $mode = 'show') {
        if ( ! file_exists($dir) || ! is_dir($dir) ) {
            return false;
        }
        $cdir = scandir($dir);
        natcasesort($cdir);

        if ($mode == 'export') {
            $filename = str_replace('/', '--', $dir);
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename=' . $filename . '.txt');
        }

        echo $this->dir_list_before;
        foreach ($cdir as $key => $value) {
            if ( ! in_array($value, array(".","..")) ) {
                $path = $dir . DIRECTORY_SEPARATOR . $value;
                $short_path = Helper::get_short_path($path);
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
                if ( strpos( $output, '%%content%%' ) !== false && file_exists($path) && ! is_dir($path) ) {
                    $content = file_get_contents($path);
                    $output = str_replace('%%content%%', $content, $output);
                }
                echo $output;
                if ( is_dir($path) ) {
                    $this->show($path);
                }
            }
        }
        echo $this->dir_list_after;
        if ($mode == 'export') {
            die();
        }
    }

    public function delete ($path) {
        if (is_file($path)) {
            $file = new File;
            $file->set_path($path);
            $file->del_file();
        }
        if (is_dir($path)) {
            $cdir = scandir($path);
            foreach ($cdir as $key => $value) {
                if ( ! in_array($value, array(".","..")) ) {
                    $this->delete($path . DIRECTORY_SEPARATOR . $value);
                }
            }
            return rmdir($path); 
        }
        return false;
    }

    public function rename ($old_path, $new_path) {
        if ( ! Helper::validate_path($new_path, $old_path) ) {
            return false;
        }
        return rename($old_path, $new_path); 
    }

    public function mass_rename ($dirs) {
        if ( ! empty($dirs) && is_array($dirs) ) {
            foreach ($dirs as $old_path => $new_name) {
                $new_path = MAIN_DIR . DIRECTORY_SEPARATOR . $new_name;
                $new_path = str_replace('//', '/', $new_path);
                if ( $old_path != $new_path ) {
                    $this->rename($old_path, $new_path);
                }
            }
        }
    }

}