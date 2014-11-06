<?php
/*******************************************************************************/
//                          InstantCMS v1.10.4                                 //
//                      http://www.instantcms.ru/                              //                                                    //
//                  плагин "Фронт-редактор" v.1.0.1                            //
//                        (p_front_editor)                                     //
//                     written by Marat Fatikhov                               //
//                       E-mail: f-marat@mail.ru                               //
//                                                                             //
//                      LICENSED BY GNU/GPL v2                                 //
//                                                                             //
/********************************************************************************/
class p_front_editor extends cmsPlugin {
    
    
//=============================================================================//


public function __construct(){

        parent::__construct();

        // Информация о плагине

        $this->info['plugin']           = 'p_front_editor';
        $this->info['title']            = 'Фронт-редактор';
        $this->info['description']      = 'Позволяет редактировать контент с фронтэнда без перезагрузки страницы';
        $this->info['author']           = 'Marat Fatikhov';
        $this->info['version']          = '1.0.0';        

        // Настройки по-умолчанию
        
        
        // События, которые будут отлавливаться плагином

        $this->events[]                 = 'GET_ARTICLE'; 
        $this->events[]                 = 'GET_POST';
    }

// ==================================================================== //

    /**
     * Процедура установки плагина
     * @return bool
     */
    public function install(){        

        return parent::install();

    }

// ==================================================================== //

    /**
     * Процедура обновления плагина
     * @return bool
     */
    public function upgrade(){               
         
        return parent::upgrade();

    }

// ==================================================================== //

     /**
     * Обработка событий
     * @param string $event
     * @param mixed $item
     * @return mixed
     */
    public function execute($event, $item){

        $inUser = cmsUser::getInstance();
        
        parent::execute();
        
        //доступ только админам
        if(!$inUser->is_admin){
            return $item;
        }

        switch ($event){
            case 'GET_ARTICLE': 
                $code = $this->getAddedCode('content', $item['id']);
                //если есть разбивка на страницы, добавляем код в каждую(кроме последней)
                $item['content'] = preg_replace('/({pagebreak})/iu', $code.' $1 ', $item['content']);
                //добавляем код в конец контента(последняя страница)
                $item['content'] .= $this->getAddedCode('content', $item['id']); break;
            case 'GET_POST':
                $item['content_html'] .= $this->getAddedCode('blog_posts', $item['id']); break;
        }

        return $item;

    }
    
    // ==================================================================== //
    private function getAddedCode($component, $item_id){
        
        $html = '<div id="fe_plugin_block">'
                . '<div id="fe_context_menu">'
                . '<a href="javascript:void(0);" id="fe_context_menu_link" onclick="showEditorBlock(\''.$component.'\', '.$item_id.');">Редактировать</a>'
                . '</div>'
                . '<div id="fe_editor_block" class="fe_small_screen">'               
                . '<span class="fe_cancel" onclick="hideEditorBlock();"></span>'
                . '<span class="fe_maximize" onclick="resizeEditorBlock(\'maximize\');"></span>'
                . '<span class="fe_minimize" onclick="resizeEditorBlock(\'minimize\');"></span>'
                . '<div id="fe_error_msg"></div>'
                . '<div id="fe_succes_msg"></div>'
                . '<div id="fe_editor_body"></div>'
                . '</div>'
                . '</div>'
                .'<input type="hidden" name="csrf_token" id="csrf_token" value="'.cmsUser::getCsrfToken().'">'
                . '<script type="text/javascript" src="/plugins/p_front_editor/js/common.js"></script>'
                . '<link href="/plugins/p_front_editor/css/styles.css" type="text/css" rel="stylesheet">';
        return $html;
    }
}
?>