<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pointsbase/php/_dbpoints.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pointsbase/php/_asspoints.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';
include_once 'pdf_report.php';

class assist {

    public static $copyright_str = '&copy;ПКТБ Л, 2020', 
                  $plaining_period = [0 => "", 1 =>  "по достижению срока проведение кап. ремонта", 2 => "по достижению срока/пробега проведение кап. ремонта"];
    
    public function __construct() {
        if (strlen(trim(session_id())) == 0)
            session_start();
    }
    
    public static function siteRootDir() : string {    // site root must have index.php. directory will return with starting slash, ex: /IcmrM
        return _assbase::siteRootDir_($_SERVER['PHP_SELF']);
    }
    
    private function make_pagination(string $pg_id, int $offset, int $rows, int $currpage, int $totalrows) : string {
        $ass = new _assbase();
        $result = $ass->make_pagination($pg_id, $offset, $rows, $currpage, $totalrows);
        unset($ass);
        
        return $result;
    }

    public function get_fm(string $fm_name, string $sparam = '', string $section = ''){
        $result = '';
        
        $fm_path = 'forms/'. $fm_name .'.php';
        
        if(file_exists($fm_path)){
            $form = file_get_contents($fm_path);
            
            if($fm_name == 'form_works_adaptation'){
               
                $options = "";
                
                foreach(self::$plaining_period as $key => $val)
                       $options .= "<option value='".$key."'>".$val."</option>";
                
                $form = str_replace('{ROWS}', $options, $form);
            }
            else if($fm_name == 'multilevel_form' || $fm_name == 'multi_train_form'){  // ??
                $db = new db_depo();                
                $directs_records = $db->org_getList('');
                
                $directs_list = '';
                
                $roads_list_FPK = '';
                $roads_list_DOSS = '';
                $roads_list_CDMV = '';
                $roads_list_Total = '';                
                
                $FPK_rid = '';
                $CDMV_rid = '';
                $DOSS_rid = '';
                               
                foreach($directs_records as $row){
                    if(preg_match('#ДОСС#i', $row['abb'])) $DOSS_rid = $row['rid'];
                    else if (preg_match('#ФПК#i', $row['abb'])){
                        $FPK_rid = $row['rid'];
                    }else if(preg_match('#ЦДМВ#i', $row['abb'])){
                        $CDMV_rid = $row['rid'];
                    }
                }
                
                $roads_records_FPK = $db->get_roads_list_by_high_org_vw($FPK_rid);  
                $roads_records_DOSS = $db->get_roads_list_by_high_org_vw($DOSS_rid);  
                $roads_records_CDMV = $db->get_roads_list_by_high_org_vw($CDMV_rid);  
                
                unset($db);
                
                // нужно по отдельности вывести дороги для каждой дирекуции
                for($i = 0; $i < count($roads_records_FPK); $i++){
                    $roads_list_FPK .=  "<div class='custom-control custom-checkbox d-none' data-flg='".$roads_records_FPK[$i]['flg']."' data-habb='".$roads_records_FPK[$i]['habb']."' data-httl='".$roads_records_FPK[$i]['httl']."' data-form-high='".$roads_records_FPK[$i]['high']."'>" .
                                            "<input id='road-" . $roads_records_FPK[$i]['rid'] . "' type='checkbox' class='custom-control-input name-road d-none' " .
                                                "data-shrt=''>" .
                                            "<label class='custom-control-label y-dgray-text' for='road-" . $roads_records_FPK[$i]['rid'] . "'>" .
                                                $roads_records_FPK[$i]['ttl'] . " <span class='y-lgray-text'>&lsaquo;" . $roads_records_FPK[$i]['abb'] . "&rsaquo;</span>" .
                                            "</label>" .
                                        "</div>";
                    
                    if($i == (count($roads_records_FPK) - 1)) $roads_list_FPK .= "<div class='d-none last-div".$roads_records_FPK[$i]['high']."'><hr></div>";
                }    
                $roads_list_Total .= $roads_list_FPK;
                
                for($i = 0; $i < count($roads_records_DOSS); $i++){
                    $roads_list_DOSS .=  "<div class='custom-control custom-checkbox d-none' data-flg='".$roads_records_DOSS[$i]['flg']."' data-habb='".$roads_records_DOSS[$i]['habb']."' data-httl='".$roads_records_DOSS[$i]['httl']."' data-form-high='".$roads_records_DOSS[$i]['high']."'>" .
                                            "<input id='road-" . $roads_records_DOSS[$i]['rid'] . "' type='checkbox' class='custom-control-input name-road d-none' " .
                                                "data-shrt=''>" .
                                            "<label class='custom-control-label y-dgray-text' for='road-" . $roads_records_DOSS[$i]['rid'] . "'>" .
                                                $roads_records_DOSS[$i]['ttl'] . " <span class='y-lgray-text'>&lsaquo;" . $roads_records_DOSS[$i]['abb'] . "&rsaquo;</span>" .
                                            "</label>" .
                                        "</div>";
                    
                    if($i == (count($roads_records_DOSS) - 1)) $roads_list_DOSS .= "<div class='d-none last_div-".$roads_records_DOSS[$i]['high']."'><hr></div>";
                }    
                
                 $roads_list_Total .= $roads_list_DOSS;
                 
                 
                if($section == 'train') 
                   $roads_list_CDMV .= '';
                else{ 
                   for($i = 0; $i < count($roads_records_CDMV); $i++){
                    $roads_list_CDMV .=  "<div class='custom-control custom-checkbox d-none' data-flg='".$roads_records_CDMV[$i]['flg']."' data-habb='".$roads_records_CDMV[$i]['habb']."' data-httl='".$roads_records_CDMV[$i]['httl']."' data-form-high='".$roads_records_CDMV[$i]['high']."'>" .
                                            "<input id='road-" . $roads_records_CDMV[$i]['rid'] . "' type='checkbox' class='custom-control-input name-road d-none' " .
                                                "data-shrt=''>" .
                                            "<label class='custom-control-label y-dgray-text' for='road-" . $roads_records_CDMV[$i]['rid'] . "'>" .
                                                $roads_records_CDMV[$i]['ttl'] . " <span class='y-lgray-text'>&lsaquo;" . $roads_records_CDMV[$i]['abb'] . "&rsaquo;</span>" .
                                            "</label>" .
                                        "</div>";
                    
                    if($i == (count($roads_records_CDMV) - 1)) $roads_list_CDMV .= "<div class='d-none last_div-".$roads_records_CDMV[$i]['high']."'><hr></div>";
                    }
                }
                
                
                   
                $roads_list_Total .= $roads_list_CDMV;
                
           
                foreach($directs_records as $row){
                    if(preg_match('#ЦДМВ#i' ,$row['abb']) == 1 AND $section == 'train') continue;
                    
                    $directs_list .=  "<div onclick='getTarget(this);' id='". $row['rid'] ."' class='custom-control custom-checkbox directs' data-flg='".$row['flg']."' data-habb='".$row['abb']."' data-httl='".$row['ttl']."'>" . 
                                            "<input id='road-" . $row['rid'] . "' type='checkbox' class='custom-control-input name-road d-none' " .
                                                "data-shrt=''>" . 
                                            "<label class='custom-control-label y-dgray-text' for='road-" . $row['rid'] . "'>" .
                                                $row['ttl'] . " <span class='y-lgray-text'>&lsaquo;" . $row['abb'] . "&rsaquo;</span>" .
                                            "</label>" .
                                        "</div>"; 
                }
                
               $form = str_replace('{orgs:options}', $directs_list, $form); 
               
               $form = str_replace('{roads:options}', $roads_list_Total, $form);
            }
       
                $result = $form;
        }
        
        else{
                /*    if($fm_name == 'form_for_railwayCarriage'){
                        
                         $fm_path = $_SERVER['DOCUMENT_ROOT']. '/dmgnPs/php/forms/'. $fm_name .'.php';
                         $form = file_get_contents($fm_path);
                       //  file_put_contents($form);
                         $result = $form;
                    }
             */
                    $assbase = new _assbase();
                    $result = $assbase->get_fm($fm_name, $sparam);
                    unset($ass);
                }

         return $result;
    }
   
    public function get_btns_for_user_FPK(){
        $result = [];
        
        $result['btn_for_curriage'] = '<button class="card-header btn modal-header link" id="show_carriage">Паспорт моделей вагонов</button>';
        $result['btn_for_train'] = '<button class="card-header btn modal-header link" id="show_train">Паспорт пассажирских поездов</button>';
        
        return $result;
    }
    
    public function get_detail_body($road_or_mdl, $section, $high){
        $result = [];
        $db = new db_depo();
        $org_rec = $db->org_getRec($road_or_mdl);
        unset($db);		
		
	$org_abb = count($org_rec) > 0 ? '<span id="org_abb">' . $org_rec['habb'] . '. </span>' : "";
        
    /**********************************************pdf**********************************************************/	
        $rep = new Report();
        //$path = $_SERVER['PHP_SELF'];   // вернет файл, откуда запускаетс скрипт. 
        
        $path = self::siteRootDir();
                
       // while (mb_strlen($path) > 1) {  // '>1' значит длиннее '\'
          //  $path = dirname($path); // возвращает имя родительского каталога из указанного пути

          //  if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . '/index.php'))
              //  break;
    //    }
        
        $tmp_dir = $_SERVER['DOCUMENT_ROOT'] . $path . '/tmp';
       
        // Если директория не существует, создаем
         if (!is_dir($tmp_dir))
            mkdir($tmp_dir, 0777, true);
         
        $pdf_file = $tmp_dir . '/report_list.pdf';

        // удалить существующий файл rwc_list.csv, если существует
        if (file_exists($pdf_file))
            unlink($pdf_file);

        $result_pdf = $rep->create_PDF_report($pdf_file, $section); 
        unset($rep);
        
        if($result_pdf) 
           $pdf_path = $path . '/tmp/report_list.pdf';
        else $pdf_path = 'javascript:;';
    /********************************************************************************************************/
        
        $content = '<div id="tmp_for_form" class="y-flex-row-nowrap h-100">'.
                
                                            '<div class="card detail-card detail-dmgn-card y-shad">' .
                
                                                  '<div class="card-header y-flex-row-nowrap y-align-items-center" id="center_part_header">'.
                                                        '<div id="filter_tmp" class="dropdown">'. //style="visibility:'.($section == 'carriage' ? 'visible ' : 'hidden').'"
                                                             '<img src="img/dots_mnu_32.png" id="show_filter" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.                
                
                                                             '<div id="filter" class="dropdown-menu" aria-labelledby="dropdownMenuButton">'.
                                                                    '<div class=\'text-center y-lgray-text\'>&bull;&nbsp;'.
                                                                        '<span class=\'y-lgray-text y-fz08 \'>Реестры доступности</span>' .
                                                                    '&nbsp;&bull;</div>' . 

                                                                    '<a id="get_multilevel_from" class="dropdown-item" onclick="'.($section == 'carriage' ? 'show_multilevel_from();">' : 'show_multilevel_train_from();">').
                                                                       "<i class='fa fa-server y-lgray-text'></i> &nbsp; " . 'Pеестр по многоуровневым параметрам'.
                                                                    '</a>'.

                                                                    '<a id="show_org_reestr" class="dropdown-item" onclick="show_org_excel();">'.
                                                                        "<i class='fas fa-building y-lgray-text'></i> &nbsp; " . 'Pеестр по всем дирекциям'.
                                                                    '</a>'.

                                                                    '<a id="show_categoryes" class="dropdown-item" onclick="show_categoryes_form();">'.
                                                                         "<i class='fas fa-align-center y-lgray-text'></i> &nbsp; " . 'Pеестр по дирекции'. 
                                                                     '</a>' .

                                                                    '<a id="show_categoryes" class="dropdown-item" onclick="'.($section == 'carriage' ?' show_cat_params_form();' : 'show_cat_params_train_form();').'">'.
                                                                         "<i class='fab fa-accessible-icon y-lgray-text'></i> &nbsp; " . 'Pеестр по категориям'. 
                                                                     '</a>' .

                                                                 /*   ($section == 'carriage' ? '' :
                                                                    '<a id="show_group_reestr" class="dropdown-item" onclick="show_group_excel()">'.
                                                                        "<i class='fas fa-road y-lgray-text'></i> &nbsp; " . 'Реестр по балансодержателю'.
                                                                    '</a>').*/
                                                                       
                                                                    '<a id="show_form_4_reestr" class="dropdown-item" onclick="get_form_for_reestr();">'.
                                                                        "<i class='fab fa-medium y-lgray-text'></i> &nbsp; " . 'Реестр по '.($section == 'carriage' ? 'модели' : 'поезду').
                                                                    '</a>'.
                
                                                                    '<div class=\'text-center y-lgray-text\'>&bull;&nbsp;'.
                                                                        '<span class=\'y-lgray-text y-fz08 \'>По заполнению</span>' .
                                                                    '&nbsp;&bull;</div>' . 
                
                                                                    '<a id="pdf_link" class="dropdown-item" target="_blank" href="'.$pdf_path.'" >'.
                                                                        '<i class="far fa-file-pdf y-lgray-text"></i> &nbsp; Отчет' .
                                                                    '</a>'.

                                                             '</div>'.
                
                                                        '</div>'.
                
                                                        '<div class="p-0">' . $org_abb . 'Реестры доступности '.($section == 'carriage' ? 'моделей вагонов' : 'поездов').'</div>'.
                                                   '</div>' .

                                                    '<div class="y-flex-column-nowrap" id="registr_div">'.
                                                               '<div id="registr_div_table" style="overflow-y: auto;">'.
                                                                    '{registers_table}'.
                                                              '</div>'.
                
                                                       // ($section == 'carriage' ?  
                                                           '<div id="detail_info_block" class="y-flex-row-nowrap mt-auto" style=\'height:60%;\'></div>'
                                                         //  : ''
                                                       // ).                                  

                                                       . '<div id="common_div_docs" class="y-flex-row-nowrap mt-auto" style=\'height:25%;\'>'.
                                                            
                                                            ($section == 'carriage' ? 
                                                                '<div id="passport_div_tmp" class="y-mrg-r10 y-flex-column-nowrap" style=\'align-items:stretch;\'>'. // див с документами

                                                                        '<div class="card-header y-flex-row-nowrap y-align-items-center">'. 
                                                                           '<div class="p-0">Паспорт </div>'.
                                                                           
                                                                        '</div>' .

                                                                        '<div id="passport_tmp">'.   
                                                                               // '{passport_cards}'.
                                                                        '</div>' .

                                                                '</div>' // passport_div_tmp  
                                                            : '').
                
                                                            '<div id="docs_div_tmp" class="y-flex-column-nowrap">'. //                                                                 
                                                           
                                                                    '<div class="card-header y-flex-row-nowrap y-align-items-center">'. 
                                                                        '<div class="p-0">Документация </div>'.
                                                                       
                                                                    '</div>' .

                                                                    '<div id="docs_tmp">'.   
                                                                        // '{docs_cards}'.
                                                                    '</div>' .
                
                                                            '</div>'. // docs_div_tmp
                               
                                                        '</div>'. //end of common_div_docs
 
                                                    '</div>' . //end of registr_div
                
                                            '</div>' . // end of detail-dmgn-card
                
                                             '<div class="card detail-card detail-comm-card y-shad">' . 
                
                                                 '<div class="card-header y-flex-row-nowrap y-align-items-center">' .                                                       
                                                    '<div class="p-0">Работы по адаптации</div>'.
                                                '</div>'. 
                
                                                '<div id="actions_div">'.  // class="y-flex-column-nowrap"
                                            //      '{actions_table}'.
                                               '</div>'.
                
                                            '</div>'.
                                                                   
                                       '</div>'; // end of tmp_for_form

        $content = str_replace("{registers_table}", $this->get_registers_table($road_or_mdl, $section, $high) ,$content); // что заменяем, чем заменяемб где заменяем 
    //    $content = str_replace("{pdf_cards}", $this->get_document_part($pid), $content);

                $result['content'] = $content;
                
        return $result;
    }
    
    /******************************indicators**************************************/
    
    public function show_detail_info ($rid, $section){
        $result = '';
        $db = new db_depo();
        
        if($section == 'carriage'){
           $record = $db->carriage_getRec($rid);
 
            if(count($record)>0)
              $result .= $this->getTableRow_detail_info_mdl($record);           
        }else if($section == 'train'){
           $record = $db->train_getList_by_train_rid($rid);
            
            if(count($record)>0)                           
              $result .= $this->getTableRow_detail_info_train($record, $db);   
             
        }

        unset($db);
        return $result;
    }
    
    public function getTableRow_detail_info_train($record, $adap_actionsList = []){
        $result = "";

        if (count($record) > 0) {
            $td_class_l = "class='text-left align-middle y-border-no-t y-border-b y-gray-text y-fz08 position-relative' ";
            $td_class_r = "class='text-left align-middle y-border-no-t y-border-b y-fz09' ";
            
            $td_style_l = " style='padding:2px 10px 2px 20px;' "; //background:#F8F8F8;
            $td_style_r = " style='padding:2px 10px;' ";
            
            $flg = intval($record['flg']);
            $total_estimate = (($flg >> 2) & 0x3);
            $total_estimate_val = '';
            
            if($total_estimate == 0){
                $total_estimate_val = 'Не определено';
            }else if($total_estimate == 1){
               $total_estimate_val = 'ДП';
            }else if($total_estimate == 2){
               $total_estimate_val = 'ДЧ';
            }else if($total_estimate == 3){
               $total_estimate_val = 'НД';  
            }
                        
            $quantity_workers = (($flg >> 8) & 0xFF);
            $quantity_services = (($flg >> 16) & 0xFF);
            
            $row_num_train = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Номер поезда</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['num_train']) == 0 ? "" : $record['num_train']) . "</td>" .
                        "</tr>";
            
            $row_route = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Маршрут поезда</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['route']) == 0 ? "" : $record['route']) . "</td>" .
                        "</tr>";
            
            $row_date_num_registr = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Дата и номер регистрации паспорта</td>" .                            
                            "<td " . $td_class_r . $td_style_r . ">" . $record['date_num_registr'] . "</td>" .
                        "</tr>";
            
            $row_adr_formation = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Адрес преприятия формирования поезда</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['adr_formation']) == 0 ? "" : $record['adr_formation']) . "</td>" .
                        "</tr>";
            
            $row_availability_1_vagon = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Наличие в составе не менее 1 вагона для инвалидов</td>" .
                            "<td " . $td_class_r . $td_style_r . ">".(($record['flg'] & 0x3) == 1 ? "Да" : "Нет") ."</td>" .
                        "</tr>";
            
            $row_total_estimate = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Итоговая оценка доступности пассажирского поезда</td>" .
                            "<td " . $td_class_r . $td_style_r . ">".$total_estimate_val."</td>" .
                        "</tr>";
            
            $row_quantity_workers = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Процент работников, для обслуживания инвалидов</td>" .
                            "<td " . $td_class_r . $td_style_r . ">".$quantity_workers." %</td>" .
                        "</tr>";
            
            $row_quantity_services = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Процент услуг, предоставляемых инвалидам</td>" .
                            "<td " . $td_class_r . $td_style_r . ">".$quantity_services." %</td>" .
                        "</tr>";
                     
            
            $row_recoms_using = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Рекомендации по использованию объекта транспортной инфраструктуры</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['recoms_using']) == 0 ? "" : $record['recoms_using']) . "</td>" .
                        "</tr>";
            
            $row_exp_res = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Ожидаемый результат</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['exp_res']) == 0 ? "" : $record['exp_res']) . "</td>" .
                        "</tr>";

              $row_dtai = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Дата актуализации информации</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['date_act']) == 0 ? "" : "<span class='y-steel-blue-text'>" . _dbbasemy::dates_YYYYMMDD2RuD($record['date_act']) . "</span>") . "</td>" .
                        "</tr>";
            
            
            $row_note = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Примечание</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['note']) == 0 ? "" : $record['note']) . "</td>" .
                        "</tr>";
            
            
            $row_uooi = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Отметка об участии общественных объединений инвалидов в проведении обследовании и паспортизации</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . ((($record['flg'] >> 4) & 0x3) == 1 ? "Да" : "Нет") . "</td>" .
                        "</tr>";
            
           // $adap_actionsList = $db->get_train_actionsList($record['rid']);
            
           // if(count($adap_actionsList) > 0){
           //     $row_twa = 
           // }
            /*
$mgad_lst = $db->mgad_getList($rws_rid);
            
            $td_mgad = "";
            if (count($mgad_lst) > 0)
                foreach ($mgad_lst as $mgad_rec) {
                    if (mb_strlen($td_mgad) > 0)
                        $td_mgad .= "<br>";
                    $td_mgad .= "<small>" . $mgad_rec['txt'] . "</small>";
                    
                    $sr = _asspoints::mgad_getSrFromFlg($mgad_rec['flg']);
                    if (strlen($sr) > 0) $td_mgad .= " <small class='y-lgray-text'>&lsaquo;" . $sr . "&rsaquo;</small>";
                    
                    $vp = ($mgad_rec['flg'] >> 24) & 0x1;
                    if ($vp == 1) $td_mgad .= " <small class='y-steel-blue-text'>&lsaquo;выполнено&rsaquo;</small>";
                    
                    $nv = ($mgad_rec['flg'] >> 25) & 0x1;
                    if ($nv == 1) $td_mgad .= " <small class='y-dred-text'>&lsaquo;не выполнено&rsaquo;</small>";
                    
                    if (mb_strlen($mgad_rec['pnv']) > 0) $td_mgad .= " <small class='y-lgray-text'>&lsaquo;" . $mgad_rec['pnv'] . "&rsaquo;</small>";
                }
            
            $row_mgad = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Работы по адаптации</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . $td_mgad . "</td>" .
                        "</tr>";
            
            
                         */
            $table =    "<table class='table table-striped y-border-no m-0'>" .
                            "<thead class='h-0'>" .
                                "<tr class='y-border-no'>" .
                                    "<th class='y-wdt-col-5 y-border-no m-0 p-0'></th>" .
                                    "<th class='y-maxw-col-7 y-border-no m-0 p-0'></th>" .
                                "</tr>" .
                            "</thead>" .
                            "<tbody class='y-border-no'>" .
                                $row_num_train .
                                $row_route .
                                $row_adr_formation .
                                $row_date_num_registr . 
                                $row_total_estimate .
                                $row_availability_1_vagon .                                
                                $row_quantity_workers .
                                $row_quantity_services .
                                $row_exp_res .
                                $row_recoms_using .                                
                                $row_dtai .
                                $row_uooi . 
                                $row_note .                                                               
                            "</tbody>" .
                        "</table>";
        }
        else $table = "";
        unset($db);

        // temporarily (need function with dmgn data for rws)
        $result =   "<div id='detail_info_mdl-" . $record['rid'] . "' data-rid_road='".$record['rid_road']."' data-num_prefix='".$record['num_prefix']."' class='card detail-card detail-dmgn-card y-shad'>" .
                        "<div class='card-header y-flex-row-nowrap y-align-items-center'>" .
                            "<div class='y-dgray-text'>Детальная информация</div>" .
                        "</div>" .
                        "<div class='card-body p-0'>" .
                            $table .
                        "</div>" .
                        //"<div class='card-footer y-pad-tb7'></div>" .
                    "</div>";
        
        return $result;
    }
    
    
    public function getTableRow_detail_info_mdl( $record) : string { //string $rws_rid
        $result = "";

        if (count($record) > 0) {
            $td_class_l = "class='text-left align-middle y-border-no-t y-border-b y-gray-text y-fz08 position-relative' ";
            $td_class_r = "class='text-left align-middle y-border-no-t y-border-b y-fz09' ";
            
            $td_style_l = " style='padding:2px 10px 2px 20px;' "; //background:#F8F8F8;
            $td_style_r = " style='padding:2px 10px;' ";
            
            $flg = intval($record['flg']);
            $mark_ooi = (($flg >> 8) & 0x3);
            
            $_K = $flg & 0x3;
            $_O = ($flg >> 2) & 0x3;
            $_S = ($flg >> 4) & 0x3;
            $_G = ($flg >> 6) & 0x3;
            
            $K_value = $this->define_KOSG_mdl($_K);
            $O_value = $this->define_KOSG_mdl($_O);
            $S_value = $this->define_KOSG_mdl($_S);
            $G_value = $this->define_KOSG_mdl($_G);            
            
            $row_obj_nm = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Наименование объекта</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['obj_nm']) == 0 ? "" : $record['obj_nm']) . "</td>" .
                        "</tr>";
            
            $row_mdl = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Модель вагона</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['mdl']) == 0 ? "" : $record['mdl']) . "</td>" .
                        "</tr>";
            
            $row_docs_date_num_registr = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Дата / номер регистрации паспорта</td>" .                           
                            "<td " . $td_class_r . $td_style_r . ">" . $record['date_num_registr'] . "</td>" .
                        "</tr>";
            
            
            $row_docs_constr = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Документация, регламентирующая технические требования для перевозки инвалидов, достигнутые при постройке</td>" .                           
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['docs_at_constr']) == 0 ? "" : $record['docs_at_constr']) . "</td>" .
                        "</tr>";
            
            $row_docs_modern = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Документация, регламентирующая технические требования для перевозки инвалидов, достигнутые при модернизации</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['docs_at_modern']) == 0 ? "" : $record['docs_at_modern']) . "</td>" .
                        "</tr>";
            
            $row_K = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Оценка доступности по категории <span class='y-steel-blue-text y-fw-bolder'><b>\"К\":</b></span></td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . $K_value . "</td>" .
                        "</tr>";
            
            $row_O = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Оценка доступности по категории <span class='y-steel-blue-text y-fw-bolder'><b>\"O\":</b></span></td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . $O_value . "</td>" .
                        "</tr>";
            
            $row_S = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Оценка доступности по категории <span class='y-steel-blue-text y-fw-bolder'><b>\"C\":</b></span></td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . $S_value . "</td>" .
                        "</tr>";
            
            $row_G = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Оценка доступности по категории <span class='y-steel-blue-text y-fw-bolder'><b>\"Г\":</b></span></td>" .
                            "<td " . $td_class_r . $td_style_r . ">". $G_value . "</td>" .
                        "</tr>";
                     
            
            $row_exp_res = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Ожидаемый результат по состоянию доступности</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['exp_res']) == 0 ? "" : $record['exp_res']) . "</td>" .
                        "</tr>";
            
            $row_recoms_using = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Рекомендации по использованию объекта</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['recoms_using']) == 0 ? "" : $record['recoms_using']) . "</td>" .
                        "</tr>";
           
            
              $row_dtai = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Дата актуализации информации</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['date_act']) == 0 ? "" : "<span class='y-steel-blue-text'>" . _dbbasemy::dates_YYYYMMDD2RuD($record['date_act']) . "</span>") . "</td>" .
                        "</tr>";
            
            
            $row_note = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Примечание</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . (mb_strlen($record['note']) == 0 ? "" : $record['note']) . "</td>" .
                        "</tr>";
            
            
            $row_uooi = "<tr>" .
                            "<td " . $td_class_l . $td_style_l . ">Участие общественных объединений инвалидов в проведении обследований и паспортизации</td>" .
                            "<td " . $td_class_r . $td_style_r . ">" . ($mark_ooi == 1 ? "Да" : "Нет") . "</td>" .
                        "</tr>";
            
            $table =    "<table class='table table-striped y-border-no m-0'>" .
                            "<thead class='h-0'>" .
                                "<tr class='y-border-no'>" .
                                    "<th class='y-wdt-col-5 y-border-no m-0 p-0'></th>" .
                                    "<th class='y-maxw-col-7 y-border-no m-0 p-0'></th>" .
                                "</tr>" .
                            "</thead>" .
                            "<tbody class='y-border-no'>" .
                                $row_obj_nm .
                                $row_mdl .
                                $row_docs_date_num_registr .
                                $row_docs_constr .
                                $row_docs_modern .
                                $row_K .
                                $row_O . 
                                $row_S .
                                $row_G .
                                $row_exp_res .
                                $row_recoms_using .
                                $row_dtai .                               
                                $row_uooi .  
                                $row_note .
                            "</tbody>" .
                        "</table>";
        }
        else $table = "";
        unset($db);

        // temporarily (need function with dmgn data for rws)
        $result =   "<div id='detail_info_mdl-" . $record['rid'] . "' data-carr_mdl='".$record['carr_mdl']."' data-org='".$record['org']."' class='card detail-card detail-dmgn-card y-shad'>" .
                        "<div class='card-header y-flex-row-nowrap y-align-items-center'>" .
                            "<div class='y-dgray-text'>Детальная информация</div>" .
                        "</div>" .
                        "<div class='card-body p-0'>" .
                            $table .
                        "</div>" .
                        //"<div class='card-footer y-pad-tb7'></div>" .
                    "</div>";
        
        return $result;
    }
    
    public function define_KOSG_mdl ($flg){
        $str = '';
         if($flg == 0){
                $str = 'Не определено';
            }else if($flg == 1){
                $str = 'ДП';
            }else if($flg == 2){
                $str = 'НД';
            }else if($flg == 3){
                $str = 'ДЧ';
            }
        return $str;
    }
    
    /******************************************* document ***********************************************************************************/

    
    public function get_document_part($pid, $section){
        
        $part = "<div class=\"mt-auto\" id=\"docs_div\" data_registr-rid=''>".
                       "{ROWS}".
                "</div>";
        
        $arr = [];
        $db = new db_depo();
        $arr = $db->docs_getDocsList4Pid($pid);
/*  для поиска доков по carr_mdl, когда отображались все модели      
        if($section == 'train'){
            $arr = $db->docs_getDocsList4Pid($pid);
        }else if($section == 'carriage'){
            $arr = $db->get_docs_list_by_activeMdl($pid);
        }
 */       
        $rowset = '';
        
        foreach($arr as $row){
            $ftype = _assbase::getFtypeByFname($row['fnm']); 
            
            $img = $ftype == "unk" ? "" : "<img src='/pktbbase/img/file/" . $ftype . "_32.png' data-doc='" .$row['rid']."'>";
            
             $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";
             
             $fnm = "<span class='y-gray-text' " . $fnm_ttip . ">" . _dbbase::shrink_filename($row['fnm'], 30) . "</span>";            
       
            
            $rowset .= "<span class='badge badge-light y-fw-normal y-shad y-cur-point badge-dmgn-doc' style='margin:8px 4px;' onclick='doc_view_click(this);' data-doc='" . $row['rid'] . "'>" .
                                    $img . $fnm . "&nbsp; "  . //<img src='img/delete_15.png' onclick='delete_badge(this);' id='docs-".$row['rid']."'>
                        "</span>";
        }
        
       $part = str_replace("{ROWS}", $rowset, $part);
        
        return $part;
    }
    
    public function get_passportPdf_part($pid){ 
        $part = "<div class=\"mt-auto docs_div\">".
                       "{ROWS}".
                "</div>";
         
        $db = new db_depo();
        $arr = $db->passport_pdf_record_by_roadRid($pid); 
        $rowset = '';
        
        foreach($arr as $row){
            $ftype = _assbase::getFtypeByFname($row['fnm']); 
             
            $img = $ftype == "unk" ? "" :                                                       //  // doc_view_click ??????   
               "<img class='y-cur-point' src='/pktbbase/img/file/" . $ftype . "_32.png' onclick='pass_pdf_view_click(this);' data-doc='" .$row['rid']."'>";
            
            $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";
             
                                                                 // doc_view_click ??????
            $fnm = "<span class='y-cur-point y-gray-text' onclick='pass_pdf_view_click(this);' data-doc='" . $row['rid'] . "' " . $fnm_ttip . ">" . 
                                _dbbase::shrink_filename($row['fnm'], 30) . "</span>";            
       
            // <img src='img/delete_15.png' onclick='delete_passport_pdf_badge(this);' id='passportPdf-".$row['rid']."'>
            $rowset .= "<span class='badge badge-light y-fw-normal y-shad y-cur-point badge-dmgn-doc' style='margin:8px 4px;'>" .
                                    $img . $fnm . "&nbsp; "  .
                        "</span>";
        }
        
        $part = str_replace("{ROWS}", $rowset, $part);
        
        return $part;    
    }
    
  /*  
      
    public function get_document_part_by_act_mdl($rid_mdl){
        
        $part = "<div class=\"mt-auto\" id=\"docs_div\" data_registr-rid=''>".
                       "{ROWS}".
                "</div>";
        
        $db = new db_depo();
        $arr = $db->get_docs_list_by_activeMdl($rid_mdl);
        $rowset = '';
        
        foreach($arr as $row){
            $ftype = _assbase::getFtypeByFname($row['fnm']); 
            
            $img = $ftype == "unk" ? "" : "<img src='/pktbbase/img/file/" . $ftype . "_32.png'>";
            
             $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";
             
             $fnm = "<span class='y-gray-text' " . $fnm_ttip . ">" . _dbbase::shrink_filename($row['fnm'], 30) . "</span>";            
       
            
            $rowset .= "<span class='badge badge-light y-fw-normal y-shad y-cur-point badge-dmgn-doc' style='margin:8px 4px;' onclick='doc_view_click(this);' data-doc='" . $row['rid'] . "'>" .
                                    $img . $fnm . "&nbsp; "  . //<img src='img/delete_15.png' onclick='delete_badge(this);' id='docs-".$row['rid']."'>
                        "</span>";
        }
        
       $part = str_replace("{ROWS}", $rowset, $part);
        
        return $part;
    }
 
*/
     
    
/**************************************** actions **********************************************/   
    
    public function get_common_part($curr_pass, $section){
        $result = '';
        $db = new db_depo();
        
        if($section == 'carriage'){
            $arr = $db->get_actionsList($curr_pass);       
            
        }else if ($section == 'train'){
            $arr = $db->get_train_actionsList($curr_pass);
        }
        
            foreach($arr as $row)
                 $result .= $this->get_common_part_card_rows_carriage($row, $section);
        
        return $result;
    }
    
    public function get_common_part_card_rows_carriage($row, $section){
        
       $flg = intval($row['flg']) & 0x3FFFFFF;   // все 26 битов , во всех F по 4 единицы, 3 в hex тоже две единицы, с операцией & биты остаются такими какими есть(1 остается еденицей, 0 - нулем)
        
       $pp = $flg & 0xFFFFFF; // 24 бита  группы plaining period, F = 4 бита(1111) 
       $ppStr ='';
    
             if($pp > 0){ 
                    $year_quarter = $pp & 0xFFFF; // год и квартал вместе (16 бит)
                    if($year_quarter > 0){
                        $year = $year_quarter & 0xFFF; // (на year 12 бит)
                        $quarter = ($year_quarter >> 12) & 0xF;       // (на quarter 4 бита) 
                        if($year > 2000 && $year < 2070 && $quarter >= 0 && $quarter < 5){
                            $ppStr = ($quarter > 0 ? $quarter. ' кв. ' : ''). $year;
                        }
                    }else{
                              // $flg >> 16 потому что берем его из той же группы($pp), а биты для селекта начинаются с 16!!! Поэтому и сдвинуть надо на 16   
                        $pp1 = ($flg >> 16) & 0xFF;  // (0xFF) 8 бит на селект, они с 16 по 23й бит ($pp1 это селкет)
                        $ppStr = self::$plaining_period[$pp1];
                    }
             }
             
             if(mb_strlen($ppStr) == 0){
                 $ppStr = 'Не определено';
             }
          
             
             
       switch  (($flg >> 24) & 0x3){ //  (($flg >> 24) - берем  с 24 по 25 бит, 3 в хекс это 11
           case 1 : $markStr = 'Выполнено'; break;
           case 2 : $markStr=  'Не выполнено'; break;
           default : $markStr = 'Не определено';
       }     

        
        $l_comm_classes      = "d-table-cell y-lgray-text y-fz08 y-wdt-col-4 align-middle y-border-b";
        $l_last_row_classess = "d-table-cell y-lgray-text y-fz08 y-wdt-col-4 align-middle";
        
        $r_comm_classes      = "d-table-cell y-wdt-col-8 y-fz10 y-pad-lr5 y-steel-blue-text align-middle y-border-b";
        $r_last_row_classess = "d-table-cell y-wdt-col-8 y-fz10 y-pad-lr5 y-steel-blue-text align-middle";
        
        return  "<div class='card y-mrg-a10 y-shad'>".    // d-inline-block
               
                       "<div id='action_card-".$row['rid']."' data-flg='". $row['flg']."' class='card-body y-cur-point d-table y-pad-a10'>".   //self::$plaining_period      y-wdt-col-12 y-fs-i
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Меры по адаптации </span><span id='twa-".$row['rid']."' class='" . $r_comm_classes . "'>" . $row['twa'] . "</span></div>".
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Планируемый период выполнения работ </span> <span class='". $r_comm_classes ."' id='pp-".$row['rid']."'>".$ppStr."</span></div>".
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Отметка о выполнении работ </span><span class='". $r_comm_classes ."' id='mark-".$row['rid']."'>".$markStr."</span></div>".
                            "<div class='d-table-row'><span class='" . $l_last_row_classess ."'>Причины невыполнения </span><span class='". $r_last_row_classess ."' id='pnv-".$row['rid']."'>".$row['pnv']."</span></div>".
                       "</div>".              

                       "<div class='card-footer'>".  // bg                            
                               //    "<img id='edit-".$row['rid']."' src='img/edit_32_transp.png' onclick='edit_action_record(this);' data-section='".$section."'>" .
                                //   "<small><img id='del_pp-".$row['rid']."' src='img/deleter_24.png' onclick='delete_action_record(this);' data-section='".$section."'></small>" .
                       "</div>".       
               
                "</div>";
       /*
        $comm_classes = "d-table-cell align-middle y-border-b";
        $last_row_classess = "d-table-cell align-middle";
        
       return  "<div class='card y-mrg-a10 y-shad'>".    // d-inline-block
               
                    "<div id='action_card-".$row['rid']."' data-flg='". $row['flg']."' class='card-body  y-cur-point d-table y-wdt-col-12 y-pad-a10'>".   //self::$plaining_period   // y-fs-i
                        "<div class='d-table-row '><span class='" . $comm_classes . " y-lgray-text y-fz08 y-wdt-col-4'>Меры по адаптации </span><span id='twa-".$row['rid']."' class='" . $comm_classes . " y-pad-lr5 y-wdt-col-8 y-fz10 y-steel-blue-text'>" . $row['twa'] . "</span></div>".
                            "<div class='d-table-row'><span class='d-table-cell y-wdt-col- y-lgray-text y-fz08 y-wdt-col-4 ". $comm_classes ."'>Планируемый период выполнения работ </span> <span class='d-table-cell ". $comm_classes ." y-fz10 y-steel-blue-text' id='pp-".$row['rid']."'>".$ppStr."</span></div>".
                            "<div class='d-table-row'><span class='d-table-cell y-wdt-col- y-lgray-text y-fz08 y-wdt-col-4 ". $comm_classes ."'>Отметка о выполнении работ </span><span class='d-table-cell ". $comm_classes ." y-wdt-col-8 y-fz10 y-steel-blue-text' id='mark-".$row['rid']."'>".$markStr."</span></div>".
                            "<div class='d-table-row'><span class='d-table-cell y-wdt-col- y-lgray-text y-fz08 y-wdt-col-4 ". $last_row_classess ."'>Причины невыполнения </span><span class='d-table-cell ". $last_row_classess ." y-wdt-col-8 y-fz10 y-steel-blue-text' id='pnv-".$row['rid']."'>".$row['pnv']."</span></div>".
                        "</div>".              

                        "<div class='card-footer bg'>".                            
                               //    "<img id='edit-".$row['rid']."' src='img/edit_32_transp.png' onclick='edit_action_record(this);' data-section='".$section."'>" .
                                //   "<small><img id='del_pp-".$row['rid']."' src='img/deleter_24.png' onclick='delete_action_record(this);' data-section='".$section."'></small>" .
                        "</div>".       
               
                "</div>";
       
    */
    }

/**************************************** actions by mdl **********************************************/      
       
    public function actions_list_by_mdl($act_mdl){
        $result = '';
        $db = new db_depo();

            $arr = $db->get_actionsList_by_mdl($act_mdl);       
            
            foreach($arr as $row)
                 $result .= $this->rows_actionsList_by_mdl($row);
        
        return $result; 
    }
    
    public function rows_actionsList_by_mdl($row){
        
       $flg = intval($row['flg']) & 0x3FFFFFF;   // все 26 битов , во всех F по 4 единицы, 3 в hex тоже две единицы, с операцией & биты остаются такими какими есть(1 остается еденицей, 0 - нулем)
        
       $pp = $flg & 0xFFFFFF; // 24 бита  группы plaining period, F = 4 бита(1111) 
       $ppStr ='';
    
             if($pp > 0){ 
                    $year_quarter = $pp & 0xFFFF; // год и квартал вместе (16 бит)
                    if($year_quarter > 0){
                        $year = $year_quarter & 0xFFF; // (на year 12 бит)
                        $quarter = ($year_quarter >> 12) & 0xF;       // (на quarter 4 бита) 
                        if($year > 2000 && $year < 2070 && $quarter >= 0 && $quarter < 5){
                            $ppStr = ($quarter > 0 ? $quarter. ' кв. ' : ''). $year;
                        }
                    }else{
                              // $flg >> 16 потому что берем его из той же группы($pp), а биты для селекта начинаются с 16!!! Поэтому и сдвинуть надо на 16   
                        $pp1 = ($flg >> 16) & 0xFF;  // (0xFF) 8 бит на селект, они с 16 по 23й бит ($pp1 это селкет)
                        $ppStr = self::$plaining_period[$pp1];
                    }
             }
             
             if(mb_strlen($ppStr) == 0){
                 $ppStr = 'Не определено';
             }
          
             
             
       switch  (($flg >> 24) & 0x3){ //  (($flg >> 24) - берем  с 24 по 25 бит, 3 в хекс это 11
           case 1 : $markStr = 'Выполнено'; break;
           case 2 : $markStr=  'Не выполнено'; break;
           default : $markStr = 'Не определено';
       }     

        
        $l_comm_classes      = "d-table-cell y-lgray-text y-fz08 y-wdt-col-4 align-middle y-border-b";
        $l_last_row_classess = "d-table-cell y-lgray-text y-fz08 y-wdt-col-4 align-middle";
        
        $r_comm_classes      = "d-table-cell y-wdt-col-8 y-fz10 y-pad-lr5 y-steel-blue-text align-middle y-border-b";
        $r_last_row_classess = "d-table-cell y-wdt-col-8 y-fz10 y-pad-lr5 y-steel-blue-text align-middle";
        
       return   "<div class='card y-mrg-a10 y-shad'>".    // d-inline-block
               
                       "<div id='action_card-".$row['rid']."' data-flg='". $row['flg']."' class='card-body y-cur-point d-table y-pad-a10'>".   //self::$plaining_period      y-wdt-col-12 y-fs-i
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Меры по адаптации </span><span id='twa-".$row['rid']."' class='" . $r_comm_classes . "'>" . $row['twa'] . "</span></div>".
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Планируемый период выполнения работ </span> <span class='". $r_comm_classes ."' id='pp-".$row['rid']."'>".$ppStr."</span></div>".
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Отметка о выполнении работ </span><span class='". $r_comm_classes ."' id='mark-".$row['rid']."'>".$markStr."</span></div>".
                            "<div class='d-table-row'><span class='" . $l_last_row_classess ."'>Причины невыполнения </span><span class='". $r_last_row_classess ."' id='pnv-".$row['rid']."'>".$row['pnv']."</span></div>".
                       "</div>".              

                       "<div class='card-footer'>". // bg
                               //    "<img id='edit-".$row['rid']."' src='img/edit_32_transp.png' onclick='edit_action_record(this);' data-section='".$section."'>" .
                                //   "<small><img id='del_pp-".$row['rid']."' src='img/deleter_24.png' onclick='delete_action_record(this);' data-section='".$section."'></small>" .
                       "</div>".       
               
                "</div>";
    } 
    
 /***************************************************/
 // carriage reestr part
 /************************************************************/   
    
    public function get_registers_table($road_or_mdl, $section, $high){
        $result = '';
        
        
        $db = new db_depo();
        $records = '';
        
        if($section == 'carriage'){
           $result .= $this->roads_active_mdl_TableHead();
          //  $arr = $db->roads_4_active_mdl($high, $road_or_mdl);
            $arr = $db->carriage_getList($road_or_mdl);
                           
            foreach($arr as $row)
                $records .= $this->get_registers_table_rows($row);
              //  $records .= $this->roads_active_mdl_TableRow($row); - для ВСЕХ моделей
        }else {
            $result .= $this->get_registers_table_head($section);
            $arr = $db->train_getList_by_NUM_PREFIX($road_or_mdl);
            
            foreach($arr as $row)
                $records .= $this->get_registers_train_rows($row);
        }     
        $result = str_replace("{ROWSET}", $records, $result);
        
        return $result;
    }
    
    
    public function get_active_mdl_roads($high, $carr_mdl){
        $result = '';
        $db = new db_depo();
        $arr = $db->roads_4_active_mdl($high, $carr_mdl);
        
        $thead = $this->roads_active_mdl_TableHead();
                
        $rowset = '';
        
        foreach($arr as $record)
            $rowset .= $this->roads_active_mdl_TableRow($record);
        
        $result = str_replace("{ROWSET}", $rowset, $thead);
        return $result;
    }
    
    public function roads_active_mdl_TableHead(){
       $comm_classes = "text-center align-middle y-border-no";

        return  "<div id='div_ta_active_mdl_road' class='table-responsive y-mrg-b20 p-0' style='overflow-y:auto;'>" .
                   "<table id='ta_active_mdl_road' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                               "<th class='" . $comm_classes . "'>Модель</th>" .
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .
               "</div>";
    }
    
    private function roads_active_mdl_TableRow($row) : string { //string $org_current_rid
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
            
        //   if (strcasecmp($row['rid'], $org_current_rid) == 0)
          //             $comm_classes .= " tbl-act-cell";
          
            $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
          
            
            $result =   "<tr id='tr_active_mdl_road-" . $row['rid1'] . "' class='y-cur-def' onclick='acive_road_4_active_mdl_click(this);' >" .
                            "<td id='td_active_mdl_road_ttl-" . $row['rid1'] . "' " . $td_style_ttl . " data-carr_pasport_rid=".$row['rid'].">" .  //rid1 -  id организации  Для группы отчетов                
                                $row['ttl'] .
                            "</td>" .
                                   
                        "</tr>";
        }
        
        return $result;
    }
   
    public function get_registers_table_head ($section){
        $comm_classes = "text-center align-middle y-border-no";
        
        return  "<div id='div_ta_registr' class='table-responsive y-mrg-b20 y-mrg-b10' style='overflow-y:auto;'>" .
                   "<table id='ta_registr' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                               "<th class='y-maxw-col-8 " . $comm_classes . "'>".($section == 'carriage' ? 'Наменование' : 'Номер поезда') ."</th>" .
                                "<th class='y-wdt-col-4 " . $comm_classes . "'>".($section == 'carriage' ? 'Модель' : 'Маршрут') ."</th>" .
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .
               "</div>";
    }

    public function get_registers_table_rows($row){
         $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
        } 
       
        
        $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
        $td_style_mdl = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";               
        
         if(strlen($row['rid']) > 0){
            $result =   "<tr id='tr_registr-" . $row['rid'] . "' onclick='mdl_click(this);' class='y-cur-def'  data-dt_nm_reg='".$row['date_num_registr']."' data-docs_constr='".$row['docs_at_constr']."'".
                                                                                    "data-docs_modern='".$row['docs_at_modern']."' data-flg='".$row['flg']."' data-exp_res='".$row['exp_res']."' ". 
                                                                                     "data-recoms_using='".$row['recoms_using']."' data-date_act='".$row['date_act']."' data-note='".$row['note']."' data-org='".$row['org']. "' >".                            

                             //   "<td id='td_registr_nm-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                            //        $row['obj_nm'] .
                           //     "</td>" .

                                "<td id='td_registr_mdl-" . $row['rid'] . "' " . $td_style_mdl . ">" .
                                   "<span id='a_registr_mdl-" . $row['rid'] . "' class='y-steel-blue-text d-block y-pad-tb0'>" . 
                                        $row['mdl'] .
                                    "</span>" .

                                   "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                    "</div>" .
                                "</td>" .

                            "</tr>";
         }else{
              $result = 'таблица базы данных пуста';
         }                
      return $result;                 
    }

 /***************************************************/
 //  train reestr part
 /************************************************************/   

    public function get_registers_train_rows($row){
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
        }       
        
        $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
        $td_style_mdl = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";      
        
        $act_show   = "<a id='a_edit_train-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='train_show_click(this);' data-toggle='tooltip' title='Посмотреть' data-delay='100'>" .
                          "<img src='/pktbbase/img/view_32.png'>" . 
                      "</a>";
        
         if(strlen($row['rid']) > 0){
            $result =   "<tr id='tr_train-" . $row['rid'] . "' class='y-cur-def' onclick='train_click(this);' data-num_train='".$row['num_train']."' data-route='".$row['route']."'".
                                                                                  "data-adr_formation='".$row['adr_formation']."' data-date_num_registr='".$row['date_num_registr']."' data-flg='".$row['flg']."' ". 
                                                                                  "data-recoms_using='".$row['recoms_using']."' data-exp_res='".$row['exp_res']."' data-date_act='".$row['date_act']."' data-note='".$row['note']."'".
                                                                                  "data-rid_road='".$row['rid_road']. "' >".                            

                                "<td id='td_train_num-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                                    $row['num_train'] .
                                "</td>" .

                                "<td id='td_train_route-" . $row['rid'] . "' " . $td_style_mdl . " style='display:flex;justify-content:space-between;'>" .
                                   "<span id='a_registr_route-" . $row['rid'] . "' class='y-steel-blue-text d-block y-pad-tb0'>" . 
                                        $row['route'] .
                                    "</span>" .

                                //  "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                    //     "<div class='acts-inner' style='height:auto;width:auto;'>". $act_show. "</div>" . 
                                 //   "</div>" .
                                "</td>" .

                            "</tr>";
         }else{
              $result = 'таблица базы данных пуста';
         }                
      return $result;                 
    }
  /***************************************************/
 //  org part
 /************************************************************/   

    public function get_org_accardion_part(string $open_panels) : array {
        $result = [];

        $road_lst = ['Организации']; 

        $body = '';
                   
        if (count($road_lst) > 0) {
            foreach($road_lst as $row)
                $body .= $this->org_get_accRow($row, $open_panels);
                                     
        }else $body = "<p class='y-empty-result-text y-mrg-a20'>Нет записей.</p>";
               
        $result['body'] = $body;

        return $result;
    }
    
    
    public function org_get_accRow($row, string $open_panels) : string {
        $result = '';
        $comm_classes = "d-table-cell align-middle y-pad-w5h15";

       // $collapsed_head  = "road_head-" . $row['rid'];
      //  $collapsed_panel = "road_panel-" . $row['rid'];
        $collapsed_head = "org_head";
        $collapsed_panel = "org_panel";
        
        if (strlen($open_panels)) {    // FALSE if not found  is_numeric(stripos($open_panels, ',' . $row['rid'] . ','))
            
            $show_class = "";
            $aria_exp   = "false";
            
            $collapsed_bk = "";

            $updn = "down";
            
          /*  $show_class = " show";
            $aria_exp   = "true";
            
            $collapsed_bk = " acc-open-bk";
            $updn = "up";
          */ 
        }
    /*    else {
            $show_class = "";
            $aria_exp   = "false";
            
            $collapsed_bk = "";

            $updn = "down";
        }
    */
        $updn = "<code class='fas fa-angle-" . $updn . "'></code>";

        $head_aria_attrs = "data-toggle='collapse' data-target='#" . $collapsed_panel . "' aria-expanded='" . $aria_exp . "' aria-controls='" . $collapsed_panel . "'";
   
        $result .=  "<div id='div_ta_org' class='acc' >" .
                        "<div id='ta_org'>" .
                
                            "<div id='" . $collapsed_head . "' class='d-table w-100 acc-head" . $collapsed_bk . "'>" . 
                                "<div class='d-table-row'>" .
                
                                    "<div class='y-wdt-col-11 text-left position-relative " . $comm_classes . "'>" .
                                        "<img src='/pktbbase/img/app_28.png' class='d-inline-block' style='margin-bottom:4px;padding-right:10px;'>" .
                                        "<span class='d-inline-block y-navy-text y-fz12'>" . 
                                         $row.  // $row['ttl'] . 
                                        "</span> " .
                                    "</div>" .

                                    "<div id='acc_updn-' class='round-corner y-wdt-col-1 text-center y-cur-point acc-updn " . $comm_classes . "' " . $head_aria_attrs . " onclick='toggle_updn(this);'>" .
                                        $updn .
                                    "</div>" .
                
                                "</div>" .
                            "</div>" .

                            "<div id='" . $collapsed_panel . "' class='collapse ta-org-tbody" . $show_class . "' aria-labelledby='" . $collapsed_head . "' data-parent='#road_acc'>" .
                                 $this->orgs_getAccPanelBody() .
               // "<div>rere</div>".
                            "</div>" .
                
                        "</div>" . // end of ta_org
                    "</div>"; // end of div_ta_org
        
        return $result;
    }
    
    
    public function orgs_getAccPanelBody() : string {
        $result = "<div id='org_panel_body' class='acc-panel'>"; //y-pad-lr20

        $db = new db_depo();
        $orgs_lst = $db->org_getList('');
        unset($db);  

        if (count($orgs_lst) > 0) {
            $comm_classes = "d-table-cell align-middle y-pad-lr10 acc-nested";

          //  $gdots = "<img src='img/gdots_22.png' class='y-mrg-lr10'>";

            foreach($orgs_lst as $row) {
                
              //  if($part == 'train' && preg_match('#ЦДМВ#i' ,$row['abb']) == 1) continue;

                $result .=  "<div id='tr_org-" . $row['rid'] . "' data-abb='".$row['abb']."' class='acc-child-table d-table w-100' onclick='org_click(this);'>" .
                             //   "<div class='d-table-row'>" .
                                    "<div id='div_org_ttl-" . $row['rid'] . "' class='acc-child-title y-wdt-col-10 text-left position-relative ". $comm_classes . " tbl-order'>" . 
                                        "<span id='td_org_ttl-" . $row['rid'] . "' class='d-inline-block y-fz12'>" . //y-navy-text
                                             $row['abb'] . 
                                        "</span> " .
                                    "</div>" .
                           //     "</div>" .
                            "</div>";
            }

            $result .= "<p></p>";
        }
        else $result .= "<p></p><p class='y-empty-result-text y-mrg-a10'>Нет записей.</p>" .
                        "<hr>";

        $result .= "</div>";
                
        return $result;
    }    
    
 /*   верстка на таблице
    
   public function get_org_records($high){
       $result='';
       $db = new db_depo();
       $records = $db->org_getList($high);
       unset($db);
       
       $thaed = $this->org_getTableHead();
       
       $rowset = '';
       
       foreach($records as $row){
           $rowset .= $this->org_getTableRow($row);
       }
       
       $result = str_replace("{ROWSET}", $rowset, $thaed);
       
       return $result;
   } 
   

    private function org_getTableHead() : string {
        $comm_classes = "text-center align-middle y-border-no";

        return  "<div id='div_ta_org' class='table-responsive y-mrg-b20 p-0' style='overflow-y:auto;'>" .
                   "<table id='ta_org' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                               "<th class='" . $comm_classes . "'>Организации ".
                                
                                "</th>" . 
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .
               "</div>";
    }
    
    private function org_getTableRow($row) : string { //string $org_current_rid
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
            
         //  if (strcasecmp($row['rid'], $org_current_rid) == 0)
           //            $comm_classes .= " tbl-act-cell";
          
            $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
            $td_style_abb = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";
          
            
            $result =   "<tr id='tr_org-" . $row['rid'] . "' class='y-cur-def' onclick='org_click(this);'>" .
                            "<td id='td_org_ttl-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                                $row['abb'] .
                            "</td>" .
                                   
                        "</tr>";
        }
        
        return $result;
    }
*/
     
  /*********************************************************************************/
  // all models  
  /****************************************************************************************/     
    
    public function get_all_models($offset, $rows, $currpage){
       $result = [];
       $db = new db_depo();       
       $totalrows = $db->table_getRowcount('carr_pasport');
       
        if ($totalrows > 0) {
            $db = new db_depo();
            if ($offset >= 0 && $offset < $totalrows && $rows > 0 && $rows < $totalrows)
                $records = $db->all_carr_passport_getListSubset($offset, $rows);
            else
                $records = $db->get_all_carr_passport_rows();
        }
       
       //$records = $db->get_all_carr_passport_rows();
              
       unset($db);
       
       $thaed = $this->all_mdl_getTableHead();
       
       $rowset = '';
       
       foreach($records as $row){
           $rowset .= $this->all_mdl_getTableRow($row);
       }
       
       $result['search'] = "<div id='search'><input id=\"srch_box\" type=\"text\" placeholder=\"Поиск <min 2 символа>...\" ondrop=\"return false;\" ondragover=\"return false;\" class=\"form-control\"></div>";
       $result['models'] = str_replace("{ROWSET}", $rowset, $thaed);
                                           //(string $pg_id, int $offset, int $rows, int $currpage, int $totalrows)
       $result['pagination'] = $this->make_pagination('carriage', $offset, $rows, $currpage, $totalrows); 
       
       return $result;
   } 
   
    private function all_mdl_getTableHead() : string {
        $comm_classes = "text-center align-middle y-border-no";

        return  "<div id='div_ta_mdl' class='table-responsive y-mrg-b20 p-0'>" . // style='overflow-y:auto;' - in css
                   "<table id='ta_mdl' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                               "<th class='" . $comm_classes . "'><span>Все модели </span>".
                                 // "<input id=\"srch_box\" type=\"text\" placeholder=\"Поиск <min 2 символа>...\" ondrop=\"return false;\" ondragover=\"return false;\" class=\"form-control\">".
                               "</th>" .
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .                   
               "</div>";
    }
    
    private function all_mdl_getTableRow($row) : string { //string $org_current_rid
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
            
         //  if (strcasecmp($row['rid'], $org_current_rid) == 0)
           //            $comm_classes .= " tbl-act-cell";
          
            $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
            //$td_style_abb = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";
          
            
            $act_show   = "<a id='a_show_mdl-" . $row['rid'] . "' data-carr_mdl='".$row['carr_mdl']."' href='javascript:;' class='y-mrg-lr5' onclick='mdl_show_click(this);' data-toggle='tooltip' title='Посмотреть' data-delay='100'>" .
                              "<img src='/pktbbase/img/view_32.png'>" . 
                          "</a>";
            
            $result =   "<tr id='tr_mdl-" . $row['carr_mdl'] . "' data-obj_nm='".$row['obj_nm']."' data-rid='".$row['rid']."'".
                        " data-org='".$row['org']."' data-high='".$row['high']."' data-habb='".$row['habb']."'".
                        " data-ttl='".$row['ttl']."'  data-abb='".$row['abb']."'  data-httl='".$row['httl']."' data-flg='".$row['flg']."'".
                        " dat-date_num_reg='".$row['date_num_registr']."' data-docs_constr='".$row['docs_at_constr']."'" .
                        " data-docs_modern='".$row['docs_at_modern']."' data-exp_res='" .$row['exp_res']. "' data-recoms_using='".$row['recoms_using']."'" .
                        " data-date_act='".$row['date_act']."' data-note='".$row['note']."'" .   
                        " class='y-cur-def' onclick='model_click(this);'>" .
                    
                            "<td id='td_mdl_ttl-" . $row['carr_mdl'] . "' " . $td_style_ttl . ">" . // style='display:flex;justify-content:space-between;'
                                "<span  class='y-steel-blue-text d-block y-pad-tb0'>" . 
                                      $row['mdl'] .   
                                "</span>".
                    
                    //  кнопка показывающая форму
                            //   "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" . 
                                //    "<div class='acts-inner' style='height:auto;width:auto;'>". $act_show  . "</div>" . 
                              //  "</div>" .
                                        
                            "</td>" .       
                    
                        "</tr>";
        }
        
        return $result;
    }
    
    
    
  /*********************************************************************************/
  // models  
  /****************************************************************************************/  
    
   public function get_models($rid){
       $result = '';
       $db = new db_depo();
       $records = $db->mdls_getList($rid);
       unset($db);
       
       $thaed = $this->mdl_getTableHead();
       
       $rowset = '';
       
       foreach($records as $row){
           $rowset .= $this->mdl_getTableRow($row);
       }
       
       $result = str_replace("{ROWSET}", $rowset, $thaed);
       
       return $result;
   } 
   
    private function mdl_getTableHead() : string {
        $comm_classes = "text-center align-middle y-border-no";

        return  "<div id='div_ta_mdl' class='table-responsive y-mrg-b20 p-0' style='overflow-y:auto;'>" .
                   "<table id='ta_mdl' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                               "<th id='search' class='" . $comm_classes . "'><span>Модели </span>".
                                  //"<input id=\"srch_box\" type=\"text\" placeholder=\"Поиск <min 2 символа>...\" ondrop=\"return false;\" ondragover=\"return false;\" class=\"form-control\">".
                               "</th>" .
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .
               "</div>";
    }
    
    private function mdl_getTableRow($row) : string { //string $org_current_rid
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
            
         //  if (strcasecmp($row['rid'], $org_current_rid) == 0)
           //            $comm_classes .= " tbl-act-cell";
          
            $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
            //$td_style_abb = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";
          
            
            $result =   "<tr id='tr_mdl-" . $row['rid'] . "' class='y-cur-def' onclick='model_click(this);'>" .
                            "<td id='td_mdl_ttl-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                                $row['mdl_nm'] .
                            "</td>" .
                                   
                        "</tr>";
        }
        
        return $result;
    }
    
  /***************************************************/
 //  roads part
 /************************************************************/   
    
   public function get_roads_records($high){
       $result='';
       $db = new db_depo();
       $records = $db->org_getList($high);
       unset($db);
       
       $thaed = $this->roads_getTableHead();
       
       $rowset = '';
       
       foreach($records as $row){
           $rowset .= $this->roads_getTableRow($row);
       }
       
       $result = str_replace("{ROWSET}", $rowset, $thaed);
       
       return $result;
   } 
   
    private function roads_getTableHead() : string {
        $comm_classes = "text-center align-middle y-border-no";

        return  "<div id='div_ta_road' class='table-responsive y-mrg-b20 p-0' style='overflow-y:auto;'>" .
                   "<table id='ta_road' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                               "<th class='" . $comm_classes . "'>Подразделения</th>" .
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .
               "</div>";
    }
    
    private function roads_getTableRow($row) : string { //string $org_current_rid
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
            
        //   if (strcasecmp($row['rid'], $org_current_rid) == 0)
          //             $comm_classes .= " tbl-act-cell";
          
            $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
            $td_style_abb = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";
          
            
            $result =   "<tr id='tr_road-" . $row['rid'] . "' class='y-cur-def' onclick='road_click(this);'>" .
                            "<td id='td_road_ttl-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                                $row['ttl'] .
                            "</td>" .
                                   
                        "</tr>";
        }
        
        return $result;
    }

/***************************************************/
 //  roads mdl part
 /************************************************************/   
    /*
    public function get_active_mdl_roads($high, $carr_mdl){
        $result = '';
        $db = new db_depo();
        $arr = $db->roads_4_active_mdl($high, $carr_mdl);
        
        $thead = $this->roads_active_mdl_TableHead();
                
        $rowset = '';
        
        foreach($arr as $record)
            $rowset .= $this->roads_active_mdl_TableRow($record);
        
        $result = str_replace("{ROWSET}", $rowset, $thead);
        return $result;
    }
    
    public function roads_active_mdl_TableHead(){
       $comm_classes = "text-center align-middle y-border-no";

        return  "<div id='div_ta_active_mdl_road' class='table-responsive y-mrg-b20 p-0' style='overflow-y:auto;'>" .
                   "<table id='ta_active_mdl_road' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                               "<th class='" . $comm_classes . "'>Подразделение</th>" .
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .
               "</div>";
    }
    
    private function roads_active_mdl_TableRow($row) : string { //string $org_current_rid
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
            
        //   if (strcasecmp($row['rid'], $org_current_rid) == 0)
          //             $comm_classes .= " tbl-act-cell";
          
            $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
          
            
            $result =   "<tr id='tr_active_mdl_road-" . $row['rid'] . "' class='y-cur-def' onclick='acive_road_4_active_mdl_click(this);'>" .
                            "<td id='td_active_mdl_road_ttl-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                                $row['ttl'] .
                            "</td>" .
                                   
                        "</tr>";
        }
        
        return $result;
    }
   */ 
}

/*
                $db = new db_depo();                
                $directs_records = $db->org_getList('');
                
                $directs_list = '';
                $roads_list_FPK = '';
                
                $FPK_rid = '';
                $CDMV_rid = '';
                $DOSS_rid = '';
                               
                //preg_match('#ДОСС#i' , $habb) == 1
                foreach($directs_records as $row){
                    if(preg_match('#ДОСС#i', $row['abb'])) $DOSS_rid = $row['rid'];
                    else if (preg_match('#ФПК#i', $row['abb'])){
                        $FPK_rid = $row['rid'];
                    }else if(preg_match('#ЦДМВ#i', $row['abb'])){
                        $CDMV_rid = $row['rid'];
                    }
                }
                
                $roads_records_FPK = $db->get_roads_list_by_high_org_vw($FPK_rid);  
                $roads_records_DOSS = $db->get_roads_list_by_high_org_vw($DOSS_rid);  
                $roads_records_CDMV = $db->get_roads_list_by_high_org_vw($CDMV_rid);  
                unset($db);
                
                var_dump($roads_records_CDMV);
*/
//$db = new db_depo();
//var_dump($db->passport_pdf_record_by_orgRid_and_carrMdl('2998bb6a-57cb-44ca-a625-1e3dcf7afc1c', '660538e8-1540-45b8-b9aa-04e2874b8312'));
/*
 $path = $_SERVER['PHP_SELF'];   // вернет файл, откуда запускаетс скрипт. 
        
        while (mb_strlen($path) > 1) {  // '>1' значит длиннее '\'
            $path = dirname($path); // возвращает имя родительского каталога из указанного пути
            echo $path . '<br>';

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . '/index.php')){
                 echo $path . '11 <br>';
                break;
            }    
        } 
 
 echo $path;
 */
?>
