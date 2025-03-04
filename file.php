<?php

class File {
    private $path = '';
    private $content = '';

    public function __construct () {
        if ( ! empty($_GET['path']) ) {
            $path = $_GET['path'];
            if ( file_exists($path) && ! is_dir($path) ) {
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
        $save = false;
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
        $file_path = $dir . DIRECTORY_SEPARATOR . $data['title'] . '.txt';
        $redirect = '/?path=' . $file_path;
        if ( $this->validate_path($file_path, $data['old_path']) ) {
            if ( ! empty($data['rename']) ) {
                $this->set_path($data['old_path']);
                $this->delete();
                // $_SESSION['success'][] = 'Заметка успешно переименована / перемещена в другую папку';
            }
            $text = $data['text'];
            $this->set_path($file_path);
            $this->set_content($text);
            $save = $this->save();
        }
        if ( $save ) {
            if ( ! empty($data['trash']) ) {
                $redirect = '/';
            } else {
                $_SESSION['success'][] = 'Заметка успешно сохранена';
            }
        } else {
            unset($_SESSION['success']);
            $_SESSION['errors'][] = 'Ошибка записи. Попробуйте позднее';
        }
        header("Location: " . $redirect); 
        die();
    }
    public function trash_file () {
        $name = $this->get_name();
        if ( $this->is_trash() ) {
            $_SESSION['success'][] = 'Заметка была восстановлена из корзины';
            $title = substr($name, 1); 
        } else {
            $_SESSION['success'][] = 'Заметка была перемещена в корзину';
            $title = '.' . $name;
        }
        return $this->set_file([
            'dir'      => $this->get_dir(),
            'title'    => $title,
            'old_path' => $this->path,
            'text'     => $this->content,
            'rename'   => 1,
            'trash'    => 1,
        ]);
    }
    public function del_file () {
        if ( $this->delete() ) {
            $_SESSION['success'][] = 'Заметка ' . $this->path . ' удалена';
            header("Location: /"); 
        } else {
            $_SESSION['errors'][] = 'Ошибка удаления ' . $this->path . '. Попробуйте позднее';
            header("Location: " . $_SERVER['REQUSET_URI']); 
        }
        die();
    }
    public function get_css_classes () {
        $css_classes = [];
        if ( $this->is_trash() ) {
            $css_classes[] = 'page_trash';
        }
        return implode(' ', $css_classes);
    }
    private function is_trash () {
        $name = $this->get_name();
        if ( ! empty($name) && $name[0] == '.' ) {
            return true;
        }
        return false;
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
        if ( ! isset($data['text']) || ! isset($data['old_path']) ) {
            $_SESSION['errors'][] = 'В запросе не указаны необходимые поля';
        }
        if ( ! empty($_SESSION['errors']) ) {
            return false;
        }
        return true;
    }
    private function validate_path ($new_path, $old_path = '') {
        if ( $old_path != $new_path && file_exists($new_path) ) {
            $_SESSION['errors'][] = 'Элемент с таким названием уже существует';
        }
        $symbols = [
            '?', ':', '..',
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
    private function get_file_content($path) {
        if ( is_file($path) ) {
            return file_get_contents($path);
        }
        return '';
    }
}
