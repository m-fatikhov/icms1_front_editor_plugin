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
        <li><a href="#fe_title"><span>Название, теги</span></a></li>
        <li><a href="#fe_music"><span>Настроение, музыка</span></a></li>
    </ul>

<form name="fe_con_edit_form">
    <div id="fe_content">
        <div class="fe_tab_content">
            <div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
				{$smilies}				
                <div class="cm_editor"><textarea rows="15" class="ajax_autogrowarea" name="content" id="message">{$item.content}</textarea></div>
                <div style="margin-top:12px;margin-bottom:15px;" class="hinttext">
                    <strong>Важно:</strong> если текст поста достаточно большой, не забудьте разделить его на две части (анонс и основное тело),<br/>
                    <a href="javascript:addTagCut('message');" class="ajaxlink">вставив разделитель</a> между ними.
                </div>
        </div>
    </div>
    <div id="fe_title">
        <div class="fe_tab_content">           
            <span class="fe_title_span">Название:</span>
            <textarea name="title" class="fe_textarea">{$item.title}</textarea>
            <span class="fe_title_span">Теги(через запятую):</span>
            <textarea name="tags" class="fe_textarea">{$item.tags}</textarea>
        </div>
    </div>
    <div id="fe_music">
        <div class="fe_tab_content">           
            <span class="fe_title_span">Настроение:</span>
            <textarea name="feel" class="fe_textarea" >{$item.feel}</textarea>
            <span class="fe_title_span">Музыка:</span>
            <textarea name="music" class="fe_textarea" maxlength="100">{$item.music}</textarea>
        </div>
    </div>    
    <div class="fe_buttons_block">
    <input type="hidden" name="id" value="{$item.id}">
    <input type="hidden" name="component" value="blog_posts">
    <input type="hidden" name="csrf_token" id="csrf_token" value="{csrf_token}">
    <input type="button" onclick="saveForm(document.fe_con_edit_form);" name="fe_apply_button" value="Применить">
    <input type="button" onclick="saveForm(document.fe_con_edit_form, true);" name="fe_save_button" value="Сохранить">
    <input type="button" onclick="hideEditorBlock();" name="fe_cansel_button" value="Отмена">
    </div>
</form>
</div>