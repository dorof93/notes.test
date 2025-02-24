<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once('file.php');
require_once('helper.php');
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
        <title></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/assets/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/style.css') ?>" rel="stylesheet">
        <script src="/assets/script.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/script.js') ?>"></script>
    </head>
    <body>
        <form method="POST" class="form">
            <?php if ( ! empty($_SESSION['errors']) ) { ?>
                <div class="form__field form__field_full form__backend-errors">
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
                <div class="form__field form__field_full form__backend-success">
                    <?php foreach ($_SESSION['success'] as $success) { ?>
                        <div class="form__success form__success_active">
                            <div class="form__success-marker">!</div>
                            <div class="form__success-text"><?php echo $success ?></div>
                        </div>
                    <?php } ?> 
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php } ?>
            <div class="form__field form__field_full">
                <label class="form__label">Заголовок</label>
                <input class="form__text form__input" type="text" name="title" value="<?php echo $file->get_name() ?>">
            </div>
            <div class="form__field">
                <label class="form__label">Текст</label>
                <textarea class="form__text form__textarea" name="text"><?php echo $file->get_content() ?></textarea>
            </div>
            <div class="form__field">
                <label class="form__label">Папка</label>
                <div class="form__field form__field_full">
                    <input class="form__text form__input" type="text" name="new_dir" placeholder="Создать папку">
                </div>
                <?php 
                    $dirs = $file->get_dirs();
                    foreach ($dirs as $dir) {
                        ?>
                        <input type="radio" name="dir" value="<?php echo $dir; ?>" <?php echo Helper::checkbox_value($dir, $file->get_dir()) ?>>
                        <?php echo $dir; ?><br />
                <?php } ?>
            </div>
            <div class="form__field">
                <input class="button form__button" type="submit" value="Сохранить">
            </div>
        </form>
        <ul class="tree list">
            <?php 
                $tree = $file->get_files();
                foreach ($tree as $name) {
                    ?>
                    <li class="list__item">
                        <a class="link list__link list__link_red link_confirm" data-confirm="Удалить заметку?" href="/?path=<?php echo $name; ?>&del=1">
                            [Удалить]
                        </a>
                        <a class="link list__link" href="/?path=<?php echo $name; ?>">
                            <?php echo $name; ?>
                        </a>
                    </li>
            <?php } ?>
        </ul>
    </body>
</html>