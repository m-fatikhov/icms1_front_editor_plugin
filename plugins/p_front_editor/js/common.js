//позиция курсора    
var position = {
    x: 0,
    y: 0
};

//флаг использующийся для переключения с обычного контекстного меню браузера на меню плагина
//контекстное меню плагина показывается через раз
var contextMenu = true;

//устанавливаем обработчик правого клика мыши
$(document).ready(function() {
    $('.component').on('contextmenu', setContextMenu);
});

//обработчик перемещения  мыши, записываем координаты курсора
document.onmousemove = function(e) {

    e = e || window.event;
    //позиция курсора
    position.x = e.clientX;
    position.y = e.clientY;

}

//при потере фокуса скрываем контекстное меню
$('#fe_context_menu_link').blur(function() {
    $('#fe_context_menu').hide();
});


//обработчик правого клика мыши, меняем контекстное меню
function setContextMenu() {

    if (contextMenu) {
        //устанавливаем позицию контекстного меню
        $('#fe_context_menu').css('top', position.y + 'px');
        $('#fe_context_menu').css('left', position.x + 'px');
        //показываем сам меню
        $('#fe_context_menu').show();
        $('#fe_context_menu_link').focus();

        contextMenu = false;//показали меню плагина, переключаем флаг для показа контекст. меню браузера
        return false;
    } else {
        contextMenu = true;//наоборот
        return true;
    }
}

function showEditorBlock(component, item_id) {

    var csrf_token = $("#csrf_token").val();

    $.post('/plugins/p_front_editor/ajax/get_edit_form.php', {component: component, item_id: item_id, csrf_token: csrf_token}, function(data) {
        $('#fe_editor_body').html(data.html);
        $('#fe_context_menu').hide();
        $('#fe_editor_block').show();
    });
}

function hideEditorBlock() {
    $('#fe_editor_block').hide();
    $("#fe_error_msg").html('');
    $("#fe_error_msg").hide();
    //чтобы показать сохраненные изменения, перезагружаем страницу
    window.location.reload();
}

function resizeEditorBlock(act) {
    //разворачиваем окно редактирования
    if (act == 'maximize') {
        $('#fe_editor_block').attr({'class': 'fe_full_screen'});
        $('.fe_maximize').hide();
        $('.fe_minimize').show();
    }
    //сворачиваем окно
    if (act == 'minimize') {
        $('#fe_editor_block').attr({'class': 'fe_small_screen'});
        $('.fe_minimize').hide();
        $('.fe_maximize').show();
    }
}

function saveForm(form, close) {

    var els = getFormElements(form);

    $.post('/plugins/p_front_editor/ajax/save_form.php', {item: els, csrf_token: els.csrf_token}, function(data) {
        //если есть ошибка
        if (data.error) {
            $("#fe_error_msg").html(data.html).fadeIn(1000);
            $("#fe_error_msg").fadeOut(5000);
            return;
        }

        if (close) {//закрыть окно редактора, перезагрузка страницы
            window.location.reload();
        } else {
            //сообщение об успешном изменении
            $("#fe_succes_msg").html('Изменения успешно сохранены!').fadeIn(1000);
            //вносим изменения в поля формы, не закрывая окно
            jQuery.each(els, function(key, val) {
                $("[name=" + key + "]").val(data.item[key]);
            });
            //закрываем див с сообщением
            $("#fe_succes_msg").fadeOut(3000);
            //изменяем  csrf  в дочернем ифрейме для загрузки изображения
            document.getElementById("fe_img_iframe").contentWindow.document.getElementById("csrf_token").value = data.item['csrf_token'];
        }

    });
}

//получает массив элементов формы
function getFormElements(form) {
    var els = form ? form.elements : '', args = {}, el, i = 0;
    while (el = els[i++]) {
        args[el.name] = el.value; //массив элементов  формы
    }

    //перебираем массив элементов формы
    jQuery.each(args, function(key, val) {

        //ифрейм fck-редактора
        var fck_iframe = window.frames[key + "___Frame"];
        if (fck_iframe) {
            //синхронизуем ифрейм fck-редактора с инпутом формы
            args[key] = fck_iframe.contentWindow.frames[0].document.getElementsByTagName('body')[0].innerHTML;
        }

        //синхронизация cke-редактора
        var cke_div = $("#cke_" + key);
        if (cke_div) {
            var frames = document.getElementsByTagName('iframe');
            for (var i = 0; i < frames.length; i++) {
                if (frames[i].title == 'Визуальный редактор текста,' + key) {
                    args[key] = frames[i].contentWindow.document.getElementsByTagName('body')[0].innerHTML;
                }
            }
        }

    });

    return args;
}
