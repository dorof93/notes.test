<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
const MAIN_DIR = 'notes';
require_once('file.php');
require_once('helper.php');
require_once('tree.php');
$file = new File;
if ( ! empty($_POST) ) {
    $file->set_file($_POST);
}
if ( ! empty($_GET['del']) ) {
    $file->del_file();
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Заметки</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/assets/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/style.css') ?>" rel="stylesheet">
        <script src="/assets/script.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/script.js') ?>"></script>
    </head>
    <body>
        <div class="section">
            <div class="section__container">
                <div class="workspace">
                    <ul class="tree list">
                        <li class="list__item">
                            <a class="link list__link link_big" href="/">
                                Создать заметку
                            </a>
                        </li>
                        <?php 
                            $files = new Tree;
                            $files->dir_list_after = '</ul></li>';
                            $files->dir_output = '<li class="list__item link_bold">%%short_path%%<ul class="list list__sub">';
                            $files->file_output = '
                                <li class="list__item">
                                    <a class="link list__link tree__link_red link_confirm" data-confirm="Удалить заметку?" href="/?path=%%path%%&del=1">
                                        [x]
                                    </a>
                                    <a class="link list__link" href="/?path=%%path%%">
                                        %%short_path%%
                                    </a>
                                </li>
                            ';
                            $files->show_dir(MAIN_DIR);
                        ?>
                    </ul>
                    <form method="POST" class="form note-form">
                        <div class="form__field form__field_full">
                            <?php if ( ! empty($_SESSION['errors']) ) { ?>
                                <div class="form__backend-errors note-form__backend-errors">
                                    <?php foreach ($_SESSION['errors'] as $error) { ?>
                                        <div class="form__error form__error_active">
                                            <div class="form__error-marker">!</div>
                                            <div class="form__error-text"><?php echo $error ?></div>
                                        </div>
                                    <?php } ?> 
                                </div>
                                <?php unset($_SESSION['errors']); ?>
                            <?php } ?>
                            <?php if ( ! empty($_SESSION['success']) ) { ?>
                                <div class="form__backend-success note-form__backend-success">
                                    <?php foreach ($_SESSION['success'] as $success) { ?>
                                        <div class="form__success form__success_active">
                                            <div class="form__success-marker">!</div>
                                            <div class="form__success-text"><?php echo $success ?></div>
                                        </div>
                                    <?php } ?> 
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php } ?>
                        </div>
                        <div class="form__field">
                            <label class="form__label">Заголовок</label>
                            <input class="form__text form__input note-form__input" type="text" name="title" value="<?php echo $file->get_name() ?>">
                        </div>
                        <div class="form__field form__field_h-center">
                            <input class="form__checkbox" type="checkbox" name="rename" value="<?php echo $file->get_path() ?>"> Переместить / переименовать
                        </div>
                        <div class="form__field">
                            <textarea class="form__text form__textarea note-form__textarea" name="text" placeholder="Текст заметки"><?php echo $file->get_content() ?></textarea>
                        </div>
                        <div class="form__field">
                            <label class="form__label">Папка</label>
                            <div class="form__field form__field_full">
                                <input class="form__text form__input note-form__input" type="text" name="new_dir" placeholder="Создать папку">
                            </div>
                            <?php 
                                $dirs = new Tree;
                                $dirs->dir_output = '<input type="radio" name="dir" value="%%path%%" %%checked%%> %%short_path%%<br />';
                                $dirs->current_path = $file->get_dir();
                                $dirs->show_dir(MAIN_DIR);
                            ?>
                        </div>
                        <div class="form__field">
                            <input class="button form__button note-form__button" type="submit" value="Сохранить">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>