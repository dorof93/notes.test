<?php

class File {
    private $path = '';
    private $content = '';

    public function __construct () {
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
        if ( ! empty($pathinfo['filename']) ) {
            return $pathinfo['filename'];
        }
        return '';
    }
    public function get_dir () {
        $pathinfo = pathinfo($this->path);
        if ( ! empty($pathinfo['dirname']) ) {
            return $pathinfo['dirname'];
        }
        return '';
    }
    public function get_content () {
        return $this->content;
    }
    public function set_path ($path) {
        $this->path = $path;
    }
    public function set_content ($content) {
        $this->content = $content;
    }
    public function set_file ($data) {
        if ( ! $this->validate_data($data) ) {
            header("Location:" . $_SERVER['REQUSET_URI']); 
            die();
        }
        if ( ! empty($data['new_dir']) ) {
            $dir = MAIN_DIR . DIRECTORY_SEPARATOR . $data['new_dir'];
            $this->save_dir($dir);
        } else {
            $dir = $data['dir'];
        }
        if ( ! empty($data['rename']) ) {
            $this->set_path($data['rename']);
            $this->delete();
        }
        $file_path = $dir . DIRECTORY_SEPARATOR . $data['title'] . '.txt';
        $text = $data['text'];
        $this->set_path($file_path);
        $this->set_content($text);
        $save = $this->save();
        if ( $save ) {
            $_SESSION['success'][] = 'Заметка успешно сохранена';
        } else {
            $_SESSION['errors'][] = 'Ошибка записи. Попробуйте позднее';
        }
        header("Location:/?path=" . $file_path); 
        die();
    }
    public function del_file () {
        if ( $this->delete() ) {
            $_SESSION['success'][] = 'Заметка ' . $this->path . ' удалена';
            header("Location:/"); 
        } else {
            $_SESSION['errors'][] = 'Ошибка удаления ' . $this->path . '. Попробуйте позднее';
            header("Location:" . $_SERVER['REQUSET_URI']); 
        }
        die();
    }
    private function save () {
        if ( ! empty($this->path) ) {
            return file_put_contents($this->path, $this->content);
        }
        return false;
    }
    private function delete () {
        if ( ! empty($this->path) ) {
            return unlink($this->path);
        }
        return false;
    }
    private function save_dir ($path) {
        if ( ! empty($path) ) {
            return mkdir($path, 0755);
        }
        return false;
    }
    private function validate_data ($data) {
        if ( empty($data['dir']) && empty($data['new_dir']) ) {
            $_SESSION['errors'][] = 'Пожалуйста, выберите папку';
        }
        if ( empty($data['title']) ) {
            $_SESSION['errors'][] = 'Пожалуйста, введите корректное название';
        }
        if ( ! empty($_SESSION['errors']) ) {
            return false;
        }
        if ( ! isset($data['text']) ) {
            return false;
        }
        return true;
    }
    private function get_file_content($path) {
        if ( is_file($path) ) {
            return file_get_contents($path);
        }
        return '';
    }
}
