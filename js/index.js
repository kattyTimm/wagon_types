var _index = {   // interface to index.js
   is_valid_user:        function ()                { return is_valid_user(); },
    validate_ok:          function (ead, save_ead)   { validate_ok(ead, save_ead); },

    app_clnf:             function ()                { return app_clnf(); },
    app_ead:              function ()                { return app_ead(); },
    app_orgs:             function ()                { return app_orgs(); },
    app_orgsnm:           function ()                { return app_orgsnm(); },
    
    app_who:              function ()                { return app_who(); },
    app_pdfViewerType:    function ()                { return app_pdfViewerType(); },
    
    //view_data_changed:    function (orgs_to)         { view_data_changed(orgs_to); },   // for after fm_loader
    
    fill_sideout:         function ()                { fill_sideout(); },
  
   
   docget_ok_click:      function(data_sec, data_rid, doc_nm, doc_flg){docget_ok_click(data_sec, data_rid, doc_nm, doc_flg); },
   glob_fm_before_show:  function ()                { glob_fm_before_show(); },
   glob_fm_after_show:   function ()                { glob_fm_after_show(); },
   pg_performGo:         function (pg_id)           { pg_performGo(pg_id); }
}


$(function() {      // Shorthand for $(document).ready(function() {
    "use strict";
    
    window.addEventListener("popstate", function() { 
        if ($(".modal.show").length > 0)
            $(".modal.show").modal('hide');
    });
    
    $(window).resize(function() {
        measure_listsBody();
        measure_contentBody();
        
    });
    $(document).mouseup(function(e) {
        if ($(".popover.outhide").length > 0)
            $(".popover.outhide").each(function () {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0) $(this).popover("dispose");
            });
            
            var sideout = $("#sideout");
        
        if (sideout.hasClass("active")) {
            // if the target of the click isn't button or menupanel nor a descendant of this elements
            if (!sideout.is(e.target) && sideout.has(e.target).length === 0)
               _sout.hide_sideout();
        }
            
        _common.close_tooltips();
    });
    
     $("#sideout").swipe( {
        swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
          if (direction == "left")
              _sout.hide_sideout();
        }
    });

    /*
    $(window).keydown(function(e){
        if (e.keyCode === 8) { // Backspace
            // Backspace in browsers used for 'Back' navigate.
            // See here: https://stackoverflow.com/questions/1495219/how-can-i-prevent-the-backspace-key-from-navigating-back
            var $target = $(e.target||e.srcElement);
            if (!$target.is('input,[contenteditable="true"],textarea'))
                e.preventDefault();
        }
        else check_keyDown(e);
    });
    */
   
    $.when($.getScript("/pktbbase/js/_common.js") ,
           $.getScript("/pktbbase/js/_fdb.js",
           $.getScript("/pktbbase/js/_help.js"),
           $.getScript("/pktbbase/js/_bbmon.js")),
           $.getScript("/pktbbase/js/_viewer.js"),
           $.getScript("/pktbbase/js/_sout.js"),
           $.getScript("/pktbbase/js/_docget.js")
           )
        .done(function () {
            //???????????????
            window.active_dsrc_ajax_orgs_id = "NONE";  // for prevent double dsrc ajax (load_vw_dsrc). Can't use '' - it's CL id.
            
            // all synchronous functions here
            set_initials();
            
            $("#content_types").removeClass("d-none");
            $("#content_info").removeClass("d-none");

            set_clnf_vars();        // recreate_page here
        });
        
}); // End of use strict

function set_initials() {
  
    setMainPanelsSz(); //_common.getStoredLocalInt('nxc_orgs_panel_sz')
}

function setMainPanelsSz() { 
/*  
  flex-grow flex-shrink flex-basis 
            
  flex-grow (определяет то насколько этот блок м.б больше соседних элементов)
  flex-basis (размер блока) 
  flex-shrink(определяет то насколько блок будет уменьшаться относительно др эл-в внутри flex контейннера)
*/

   var orgs_sz = 20;
            
    //$("#content_types").css({ 'flex': '0 0 ' + orgs_sz + '%', '-ms-flex': '0 0 ' + orgs_sz + '%' });
    //$("#content_mdls").css({ 'flex': '1 0 ' + orgs_sz + '%', '-ms-flex': '1 0 ' + orgs_sz + '%' });            
    measure_contentBody();

}

// user validate section
function is_valid_user()   { return _common.getStoredSessionStr('clnf_ip').length > 0; }    // clinfO version

function validate_ok(ead, save_ead) {
    _common.storeLocal('nxc_login_ead', ead);
    _common.storeLocal('nxc_save_ead', Number(save_ead))
    
    _common.storeSession('nxc_clnf_ead', ead);

    set_clnf_vars();        // recreate_page here
}
// end: validate


function measure_contentBody() {
    var nav_h = $(".navbar").offset().top + $(".navbar").outerHeight(true);
    
    $(".content-wrapper").css({ 'top': nav_h + 'px' });
    
    // Make CL panel width equal to other panels
    //$(".orgs-card-cl").css('width', $(".orgs-card-any").innerWidth() + 'px' );
    //$("#content_types_head").css('width', $(".orgs-card-cl").outerWidth(true) + 'px' );
    
    /*PDV. еще добавил этот обработчик в show_active_execution_info()*/
    var detail_exe_h = $("#exe_info").innerHeight() - 20;	// 20: experimental

    $("#other_info").css({ 'height': (detail_exe_h - $("#main_info").innerHeight() - 80) + 'px' }); 
    $("#docs_block").css({ 'height': detail_exe_h + 'px' });
	
    $("#executions_list").css({ 'height': detail_exe_h + 'px' });
	
    var content_mdls_h = $("#content_lists").innerHeight() - $("#content_types").outerHeight(true) - 30;	
    $("#content_mdls").css({ 'height': content_mdls_h + 'px' });
}


        // надо пересчитать высоту для content_mdls_body и чтобы появился скролл 
function measure_listsBody() {
    /*PDV */
    $('#content_mdls_body').css('height', ($('#content_lists').offset().top + $('#content_lists').innerHeight() - 
    $('#content_mdls_head').offset().top - $('#content_mdls_head').outerHeight(true)) + 'px');
}

function reset_clnf_vars(){
    _common.storeSession('clnf_ip', '');
    _common.storeSession('clnf_flg', 0);

    _common.storeSession('clnf_org', ''); //data.org      370dd604-806a-4406-a907-e69f536e8cb3
    _common.storeSession('clnf_rid', '');
};

function clear_all() {
    reset_clnf_vars();
    
    $('#content_types_body').empty();
    $('#content_mdls_body').empty();
    $('#content_info').empty();
    $('#tmp_filter_items').empty();
    
    recreate_page();
}

function set_clnf_vars() {
    reset_clnf_vars();
    
    var postForm = {
       'part'  : 'clnf_get_rec_by_ip'
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {
           
        },
        success   : function(data) {    // always success
                    var flg = Number(data.flg); 

                        _common.storeSession('clnf_ip', data.ip); //data.ip
                        _common.storeSession('clnf_flg', flg);
                        
                        _common.storeSession('clnf_org', data.org); //data.org      370dd604-806a-4406-a907-e69f536e8cb3
                        _common.storeSession('clnf_rid', data.rid);
                        
                        _common.storeSession('app_ip', data.ip);
                   //     _common.storeSession('fdb_blk', data.fdb_blk);
                        
                        if (((flg >> 1) & 0x1) == 1){ // flg >> 1) & 0x1, >> означает сдвиг вправо на 1 бит, и если он равен 1, то загрузить страницу
                            recreate_page();
                        }         
        }
    });
} 

function ta_refreshRows(table_id) {

    if ($('#ta_' + table_id).length > 0) {     
        $("[id^='tr_" + table_id + "-']")
            .mouseenter(function() {
                _common.swipe_showMenu($(this));
            })
            .mouseleave(function() {
                _common.swipe_hideMenu();
            });  
    }else{
        console.log($('[id^="exe_rid-"]'));
        $('[id^="exe_rid-"]').mouseenter(function() {
                _common.swipe_showMenu($(this));
            })
            .mouseleave(function() {
                _common.swipe_hideMenu();
            });  
    }
    
    _common.refresh_tooltips();
}


function start_ynPopoverForDeleteX(jq_elem) {
    var el_id = jq_elem.attr('id'),
            
        rid = _common.value_fromElementID(el_id),
        
        pref = el_id.substring(0, el_id.indexOf('-')),
        part = pref.substring(pref.lastIndexOf('_') + 1);
                                  
    var subj_type = '', subj_name = '';

    switch (part) {
        case 'type': {
            subj_type = 'тип вагона';
            subj_name = $('#type_rid-' + rid).text();
            break;
        }
        case 'mdl' : {
            subj_type = 'модель';
            subj_name = $('#mdl_nm_rid-' + rid).text();    
            break;
        }
        case 'doc' : {
            subj_type = 'документ';
            subj_name = jq_elem.attr('data-fnm');  
            break;
        }
        case 'exe':{
            subj_type = 'исполнение';
            subj_name = $('#exe_nm-' + rid).text(); 
            break;    
        }
    }
        
    var btn_comm_classes = "btn btn-sm y-mrg-t10 y-mrg-r10 y-mrg-b10 y-shad";
    
    //var arrow = jq_elem.closest('.acts-inner').length > 0 ? "" : "<div class='arrow'></div>";
	var arrow = "";
    
    jq_elem.popover({
        delay: { "show": 500, "hide": 100 },
        placement : 'left',
        html : true,
        template: '<div class="popover yn-popover outhide" role="tooltip">' + arrow + 
                    '<h3 class="popover-header"></h3><div class="popover-body"></div></div>',
        content : 
                '<span>Действительно удалить ' + subj_type + ' <i class="y-dred-text">' + subj_name + '</i> ?</span>' +
                "<div class='text-right'>" +
                   "<button id='popover_yes' type='button' class='btn-warning " + btn_comm_classes + "'>Да</button>" +
                   "<button id='popover_no' type='button' class='btn-light " + btn_comm_classes + "'>Нет</button>" +
               "</div>"
    }).popover('show');

    $('.popover-header').text('Требуется подтверждение');
    
    $('#popover_yes').unbind('click').on('click',function() {
        $('.yn-popover').popover('dispose');

        switch (part) {
            case 'type':{
                delete_type(rid);
                break;   
            }
            case 'mdl' : {
                delete_mdl(rid);
                break;
            }
            case 'doc': {
                delete_doc(rid, jq_elem);
                break;
            }
            case 'exe':{
                delete_exe(rid, jq_elem);
                break;
            }
        }
        
        jq_elem.tooltip('hide');
    });

    $('#popover_no').unbind('click').on('click',function() {
        $('.yn-popover').popover('dispose');
    });
}

function recreate_page() {   
    if(_common.getStoredSessionStr('clnf_ip').length > 0){
             
        var postForm = {
           'part'      : 'load_types_tbl'
        };

        $.ajax({
            type       : 'POST',
            url        : 'php/jgate.php',
            data       : postForm,
            dataType   : 'json',
         //   localCache : true,
        //    cacheKey   : vw_id,
            beforeSend: function() {
               $('#content_types_body').empty();
                
            },
            complete: function() {
                set_initials();
                $('#type_rid-' + _common.getStoredSessionStr('present_type')).addClass('tbl-act-cell'); 
                measure_listsBody();
                
                load_mdls(_common.getStoredSessionStr('present_type'));
            },
            error: function (jqXHR, exception) {
                //$('#content_orgs_body').html(_common.err_span());
            },
            success   : function(data) {
                            if (data.success) {
                               $('#content_types_body').html(data.body);                    
                            }
                            ta_refreshRows('type');
                        }
        }).always(function () {
          //  measure_listsBody();
        });
    }else clear_all();
}


function select_search_item(rid) {  // ex: rws:879876876.... перескакивает на нужное место в таблице
    
    drop_filter();
    
    var postForm = {
       'part' : 'search_get_mdls_row',
       'rid'  : rid
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        success   : function(data) {
                               
                        if (data.success) {
                            // $$_scroll_to_id - глобальная переменная, используется в _common.process_scrollToID();
                            $$_scroll_to_id = '#mdl_rid-' + rid;  
                            
                           _common.storeSession('present_mdl', rid);

                           if (data.rownum >= 0){ 
                                start_gopage('index', Math.floor(data.rownum/10));  
                            }   
                        }
        }
    });
}



function start_gopage(pg_id, new_page) {
    
   _common.storeSession(pg_id + '_last_page', new_page);

   load_mdls(_common.getStoredSessionStr('present_type'));
}

function pg_performGo(pg_id) {  // function is called when pagination link is clicked
    switch (pg_id) {
        default:
            recreate_page();
    }
}

function load_mdls(rid_type){ 

    $('#executions_block .card-body').empty();
    $('#exe_info').empty();
    $('#docs').empty();   
       
    var lastpage = _common.get_currentLastpage('index');
    var pg = 'index';
    rid_type = (rid_type.length > 0) ?  rid_type : '';
        
     var postForm = {
        'part'      : 'load_mdls_tbl',
        'offset': lastpage * 10,
        'rows': 10,
        'currpage': lastpage,
        'rid_type' : rid_type,
        'from': _common.getStoredSessionStr('from'),
        'to': _common.getStoredSessionStr('to'),
        'country': _common.getStoredSessionStr('country'),
        'factory': _common.getStoredSessionStr('factory')
    };

    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        beforeSend: function() {
           $('#content_mdls_body').empty();
        //     show_active_type_info();
             
        },
        complete: function() {
            check_active_mdl();
        },
        success: function(data){
            
            if (data.success) {
                $('#tmp_filter_items').html(data.filter);
                $('#pagination').html(data.pagination);
                $('#content_mdls_body').html(data.body);
            //    $('.pagination').replaceWith(data.pagination); // с replaceWith будь аккуратна!
            
                //if(_common.getStoredSessionStr('from').length > 0)
                 //   $('#pagination').empty();

                 ta_refreshRows('mdls'); 
                 
                    if ($('.' + pg + '-pagination').length == 0)
                    _common.storeSession(pg + '_last_page', -1);
                   else {
                        var li_id = '#' + pg + '_li_to_page-' + lastpage;

                        $(li_id + '.active').removeClass('active');
                        $(li_id).addClass('active');                                     
                   } 

                     _common.process_scrollToID();

                     $('#srch_box')
                                .autocomplete({
                                    serviceUrl: 'php/mdls_search.php',
                                    paramName:  'srch_box',
                                    autoSelectFirst: true,
                                    //maxHeight: 350,
                                    triggerSelectOnValidInput: false,   // block onselect firing on browser activate
                                    showNoSuggestionNotice: true,
                                    noSuggestionNotice: 'Совпадений не найдено',
                                    minChars: 2,
                                    //lookupLimit: 100,
                                    onSelect: function (suggestion) {

                                        select_search_item(suggestion.data.trim()); // suggestion.data: rid, suggestion.value: pname

                                        $('#srch_box').val('');   //  и есть контекст
                                    }
                                }); 
                                
                   if(data.body.length == 0){
                       _common.say_noty_err("По заданным параметрам моделей не существует");
                
                        $('#pagination').html(data.pagination);
                        $('#content_mdls_body').html(data.body);
                        $('#executions_block .card-body').empty();
                        $('#exe_info').empty();
                        $('#docs').empty();  
                   }
                 
            }
             _common.refresh_tooltips();
        }
    });
}


function check_active_mdl(){
   var present_mdl = _common.getStoredSessionStr('present_mdl');      
  
   if(present_mdl.length == 0 || $('#mdl_rid-'+present_mdl).length == 0){
       
       present_mdl = _common.value_fromElementID($('[id^="mdl_rid-"]').first().attr('id'));
       
        _common.storeSession('present_mdl', present_mdl);
        
        select_active_mdl(present_mdl);
    }
    else select_active_mdl(present_mdl); 
   
}

function select_active_mdl(rid){
    
    $("[id^='mdl_rid-']").removeClass('tbl-act-cell');
    $('#mdl_rid-' + rid).addClass('tbl-act-cell');
    
    show_active_mdl_executions(rid);  
}

function mdl_click(e){
    var rid = _common.value_fromElementID($(e).attr('id'));
    _common.storeSession('present_mdl', rid);
    check_active_mdl();
}



function check_active_exe(){
   var now_exe = _common.getStoredSessionStr('now_exe');      
   
   if(now_exe.length == 0 || $('#exe_rid-'+now_exe).length == 0){
       var elem = $('[id^="exe_rid-"]').first();
      
       if(elem.length == 0){
           now_exe = null;
 
           $('#exe_info').empty();
           $('#docs').empty();
       }else{
            now_exe = _common.value_fromElementID(elem.attr('id'));

            _common.storeSession('now_exe', now_exe);

            select_active_execution(now_exe);
       } 

    }
    else select_active_execution(now_exe);
}

function select_active_execution(rid){
    $('[id^="exe_rid-"]').removeClass('tbl-act-cell');
    $('#exe_rid-'+rid).addClass('tbl-act-cell');
    
    show_active_execution_info(rid);
    show_docs(rid);
}

function execution_click(e){
    _common.storeSession('now_exe', _common.value_fromElementID($(e).attr('id')));
    check_active_exe();
}

function show_active_mdl_executions(rid){
    var postForm = {
       'part'      : 'load_execution',
       'rid_mdl' : rid
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        beforeSend: function() {
 
        },
        complete: function() {
            check_active_exe();
        },
        success: function(data){
            if (data.success) {
                $('#executions_block').html(data.body); 
                ta_refreshRows('exe');
            }
             _common.refresh_tooltips();
        }
    });
}


function on_add_execution (){
    add_or_edit_execution('');
}

function edit_exe(e){
   // когда ссылка редатировать была в списке с исполгнгиями, брала rid из родителя родителя 
  //  add_or_edit_execution(_common.value_fromElementID($(e).parent().parent().attr('id')));
  add_or_edit_execution(_common.value_fromElementID($(e).attr('id')));
  console.log(_common.value_fromElementID($(e).attr('id')));
}

function add_or_edit_execution(rid){
    var rid_mdl = _common.value_fromElementID($("[id^='mdl_rid-']").filter('.tbl-act-cell').attr('id'));
    
    var nm = '', factory ='', country ='', spec_n ='', countryspec_n = '', flg = 0, desigDraw_techCon = '', comm_draw = '', w_base = '', w_length = '',
        b_length = '', b_width = '', b_height = '', composition = '', layout= '', type_coup_dev='', tM_cart='', weight_tare='',
        payload='', size_bC='', quan_sit_plcs='', constr_speed='', existType_EHTK='', type_brake='', type_trans_dev='',
        type_B_dev = '', systemElectr='', type_gen='', DGY = '', syfle = '', video = '', 
        type_drive = '', vent_cond = '', air_decon = '', aqua_decon = '', F_MV_B_C = '', BRISS = '',
        sys_heat = '', cert = '', serv_life = '', runs = '', prod_ys = '';    
    
    if(rid.length > 0){
       nm = $('#exe_nm-'+rid).text();
       factory = $('#exe_rid-'+rid).attr('data-factory');
       country = $('#exe_rid-'+rid).attr('data-country');
       spec_n = $('#exe_rid-'+rid).attr('data-spec');
       desigDraw_techCon = $('#exe_rid-'+rid).attr('data-desigDraw-techCon');
       comm_draw = $('#exe_rid-'+rid).attr('data-comm-draw');
       w_base = $('#exe_rid-'+rid).attr('data-w-base');
       w_length = $('#exe_rid-'+rid).attr('data-w-length');
       b_length = $('#exe_rid-'+rid).attr('data-b-length');
       b_width = $('#exe_rid-'+rid).attr('data-b-width');
       b_height = $('#exe_rid-'+rid).attr('data-b-height');
       composition = $('#exe_rid-'+rid).attr('data-composition');
       
       layout = $('#exe_rid-'+rid).attr('data-layout');
       type_coup_dev = $('#exe_rid-'+rid).attr('data-type-coupDev');
       tM_cart = $('#exe_rid-'+rid).attr('data-tM-cart');
       weight_tare = $('#exe_rid-'+rid).attr('data-weight-tare');
       payload = $('#exe_rid-'+rid).attr('data-payload');
       size_bC = $('#exe_rid-'+rid).attr('data-size-bC');
       quan_sit_plcs = $('#exe_rid-'+rid).attr('data-quan-plcs');
       constr_speed = $('#exe_rid-'+rid).attr('data-speed');
       existType_EHTK = $('#exe_rid-'+rid).attr('data-EHTK');
       type_brake = $('#exe_rid-'+rid).attr('data-type-brake');
       type_trans_dev = $('#exe_rid-'+rid).attr('data-type-transDev');
       type_B_dev = $('#exe_rid-'+rid).attr('data-type_B_dev');
       systemElectr = $('#exe_rid-'+rid).attr('data-systemElectr');
       type_gen = $('#exe_rid-'+rid).attr('data-type_gen');
       
       DGY = $('#exe_rid-'+rid).attr('data-DGY');
       syfle = $('#exe_rid-'+rid).attr('data-syfle');
       video = $('#exe_rid-'+rid).attr('data-video');
       
       type_drive = $('#exe_rid-'+rid).attr('data-type_drive');
       vent_cond = $('#exe_rid-'+rid).attr('data-vent_cond');
       air_decon = $('#exe_rid-'+rid).attr('data-air_decon');
       
       aqua_decon = $('#exe_rid-'+rid).attr('data-aqua_decon');
       F_MV_B_C = $('#exe_rid-'+rid).attr('data-F_MV_B_C');
       BRISS = $('#exe_rid-'+rid).attr('data-BRISS');
       
       sys_heat = $('#exe_rid-'+rid).attr('data-sys_heat');
       cert = $('#exe_rid-'+rid).attr('data-cert');
       serv_life = $('#exe_rid-'+rid).attr('data-serv_life');
       
       runs = $('#exe_rid-'+rid).attr('data-runs');
       prod_ys = $('#exe_rid-'+rid).attr('data-prod_ys');
    } 
    
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'execution_form',
       'mdl_rid': rid_mdl
   }; 
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                
            $('#execution_form').attr('data-rid', rid).attr('data-mdl-rid', rid_mdl)
           .on('show.bs.modal', function () {  
               
               if(rid.length > 0){
                   $('#load_exe_data').addClass('d-none');
               }


              
                  
               $('#send_exe_ok').click(function(){
                   
                   if(/^\d{4}$/.test(Number($('#prod_ys').val())) || $('#prod_ys').val().length == 0){
                   send_ok();
                   
                    }
                    else  _common.say_noty_err("Поле 'Год производства' должно быть в формате YYYY");
               });
              

           })
           .on('shown.bs.modal', function () {
                 $('#nm').val(nm);  
                 $('#factory').val(factory);
                 $('#country').val(country);
                 $('#spec_n').val(spec_n);
                 $('#desigDraw_techCon').val(desigDraw_techCon);
                 $('#comm_draw').val(comm_draw);
                 $('#w_base').val(w_base);
                 $('#w_length').val(w_length);
                 
                 $('#b_length').val(b_length);
                 $('#b_width').val(b_width);
                 $('#b_height').val(b_height);
                 $('#composition').val(composition);
                 
                 $('#layout').val(layout);
                 $('#type_coup_dev').val(type_coup_dev);
                 $('#tM_cart').val(tM_cart);
                 $('#weight_tare').val(weight_tare);
                 $('#payload').val(payload);
                 
                 $('#size_bC').val(size_bC);
                 $('#quan_sit_plcs').val(quan_sit_plcs);
                 $('#constr_speed').val(constr_speed);
                 $('#existType_EHTK').val(existType_EHTK);
                 $('#type_brake').val(type_brake);
                 $('#type_trans_dev').val(type_trans_dev);
                 
                 $('#type_B_dev').val(type_B_dev);
                 $('#systemElectr').val(systemElectr);
                 $('#type_gen').val(type_gen);
                 
                 $('#DGY').val(DGY);
                 $('#syfle').val(syfle);
                 $('#video').val(video);
                 
                 $('#type_drive').val(type_drive);
                 $('#vent_cond').val(vent_cond);
                 $('#air_decon').val(air_decon);
                 $('#aqua_decon').val(aqua_decon);
                 $('#F_MV_B_C').val(F_MV_B_C);
                 $('#BRISS').val(BRISS);
                 
                 $('#sys_heat').val(sys_heat);
                 $('#cert').val(cert);
                 $('#serv_life').val(serv_life);
                 $('#runs').val(runs);
                 $('#prod_ys').val(prod_ys);
           })            
           .modal('show');
        }
    });
   
}

function send_ok(){
     
   var nm = $('#nm').val();

   if(nm.length > 0){ 
     
        var postForm = {
            'part' : 'add_new_exe',
            'rid':  $('#execution_form').attr('data-rid'),
            'rid_mdl' : $('#execution_form').attr('data-mdl-rid'),
            'flg' : 0,
            'nm' : nm,
            'factory': $('#factory').val(),
            'country': $('#country').val(),
            'spec_n': $('#spec_n').val(),
            'desigDraw_techCon': $('#desigDraw_techCon').val(),
            'comm_draw' : $('#comm_draw').val(),
            'w_base': $('#w_base').val(),
            'w_length': $('#w_length').val(), 
            'b_length' : $('#b_length').val(),
            'b_width' : $('#b_width').val(),
            'b_height': $('#b_height').val(),
            'composition' : $('#composition').val(),

            'layout': $('#layout').val(),
            'type_coup_dev': $('#type_coup_dev').val(),
            'tM_cart': $('#tM_cart').val(),
            'weight_tare': $('#weight_tare').val(),
            'payload': $('#payload').val(),
            'size_bC': $('#size_bC').val(),
            'quan_sit_plcs' : $('#quan_sit_plcs').val(),
            'constr_speed':  $('#constr_speed').val(),
            'existType_EHTK': $('#existType_EHTK').val(),
            'type_brake' : $('#type_brake').val(),
            'type_trans_dev': $('#type_trans_dev').val(),
            'type_B_dev' : $('#type_B_dev').val(),
            'systemElectr' : $('#systemElectr').val(),
            'type_gen' : $('#type_gen').val(),
            'DGY': $('#DGY').val(),
            'syfle': $('#syfle').val(),
            'video': $('#video').val(),
            'type_drive' : $('#type_drive').val(),
            'vent_cond': $('#vent_cond').val(),
            'air_decon': $('#air_decon').val(),
            'aqua_decon' : $('#aqua_decon').val(),
            'F_MV_B_C' : $('#F_MV_B_C').val(),
            'BRISS' : $('#BRISS').val(),
            'sys_heat': $('#sys_heat').val(),
            'cert' : $('#cert').val(),
            'serv_life' :$('#serv_life').val(),
            'runs' : $('#runs').val(),
            'prod_ys' : $('#prod_ys').val()
        };

        $.ajax({
          type       : 'POST',
          url        : 'php/jgate.php',
          data       : postForm,
          dataType   : 'json',
          success: function(data){            
             if(data.success){                 
               
                    $('#execution_form').modal('hide');

                    show_active_mdl_executions($('#execution_form').attr('data-mdl-rid'));        
               
             }        
          }   
        });
     
   }
}

function load_last_exe(){
   var postForm = {
       'part' : 'get_last_exe',
       'rid_mdl': $('[data-mdl-rid]').attr('data-mdl-rid')
   };    
 
    $.ajax({
      type       : 'POST',
      url        : 'php/jgate.php',
      data       : postForm,
      dataType   : 'json',
      success: function(data){    
         if(data.success){ 
           // $('#nm').val(data.nm);  
            $('#factory').val(data.factory);
            $('#country').val(data.country);
            $('#spec_n').val(data.spec_n);
            $('#desigDraw_techCon').val(data.desigDraw_techCon);
            $('#comm_draw').val(data.comm_draw);
            $('#w_base').val(data.w_base);
            $('#w_length').val(data.w_length);

            $('#b_length').val(data.b_length);
            $('#b_width').val(data.b_width);
            $('#b_height').val(data.b_height);
            $('#composition').val(data.composition);

            $('#layout').val(data.layout);
            $('#type_coup_dev').val(data.type_coup_dev);
            $('#tM_cart').val(data.tM_cart);
            $('#weight_tare').val(data.weight_tare);
            $('#payload').val(data.payload);

            $('#size_bC').val(data.size_bC);
            $('#quan_sit_plcs').val(data.quan_sit_plcs);
            $('#constr_speed').val(data.constr_speed);
            $('#existType_EHTK').val(data.existType_EHTK);
            $('#type_brake').val(data.type_brake);
            $('#type_trans_dev').val(data.type_trans_dev);

            $('#type_B_dev').val(data.type_B_dev);
            $('#systemElectr').val(data.systemElectr);
            $('#type_gen').val(data.type_gen);

            $('#DGY').val(data.DGY);
            $('#syfle').val(data.syfle);
            $('#video').val(data.video);

            $('#type_drive').val(data.type_drive);
            $('#vent_cond').val(data.vent_cond);
            $('#air_decon').val(data.air_decon);
            $('#aqua_decon').val(data.aqua_decon);
            $('#F_MV_B_C').val(data.F_MV_B_C);
            $('#BRISS').val(data.BRISS);

            $('#sys_heat').val(data.sys_heat);
            $('#cert').val(data.cert);
            $('#serv_life').val(data.serv_life);
            $('#runs').val(data.runs);
            $('#prod_ys').val(data.prod_ys); 
         }        
      }   
    });
}


function show_active_execution_info(rid){

    var postForm = {
       'part'      : 'load_exe_info',
       'rid' : rid
    };
    if(rid.length > 0){
        $.ajax({
            type       : 'POST',
            url        : 'php/jgate.php',
            data       : postForm,
            dataType   : 'json',
            beforeSend: function() {

            },
            complete: function() {
                measure_contentBody();  //PDV
            },
            success: function(data){

                if (data.success) {
                    $('#exe_info').html(data.body); 
                }
                 _common.refresh_tooltips();
            }
        });
    }else $('#exe_info').html(''); 
}

function show_docs(pid){
    var postForm = {
        'part': 'get_docs',
        'pid': pid
    };
           
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        beforeSend: function() {
 
        },
        complete: function() {
            measure_contentBody();  //PDV
            _common.refresh_tooltips();
        },
        success: function(data){
            if (data.success) {
                $('#docs').html(data.body); 
                
            }
        }
    });
}

function add_doc(e){
    var tbl = 'executions';
    var flg = 1;
    var docs_pid = $(e).attr('data-pid');
    
    _common.close_dropdowns();
 
   _docget.fm_get_doc_startForm(tbl, docs_pid, ".pdf.doc.docx.xls.xlsx.rtf.txt.", 0, flg); // 0 name length, 1 - flag
}

function docget_ok_click(data_tbl, docs_pid, doc_nm, doc_flg){

    if (window.docget_file_up != null && docs_pid.length > 0) {
        
     var reader = new FileReader();
        reader.onload = function(e) {              
        
            var postForm = {
                'part' : 'add_docs',
                'tbl'  : data_tbl,
                'pid'  : docs_pid,
                'fnm'  : window.docget_file_up.name,
                'nm'   : doc_nm,
                'doc_flg' : doc_flg,
                'rdat' : reader.result
            };
            
            console.log(postForm);

            $.ajax({
                type      : 'POST',
                url       : 'php/jgate.php',
                data      : postForm,
                dataType  : 'json',
                beforeSend: function() {
                    $(".y-ajax-wait").css('visibility', 'visible');
                },
                complete: function() {
                    $(".y-ajax-wait").css('visibility', 'hidden');
                },
                success: function(data) {
                 
                            if (data.success) {                                 
                               _common.say_noty_ok("Документ загружен"); 
                            }
                            else _common.say_noty_err("Ошибка загрузки документа");

                            show_docs(docs_pid);
                            }
                }
            );
        }
        reader.readAsDataURL(window.docget_file_up);
     }
}

function delete_doc(rid, jq_elem){
     // types - 1 , mdls - 2
    var pid = jq_elem.attr('data-pid');

    var postForm = {
        'part': 'delete_doc',
        'rid': rid
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           if(data.success){
             show_docs(pid);
           }   
        }
    });
}

function delete_exe_click(e){
    start_ynPopoverForDeleteX($(e));
}

function delete_exe(rid, elem){
  //   console.log(elem.attr('data-rid-mdl'))
    var postForm = {
        'part': 'delete_exe',
        'rid': rid
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
           if(data.success){               
             show_active_mdl_executions(elem.attr('data-rid-mdl'));
           }   
        }
    });
}

function doc_view_click(e){

    _common.close_dropdowns();
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    _viewer.viewer_view("file_put_tmp", "rid", $(e).attr('data-doc'), false);
}

function delete_doc_click(e){
    _common.stop_propagation(e);
    start_ynPopoverForDeleteX($(e));
}

function glob_fm_before_show() {
      // $("#div_ta_registr").css({ 'overflow-y': 'hidden' });  // IE can show scrollbar over modal
}

function glob_fm_after_show() {
         //   $("#div_ta_registr").css({ 'overflow-y': 'auto' });
}

function add_new_mdl (){
    addNew_orEdit_mdl('');
}

/*function mdl_edit_click(){
   addNew_orEdit_mdl(_common.value_fromElementID($(e).attr('id')));
 }*/

function add_new_type(){
    addNew_orEdit_type('');
}

function type_edit_click(e){    
  addNew_orEdit_type(_common.value_fromElementID($(e).attr('id')));
}

function addNew_orEdit_type(rid){
    var nm = '', flg = '';
    
    if(rid.length > 0){
         nm = $('#type_rid-'+rid).text();
         flg = $('#tr_type-'+rid).attr('data-flg');
    }

    var postForm = {
        'part': 'get_fm',
        'fm_id': 'fm_add_or_edit_type'
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
            if (data.success) {
                $('#div_tmp').empty().html(data.html);
                
                 $('#fm_add_or_edit_type').attr('data-rid', rid)
                .on('show.bs.modal', function () {                    
                        
                    $('#type_ok').click(function(){
                        send_type_data();
                    });
                })
                .on('shown.bs.modal', function () {
                    $('#type_nm').val(nm); 
                    
                })            
                .modal('show');
            }
        }
    });
}

function send_type_data(){
    var nm =  $('#type_nm').val();
    var flg = 0;
    var rem = $('#type_rem').val();
    var rid = $('#fm_add_or_edit_type').attr('data-rid');
    
    if(nm.length > 0){
        
        var postForm = {
            'part': 'type_data',
            'nm': nm,
            'flg': flg,
            'rid': rid,
            'rem' : rem
        };
       
        $.ajax({
            type       : 'POST',
            url        : 'php/jgate.php',
            data       : postForm,
            dataType   : 'json',
            success: function(data){
                    $('#fm_add_or_edit_type').modal('hide');

                    // _common.storeSession('last_train', data.rid);
                     
                    recreate_page();
            }
        });
    }
}

function addNew_orEdit_mdl(rid){
   // var rid_type = _common.value_fromElementID($('[id^="tr_type-"]').find('.tbl-act-cell').attr('id')); 
    var nm = '', flg = '';

    if(rid.length > 0){
        nm = $('#mdl_nm_rid-'+rid).text();
        flg = 0;        
    }
    
    var postForm = {
        'part': 'get_fm',
        'fm_id': 'fm_add_or_edit_mdl'
    };

    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                
            $('#fm_add_or_edit_mdl').attr('data-rid', rid)
           .on('show.bs.modal', function () {                    

               $('#mdl_ok').click(function(){
                   send_mdl_data();
               });
               
                              
             //  if(rid.length == 0){
                    $('#mdl_nm').on('focusout', function(){
                        verify_mdl_nm($(this).val());
                    });
               // }
                   
           }).on('shown.bs.modal', function () {

                if(rid.length == 0){                    
                    $('#typs_list').children().each(function(){
                        if($(this).attr('value') == _common.getStoredSessionStr('present_type')){                        
                            $(this).prop('selected', true); //prop('disabled', true)
                        }
                    }); 
                }

                $('#typs_list').children().each(function(){
                    if($(this).attr('value') == $('#tr_mdls-'+rid).attr('data-type-rid')){                        
                       $(this).prop('selected', true);
                    }
                }); 

               $('#mdl_nm').val(nm); 
              
           })            
           .modal('show');
        }
    });
}

function verify_mdl_nm(nm){  
    if(nm.length > 0){
        
        var postForm = {
            'part': 'verify_mdl_name',
            'nm': nm
        };
        
        $.ajax({
            url: 'php/jgate.php',
            type: 'POST',
            data: postForm,
            dataType: 'json',
            success: function(data){

                   if(data.nm.length > 0){
                      $('#mdl_ok').prop('disabled', true);
                     _common.say_noty_warn('Такая модель уже существует!');        
                     
                     $('#mdl_nm').on('input', function(){
                        $('#mdl_ok').prop('disabled', false);
                    }); 
                     
                }else{
                    $('#mdl_ok').prop('disabled', false);
                }    
            }
        });
        
    }
}

function send_mdl_data(){
     var nm = $('#mdl_nm').val();
     var rid_type = $('#typs_list').val(); // option:selected
     var flg = 0;
     var rid =  $('#fm_add_or_edit_mdl').attr('data-rid');
  //   var active_option_text = $('#typs_list option:selected').text();
     
     if ($('#typs_list option:selected').val() == '')
         _common.say_noty_warn('Укажите тип модели');  
     else{
        if(nm.length > 0){// && rid_type.length > 0

            var postForm ={
                'part': 'mdl_data',
                'nm': nm,
                'rid_type': rid_type,
                'flg': flg,
                'rid': rid
            };

            $.ajax({
                 type       : 'POST',
                 url        : 'php/jgate.php',
                 data       : postForm,
                 dataType   : 'json',
                 success: function(data){                   
                     if(data.success){

                        $('#fm_add_or_edit_mdl').modal('hide');   

                       load_mdls(_common.getStoredSessionStr('present_type')); 

                      // if(data.rid == 'reject') _common.say_noty_warn('Модель с таким именем уже существует!'); это рудимент

                     }
                 }
            });
        }else _common.say_noty_warn('Поле модель и выбор типа обязательны!');
     }
}

function delete_mdl_click(e){
    start_ynPopoverForDeleteX($(e));
}

function delete_mdl(rid){
     var postForm = {
        'part': 'delete_mdl',
        'rid': rid
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           if(data.success){
               load_mdls('');
           }   
        }
    });
}

function delete_type_click(e){
    start_ynPopoverForDeleteX($(e));
}

function delete_type(rid){
    var postForm = {
        'part': 'delete_type',
        'rid': rid
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           if(data.success){
               recreate_page();
           }   
        }
    });
}

function mdl_edit_click(e){
      addNew_orEdit_mdl(_common.value_fromElementID($(e).attr('id')));
}

function check_active_type(){   
    var present_type = _common.getStoredSessionStr('present_type');
    
    if(present_type.length == 0 || $('#type_rid-'+present_type).length == 0){
        
        present_type = _common.value_fromElementID($('[id^="type_rid-"]').first().attr('id'));
        _common.storeSession('present_type', present_type);
        
        select_active_type(present_type);
    }
    else select_active_type(present_type);
}

function select_active_type(rid){     
    $("[id^='type_rid-']").removeClass('tbl-act-cell');
   // $('#type_rid-' + rid).addClass('tbl-act-cell');      

    set_clnf_vars();
}


function type_click(e){
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', ''); 
   _common.storeSession('factory', ''); 
    
    var rid = _common.value_fromElementID($(e).attr('id'));
    _common.storeSession('present_type', rid);
    check_active_type();
}



/************************* filter  *************************/


function drop_filter(){

   _common.storeSession('present_type', '');
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', ''); 
   _common.storeSession('factory', '');
   
   set_clnf_vars();  
}

function get_form_multi(e){
   _common.storeSession('present_type', '');
    
    var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_multi'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                
            $('#select_mdls_by_multi').attr('data-rid', '')
           .on('show.bs.modal', function () {      
               
                $('#multi_ok').click(function(){
                    send_multi_params();
               });

           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}


function send_multi_params(){  
/*    
    var postForm = {
       'part' : 'get_mdls_by_multi',
       'from' : $('#from_year').val(),
       'to' : $('#to_year').val(),
       'country': $('#countries_list option:selected').text(),
       'factory': $('#factories_list option:selected').text()
   };
*/
    _common.storeSession('from', $('#from_year option:selected').text());
    _common.storeSession('to', $('#to_year option:selected').text());
    _common.storeSession('country', $('#countries_list option:selected').text()); 
    _common.storeSession('factory', $('#factories_list option:selected').text());

    $('#select_mdls_by_multi').modal('hide'); 
    set_clnf_vars();  

}


/* не вызывается, т.к. такого фильтра больше нет, вместо нее type_click, сейчас тоже не актуально
function get_mdls_4_type(e){
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', ''); 
   _common.storeSession('factory', ''); 
    
   set_clnf_vars();  
   _common.storeSession('present_type', $(e).attr('id'));

   setTimeout(function() { $('#select_mdls_by_type').modal('hide')}, 400);
}
*/


/* больше не актуально, теперь одна форма для всех параметров
function get_form_type(){
    
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', ''); 
   _common.storeSession('factory', '');
    
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_type'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                
            $('#select_mdls_by_type').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function get_form_year(){
    
   _common.storeSession('present_type', '');  
   _common.storeSession('country', ''); 
   _common.storeSession('factory', '');
   
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_year'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                 
            $('#select_mdls_by_year').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

               $('#select_year_ok').click(function(){
                    send_years_data();
               });
               
                   
           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function send_years_data(){
    var from = $('#from_year').val();
    var to = $('#to_year').val();

    if(from.length > 0 && to.length > 0){
        if(Number(from) <= Number(to)){
                        
            _common.storeSession('from', $('#from_year option:selected').text());
            _common.storeSession('to', $('#to_year option:selected').text());
            set_clnf_vars(); 
            $('#select_mdls_by_year').modal('hide'); 
                        
         } else _common.say_noty_err("Значение первого поля не может быть больше второго");
    }else _common.say_noty_err("Года производства должны быть выбраны!");
}


function get_form_country(){
    
   _common.storeSession('present_type', '');  
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('factory', ''); 
   
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_country'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                 
            $('#select_mdls_by_country').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

               $('#select_country_ok').click(function(){
                    send_country_data();
               });
               
                   
           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function send_country_data(){
    _common.storeSession('country', $('#countries_list option:selected').text());

    set_clnf_vars(); 
    $('#select_mdls_by_country').modal('hide'); 

}

function get_form_factory(){
   _common.storeSession('present_type', '');  
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', '');
   
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_factory'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                 
            $('#select_mdls_by_factory').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

               $('#select_factory_ok').click(function(){
                    send_factory_data();
               });
               
                   
           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function send_factory_data(){
    _common.storeSession('factory', $('#factories_list option:selected').text());

    set_clnf_vars(); 
    $('#select_mdls_by_factory').modal('hide'); 
}

*/