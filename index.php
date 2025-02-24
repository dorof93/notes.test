<?php
// $file_name = '';
// $file_text = '';
// if ( ! empty($_GET['path']) ) {
//     $num_file = $_GET['file'];
//     $num_dir = $_GET['dir'];
//     $main_dir = 'notes';
//     $dirs = scandir($main_dir);
//     $dir_name = $dirs[$num_dir];
//     $files = scandir($dir);
//     $file_info = get_file($_GET['path']);
//     $file_name = $file_info['file_name'];
//     $file_text = $file_info['file_text']; file_get_contents( $main_dir . DIRECTORY_SEPARATOR . $dir_name . DIRECTORY_SEPARATOR . $file_name );
// }
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once('file.php');
$file = new File;
if ( ! empty($_POST) ) {
    $file_path = $_POST['dir'] . $_POST['title'] . '.txt';
    $text = $_POST['text'];
    $file->set_path($file_path);
    $file->set_content($text);
    $file->save();
    header("Location:" . $_SERVER['REQUEST_URI']); 
    die();
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/assets/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/style.css') ?>" rel="stylesheet">
    </head>
    <body>
        <form method="POST" class="form">
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
                <?php 
                    $dirs = $file->get_dirs();
                    foreach ($dirs as $dir) {
                        ?>
                        <input type="radio" name="dir" value="<?php echo $dir; ?>"><?php echo $dir; ?>
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
                        <a href="/?path=<?php echo $name; ?>">
                            <?php echo $name; ?>
                        </a>
                    </li>
            <?php } ?>
        </ul>
    </body>
</html>