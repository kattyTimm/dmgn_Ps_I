    // global constants
var $$_main_rows_ = 10;         // main lists (clnf) rows per page
window.innerCdmv = '';

var _index = {   // interface to index.js
    is_valid_user:        function ()                { return is_valid_user(); },
    app_who:              function ()                { return app_who(); },
    fill_sideout:         function ()                { fill_sideout(); },
    pg_performGo:         function (pg_id)           { pg_performGo(pg_id); },    // required method (_common.js)
    docget_ok_click:      function (data_sec, data_rid, doc_nm, doc_flg) { docget_ok_click(data_sec, data_rid, doc_nm, doc_flg); },
    glob_fm_before_show:  function ()                { glob_fm_before_show(); },
    glob_fm_after_show:   function ()                { glob_fm_after_show(); },
}

$(function() {      // Shorthand for $(document).ready(function() {
    "use strict";
    
    window.addEventListener("popstate", function() { // back or forward button is clicked
        if ($(".modal.show").length > 0)
            $(".modal.show").modal('hide');
    });

    $(window).resize(function() {
        if ($('#list_rwc_choose_items').length > 0)
            $('#list_rwc_choose_items').css("max-height", (window.innerHeight - $('.sticky-footer').outerHeight(true) - 10) + 'px');
        
        measure_list();
        measure_detail();
        
        if ($("#sideout").hasClass("active"))
            _sout.measure_sideout();
        
            _fdb.fdb_measureBody();
            _bbmon.bbmon_measureBody();
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

    $(window).keydown(function(e){
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 70) { // Ctrl-F
            e.preventDefault();
            srch_btnClick();
        }
        else if (e.keyCode === 8) { // Backspace
            // Backspace in browsers used for 'Back' navigate.
            // See here: https://stackoverflow.com/questions/1495219/how-can-i-prevent-the-backspace-key-from-navigating-back
            var $target = $(e.target||e.srcElement);
            if (!$target.is('input,[contenteditable="true"],textarea'))
                e.preventDefault();
        }
        //else check_keyDown(e);
    });
       
    $.when($.getScript("/pktbbase/js/_common.js") ,
           $.getScript("/pktbbase/js/_fdb.js",
           $.getScript("/pktbbase/js/_help.js"),
           $.getScript("/pktbbase/js/_bbmon.js")),
           $.getScript("/pktbbase/js/_viewer.js"),
           $.getScript("/pktbbase/js/_sout.js"),
           $.getScript("/pktbbase/js/_docget.js")
           
           //$.getScript("/pointsbase/js/_pointscomm.js")
        )
        .done(function () {
            set_app_vars();        // recreate_page here
        });
}); // End of use strict

// user validate section
function is_valid_user()   { 
    return _common.getStoredSessionInt('app_ptr') === 1 || _common.getStoredSessionInt('app_itr') === 1; 
}

function pg_performGo(pg_id) {  // function is called when pagination link is clicked
    switch (pg_id) {
        case 'fm_fdb': _fdb.fm_fdb_reloadBody(); break;
        case 'fm_bbmon': _bbmon.fm_bbmon_reloadBody(); break;
        default: recreate_page();
    }
}

function start_gopage(pg_id, new_page) {
    _common.storeSession(pg_id + '_last_page', new_page);
        
    recreate_page(); 
}


/*
function hide_search() {
    $("#global_search").empty();
}

function show_search() {
    $("#global_search").html(
                "<form class='form-inline' style='padding:5px;'>" +
                    "<div class='input-group'>" +
                        "<div class='input-group-prepend'>" +
                            "<button id='srch_btn' class='btn btn-info' type='button' onclick='srch_btnClick();'>" +
                                "<i class='fas fa-search'></i>" +
                            "</button>" +
                        "</div>" +
                        "<input id='srch_box' type='text' class='form-control' placeholder='Поиск <min 2 символа>...' ondrop='return false;' ondragover='return false;'>" +
                    "</div>" +
                "</form>"
            );

    $('#srch_box')
            .autocomplete({
                serviceUrl: '../pointsbase/php/_cdssearch.php', //rwC,rwD,rwS search
                paramName:  'srch_box',
                autoSelectFirst: true,
                //maxHeight: 350,
                triggerSelectOnValidInput: false,   // block onselect firing on browser activate
                showNoSuggestionNotice: true,
                noSuggestionNotice: 'Совпадений не найдено',
                minChars: 2,
                //lookupLimit: 100,
                params: {  //to pass extra parameter in ajax file.
                    //'clnf_rid': _common.getStoredSessionStr('clnf_rid')
                },
                onSelect: function (suggestion) {
                    select_search_item(suggestion.data.trim()); // suggestion.data: rid, suggestion.value: pname

                    $('#srch_box').val('');
                },
                onSearchStart: function () {
                    $('#srch_box').addClass('srch-in-ajax');
                },
                onSearchComplete: function (query, suggestions) {
                    $('#srch_box').removeClass('srch-in-ajax');
                },
                //onInvalidateSelection: function (suggestion) {
                //},
                onHide: function (container) {   // call only when suggestions found or "No results" was visible. So set showNoSuggestionNotice to true
                },
                beforeRender: function (container, suggestions) {
                    container.find('.autocomplete-suggestion').each(function(i, suggestion){
                        $(suggestion).html($(suggestion).html().replace('{отд}', "<small class='y-llgray-text'>&lt;отд&gt;</small>"));
                    });
                }
            });
}

function srch_btnClick() {
    if ($('#srch_box').length > 0) {
        $('#srch_box').autocomplete().clear();
        $('#srch_box').val('').focus();
    }
}

*/

function measure_list() {
    if ($(".list-title").length > 0) {
        // координаты где заканчивается .list-title
        var list_title_b = $(".list-title").offset().top + $(".list-title").outerHeight(true),
                //координаты где заканчивается .content-lis
        list_content_b = $(".content-list").offset().top + $(".content-list").outerHeight(false);    // without margins

        var card_marg_h = $(".flt-card").outerHeight(true) - $(".flt-card").outerHeight(false);

     $('.list-body').css('height', (list_content_b - list_title_b) + 'px');

           $('#tbl_tmp').css('height', (list_content_b - list_title_b - $(".flt-card").outerHeight(true)) + 'px');
           
          if ($("#tbl_tmp").length > 0){
               if(get_url() === 'train'){
                 $('#roads_tmp').css('height', ($("#tbl_tmp").outerHeight(true)- $('#orgs_tmp').outerHeight(true) )  + 'px');
              }else if(get_url() === 'carriage') { 
		 $('#roads_tmp').css('height', ($("#tbl_tmp").outerHeight(true) )  + 'px');
	      }
            }
    }
}

function measure_detail() {
    if ($(".list-title").length > 0 && $(".detail-title").length > 0)
        $('.detail-title').css('height', $(".list-title").outerHeight(true) + 'px');
    
    var detail_title_b = $(".detail-title").length > 0 ? $(".detail-title").offset().top + $(".list-title").outerHeight(true) : 0,

        detail_content_b = $(".content-detail").offset().top + $(".content-detail").outerHeight(false);    // without margins

    $('.detail-body').css('height', (detail_content_b - detail_title_b) + 'px');
    
    $('.detail-comm-card').css('height', ($('.detail-body').innerHeight() - 20) + 'px');
    
    if ($("#registr_div").length > 0 && $(".detail-dmgn-card").length > 0 && $("#center_part_header").length > 0)
          $('#registr_div').css('height', ($('.detail-dmgn-card').outerHeight() - $('#center_part_header').outerHeight()) + 'px');
            
      $('#actions_div').css('height', ($('.detail-comm-card').outerHeight() - $('.detail-comm-card>.card-header').outerHeight()) + 'px');
}

function reset_app_vars() {    // admin version
    
   _common.storeSession('app_ip', '');
    _common.storeSession('fdb_blk', 0);
    
    _common.storeSession('app_itr', 0);
    _common.storeSession('app_ptr', 0);
    
    $('#foot_info').html("<small class='y-llgray-text'>Неизвестный ПК</small>");
}

function set_app_vars() {
    reset_app_vars();
    
    var postForm = {
       'part'  : 'get_app_globals'
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {
            recreate_page();
        },
        success   : function(data) {    // always success
                    var flg = Number(data.flg); 

                        _common.storeSession('app_ip', data.ip);
                        _common.storeSession('fdb_blk', data.fdb_blk);
                        
                        _common.storeSession('app_itr', data.itr);          // see jgate
                        _common.storeSession('app_ptr', data.ptr);
                        
                        var abt = data.itr == 1 || data.ptr == 1 ? data.ip : '';

                        if (abt.length > 0) 
                            $('#foot_info').html("<small class='y-llgray-text'>" + abt + "</small>");
        
                      //  if (((flg >> 1) & 0x1) == 0){ // flg >> 1) & 0x1, >> означает сдвиг вправо на 1 бит, и если он равен 1, то загрузить страницу
                            recreate_page();
                      //  }         
        }
    });
}


 function recreate_page() {
    if (_common.ends_with(window.location.href, '#modal'))    // in single page apps (user, operator)
        window.history.back();                                // just restore url /index.php

    if (is_valid_user()) {

        $(".page-content").html(
                "<div class='content-list'>" +
                    "<div class='list-title y-flex-row-nowrap align-items-center'>" +
                             "<h6 class='m-0'>&nbsp;<span id='list_ttl_curr_rwc' data-rid=''></span></h6>" +
                          /* "<div id='list_rwc_choose_items' class='dropdown-menu dropdown-menu-right overflow-y-auto'>" +*/
                    "</div>" +
                                
                    "<div class='list-body y-flex-column-nowrap'>" +
                        //btns_for_user_FPK
                    "</div>" + 
                    
                  "</div>" +                    
              //  "</div>" +
                
                "<div class='content-detail'>" +
                        
                    "<div class='detail-title m-0 y-flex-row-nowrap y-align-items-center'>" +
                    
                        "<div id='detail_ttl_dots_mnu' class='btn-group y-mrg-r5 p-0'>" + // dropdown container should not be overflow-hidden
                        "</div>" +
                                
                        "<h4 class='m-0 y-navy-text'>&nbsp;<span id='detail_ttl_curr_rws' data-rid=''>&nbsp;</span></h4>" +
                    "</div>" +
                    
                    "<div class='detail-body'>" +
                    "</div>" + /* end of  detail-body */
            
                "</div>");         
      
       fill_contentList_4_user_FPK();        
    }
    else $(".page-content").empty();
}

function show_detail_body(){
    
    var currentPg = get_url();
    /* для моделей без организаций
    var carr_road_or_mdl = _common.value_fromElementID($('#roads_tmp .tbl-act-cell').attr('id'));
    var main_org_rid = currentPg == 'carriage' ?
                        $('#roads_tmp .tbl-act-cell').first().closest('tr').attr('data-high') :
                        _common.value_fromElementID($('#orgs_tmp .tbl-act-cell').attr('id'));    
    */
   
                
    var carr_road_or_mdl = _common.value_fromElementID($('#roads_tmp .tbl-act-cell').attr('id'));
    var main_org_rid = _common.value_fromElementID($('#orgs_tmp .tbl-act-cell').attr('id'));                
                
        var postForm = {
            'part': 'get_detail_body',  
            'road_or_mdl' : carr_road_or_mdl,
            'section' : currentPg,
            'rid' : main_org_rid
        }

        $.ajax({
            type: 'POST',
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            complete: function(){   
                    refresh_reestr_dropdown();     
                },
            success: function(data){
                if(data.success){
                    
                    $('.detail-body').html(data.content); 

                         _common.refresh_tooltips(); 

                         measure_detail();

                         setTimeout(function(){
                             if(get_url() == 'carriage'){
                               
                               
                               check_active_mdl();
                               
                              var rid_mdl = $('[id="tr_mdl-'+carr_road_or_mdl+'"]').attr('data-rid');   
                              
                              // check_active_road_4Active_mdl();
         
                              // оставшиеся части страницы для МОДЕЛИ ВАГОНА (carr_mdl)
                              
                              // ищется все по carr_mdl
                              /* 
                                show_actions_by_active_mdl(carr_road_or_mdl);                             
                                show_detail_info(rid_mdl);
                                get_docs_part(carr_road_or_mdl);
                                get_pass_pdf_part(main_org_rid, carr_road_or_mdl); */
                                
                             }else{
                                 check_active_train();
                             //    ta_refreshRows('ta_registr', 'train');
                             }

                          }, 100);
                          
                          $("#foot_bbmon_root").html("<img src='/pktbbase/img/bbmon_28.png' class='y-cur-point y-mrg-r5'" +
                                                                " onclick='_bbmon.bbmon_start(this);' data-toggle='tooltip' title='Оповещения'>");
                                                        
                        _bbmon.badge_showActiveCount();                                        

                         $("#foot_fdb_root").html("<img src='/pktbbase/img/feedback_28.png' class='y-cur-point y-mrg-r5'" +
                                                                            " onclick='_fdb.fdb_start(this);' data-toggle='tooltip' title='Обратная связь'>");

                         _fdb.badge_showActiveCount();

                }else clear_detail_body();
            }
        });
} 


function fill_contentList_4_user_FPK (){ 
    
  var currentPg = get_url();
  
  if(currentPg == ''){
      currentPg = 'carriage';
      history.pushState('', $(document).find("title").text(), window.location.protocol + '//' + window.location.host + window.location.pathname + "?pg="+currentPg);
  }
  
    var postForm = {
        'part': 'show_buttons',
    }
    
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){
                  $('.list-body')
                     .append('<div class="card flt-card y-shad">'
                                + data.btn_for_curriage + 
                                  data.btn_for_train +  
                             '</div>' + 
                             '<div id="tbl_tmp" class="">' +        
                                    '<div id="orgs_tmp" class="border-bottom border-default accordion-set">' +
                                    '</div>' +
                                    '<div id="roads_tmp">' + 
                                         
                                     '</div>' +
                              '</div>'); // table temptation 

                            remove_active_class_btn($('.link')); 

                            switch (currentPg) {
                                case 'train':{
                                    $('#show_train').addClass('active-btn');    
                                    
                                     break;
                                }
                                default:{
                                    $('#show_carriage').addClass('active-btn');     
                                    
                                }    
                            }
                                
                            get_orgs();
                            
                      //      show_or_not_orgs_tmp(); 

                        $('#show_carriage').click(function(){      
                            history.pushState('', $(document).find("title").text(), window.location.protocol + '//' + window.location.host + window.location.pathname + "?pg=carriage");
                              
                             remove_active_class_btn($('.card-header'));
                             $(this).addClass('active-btn');

                         //    show_or_not_orgs_tmp();  
                             
                          //   get_models(); она выводит список ВСЕХ моделей, 
                      
                            $('#org_panel_body [data-abb="ЦДМВ"]').html(window.innerCdmv); 
                             console.log(window.innerCdmv);

                            get_roads(); 
              
                            show_detail_body();
                          
                        });

                         $('#show_train').click(function(){
                             history.pushState('', $(document).find("title").text(), window.location.protocol + '//' + window.location.host + window.location.pathname + "?pg=train");

                            remove_active_class_btn($('.card-header'));
                            $(this).addClass('active-btn');
                            
                               $('#org_panel_body [data-abb="ЦДМВ"]').empty();
                                _common.storeSession('last_org', '' );
                                check_active_org();
                            
                        //    show_or_not_orgs_tmp();   
                          
                            get_roads(); 

                            show_detail_body();
                        });                                          
            }
        }
    });
}

function show_or_not_orgs_tmp(){
    if(get_url() === 'carriage'){
        $('#orgs_tmp').css('display', 'none');
    }else if(get_url() === 'train'){
        $('#orgs_tmp').css('display', 'block');
    }
}

function remove_active_class_btn(arr){
     arr.each(function(){
            $(this).removeClass('active-btn');      
     });
}
     

/*

function clear_page() {
    $('.list-body').empty();
    
    clear_detail();
}


function clear_detail() {
    $('#detail_ttl_dots_mnu').empty();
    $('#detail_ttl_curr_rws').empty();
    $('.detail-body').empty();
}
*/

function org_click(e) {   
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    var rid = _common.value_fromElementID($(e).attr('id'));   
      
      if(/цдмв/i.test($(e).text())){
          $('#registr_div_table').empty();
          $('#common_div_docs').empty();
          $('#detail_info_block').empty();
          $('#actions_div').empty();
      }
         
    select_active_org(rid);
    _common.storeSession('last_org', rid);    
}

function road_click(e) {   
    _common.stop_propagation(e);

    var rid = _common.value_fromElementID($(e).attr('id'));
    select_active_road(rid);
    _common.storeSession('last_road', rid);
     
}



function select_active_org(rid){     
  
    $("[id^='tr_org-']").removeClass('tbl-act-cell');
    $('#tr_org-' + rid).addClass('tbl-act-cell');

    get_roads(); 

   /* if(get_url() == 'train'){ 
        get_roads(); 
    }else{
        get_models();
    }
    */
}


function check_active_org(){ // красит активные организацци, или первую
       
   var cur_org =  _common.getStoredSessionStr('last_org');

   if(cur_org.length == 0 || $('#tr_org-'+cur_org).length == 0) { 
       if($('[id^="tr_org-"]').length > 0) {
            
            cur_org = _common.value_fromElementID($('[id^="tr_org-"]').first().attr('id'));
           
           _common.storeSession('last_org', cur_org );     
            select_active_org(cur_org);              
       }
   }else{
             select_active_org(cur_org);
   } 
}


function select_active_road(rid){     

    $("[id^='td_road_ttl-']").removeClass('tbl-act-cell');
    $('#td_road_ttl-' + rid).addClass('tbl-act-cell');
                  
     show_detail_body() ;
 
}


function check_active_road(){ // красит активные организации, или первую
    
   var cur_road =  _common.getStoredSessionStr('last_road');
   if(cur_road.length == 0 || $('#tr_road-'+cur_road).length == 0) { // 
       if($('[id^="tr_road-"]').length > 0) {
           
           cur_road = _common.value_fromElementID($('[id^="tr_road-"]').first().attr('id'));
           
           select_active_road(cur_road);         
       }
   }else{
             select_active_road(cur_road);
   } 
}


function glob_fm_before_show() {
       $("#div_ta_registr").css({ 'overflow-y': 'hidden' });  // IE can show scrollbar over modal
}

function glob_fm_after_show() {
            $("#div_ta_registr").css({ 'overflow-y': 'auto' });
}

function get_openAccPanels(acc_id) {

     var key_name  = acc_id + '_open_panels';
   // if (acc_id == 'orgs')
       // key_name += '-' + _common.getStoredSessionStr(key_name);
    
    return key_name; //_common.getStoredLocalStr(key_name);
}


/********************************** orgs roads***********************************************/

function get_orgs(){
          
    var postForm = {
        'high': '',
        'part' : 'get_orgs',
        'open_panels' : get_openAccPanels('orgs')//,
        //'section' : get_url()
    };

    $.ajax({
           type      : 'POST',
           url       : 'php/jgate.php',
           data      : postForm,
           dataType  : 'json',
           success: function(data){
              
               if(data.success){
                    $('#orgs_tmp').html(data.body.body);
                    window.innerCdmv =  $('#org_panel_body [data-abb="ЦДМВ"]').html();
                    
                    if(get_url() == 'train'){                                             
                       $('#org_panel_body [data-abb="ЦДМВ"]').empty(); 
              //        $('#org_panel_body [data-abb="ЦДМВ"]').prop('hidden', true); 
                      _common.storeSession('last_org', '' );  
                  }    
               }
            },           
           complete: function(){   
		  measure_list();
                  check_active_org(); 
           }   
      });     
}


function get_roads(){
    
    var high = _common.value_fromElementID($('#orgs_tmp .tbl-act-cell').attr('id'));

    var postForm = {
        'high': high,
        'part' : 'get_roads',
    };
    
    $.ajax({
           type      : 'POST',
           url       : 'php/jgate.php',
           data      : postForm,
           dataType  : 'json',
           success: function(data){
               if(data.success){
                   $('#roads_tmp').empty();
                   $('#roads_tmp').append(data.orgs);
               }
           },
           complete: function(){     
               
            setTimeout(function(){
                 check_active_road();
            }, 150);
            
               measure_list();
           }
      });     
}


/**********************************actions , actions by active mdl **************************************************/

function show_actions_for_active_registr(rid){ // покажет меры по адаптации
    
      var url = get_url();
   
      if(rid.length > 0){
          var postForm = {
                  'part': 'get_actions',
                  'rid' : rid,
                  'section' : url,                
              };
                
          $.ajax({
               type      : 'POST',
               url       : 'php/jgate.php',
               data      : postForm,
               dataType  : 'json',
               success: function(data){
                   if(data.success){
                      $('#actions_div').empty().html(data.actions);       
                      
                      measure_detail();
                   }
               }
          });    
      }
}


function show_actions_by_active_mdl(rid){ // покажет меры по адаптации
    
      var url = get_url();

      if(rid.length > 0){
          var postForm = {
                  'part': 'get_actions_by_mdl',
                  'act_mdl_rid' : rid,
              //    'section' : url,                
              };
                
          $.ajax({
               type      : 'POST',
               url       : 'php/jgate.php',
               data      : postForm,
               dataType  : 'json',
               success: function(data){
                   if(data.success){
                      $('#actions_div').empty().html(data.actions);       
                      
                      measure_detail();
                   }
               }
          });    
      }
}


//*********************************************************documentation ***********************************/


function doc_view_click(e){
    //console.log($(e).attr('data-doc'))
    
    _common.close_dropdowns();
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    _viewer.viewer_view("file_put_tmp", "rid", $(e).attr('data-doc'), false);
}

function pass_pdf_view_click(e){
    _common.close_dropdowns();
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    _viewer.viewer_view("file_put_tmp_pdf", "rid", $(e).attr('data-doc'), false);
}
/************************************ passport carriage *********************************************/
// надо переделать чтобы адаптации и документы подгружались из активной tr в roads_tmp

// Их больше нет на странице, вместо них моделт из carr_mdl

function mdl_click(e) {   
    _common.stop_propagation(e);

    var rid = _common.value_fromElementID($(e).attr('id'));
    select_active_mdl(rid);
    _common.storeSession('last_mdl', rid);
}
    
function select_active_mdl(rid){     
  //  var main_org_rid = _common.value_fromElementID($('#orgs_tmp .tbl-act-cell').attr('id'));    
    
 //   $("[id^='td_registr_nm-']").removeClass('tbl-act-cell');
    $("[id^='td_registr_mdl-']").removeClass('tbl-act-cell');
    
  //  $('#td_registr_nm-' + rid).addClass('tbl-act-cell'); 
    $('#td_registr_mdl-' + rid).addClass('tbl-act-cell');

     get_docs_part(rid);
     show_actions_for_active_registr(rid);
     get_pass_pdf_part(rid); //main_org_rid, 
     show_detail_info(rid);
}


function check_active_mdl(){ // красит активные организацци, или первую
    
   var cur_mdl =  _common.getStoredSessionStr('last_mdl');
     
   if(cur_mdl.length == 0 || $('#td_registr_mdl-'+cur_mdl).length == 0) {  
       if($('[id^="td_registr_mdl-"]').length > 0) {
           
           cur_mdl = _common.value_fromElementID($('[id^="td_registr_mdl-"]').first().attr('id'));
           
           _common.storeSession('last_mdl', cur_mdl);  
           select_active_mdl(cur_mdl);      
       
       }
   }else{
           select_active_mdl(cur_mdl);
   } 
}
                
                    
/*************************************** train part ***********************************************/

function train_click(e){
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    var rid = _common.value_fromElementID($(e).attr('id'));
    select_active_train(rid);
    
    _common.storeSession('last_train', rid);
}
  
function select_active_train(rid){     
    
    $("[id^='td_train_num-']").removeClass('tbl-act-cell');
    $("[id^='td_train_route-']").removeClass('tbl-act-cell');
    
    $('#td_train_num-' + rid).addClass('tbl-act-cell'); 
    $('#td_train_route-' + rid).addClass('tbl-act-cell');
    
     show_actions_for_active_registr(rid);
     get_docs_part(rid);
     show_detail_info(rid);
     
}


function check_active_train(){ 
    
   var cur_train =  _common.getStoredSessionStr('last_train');
   
   if(cur_train.length == 0 || $('#td_train_num-'+cur_train).length == 0) {  
       if($('[id^="td_train_num-"]').length > 0) {
           
           cur_train = _common.value_fromElementID($('[id^="td_train_num-"]').first().attr('id'));
           
           _common.storeSession('last_train', cur_train);  
           select_active_train(cur_train);       
       }
   }else{
            select_active_train(cur_train);
   } 
}
/***************************************documentation *********************************************/
function get_docs_part(pid){
    
    if(pid.length > 0){
        
        var postForm = {
            part : 'docs_part',
            pid : pid,
            'section' : get_url(),
        };
        $.ajax({
                url: 'php/jgate.php',
                type: 'POST',
                dataType: 'json',
                data : postForm,
                success: function(data){
                    if(data.success){
                     
                        $('#docs_tmp').empty().append(data.html);
                         _common.refresh_tooltips(); 
                    }
                }
        });
      
    }
}

function get_pass_pdf_part(pid){ //main_org, 
    
    if(pid.length > 0){
       
        var postForm = {
            'part' : 'pass_pdf_part',
            'rid' : pid
          //  'main_org' : main_org,  not actual now
        };
  
        $.ajax({
                url: 'php/jgate.php',
                type: 'POST',
                dataType: 'json',
                data : postForm,
                success: function(data){ 
                    if(data.success){                     
                        $('#passport_tmp').empty().append(data.html);
                         _common.refresh_tooltips(); 
                    }
                }
        });
      
    } 
}


/************************************** excel reestr ********************************************/
//tr_registr-
function refresh_reestr_dropdown() {
	if ($('[id^="tr_registr-"]').length != 0  || $('[id^="tr_train-"]').length != 0) { //  $('[id^="tr_active_mdl_road-"]')
                $('#show_form_4_reestr').removeClass('disabled');
		$('#show_group_reestr').removeClass('disabled');	
	}
	else{ 
	     $('#show_form_4_reestr').addClass('disabled');
	     $('#show_group_reestr').addClass('disabled');	
	} 
}


function get_form_for_reestr(){
    var url = get_url();
    var road_rid, rid_mdl_or_train;
    
    if(url == 'train'){
        road_rid = _common.value_fromElementID($('#roads_tmp .tbl-act-cell').attr('id'));
        rid_mdl_or_train = _common.value_fromElementID($('[id^="tr_train-"]').find('.tbl-act-cell').attr('id')); 
    }else if(url == 'carriage'){
        road_rid = _common.value_fromElementID($('#roads_tmp .tbl-act-cell').attr('id'));
        rid_mdl_or_train = _common.value_fromElementID($('[id^="tr_registr-"]').find('.tbl-act-cell').attr('id')); 
        
        //road_rid = _common.value_fromElementID($('#ta_active_mdl_road').find('.tbl-act-cell').attr('id'));
        //rid_mdl_or_train = _common.value_fromElementID($('#roads_tmp .tbl-act-cell').attr('id'));      
    }

   if(rid_mdl_or_train.length > 0){
       
        var postForm = {
            'part' : 'rep_carriage',
            'rid_mdl' : rid_mdl_or_train,
            'section' : url,
            'ttl' : road_rid
        };

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
            success: function(data){
                  if(data.success){
                      _viewer.viewer_viewFile(data.frelname, false);
                       //console.log(data);
                  }
            },
        });
    } 
}

function show_group_excel(){ // ЭТО РЕЕСТР по баёлонсодержателю, сейчас не актуален
    var url = get_url();
    var rid_road = '';
    
    (url == 'train') ? rid_road = _common.value_fromElementID($('#roads_tmp .tbl-act-cell').attr('id')) 
                     : rid_road = _common.value_fromElementID($('[id^="td_active_mdl_road_ttl-"].tbl-act-cell').attr('id')); // [id^="td_active_mdl_road_ttl-"] - это серёдка

    var postForm = {
            'part' : 'road_reestr',
            'rid_road' : rid_road,
            'section' : url
        };
      
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
            success: function(data){			
                if(data.success){
                      _viewer.viewer_viewFile(data.frelname, false);
                      //console.log(data);
                 
                }
            }
      });
}

function show_org_excel(){
    
    var rid_org = _common.value_fromElementID($('#orgs_tmp .tbl-act-cell').attr('id'));
    var url = get_url();
        
        var postForm = {
            'part' : 'main_org_reestr',
            'rid_org' : rid_org,
            'section' : url,
        };
        
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
            success: function(data){
                if(data.success){
                   
                      _viewer.viewer_viewFile(data.frelname, false);
                }
            }
      });
}

/********************************************** categoryes ******************************************/

function show_categoryes_form(){
    var rid_fpk, rid_dss, rid_cdmv;
 
    $('.ta-org-tbody').children().children().each(function(tr){        
        if(/ДОСС/i.test($(this).text())){
          rid_dss = _common.value_fromElementID($(this).attr('id'));
        }else if(/ФПК/i.test($(this).text())){
          rid_fpk = _common.value_fromElementID($(this).attr('id'));
        }else if (/ЦДМВ/i.test($(this).text())){
          rid_cdmv = _common.value_fromElementID($(this).attr('id'));
        }
        
    });
    
  //  console.log(rid_fpk, rid_dss, rid_cdmv);
    
    var postForm = {
        'part': 'get_fm',
        'fm_id' : 'categoryes_form'
        
    };

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
            success: function(data){
                if(data.success){
                    
                   $('#div_tmp').html(data.html); 
                    
                   $('#categoryes_form')//.attr('data-rid', rid)
                        .on('show.bs.modal', function(){      
                            
                            if(get_url() == 'train'){
                                $('#all_categories_list').empty();
                                $('#row_cdmv').empty();
                            }
                                                        
                            $('#chosen_category_ok').unbind('click').on('click',function() {
                                send_chosen_category();
                            });
                            
                            $('#categories_none').unbind('click').on('click', function(){
                                $('#all_directs').find($('[type="checkbox"]')).each(function(){
                                     $(this).prop('checked', false);
                                });
                            });
                      
                            $('#categories_all').unbind('click').on('click', function(){
                                $('#all_directs').find($('[type="checkbox"]')).each(function(){  
                                    $(this).prop('checked', true);
                                });
                                
                               // $('[id=\'type_cdmv\']').prop('checked', false);
                                                                
                            });
                           
                        })
                        .on('shown.bs.modal', function(){  
                            $('#type_dss').attr('data-rid', rid_dss);
                            $('#type_fpk').attr('data-rid', rid_fpk);
                            $('#type_cdmv').attr('data-rid', rid_cdmv);
                        })
                        .modal('show');                     
                }
            }
      });
}

function send_chosen_category(){  
    var ini_str = '';

     $('.reestr-type:checked').each(function(i){    
        if(ini_str.length > 0){
             ini_str += ',';
        }
             // ini_str = '2998bb6a-57cb-44ca-a625-1e3dcf7afc1c','38400d1f-dc47-4915-b44d-45ed3ae494a0','cc6f0f1a-3866-4e79-b3ab-b9bca0029f40' представляет из себя это
             ini_str += '\'' + $(this).attr('data-rid') + '\'';         
    });

    var postForm  = {
        'part': 'main_org_reestr_category',
        'ini_str' : ini_str,
        'section' : get_url(),
        'k' : $('#cat_params_k').prop('checked') ? 1 : 0,
        'o': $('#cat_params_o').prop('checked') ? 1 : 0,
        's' : $('#cat_params_s').prop('checked') ? 1 : 0,
        'g' : $('#cat_params_g').prop('checked') ? 1 : 0,
        'dp' : $('#cat_params_rate_1').prop('checked') ? 1 : 0,
        'nd': $('#cat_params_rate_2').prop('checked') ? 1 : 0,
        'dc': $('#cat_params_rate_3').prop('checked') ? 1 : 0               
    };
    
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
         success: function(data){
             if(data.success){                    
                $('#categoryes_form').modal('hide');
                         
               // _common.say_noty_warn(data.frelname);
                            
                 _viewer.viewer_viewFile(data.frelname, false);          
             }
         }
   });  
}


function show_cat_params_form(){
     var postForm = {
        'part': 'get_fm',
        'fm_id' : 'fm_cat_params'
        
    };
    
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
            success: function(data){
                if(data.success){
                    
                   $('#div_tmp').html(data.html); 
                    
                   $('#fm_cat_params')//.attr('data-rid', rid)
                        .on('show.bs.modal', function(){                          
                             
                            $('#cat_params_ok').unbind('click').on('click',function() {
                                send_cat_params();
                            });          
                      
                        })
                        .on('shown.bs.modal', function(){   
                        })
                        .modal('show');                     
                }
            }
      });
}

function send_cat_params(){
    
    var postForm = {
        'part': 'send_cat_params',
        'k' : $('#cat_params_k').prop('checked') ? 1 : 0,
        'o' : $('#cat_params_o').prop('checked') ? 1 : 0,
        's' : $('#cat_params_s').prop('checked') ? 1 : 0,
        'g' : $('#cat_params_g').prop('checked') ? 1 : 0,
        'dp' : $('#cat_params_rate_1').prop('checked') ? 1 : 0,
        'nd' : $('#cat_params_rate_2').prop('checked') ? 1 : 0,
        'dc' : $('#cat_params_rate_3').prop('checked') ? 1 : 0       
    };
    if(($('#cat_params_k').prop('checked') ||$('#cat_params_o').prop('checked') || 
        $('#cat_params_s').prop('checked') || $('#cat_params_g').prop('checked')) && 
        ($('#cat_params_rate_1').prop('checked') || $('#cat_params_rate_2').prop('checked') || $('#cat_params_rate_3').prop('checked'))) {
    
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
            success: function(data){
                if(data.success){
                        $('#fm_cat_params').modal('hide');
                         
                       _viewer.viewer_viewFile(data.frelname, false);
                }
            }
        });
    }else _common.say_modal_err("Не менее одного флажка в каждой группе");
}


function show_cat_params_train_form(){
     var postForm = {
        'part': 'get_fm',
        'fm_id' : 'fm_cat_params_train'       
    };
    
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
            success: function(data){
                if(data.success){
                    
                   $('#div_tmp').html(data.html); 
                    
                   $('#fm_cat_params_train')
                        .on('show.bs.modal', function(){                                                       
                            $('#cat_params_train_ok').unbind('click').on('click',function() {
                                send_cat_params_train();
                            });                               
                        })
                        .on('shown.bs.modal', function(){  

                        })
                        .modal('show');                     
                }
            }
      });
}

function send_cat_params_train(){
    
    var postForm = {
        'part': 'send_cat_params_train',
        'dp' : $('#cat_params_train_1').prop('checked') ? 1 : 0,
        'nd' : $('#cat_params_train_3').prop('checked') ? 1 : 0,
        'dc' : $('#cat_params_train_2').prop('checked') ? 1 : 0       
    };
    
    if($('#cat_params_train_1').prop('checked') || $('#cat_params_train_2').prop('checked') || $('#cat_params_train_3').prop('checked') ){
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
            success: function(data){
                 //console.log(postForm);
                     if(data.success){
                             $('#fm_cat_params_train').modal('hide');
                            _viewer.viewer_viewFile(data.frelname, false);
                     }
            }
        });
    }else _common.say_modal_err("Хотя бы один флажок в каждом поле должен быть выбран");
}


/****************************************multilevel*********************************************************/

function show_multilevel_from(){
    
    var postForm = {
      'part': 'get_fm',
      'fm_id' : 'multilevel_form'
    };
    
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        beforeSend: function() {
                  $(".y-ajax-wait").css('visibility', 'visible');
        },
        complete: function() {
             $(".y-ajax-wait").css('visibility', 'hidden');
        }, 
        success: function(data){
           if(data.success){
                   $('#div_tmp').html(data.html); 
                    
                   $('#multilevel_form')
                        .on('show.bs.modal', function(){                                                       
                            $('#multilevel_params_ok').unbind('click').on('click',function() {
                                send_multilevel_params();
                            });                                                        
                            
                        })
                        .on('shown.bs.modal', function(){

                        })
                        .modal('show');      
           }   
        }
    });
}


function send_multilevel_params(){
    
    var ini_str = '';
    
    $('.name-road:checked').each(function(){
      if(!$(this).hasClass('d-none')){
        if(ini_str.length > 0)
            ini_str += ',';

        ini_str += "'"+_common.value_fromElementID($(this).attr('id'))+"'";
      }
    });

    var postForm = {
        'part': 'send_multi_params',
        'k' : $('#param-K').prop('checked') ? 1 : 0,
        'o' : $('#param-O').prop('checked') ? 1 : 0,
        's' : $('#param-S').prop('checked') ? 1 : 0,
        'g' : $('#param-G').prop('checked') ? 1 : 0,
        'dp' : $('#cdr_param_dst-1').prop('checked') ? 1 : 0,
        'nd' : $('#cdr_param_dst-2').prop('checked') ? 1 : 0,
        'dc' : $('#cdr_param_dst-3').prop('checked') ? 1 : 0,
        'ini_str' : ini_str
    };  
console.log(postForm);    
    if(ini_str.length > 0 && 
      ($('#param-K').prop('checked') || $('#param-O').prop('checked') || $('#param-S').prop('checked') || $('#param-G').prop('checked')) && 
      ($('#cdr_param_dst-1').prop('checked') || $('#cdr_param_dst-2').prop('checked') || $('#cdr_param_dst-3').prop('checked'))){
 
        $.ajax({
            type: 'POST',
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            beforeSend: function() {
                      $(".y-ajax-wait").css('visibility', 'visible');
            },
            complete: function() {
                 $(".y-ajax-wait").css('visibility', 'hidden');
            },
            success: function(data){
              if(data.success){                    
                $('#multilevel_form').modal('hide');

                 _viewer.viewer_viewFile(data.frelname, false);     
                 
                 ini_str = '';
              }         
            }
        });
    }else _common.say_modal_err("Хотя бы один флажок в каждом поле должен быть выбран");
}

function show_multilevel_train_from (){
     
    var postForm = {
      'part': 'get_fm',
      'fm_id' : 'multi_train_form',
      'section' : get_url()
    };
 
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        beforeSend: function() {
                  $(".y-ajax-wait").css('visibility', 'visible');
        },
        complete: function() {
             $(".y-ajax-wait").css('visibility', 'hidden');
        }, 
        success: function(data){
           if(data.success){
                   $('#div_tmp').html(data.html); 
                    
                   $('#multi_train_form')
                        .on('show.bs.modal', function(){                                                       
                            $('#multi_train_params_ok').unbind('click').on('click',function() {
                                send_multi_train_params();
                            });                               
                        })
                        .on('shown.bs.modal', function(){  

                        })
                        .modal('show');      
           }   
        }
    });  
}

function send_multi_train_params(){
    var ini_str = '';
    $('.name-road:checked').each(function(){
        if(!$(this).hasClass('d-none')){
            if(ini_str.length > 0) ini_str += ','

            ini_str += "'" + _common.value_fromElementID($(this).attr('id')) + "'";
        }
    });
    
    var postForm = {
        'part' : 'send_multi_train',
        'ini_str' :ini_str,
        'dp' : $('#train_param-1').prop('checked') ? 1 : 0,
        'dc' : $('#train_param-2').prop('checked') ? 1 : 0,
        'nd' : $('#train_param-3').prop('checked') ? 1 : 0,
    };
    
    if(ini_str.length > 0 && ($('#train_param-1').prop('checked') || $('#train_param-2').prop('checked') || $('#train_param-3').prop('checked'))){
        $.ajax({
            type: 'POST',
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            beforeSend: function() {
                      $(".y-ajax-wait").css('visibility', 'visible');
            },
            complete: function() {
                 $(".y-ajax-wait").css('visibility', 'hidden');
            }, 
            success: function(data){
               if(data.success){
                  $('#multi_train_form').modal('hide');

                 _viewer.viewer_viewFile(data.frelname, false);   
               }   
            }
        });
        
    }else _common.say_modal_err("Хотя бы один флажок в каждом поле должен быть выбран");
}

/************************************ models ***************************************/

function get_models(){
    
    var lastpage = _common.get_currentLastpage('carriage');  // как 1-й параметр в assist make_pagination (конец get_all_models())

    var postForm = {
        'part' : 'get_models',
        'offset' :  lastpage * 10,  // не знаю как посчитать lastpage  и currpage 
        'rows'   : 10,
        'currpage' : lastpage,
    };

    $.ajax({
           type      : 'POST',
           url       : 'php/jgate.php',
           data      : postForm,
           dataType  : 'json',
           success: function(data){
               if(data.success){

                        $('#roads_tmp').empty().append(data.search + data.models+ '<div id=\'pagination\'>' + data.pagination + '</div>'); // 
                        //$('#roads_tmp').empty().append(data.models);
                        
                        
                        if ($('.carriage-pagination').length == 0)
                            _common.storeSession('carriage_last_page', -1);
                        else {
                            var li_id = '#carriage_li_to_page-' + lastpage;

                            $(li_id + '.active').removeClass('active');
                            $(li_id).addClass('active');
                        }

               }
           },
           complete: function(){     
               
            setTimeout(function(){              
                check_active_model(); 
                
                start_search();
                
            }, 150);
            
               measure_list();
           }
      });     
}


function start_search(){
        $('#srch_box')
           .autocomplete({
               serviceUrl: 'php/search.php',
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
               },
               onSearchStart: function () {
               },
               onSearchComplete: function (query, suggestions) {
               },
               //onInvalidateSelection: function (suggestion) {
               //},
               onHide: function (container) {   // call only when suggestions found or "No results" was visible. So set showNoSuggestionNotice to true
               },
               beforeRender: function (container, suggestions) {
                   /*
                   container.find('.autocomplete-suggestion').each(function(i, suggestion){
                       $(suggestion).html($(suggestion).html()
                                                   .replace('{отд}', "<small class='y-llgray-text'>&lt;отд&gt;</small>")
                                                   .replace('{уч}', "<small class='y-llgray-text'>&lt;уч&gt;</small>"));

                   });  */
               }
           }); 
}

function select_search_item(rid) {  // ex: rws:879876876.... перескакивает на нужное место в таблице
    
    var postForm = {
       'part' : 'search_get_model_row',
       'rid'  : rid
    };
    
//_common.say_noty_err(rid);
//alert(rid);

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        success   : function(data) {
                        if (data.success) {
                            
                            var postForm = {
                               'part' : 'carriage_get_rec',
                               'rid'  : rid
                            };

                            $.ajax({
                                type      : 'POST',
                                url       : 'php/jgate.php',
                                data      : postForm,
                                dataType  : 'json',
                                success   : function(carr_data) {
                                                if (carr_data.success) {
                                                  //  $$_scroll_to_id = '#td_mdl_ttl_' + rid; 
                                                   
                                                     // $$_scroll_to_id = $('[data-rid=' + rid + ']')

                                                   _common.storeSession('last_model', carr_data.carr_mdl); // carr_mdl
                           
                                                    if (data.rownum >= 0) {
                                                        //console.log(data.rownum);
                                                        start_gopage('carriage', Math.floor(data.rownum/10));
                                                    }
                                                }                            
                                            }
                            });
                        }
        }
    });
}


function model_click(e) {   
    _common.stop_propagation(e);

    var rid = _common.value_fromElementID($(e).attr('id'));
    select_active_model(rid);
    _common.storeSession('last_model', rid);
 
}
    
function select_active_model(rid){     
    
    $("[id^='td_mdl_ttl-']").removeClass('tbl-act-cell');
    $('#td_mdl_ttl-' + rid).addClass('tbl-act-cell'); 
    
    show_detail_body();
}


function check_active_model(){ // красит активные организацци, или первую
    
   var cur_mdl = _common.getStoredSessionStr('last_model');
   
//_common.say_noty_warn(cur_mdl);
   
   if(cur_mdl.length == 0 || $('#td_mdl_ttl-'+cur_mdl).length == 0) {  
       if($('[id^="td_mdl_ttl-"]').length > 0) {
           
           cur_mdl = _common.value_fromElementID($('[id^="td_mdl_ttl-"]').first().attr('id'));
           
           _common.storeSession('last_model', cur_mdl);  
        
           select_active_model(cur_mdl);       
       }
   }else{
             select_active_model(cur_mdl);
   } 
}

function select_active_road_4Active_mdl(rid){      
    $("[id^='td_active_mdl_road_ttl-']").removeClass('tbl-act-cell');
    $('#td_active_mdl_road_ttl-' + rid).addClass('tbl-act-cell');  
}


function check_active_road_4Active_mdl(){  // вызывается в show_detail_body
    
   var cur_road =  _common.getStoredSessionStr('last_road_active_mdl');
       
   if(cur_road.length == 0 || $('#td_active_mdl_road_ttl-'+cur_road).length == 0) { 
    
       if($('[id^="td_active_mdl_road_ttl-"]').length > 0) {
           
           cur_road = _common.value_fromElementID($('[id^="td_active_mdl_road_ttl-"]').first().attr('id'));
           
           _common.storeSession('last_road_active_mdl', cur_road);  
           select_active_road_4Active_mdl(cur_road);        
       }
   }else{
             select_active_road_4Active_mdl(cur_road);
   } 
}


/* вызывалась по клику на ссылку, пока не нужна
function mdl_show_click(e){    
            //show_form_Carriage(_common.value_fromElementID($(e).attr('id')), $(e).attr('data-carr_mdl'));  
   console.log(_common.value_fromElementID($(e).attr('id')), $(e).attr('data-rid'))
} 
*/
function show_detail_info(rid){

    var postForm = {
        'part': 'get_detail_info',
        'rid' : rid,
        'section': get_url()
    };
    
   $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){
                  $('#detail_info_block').empty().append(data.detail_info);
            }
        }            
    });
  
}

function getTarget(e){
    
    $(e).find('[type="checkbox"]').prop('checked') ? $('[data-form-high="'+$(e).attr('id')+'"]').find('[type="checkbox"]').removeClass('d-none')  
                                                   : $('[data-form-high="'+$(e).attr('id')+'"]').find('[type="checkbox"]').addClass('d-none');
    
    $(e).find('[type="checkbox"]').prop('checked') ? $('[data-form-high="'+$(e).attr('id')+'"]').removeClass('d-none')  
                                                   : $('[data-form-high="'+$(e).attr('id')+'"]').addClass('d-none');
                                          
}


function check_all_click(e){
     $(e).parent().closest('[id^="all"]').find('input').prop('checked', true);
    
     if($(e).attr('id') == 'all_orgs_check'){
        $('#all_roads_list').children().each(function(){
            if($(this).hasClass('card-body')){
                $(this).children().each(function(){
                    $(this).find('[type="checkbox"]').parent().removeClass('d-none');
                    $(this).find('[type="checkbox"]').removeClass('d-none');                    
                });
            }            
        });
     }     
}

function uncheck_all_click(e){
     $(e).parent().closest('[id^="all"]').find('input').prop('checked', false);
     
    if($(e).attr('id') == 'all_orgs_none'){
       $('#all_roads_list').children().each(function(){
            if($(this).hasClass('card-body')){
                $(this).children().each(function(){
                    $(this).find('[type="checkbox"]').parent().addClass('d-none');
                    $(this).find('[type="checkbox"]').addClass('d-none');                    
                });
            }            
        });
     }   
}

function drop_all(){
    $('#all_roads_list').children().each(function(){
            if($(this).hasClass('card-body')){
                $(this).children().each(function(){
                    $(this).find('[type="checkbox"]').parent().addClass('d-none');
                    $(this).find('[type="checkbox"]').addClass('d-none');                    
                });
            }            
    });
    
    $('[type="checkbox"]').prop('checked', false);
}
function check_all(){
    $('#all_roads_list').children().each(function(){
            if($(this).hasClass('card-body')){
                $(this).children().each(function(){
                    $(this).find('[type="checkbox"]').parent().removeClass('d-none');
                    $(this).find('[type="checkbox"]').removeClass('d-none');                    
                });
            }            
     });
     
     $('[type="checkbox"]').prop('checked', true);
}
/*  формы пока не нужны
function show_form_Carriage(rid, carr_mdl){
            // здесь беруться параметры из ссылки, они указаны верно
                  
                // Катя, здесь ты прочитываешь значения
    var nm = $('#tr_mdl-'+carr_mdl).attr('data-obj_nm')
        mdl = $('#td_mdl_ttl-'+carr_mdl).text(),
        dt_num_pass = $('#tr_mdl-'+carr_mdl).attr('dat-date_num_reg'),
        docs_constr = $('#tr_mdl-'+carr_mdl).attr('data-docs_constr'),
        docs_modern = $('#tr_mdl-'+carr_mdl).attr('data-docs_modern'),
        exp_res = $('#tr_mdl-'+carr_mdl).attr('data-exp_res'),
        recoms_using = $('#tr_mdl-'+carr_mdl).attr('data-recoms_using'),
        act_dt = $('#tr_mdl-'+carr_mdl).attr('data-date_act'),
        note = $('#tr_mdl-'+carr_mdl).attr('data-note'),
        flg = $('#tr_mdl-'+carr_mdl).attr('data-flg');
 
    var postForm = {
        'part': 'get_fm',
        'fm_id' : 'form_for_railwayCarriage'
        
    };
           
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){                               
                    $('#div_tmp').html(data.html);

                                // ПРОЧИТЫВАТЬ инпуты В shown
                               $('#actual_dt_inf').datepicker({
                                                format: 'dd.mm.yyyy',
                                                autoclose: true,
                                                keyboardNavigation: false,
                                                language: 'ru',
                                                startDate: '01.01.1901'
                                });
                    
                    
                    
                    $('#form_for_railwayCarriage').attr('data-rid', rid).attr('data-carr_mdl', carr_mdl) // ????????????
                        .on('show.bs.modal', function(){                                                                                          
                                 $('#send_form_click').css('display', 'none' );
                                 $('#form_for_railwayCarriage').find('input').prop('disabled', true); 
                        
                        })
                        .on('shown.bs.modal', function () {    
                            
                            
                                              // оператор & умножает биты, | - складывает их
                                              // & 0x3 - обнуляет все биты кроме 2ч последних, (0x3 - и есть два последних бита), оператор & обнуляет все остальные биты кроме 0x3
                            $('[id^="k-'+(flg & 0x3)+'"]').prop('checked', true);
                            
                            console.log($('[id^="k-'+(flg & 0x3)+'"]').prop('checked', true))
                            
                                               // flg >> 2 cместить вправо на 2 бита(чтобы прочесть последние два и не считать их)
                             $('[id^="o-'+((flg >> 2) & 0x3)+'"]').prop('checked', true);
                                             // (flg >> 2) & 0x3 получается 1
                             
                                              // flg >> 4 cместить вправо на 4 бита(чтобы прочесть последние два и не считать их)
                             $('[id="c-'+((flg >> 4) & 0x3)+'"]').prop('checked', true);
                                     // (flg >> 4) & 0x3 получается 2
                             
                             $('[id="g-'+((flg >> 6) & 0x3)+'"]').prop('checked', true);
                                          // (flg >> 6) & 0x3 получается 3
                                          
                             var flm = (flg >> 8) & 0x3; 
                             (flm == 1) ? $('#mark').prop('checked', true) : $('#mark').prop('checked', false);
                                           
                            $('#name_object').val(nm);
                            $('#mdl').val(mdl);
                            $('#date_num_passport').val(dt_num_pass);
                            $('#docs_constr').val(docs_constr);
                            $('#docs_modern').val(docs_modern);
                            $('#expected_result').val(exp_res);
                            $('#recomendations_using').val(recoms_using); 
                            $('#note').val(note);
                            
                            if (act_dt.length > 0) $('#actual_dt_inf').datepicker('update', _common.date_ymd2DDMMYYYY(act_dt, '.'));
                            
                           
                        })
                        .modal('show');
            }
        }
   });
}   

function train_show_click(e){
    show_train_form(_common.value_fromElementID($(e).attr('id')))
}


function show_train_form(rid){

        var num_train = $('#tr_train-'+rid).attr('data-num_train'), 
            route = $('#tr_train-'+rid).attr('data-route'),
            adr_formation = $('#tr_train-'+rid).attr('data-adr_formation'),
            date_num_registr = $('#tr_train-'+rid).attr('data-date_num_registr'), 
            flg = $('#tr_train-'+rid).attr('data-flg'), 
            recoms_using = $('#tr_train-'+rid).attr('data-recoms_using'),       
            exp_res = $('#tr_train-'+rid).attr('data-exp_res'),       
            date_act = $('#tr_train-'+rid).attr('data-date_act'),    
            note = $('#tr_train-'+rid).attr('data-note'),    
            rid_road = $('#tr_train-'+rid).attr('data-rid_road'),     
            carr_pasport = $('#tr_train-'+rid).attr('data-carr_pasport');     

    
    var postForm = {
        'part': 'get_fm',
        'fm_id' : 'form_for_train'       
    }
    
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){                               
                    $('#div_tmp').html(data.html); 
                    
                        $('#actualization_date').datepicker({
                                format: 'dd.mm.yyyy',
                                autoclose: true,
                                keyboardNavigation: false,
                                language: 'ru',
                                startDate: '01.01.1901'
                        });                            
                    
                        $('#form_for_train').attr('data-rid', rid)
                            .on('show.bs.modal', function(){
                                $('#train_ok').css('display', 'none'); 
                                $('#form_for_train').find('input').prop('disabled', true); 
                        }) 
                        .on('shown.bs.modal', function () { 
                                                                                                                        
                            var presence_spc_carr = (flg & 0x3);
                            (presence_spc_carr == 1) ? $('#spc_carriage').prop('checked', true) : $('#spc_carriage').prop('checked', false);
                            
                            $('[id^="total-'+((flg >> 2) & 0x3)+'"]').prop('checked', true);
                            
                            var mark_OOI = ((flg >> 4) & 0x3);
                            (mark_OOI == 1) ? $('#mark_OOI').prop('checked', true) : $('#mark_OOI').prop('checked', false);

                            var workers_prc =((flg >> 8) & 0xFF);
                            (workers_prc !== 0) ? $('#quan_spc_workers').val(workers_prc) : $('#quan_spc_workers').val('');
                            
                            var service_prc = ((flg >> 16) & 0xFF);
                            (service_prc !== 0) ? $('#weight_service').val(service_prc) : $('#weight_service').val('');
                            
                            
                            $('#number_train').val(num_train);
                            $('#route').val(route);
                            $('#date_num_passport').val(date_num_registr);
                            $('#firm_adress').val(adr_formation);
                            $('#awaiting_result').val(exp_res);
                            $('#recomendations_for_using').val(recoms_using);
                            $('#note').val(note);
                            
                            if(date_act.length > 0) $('#actualization_date').datepicker('update', _common.date_ymd2DDMMYYYY(date_act, '.'));
                        })
                        .modal('show');
            }
        }
    });
}
*/

/*
function show_roads_for_active_model(){
   var carr_mdl = _common.value_fromElementID($('#roads_tmp .tbl-act-cell').attr('id')),
       main_org_rid = _common.value_fromElementID($('#orgs_tmp .tbl-act-cell').attr('id'));
           
   var postForm = {
       'part' : 'show_roads_for_active_model',
       'carr_mdl' : carr_mdl,
       'high' : main_org_rid
   }; 
   
   $.ajax({
           type      : 'POST',
           url       : 'php/jgate.php',
           data      : postForm,
           dataType  : 'json',
           success: function(data){
               if(data.success){
                       $('#registr_div_table').html(data.roads);
               }
           },
           complete: function(){     
               
            setTimeout(function(){
                check_active_road_4Active_mdl();   
            }, 150);
            
               measure_list();
           }
      });     
}
*/

function get_reestr (){
     $('#form_for_reestr').modal('hide');
}

function show_sideout() {
    _sout.prepare_sideout();
    
    setTimeout(function(){
        $("#sideout").addClass("active");
    }, 150);
}

function fill_sideout() {
}

function app_who() {
    return  'ДМГН ПС Оператор';
}

function get_url(){
    var currentPg = $.url().param('pg');
    return typeof(currentPg) === 'string' ? currentPg : '';     
}

function clear_detail_body(){
     $('#actions_div').empty();
     $('.detail-body').empty();
}

function toggle_updn(e){
    $(e).children().toggleClass('fa-angle-up');
}
