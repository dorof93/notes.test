<?php

class File {
    private $dirs = [];
    private $files = [];
    private $tree = [];
    private $path = '';
    private $content = '';

    public function __construct () {

        $this->set_tree('notes');
        if ( ! empty($_GET['path']) ) {
            $path = $_GET['path'];
            if ( file_exists($path) ) {
                $this->set_path($path);
                $content = $this->get_file_content($path);
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
    public function get_tree () {
        return $this->tree;
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
    private function set_tree ($dir) {
        $dir_files = array_diff(scandir($dir), array('..', '.'));
        foreach ( $dir_files as $name ) {
            $path = $dir . DIRECTORY_SEPARATOR . $name;
            $this->tree[] = $path;
            if ( ! is_dir($path) ) {
                $this->files[] = $path;
            } else {
                $this->dirs[] = $path;
                $this->set_tree($path);
            }
        }
    }
    private function get_file_content($path) {
        if ( is_file($path) ) {
            return file_get_contents($path);
        }
        return '';
    }
}
