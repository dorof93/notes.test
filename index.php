<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
setlocale(LC_ALL, 'C.UTF-8');
const MAIN_DIR = 'notes';
require_once('helper.php');
require_once('file.php');
require_once('tree.php');
$file = new File;
if ( ! empty($_POST) ) {
    if ( ! empty($_POST['dirs']) ) {
        $tree = new Tree;
        $tree->mass_rename($_POST['dirs']);
    } else {
        $file->set_file($_POST);
    }
}
if ( ! empty($_GET['del']) && ! empty($_GET['path']) ) {
    $tree = new Tree;
    $tree->delete($_GET['path']);
}
if ( ! empty($_GET['trash']) ) {
    $file->trash_file();
}
if ( ! empty($_GET['export']) && ! empty($_GET['path']) ) {
    $tree = new Tree;
    $tree->dir_list_before = 'Папка ' . $_GET['path'] . ' (экспортировано ' . date('Y-m-d H:i:s') .')';
    $tree->file_output = "\n\n\n-------- %%path%% --------\n\n%%content%%\n\n\n";
    $tree->show($_GET['path'], 'export');
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title><?php echo $file->get_name() ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Органайзер заметок - <?php echo $file->get_name() ?>">
        <link rel="icon" href="/favicon.ico">
        <link href="/assets/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/style.css') ?>" rel="stylesheet">
    </head>
    <body class="<?php echo $file->get_css_classes() ?>">
        <div class="section">
            <div class="section__container">
                <div class="header">
                    <div class="logo header__logo">
                        <a href="/" class="title link link_big logo__link" title="Создать заметку">
                            /Органайзер/
                        </a>
                    </div>
                    <ul class="menu menu_inline header__menu hide">
                        <li class="menu__item menu__item_show-trash">
                            <span class="menu__link">Показать корзину</span>
                        </li>
                        <li class="menu__item menu__item_hide-trash">
                            <span class="menu__link">Скрыть корзину</span>
                        </li>
                    </ul>
                    <div class="menu-switcher header__menu-switcher">
                        <span class="menu-switcher__line"></span>
                        <span class="menu-switcher__line"></span>
                        <span class="menu-switcher__line"></span>
                    </div>
                </div>
                <div class="workspace">
                    <ul class="tree list">
                        <?php 
                            $files = new Tree;
                            $files->current_path = $file->get_path();
                            $files->dir_list_after = '</ul></li>';
                            $files->dir_output = '<li class="list__item tree__dir">
                                <span class="list__item_bold accordeon" data-acc_id="%%short_path%%">%%short_path%%</span>
                                <ul class="list list__sub">';
                            $files->file_output = '
                                <li class="list__item tree__item">
                                    <a class="link tree__recov list__link link_green confirm" title="Восстановить" data-confirm="Восстановить заметку %%short_path%%?" href="/?path=%%path%%&trash=1">
                                        [+]
                                    </a>
                                    <a class="link tree__trash list__link link_red confirm" title="В корзину" data-confirm="Переместить в корзину заметку %%short_path%%?" href="/?path=%%path%%&trash=1">
                                        [x]
                                    </a>
                                    <a class="link tree__link list__link %%curr_active%%" href="/?path=%%path%%">
                                        %%short_path%%
                                    </a>
                                </li>
                            ';
                            $files->show(MAIN_DIR);
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
                                            <div class="form__success-marker">i</div>
                                            <div class="form__success-text"><?php echo $success ?></div>
                                        </div>
                                    <?php } ?> 
                                </div>
                                <?php unset($_SESSION['success']); ?>
                            <?php } ?>
                        </div>
                        <?php if ( ! empty($_GET['path']) && ( $_GET['path'] == MAIN_DIR ) ) { ?>
                            <!--<div class="form__field form__field_full">
                                <input class="form__text form__input note-form__input" type="text" name="new_dir" placeholder="Создать папку">
                            </div>-->
                            <div class="form__field form__field_full note-form__fieldset">
                                <?php 
                                    $dirs = new Tree;
                                    $dirs->dir_output = '<div class="form__field">
                                        <input class="form__text form__input note-form__input" type="text" name="dirs[%%path%%]" value="%%short_path%%">
                                    </div>
                                    <div class="form__field form__field_h-center">
                                        <a class="link link_red confirm" data-confirm="Удалить папку %%short_path%%?" href="/?path=%%path%%&del=1">[Удалить]</a>
                                        <a class="link" href="/?path=%%path%%&export=1">[Экспорт в txt]</a>
                                    </div>';
                                    $dirs->show($_GET['path']);
                                ?>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="old_path" value="<?php echo $file->get_path() ?>">
                            <div class="form__field">
                                <label class="form__label">Заголовок</label>
                                <input class="form__text form__input note-form__input" type="text" name="title" value="<?php echo $file->get_name() ?>">
                            </div>
                            <div class="form__field form__field_h-center">
                                <?php if ( ! empty($file->get_name()) ) { ?>
                                    <input class="form__checkbox" type="checkbox" name="rename" value="1"> Переместить / переименовать<br />
                                    <a class="link link_red confirm" title="Удалить" data-confirm="Удалить заметку <?php echo $file->get_name() ?>?" href="/?path=<?php echo $file->get_path() ?>&del=1">
                                        Удалить безвозвратно
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="form__field">
                                <textarea class="form__text form__textarea note-form__textarea form__textarea_active-tab" name="text" placeholder="Текст заметки"><?php echo $file->get_content() ?></textarea>
                            </div>
                            <div class="form__field note-form__dirs">
                                <label class="form__label">Папка</label>
                                <div class="form__field form__field_full">
                                    <input class="form__text form__input note-form__input" type="text" name="new_dir" placeholder="Создать папку">
                                </div>
                                <a class="link link_orange" href="/?path=<?php echo MAIN_DIR ?>">Управление папками</a><br /><br />
                                <?php 
                                    $dirs = new Tree;
                                    $dirs->dir_output = '<input type="radio" name="dir" class="form__radio" value="%%path%%" %%checked%%> %%short_path%%<br />';
                                    $dirs->current_path = $file->get_dir();
                                    $dirs->show(MAIN_DIR);
                                ?>
                            </div>
                        <?php } ?>
                        <div class="form__field">
                            <input class="button form__button note-form__button button_disabled" disabled type="submit" value="Сохранить">
                        </div>
                    </form>
                </div>
                <div class="footer">
                    (c) Oleg Dorofeev 2025
                </div>
            </div>
        </div>
        <script src="/assets/script.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/script.js') ?>"></script>
    </body>
</html>