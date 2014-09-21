{add_js file='includes/jquery/tabs/jquery.ui.min.js'}
{add_css file='includes/jquery/tabs/tabs.css'}

{literal}
	<script type="text/javascript">
        $(function(){$(".uitabs").tabs();});
	</script>
{/literal}


<div id="fe_tabs" class="uitabs">
    <ul id="tabs">        
        <li><a href="#fe_content"><span>Контент</span></a></li>
        <li><a href="#fe_description"><span>Описание</span></a></li>
        <li><a href="#fe_title"><span>Название, теги</span></a></li>
        <li><a href="#fe_seo"><span>СЕО-параметры</span></a></li>
    </ul>
 
    <form name="fe_con_edit_form">
    <div id="fe_content">
        <div class="fe_tab_content">{wysiwyg name='content' value=$item.content height=370 width='100%'}</div>
    </div>
    <div id="fe_description">
        <div class="fe_tab_content">{wysiwyg name='description' value=$item.description height=370 width='100%'}</div>
    </div>
    <div id="fe_title">
        <div class="fe_tab_content">           
            <span class="fe_title_span">Название:</span>
            <textarea name="title" class="fe_textarea">{$item.title}</textarea>
            <span class="fe_title_span">Теги(через запятую):</span>
            <textarea name="tags" class="fe_textarea">{$item.tags}</textarea>
        </div>
    </div>
    <div id="fe_seo">
        <div class="fe_tab_content">           
            <span class="fe_title_span">Ключевые слова (через запятую):</span>
            <textarea name="meta_keys" class="fe_textarea">{$item.meta_keys}</textarea>
            <span class="fe_title_span">Мета-описание:</span>
            <textarea name="meta_desc" class="fe_textarea">{$item.meta_desc}</textarea>
        </div>
    </div>
    <div class="fe_buttons_block">
    <input type="hidden" name="id" value="{$item.id}">
    <input type="hidden" name="component" value="content">
    <input type="hidden" name="csrf_token" id="csrf_token" value="{csrf_token}">
    <input type="button" onclick="saveForm(document.fe_con_edit_form);" name="fe_apply_button" value="Применить">
    <input type="button" onclick="saveForm(document.fe_con_edit_form, true);" name="fe_save_button" value="Сохранить">
    <input type="button" onclick="hideEditorBlock();" name="fe_cansel_button" value="Отмена">
    </div>
</form>
  </div>    
    
