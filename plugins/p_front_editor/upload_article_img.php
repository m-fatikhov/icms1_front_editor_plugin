<?php
/* * **************************************************************************** */
//                          InstantCMS v1.10.4                                 //
//                      http://www.instantcms.ru/                              //                                                    //
//                  плагин "Фронт-редактор" v.1.0.1                            //
//                        (p_front_editor)                                     //
//                     written by Marat Fatikhov                               //
//                       E-mail: f-marat@mail.ru                               //
//                                                                             //
//                      LICENSED BY GNU/GPL v2                                 //
//                                                                             //
/* * ***************************************************************************** */

define('PATH', $_SERVER['DOCUMENT_ROOT']);

Error_Reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

session_start();

define("VALID_CMS", 1);

include(PATH . '/core/cms.php');
$inCore = cmsCore::getInstance();

cmsCore::loadClass('user');

$inDB = cmsDatabase::getInstance();
$inUser = cmsUser::getInstance();

if (!$inUser->update() || !$inUser->is_admin) {
    cmsCore::halt();
}

//входные данные
$item_id = cmsCore::request('article_id', 'int', 0);
$delete_img = cmsCore::request('delete_img', 'int', 0);

if (!$item_id) {
    echo 'Не получен идентификатор статьи!';
    cmsCore::halt();
}

cmsCore::loadModel('content');
$model = new cms_model_content();


$dir = '/images/photos/';
$img_file = 'article' . $item_id . '.jpg';

if (cmsCore::inRequest("fe_submit")) {
    
    if(!cmsUser::checkCsrfToken()){
        echo 'Не получен Csrf- токен!';
        cmsCore::halt();
    }

    if ($delete_img) {
        @unlink(PATH . $dir . "small/" . $img_file);
        @unlink(PATH . $dir . "medium/" . $img_file);
    }

    // Загружаем класс загрузки фото
    cmsCore::loadClass('upload_photo');
    $inUploadPhoto = cmsUploadPhoto::getInstance();
    // Выставляем конфигурационные параметры
    $inUploadPhoto->upload_dir = PATH . $dir;
    $inUploadPhoto->small_size_w = $model->config['img_small_w'];
    $inUploadPhoto->medium_size_w = $model->config['img_big_w'];
    $inUploadPhoto->thumbsqr = $model->config['img_sqr'];
    $inUploadPhoto->is_watermark = $model->config['watermark'];
    $inUploadPhoto->input_name = 'fe_file_upload';
    $inUploadPhoto->filename = $img_file;
    // Процесс загрузки фото
    $inUploadPhoto->uploadPhoto();
    
    $reset_csrf = true;
}

$file = file_exists(PATH . $dir . 'small/' . $img_file) ? $dir . 'small/' . $img_file : $dir . 'small/' . 'no_image.png';
?>
<form name="fe_img" method="post" action="" enctype="multipart/form-data">
    <div style="width: 30%; float: left;">
        <div>      
            <img src="<?php echo $file; ?>" width="100px" height="100px">
        </div>
        <div>
            <strong style="color: #09c;">Удалить фото:</strong><br>
            <input type="checkbox" name="delete_img" value="1" style="margin-left: 45px;">
        </div>
    </div>
    <div style="width: 60%; float: left;">
        <strong style="color: #09c;">Загрузить фото(прежнее будет удалено):</strong><br>
        <input type="file" name="fe_file_upload" id="fe_file_upload">
        <div style="margin-top: 25px;">
            <input type="hidden" name="article_id" value="<?php echo $item_id; ?>">
            <input type="hidden" name="csrf_token" id="csrf_token" value="">
            <input type="submit" name="fe_submit" value=" Сохранить">
        </div>
    </div>
    <div style="clear: both;"></div>
</form>
<script type="text/javascript">
    var parent_csrf = window.parent.document.getElementById("csrf_token");
<?php 
if($reset_csrf){
    ?>
    parent_csrf.value  = "<?php echo cmsUser::getCsrfToken(); ?>";   
    <?php
}
?>
    window.document.getElementById("csrf_token").value = parent_csrf.value;
</script>
