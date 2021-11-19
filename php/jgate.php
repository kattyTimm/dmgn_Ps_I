<?php
// gate for .js ajax queries
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_dbbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_jgpktb.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pointsbase/php/_jgpoints.php';

include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';
include_once 'assist.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPs/php/xlsx.php';

$form_data = [];    //Pass back the data

$part = strval($_POST['part']);
$ass = new assist();    
$form_data['success'] = false;
$db = new db_depo();
$xlsx = new xlsx();

if ($part == 'get_app_globals') {
   
    $jg = new _jgpktb();
    $jg->jg_get_app_globals($form_data);     // $form_data pass by reference
    unset($jg);
    
    /* RESULT
        $form_data['ip']
        $form_data['ipls_id']
        $form_data['fdb_blk']
    */
    
    // extended
    $form_data['itr']    = _assbase::isAdminIP('dmgnPsIns') ? 1 : 0;                 // 1: Trusted IP
    $form_data['ptr']    = _assbase::isAdminPrefix('dmgnPsIns') ? 1 : 0;             // 1: ...

    $form_data['success'] = true;
}

else if ($part == 'get_fm') {
    $fm_id = trim(strval($_POST['fm_id']));
    $sparam = array_key_exists('sparam', $_POST) ? trim(strval($_POST['sparam'])) : "";
    $section = trim(strval($_POST['section']));

    $result = $ass->get_fm($fm_id, $sparam, $section);
    
    $form_data['html'] = $result;
    if (strlen($result) > 0)
        $form_data['success'] = true;
}

else if($part == 'get_orgs'){
  //  $high = strval($_POST['high']);
    $open_panels = strval($_POST['open_panels']);
  //  $part = strval($_POST['section']);
    
   // $result = $ass->get_org_records($high);
    $result = $ass->get_org_accardion_part($open_panels);
    
    if($result){
        $form_data['body'] = $result;
        $form_data['success'] = true;
    }
}

else if($part == 'get_models'){
   // $rid = strval($_POST['main_org_rid']);
      $offset = intval($_POST['offset']);
      $rows = intval($_POST['rows']);
      $currpage = intval($_POST['currpage']);

   // $result = $ass->get_models($rid);
      $result = $ass->get_all_models($offset, $rows, $currpage);

      
    if($result){
        $form_data['search'] = $result['search'];
        $form_data['models'] = $result['models'];
        $form_data['pagination'] = $result['pagination'];
        $form_data['success'] = true;
    }
}

else if($part == 'get_roads'){
    $high = strval($_POST['high']);
    
    $result = $ass->get_roads_records($high);
    
    if($result){
        $form_data['orgs'] = $result;
        $form_data['success'] = true;
    }
}


else if($part == 'get_reestr_4_current_road'){
    $rid = strval($_POST['rid']);
    
    $result = $ass->get_registers_table($rid);
    
    if($result){
        $form_data['reestr'] = $result;
        $form_data['success'] = true;
    }
}

else if($part == 'get_trains_4_current_road'){
    $road_rid = strval($_POST['act_road']);
    
    $result = $ass->get_registr_train($road_rid);
    
    if($result){
        $form_data['reestr'] = $result;
        $form_data['success'] = true;
    }
}

else if($part == 'clnf_get_rec_by_ip'){
    
        $ip = _dbbase::get_currentClientIP();

        $form_data['ip']  = $ip;
        $result = $db->clnf_getRecByIP($ip);
        
        /*                         'rid' => $sel_rid
                                    'org' => $sel_org,
                                    'flg' => $sel_flg,
                                    'ip'  => $sel_ip,
                                    'rem' => $sel_rem,

                                    'ohigh' => $sel_ohigh,
                                    'ottl'  => $sel_ottl,
                                    'oabb'  => $sel_oabb,
                                    'httl'  => $sel_httl,
                                    'habb'  => $sel_habb,
                                    'hhttl' => $sel_hhttl,
                                    'hhabb' => $sel_hhabb,
        
        */
        
        if (count($result) > 0) {
            $form_data['rid']  = $result['rid'];
            $form_data['org']  = $result['org'];
            $form_data['flg']   = $result['flg'];
            $form_data['ip']   = $result['ip'];
            $form_data['rem']  = $result['rem'];
            $form_data['success'] = true;
        }
}

else if($part == 'show_buttons'){
        
           $result = $ass->get_btns_for_user_FPK();
    
            if(count($result) > 0){
               $form_data['success'] = true;
               $form_data['btn_for_curriage'] = $result['btn_for_curriage'];
                $form_data['btn_for_train'] = $result['btn_for_train'];
           }
       
}

else if($part == 'get_detail_body'){
    $road_or_mdl = strval($_POST['road_or_mdl']);
    $section = strval($_POST['section']);
    $high = strval($_POST['rid']);
    
    
    $result = $ass->get_detail_body($road_or_mdl, $section, $high);
    
       if(count($result) > 0){
           $form_data['success'] = true;
           $form_data['content'] = $result['content'];
       }
           
}


else if ($part == 'show_roads_for_active_model'){
    $high = strval($_POST['high']);
    $carr_mdl = strval($_POST['carr_mdl']);
    
      $result = $ass->get_active_mdl_roads($high, $carr_mdl);   
    
    if($result){
        $form_data['roads'] = $result;
        $form_data['success'] = true;
    }
}


else if ($part == 'get_actions'){
     $rid = strval($_POST['rid']);
     $section = strval($_POST['section']);
     
         $result = $ass->get_common_part($rid, $section);   
     
   //  if(strlen($result > 0)){
            $form_data['success'] = true;
            $form_data['actions'] = $result;
  //   }
}

else if ($part == 'get_actions_by_mdl'){
     $act_mdl_rid = strval($_POST['act_mdl_rid']);
     
         $result = $ass->actions_list_by_mdl($act_mdl_rid);   
     
   //  if(strlen($result > 0)){
            $form_data['success'] = true;
            $form_data['actions'] = $result;
  //   }
}

else if($part == 'docs_part'){
     $pid = strval($_POST['pid']);
     $section = strval($_POST['section']);

     $result = $ass->get_document_part($pid, $section);
      
     $form_data['html'] = $result;
            $form_data['success'] = true;
}

else if($part == 'pass_pdf_part'){
     $pid = strval($_POST['rid']);
//     $main_org = strval($_POST['main_org']);  not actual now
     
     $result = $ass->get_passportPdf_part($pid);
      
     $form_data['html'] = $result;
            $form_data['success'] = true;
}

/*
else if($part == 'docs_part_by_active_mdl'){
     $pid = strval($_POST['pid']);
     $main_org = strval($_POST['org']);
     
     $result = $ass->get_document_part_by_act_mdl($pid);
      
     $form_data['html'] = $result;
     $form_data['success'] = true;
}

*/
else if($part == 'file_put_tmp'){
        $rid = trim(strval($_POST['val']));

        $result = $db->docs_getRec($rid);

        if (count($result) > 0) {
            $fname = _assbase::dataUri2tmpFile($_SERVER['DOCUMENT_ROOT'] . assist::siteRootDir() . '/tmp', $result['fnm'], $result['rdat']);

            if (mb_strlen($fname) > 0) {
                $form_data['frelname'] = assist::siteRootDir() . '/tmp/' . $result['fnm'];
                $form_data['success'] = true;
            }
        }
}

else if($part == 'file_put_tmp_pdf'){
        $rid = trim(strval($_POST['val']));

        $result = $db->passport_pdf_record_by_rid($rid);

        if (count($result) > 0) {
            $fname = _assbase::dataUri2tmpFile($_SERVER['DOCUMENT_ROOT'] . assist::siteRootDir() . '/tmp', $result['fnm'], $result['rdat']);

            if (mb_strlen($fname) > 0) {
                $form_data['frelname'] = assist::siteRootDir() . '/tmp/' . $result['fnm'];
                $form_data['success'] = true;
            }
        }
}


else if($part == 'rep_carriage'){
    $rid_mdl = strval($_POST['rid_mdl']);
    $section = strval($_POST['section']);
    $ttl = strval($_POST['ttl']);
    
    if($section == 'carriage'){
        $result = $xlsx->mdl_reestr($rid_mdl, $ttl);
    }else if($section == 'train'){
        $result = $xlsx->one_train_reestr($rid_mdl, $ttl);
       //  $result = 'train';
    }
    
    if (strlen($result) > 0) {
        $form_data['frelname'] = $result;
        $form_data['success'] = mb_strlen($result) > 0;
    }
    
}

else if($part == 'road_reestr'){
    $rid_road = strval($_POST['rid_road']);
    $section = strval($_POST['section']);
    
    if($section == 'carriage'){
         $result = $xlsx->road_reestr_group($rid_road);
    }else if($section == 'train'){
        $result = $xlsx->train_reestr_group($rid_road);
      //  $result = 'train _road';
    }
    
	if (strlen($result) > 0) {
		$form_data['frelname'] = $result;
		$form_data['success'] = true;
	}
}


else if($part == 'main_org_reestr'){
    $rid_org = strval($_POST['rid_org']);
    $section = strval($_POST['section']);

   // $result = $xlsx->mdl_reestr_org($rid_org);
    
    if($section == 'carriage'){
          $result = $xlsx->mdl_reestr_org();
    }else if($section == 'train'){
     $result = $xlsx->train_reestr_Total(); 
      //   $result = 'train _org';
    }
    
    if (strlen($result) > 0) {
        $form_data['frelname'] = $result;
        $form_data['success'] = true;
    }
}

else if($part == 'main_org_reestr_category'){
    //	$ini_str = str_replace('^', "'", $ini_str);
     $ini_str = trim(strval($_POST['ini_str']));
     $section = strval($_POST['section']);
     
     $k = intval($_POST['k']);
     $o = intval($_POST['o']);
     $s = intval($_POST['s']);
     $g = intval($_POST['g']);
    
     $dp = intval($_POST['dp']);
     $dc = intval($_POST['dc']);
     $nd = intval($_POST['nd']);
   
    if($section == 'train'){
          $result = $xlsx->category_train_reestr($ini_str, $dp, $dc, $nd); 
    }else{
         $result = $xlsx->category_carriage_reestr($ini_str, $k, $o, $s, $g, $dp, $dc, $nd); 
    }
    
    if (strlen($result) > 0) {
        $form_data['frelname'] = $result;
        $form_data['success'] = true;
    }
}

else if($part == 'send_cat_params'){
    
    $k = intval($_POST['k']);
    $o = intval($_POST['o']);
    $s = intval($_POST['s']);
    $g = intval($_POST['g']);
    
    $dp = intval($_POST['dp']);
    $dc = intval($_POST['dc']);
    $nd = intval($_POST['nd']);

    $result = $xlsx->cat_params_reestr_mdl($k, $o, $s, $g, $dp, $dc, $nd); 
    
    if(strlen($result) > 0){
        $form_data['frelname'] = $result;
        $form_data['success'] = true;
    }
}
else if ($part == 'send_cat_params_train'){
    $dp = intval($_POST['dp']);
    $dc = intval($_POST['dc']);
    $nd = intval($_POST['nd']);
    
    $result = $xlsx->cat_params_reestr_train($dp, $dc, $nd); 
    
    if(strlen($result) > 0){
        $form_data['frelname'] = $result;
        $form_data['success'] = true;
    }
}

else if($part == 'send_multi_params'){
    
    $k = intval($_POST['k']);
    $o = intval($_POST['o']);
    $s = intval($_POST['s']);
    $g = intval($_POST['g']);
    
    $dp = intval($_POST['dp']);
    $dc = intval($_POST['dc']);
    $nd = intval($_POST['nd']);
    
    $ini_str = strval($_POST['ini_str']);

    $result = $xlsx->multi_params_carriage_reestr($k, $o, $s, $g, $dp, $dc, $nd, $ini_str); 
    
    if(strlen($result) > 0){
        $form_data['frelname'] = $result;
        $form_data['success'] = true;
    }
}

else if ($part == 'send_multi_train'){
    $ini_str = strval($_POST['ini_str']);
    $dp = intval($_POST['dp']);
    $dc = intval($_POST['dc']);
    $nd = intval($_POST['nd']);
    
    $result = $xlsx->multi_train_reestr($dp, $dc, $nd, $ini_str); 
    
    if(strlen($result) > 0){
        $form_data['frelname'] = $result;
        $form_data['success'] = true;
    }
}

else if ($part == 'search_get_model_row') {
    $rid = trim(strval($_POST['rid']));
    $result = $db->table_getRidRowNumber('carr_pasport', 'mdl', $rid); //obj_nm

    $form_data['rownum'] = $result;
    $form_data['success'] = true;
}
else if($part == 'carriage_get_rec'){
    $rid = trim(strval($_POST['rid']));

    $result = $db->carriage_getRec($rid);


        if (count($result) > 0) {
            $form_data['rid']  = $result['rid'];
            $form_data['obj_nm']  = $result['obj_nm'];
            $form_data['mdl']  = $result['mdl'];
            $form_data['date_num_registr']   = $result['date_num_registr'];
            $form_data['docs_at_modern']  = $result['docs_at_modern'];
            $form_data['exp_res']  = $result['exp_res'];
            $form_data['recoms_using']  = $result['recoms_using'];
            $form_data['date_act']  = $result['date_act'];
            $form_data['flg']  = $result['flg'];
            $form_data['note']  = $result['note'];
            $form_data['org']  = $result['org'];
            $form_data['carr_mdl']  = $result['carr_mdl'];
            $form_data['success'] = true;
        }
}

else if($part == 'get_detail_info'){
      $rid = trim(strval($_POST['rid']));
      $section = trim(strval($_POST['section']));
      
      $result = $ass->show_detail_info($rid, $section);
      
     $form_data['detail_info'] = $result;
     $form_data['success'] = true;
}


// help interface --------------------------------------------

else if ($part == 'hlpc_get_rdat_as_html') {
    $jg = new _jgpktb();
    $jg->jhlp_hlpc_get_rdat_as_html($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['html']
}

// End of: help interface --------------------------------------------
  


// bbmon interface --------------------------------------------
else if ($part == 'bbmon_active_count') {
    $jg = new _jgpktb();
    $jg->jbbmon_active_count($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['count']  // -1 - error, 0... - active count
}
else if ($part == 'bbmon_bbrs_add') {
    $jg = new _jgpktb();
    $jg->jbbmon_bbrs_add($form_data);   // $form_data pass by reference
    unset($jg);
    
    //success: $form_data['rid']
    //error:   $form_data['errcode']    // errcodes: 0 - sql error, -1 - empty bbrd, -2 - unknown ip, -3 - pair exists
}
else if ($part == 'bbmon_get_detail') {
    $jg = new _jgpktb();
    $jg->jbbmon_get_detail($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['html']
}
else if ($part == 'bbmon_reload_body') {
    $jg = new _jgpktb();
    $jg->jbbmon_reload_body($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['totalrows']
    //$form_data['pagination']
    //$form_data['bbmon_list_body']
}
else if ($part == 'bbra_to_tmp') {
    $jg = new _jgpktb();
    $jg->jbbmon_bbra_to_tmp($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['frelname']
}
// End of: bbmon interface --------------------------------------------



// fdb interface --------------------------------------------
else if ($part == 'fdb_reload_body') {
    $jg = new _jgpktb();
    $jg->jfdb_fdb_reload_body($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['totalrows']
    //$form_data['pagination']
    //$form_data['fdbr_list_body']
    //$form_data['new_fdbr']
}
else if ($part == 'fdba_add_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdba_add_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
}
else if ($part == 'fdba_view_file') {
    $jg = new _jgpktb();
    $jg->jfdb_fdba_view_file($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['frelname']
}
else if ($part == 'fdbm_add_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbm_add_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
    // or
    //$form_data['errcode']
}
else if ($part == 'fdbm_get_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbm_get_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
    //$form_data['fdbr']
    //$form_data['flg']
    //$form_data['ips']
    //$form_data['dtcs']
    //$form_data['txt']
}
else if ($part == 'fdbr_active_count') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_active_count($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['fdbr_cnt']
    //$form_data['child_cnt']
}
else if ($part == 'fdbr_add_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_add_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
    //$form_data['rownum']
    // or
    //$form_data['errcode']
}
else if ($part == 'fdbr_cancel') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_cancel($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['success'] = true;
}
else if ($part == 'fdbr_get_conversation_icnt') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_get_conversation_icnt($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['icnt']
}
else if ($part == 'fdbr_get_detail') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_get_detail($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['html']
}
else if ($part == 'fdbr_get_detail_body') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_get_detail_body($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['html']
}
// End of: fdb interface

unset($ass);
unset($db);
unset($xlsx);
 
echo json_encode($form_data);   //, JSON_UNESCAPED_UNICODE

/*
echo trim('"Кол-во мест для сидения, чел.: головной вагон - 42 (303.00.00.002 1 класс), 53 (303.00.00.002-01
2 класс); моторный вагон - 62 (302.00.00.002 1 класс), 77 (302.00.00.002-01 2 класс), 72 (302.00.00.002-02 2 класс с багажным отсеком); прицепной вагон - 56 (304.00.00.002
1 класс), 70 (304.00.00.002-01 2 класс); 
Расчетная населенность, чел.: головной вагон - 42 (303.00.00.002 1 класс), 53 (303.00.00.002-01
2 класс); моторный вагон - 62 (302.00.00.002 1 класс), 77 (302.00.00.002-01 2 класс), 72 (302.00.00.002-02 2 класс с багажным отсеком); прицепной вагон - 56 (304.00.00.002
1 класс), 70 (304.00.00.002-01 2 класс); 
Максимальная населенность, чел.: головной вагон - 42 (303.00.00.002 1 класс), 53 (303.00.00.002-01
2 класс); моторный вагон - 62 (302.00.00.002 1 класс), 77 (302.00.00.002-01 2 класс), 72 (302.00.00.002-02 2 класс с багажным отсеком); прицепной вагон - 56 (304.00.00.002
1 класс), 70 (304.00.00.002-01 2 класс); "', " \"\" ");

*/
?>
