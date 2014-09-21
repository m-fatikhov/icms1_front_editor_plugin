<?php
/*******************************************************************************/
//                          InstantCMS v1.10.4                                 //
//                      http://www.instantcms.ru/                              //                                                    //
//                  плагин "Фронт-редактор" v.1.0.0                            //
//                        (p_front_editor)                                     //
//                     written by Marat Fatikhov                               //
//                       E-mail: f-marat@mail.ru                               //
//                                                                             //
//                      LICENSED BY GNU/GPL v2                                 //
//                                                                             //
/********************************************************************************/

define('PATH', $_SERVER['DOCUMENT_ROOT']);
include(PATH.'/core/ajax/ajax_core.php');

//входные данные
$component = cmsCore::request('component', 'str', '');
$item_id = cmsCore::request('item_id', 'int', 0);

if(!cmsUser::checkCsrfToken()){
    cmsCore::jsonOutput(array('error' => true, 'html' => ' Не получен csrf-токен!')); 
    cmsCore::halt(); 
}

if(!$inUser->is_admin){
    cmsCore::jsonOutput(array('error' => true, 'html' => ' Редактирование доступно только админам!')); 
    cmsCore::halt(); 
}

if(!$component){
    cmsCore::jsonOutput(array('error' => true, 'html' => ' Не получен идентификатор компонента!')); 
    cmsCore::halt();
}

if(!$item_id){
    cmsCore::jsonOutput(array('error' => true, 'html' => ' Не получен идентификатор записи!')); 
    cmsCore::halt();
}

//библиотека тегов
cmsCore::includeFile('/core/lib_tags.php');

//шаблон формы редактирования
$template = 'p_front_editor_'.$component.'.tpl';

//получаем запись для редактирования
$item = $inDB->get_fields('cms_'.$component, "id = {$item_id}", '*');
        // теги статьи
        if($item){
            $target = ($component == 'blog_posts') ? 'blogpost' : $component;
            $item['tags']  = cmsTagLine("{$target}", $item_id, false);
        }

if(!$item){
    cmsCore::jsonOutput(array('error' => true, 'html' => ' Не получена запись для редактирования!')); 
    cmsCore::halt();
}

//html-код формы
$html = '';
ob_start();

$smarty = cmsPage::initTemplate('plugins', $template);
$smarty ->assign('item', $item);
$smarty ->assign('csrf_token', cmsUser::getCsrfToken());
if($component == 'blog_posts'){
    //получаем код панелей bbcode и смайлов
    $bb_toolbar = cmsPage::getBBCodeToolbar('message',true, 'blogs', 'blog_post', $item['id']);
    $smilies    = cmsPage::getSmilesPanel('message');
    $smarty ->assign('smilies', $smilies);
    $smarty ->assign('bb_toolbar', $bb_toolbar);
}
$smarty ->display($template);

$html = ob_get_clean();


cmsCore::jsonOutput(array('error' => false, 'html' => $html));

?>

