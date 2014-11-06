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
include(PATH . '/core/ajax/ajax_core.php');

//входные данные
$item = cmsCore::request('item', 'array', array());

if(!cmsUser::checkCsrfToken()){
    cmsCore::jsonOutput(array('error' => true, 'html' => ' Не получен csrf-токен!')); 
    cmsCore::halt(); 
}

if (!$inUser->is_admin) {
    cmsCore::jsonOutput(array('error' => true, 'html' => ' Редактирование доступно только админам!'));
    cmsCore::halt();
}

if (!$item) {
    cmsCore::jsonOutput(array('error' => true, 'html' => '  Не получен массив данных для редактирования!'));
    cmsCore::halt();
}
//проверяем есть ли запись
$is_exists = $inDB->rows_count("cms_{$item['component']}", "id = {$item['id']}");

if (!$is_exists) {
    cmsCore::jsonOutput(array('error' => true, 'html' => '  Запись с данным идентификатором не существует!'));
    cmsCore::halt();
}

if ($item['component'] == 'blog_posts') {
    
    cmsCore::loadClass('blog');
    $inBlog = cmsBlogs::getInstance();
    
    $inBlog->updatePost($item['id'], $item);
    $succes = true;
} elseif ($item['component'] == 'content') {

//библиотека тегов
    cmsCore::includeFile('/core/lib_tags.php');

    cmsInsertTags($item['tags'], $item['component'], $item['id']);

//экранируем кавычки
    $item = $inDB->escape_string($item);


//обновляем запись
$succes = $inDB->update("cms_{$item['component']}", $item, $item['id']);

}

//обновляем и передаем защитный токен
$item['csrf_token'] = cmsUser::getCsrfToken();

if ($succes) {
    cmsCore::jsonOutput(array('error' => false, 'item' => $item));
} else {
    cmsCore::jsonOutput(array('error' => true, 'html' => '  Произошла ошибка при обновлении данных в БД!'));
}
?>

