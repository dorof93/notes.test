<?php

class File {
    private $dirs = [];
    private $files = [];
    private $path = '';
    private $content = '';

    public function __construct () {

        $this->dirs = $this->get_tree('dirs');
        $this->files = $this->get_tree('files');
        if ( ! empty($_GET['index']) ) {
            $index = $_GET['index'];
            if ( ! empty($this->files[$index]) ) {
                $path = $this->files[$index];
                $content = $this->get_file_content($path);
                $this->set_path($path);
                $this->set_content($content);
            }
        }
    }
    
    public function get_path () {
        return $this->path;
    }
    public function get_name () {
        $pathinfo = pathinfo($this->path);
        return $pathinfo['filename'];
    }
    public function get_content () {
        return $this->content;
    }
    public function get_dirs () {
        return $this->dirs;
    }
    public function get_files () {
        return $this->files;
    }
    public function set_path ($path) {
        $this->path = $path;
    }
    public function set_content ($content) {
        $this->content = $content;
    }
    public function save () {
        if ( ! empty($this->path) ) {
            return file_put_contents($this->path, $this->content);
        }
        return false;
    }
    public function save_dir () {
        if ( ! empty($this->path) ) {
            return mkdir($this->path, 0755);
        }
        return false;
    }
    private function get_tree ($mode = 'all', $path = 'notes', $index = '') {
        $dir = array_diff(scandir($path), array('..', '.'));
        $tree = [];
        foreach ( $dir as $key => $name ) {
            $path = $path . DIRECTORY_SEPARATOR . $name;
            $is_dir = is_dir($path);
            if ( empty($index) ) {
                $index = $key;
            } else {
                $index = $index . '-' . $key;
            }
            if ( $is_dir ) {
                $tree = $this->get_tree($mode, $path, $index);
            }
            switch ( $mode ) {
                case 'dirs':
                    if ( $is_dir ) {
                        $tree[$index] = $path;
                    }
                    break;
                case 'files':
                    if ( ! $is_dir ) {
                        $tree[$index] = $path;
                    }
                    break;
                default:
                    $tree[$index] = $path;
                break;
            }
        }
        return $tree;
    }
    // public function get_dirs ($path = 'notes', $index = 0) {
    //     $dir = array_diff(scandir($path), array('..', '.'));
    //     foreach ( $dir as $key => $name ) {
    //         if ( is_dir($path) ) {
    //             $path = $path . DIRECTORY_SEPARATOR . $name;
    //             $index = $index . '-' . $key;
    //             $dirs[$index] = $path;
    //             $dirs = $this->get_dirs($path, $index);
    //         }
    //     }
    //     return $dirs;
    // }
    // private function get_dir_files ($dir) {

    // }

    // private function get_file_path($path_arr, $path = 'notes') {
    //     if ( ! empty($path_arr) ) {
    //         $names = scandir($path);
    //         $name = $names[array_shift($path_arr)];
    //         $path = $path . DIRECTORY_SEPARATOR . $name;
    //         if ( is_dir($path) ) {
    //             $path = $this->get_file_path($path_arr, $path);
    //         }
    //     }
    //     return $path;
    // }
    private function get_file_content($path) {
        if ( is_file($path) ) {
            return file_get_contents($path);
        }
        return '';
    }
}
