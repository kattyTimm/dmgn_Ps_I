<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/pointsbase/php/_dbpoints.php'; // нужна для images
//include_once 'tfpdf_NC.php'; // будет работать с кириллицей      !!!!!!!!!!!!!!!!!!

if (!defined("_SYSTEM_TTFONTS")) define("_SYSTEM_TTFONTS", "C:/Windows/Fonts/");
if (!defined("FPDF_FONTPATH")) define("FPDF_FONTPATH", "/pktbbase/php/lib/font/");

include_once $_SERVER['DOCUMENT_ROOT'] .'/pktbbase/php/lib/tfpdf_132_MY.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/orgcomm/php/_dbcomm.php';
include_once 'assist.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';
  
class Report {
    public function __construct() {
        if (strlen(trim(session_id())) == 0)
            session_start();
    }
    
    public function create_PDF_report ($pdf_file, $part){  
        $pdf = new PDF(); 
        $result;
        
        $fpk = '';
        $doss = '';
        $cdmv = '';
        $fpk_rid = '';
        $doss_rid = '';
        $cdmv_rid = '';

        $db = new db_depo();
        $main_orgs = $db->org_getList('');
        
        foreach($main_orgs as $row){
            if(preg_match('#ФПК#i' , $row['abb']) == 1){
                $fpk = $row['abb'];
                $fpk_rid = $row['rid'];
            }else if(preg_match('#ДОСС#i' , $row['abb']) == 1){
                $doss = $row['abb'];
                $doss_rid = $row['rid'];
            }else if(preg_match('#ЦДМВ#i' , $row['abb']) == 1){
                $cdmv = $row['abb'];
                $cdmv_rid = $row['rid'];
            }
        }
                
        
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 30);
        $pdf->AddPage();

        $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $pdf->SetFont('DejaVu','',12);
        
        $total_fill = 0;
        $total_derived = 0;
        $total_unFill = 0;
         
        if($part == 'carriage'){
            
            $rid_carrPasports_fpk = $db->get_rid_from_carrPasport_limit_ONE($fpk_rid); 
            $rid_carrPasports_doss = $db->get_rid_from_carrPasport_limit_ONE($doss_rid); 
            $rid_carrPasports_cdmv = $db->get_rid_from_carrPasport_limit_ONE($cdmv_rid);

            $arr_fpk = $pdf->count_mdlsAndPdfs($rid_carrPasports_fpk);
            $arr_doss = $pdf->count_mdlsAndPdfs($rid_carrPasports_doss);
            $arr_cdmv = $pdf->count_mdlsAndPdfs($rid_carrPasports_cdmv);    

            $pdf->headerTable('Модели');
            
            $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
            $pdf->SetFont('DejaVu','',10);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(85, 10, $fpk, 1, 0, 'C', 1);
            $pdf->Cell(50, 10, $arr_fpk['mlds'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_fpk['pdfs'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_fpk['mlds'] - $arr_fpk['pdfs'], 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->Cell(85, 10, $doss, 1, 0, 'C', 1);
            $pdf->Cell(50, 10, $arr_doss['mlds'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_doss['pdfs'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_doss['mlds'] - $arr_doss['pdfs'], 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->Cell(85, 10, $cdmv, 1, 0, 'C', 1);
            $pdf->Cell(50, 10, $arr_cdmv['mlds'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_cdmv['pdfs'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_cdmv['mlds'] - $arr_cdmv['pdfs'], 1, 0, 'C', 1);
            
            $total_fill = $arr_fpk['pdfs'] + $arr_doss['pdfs'] + $arr_cdmv['pdfs'];
            $total_derived = $arr_fpk['mlds'] + $arr_doss['mlds'] + $arr_cdmv['mlds'];
            $total_unFill = ($arr_fpk['mlds'] - $arr_fpk['pdfs']) + ($arr_doss['mlds'] - $arr_doss['pdfs']) + ($arr_cdmv['mlds'] - $arr_cdmv['pdfs']);
        }else{
 
            $rids_arr_fpk = $db->train_getRid_by_direction($fpk_rid);
            $rids_doss = $db->train_getRid_by_direction($doss_rid);
          //  $rids_cdmv = $db->train_getRid_by_direction($cdmv_rid);
            
            $arr_fpk = $pdf->count_trainsAndPdfs($rids_arr_fpk);
            $arr_doss = $pdf->count_trainsAndPdfs($rids_doss);
       //     $arr_cdmv = $pdf->count_trainsAndPdfs($rids_cdmv); 
            
            $pdf->headerTable('Поезда');
            
            $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
            $pdf->SetFont('DejaVu','',10);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(85, 10, $fpk, 1, 0, 'C', 1);
            $pdf->Cell(50, 10, $arr_fpk['train'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_fpk['pdf'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_fpk['train'] - $arr_fpk['pdf'], 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->Cell(85, 10, $doss, 1, 0, 'C', 1);
            $pdf->Cell(50, 10, $arr_doss['train'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_doss['pdf'], 1, 0, 'C', 1);
            $pdf->Cell(30, 10, $arr_doss['train'] - $arr_doss['pdf'], 1, 0, 'C', 1);
       //     $pdf->Ln();
          //  $pdf->Cell(85, 10, $cdmv, 1, 0, 'C', 1);
       //     $pdf->Cell(50, 10, $arr_cdmv['train'], 1, 0, 'C', 1);
        //    $pdf->Cell(30, 10, $arr_cdmv['pdf'], 1, 0, 'C', 1);
       //     $pdf->Cell(30, 10, $arr_cdmv['train'] - $arr_cdmv['pdf'], 1, 0, 'C', 1);
            
            $total_fill = $arr_fpk['pdf'] + $arr_doss['pdf']; // + $arr_cdmv['pdf']
            $total_derived = $arr_fpk['train'] + $arr_doss['train']; // + $arr_cdmv['train']
            $total_unFill = ($arr_fpk['train'] - $arr_fpk['pdf']) + ($arr_doss['train'] - $arr_doss['pdf']); // + ($arr_cdmv['train'] - $arr_cdmv['pdf'])
        }
        unset($db);
        $pdf->Ln();
        
        $pdf->SetFont('DejaVu','',12);
        $pdf->Cell(80, 10, 'Итого', 0, 0, 'R', 0);
        $pdf->Cell(5, 10, '', 'T', 0, 'C', 1);
        $pdf->Cell(50, 10, $total_derived, 1, 0, 'C', 1);
        $pdf->Cell(30, 10, $total_fill, 1, 0, 'C', 1);
        $pdf->Cell(30, 10, $total_unFill, 1, 0, 'C', 1);
        
        $pdf->Ln();


        //$pdf->Output();  
      //  $pdf->Output($pdf_file); 
         $pdf->Output('F',$pdf_file , true);  /// $_SERVER['DOCUMENT_ROOT'] . 
        $result = true;
        return $result;
          
        
    }
    
    public function getTimestampFormat($str){
         //mktime(hour, min, sec, month, day, year)
        //2020-04-23 10:57:17
        
        $arr = array_reverse(explode(' ', $str)); // [10:57:17' , '2020-04-23']
        
        $hms = explode(':', $arr[0]);
        $mdy = explode('-', $arr[1]);
        
        $hour = $hms[0];
        $min= $hms[1];
        $sec = $hms[2];
      
        $month = $mdy[1];
        $day = $mdy[2];
        $year = $mdy[0];
        
        return  mktime($hour, $min, $sec, $month, $day, $year);
    }
    
    public static function get_time_without_seconds_forReport($str){
        $arr = explode(' ', $str);
        $dmYArr = explode('-', $arr[0]);
        $dmY = implode('.', array_reverse($dmYArr));
        $hmsArr = explode(':', $arr[1]);
        array_pop($hmsArr);
        $hm = implode(':', $hmsArr);
        return $dmY .' '.$hm;
    }
    
    public function seconds2times($seconds){
        $times = array();

        // считать нули в значениях
        $count_zero = false;

        // количество секунд в году не учитывает високосный год
        // поэтому функция считает что в году 365 дней
        // секунд в минуте|часе|сутках|году
        $periods = array(60, 3600, 86400, 31536000);

        for ($i = 3; $i >= 0; $i--){
            $period = floor($seconds/$periods[$i]);

            if (($period > 0) || ($period == 0 && $count_zero)){
                $times[$i+1] = $period;
                $seconds -= $period * $periods[$i];

                $count_zero = true;
            }
        }    
        $times[0] = $seconds;
        return $times;
    }

    public function timeForTable($diff){       
        $res = '';
        $seconds = array($diff);
                        // значения времени
                $times_values = array('с.','м.','ч.','д.','лет');

                    foreach ($seconds as $second){
                          //  $res .= $second . ' сек. = ';

                            $times = $this->seconds2times($second);
                            for ($i = count($times)-1; $i >= 0; $i--) {
                                $res .= $times[$i] . '' . $times_values[$i] . ' ';
                            }                           
                    }
        return $res;
    }
                    
                  
    public function image2tmpFile(string $tbl, string $rid, string $fld) : string { // image2tmpFile возвоащает картинку
        $result = "";
        
        $path = $_SERVER['PHP_SELF'];   // Путь к текущему скрипту, начиная с корневой директории проекта, например /test_a/php/jgate.php

        while (mb_strlen($path) > 1) {  // '>1' значит длиннее '\'
            $path = dirname($path);

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . '/index.php'))
                break;
        }

        $tmp_dir = $_SERVER['DOCUMENT_ROOT'] . $path . '/tmp';

        // Если директория не существует, создаем
        if (!is_dir($tmp_dir))
            mkdir($tmp_dir, 0777, true);
        
//echo $tmp_dir . '<br>';
        
        // результирующий файл (name only)
        $img_file = $tmp_dir . '/' . $rid . '_' . $fld;

        $db = new _dbpoints();
        $img_str = $db->table_getLoadedImageData($tbl, $rid, $fld);
               
        unset($db);

//echo $img_file . '<br>';
//echo $img_str . '<br>';

        $pref_pos = strpos($img_str, ",");
        $pref_ = substr($img_str, 0, $pref_pos + 1);  // ex: data:image/jpeg;base64,

        $imgData = substr($img_str, $pref_pos + 1);
        
      //  $pic = 'data://text/plain;base64,' . $imgData;
        
        $imgData = str_replace(' ', '+', $imgData);
        $imgData = base64_decode($imgData);

        // mime type
        $colon_pos = strpos($img_str, ":");
        $semi_pos = strpos($img_str, ";");
        $img_mime = substr($img_str, $colon_pos + 1, $semi_pos - $colon_pos - 1);

//echo $img_mime . '<br>';
//echo $imgData . '<br>';
        
        $img_file .= ($img_mime == 'image/png' ? '.png' : '.jpg');

        $fp = fopen($img_file, 'wb'); // get_put_content - работает только с текстом
        if ($fp) {
            fwrite($fp, $imgData);
            fclose($fp);
        }
        
        if (is_file($img_file))
           $result = $img_file;

     return $img_str;
     //return $result;
    }

    public static function get_current_num($str){
        $arr = str_split($str, 1);
        
        if($arr[0] == 0){
            $num = (int)$arr[1];
            return $num;
        }else if ($arr[0] != 0){
           $res = implode('', $arr);
           $res = (int)$res;
            return  $res;           
        }
    }
}  
  

class PDF extends tFPDF{
    
     function header(){   
        $title = 'Отчет';  
        
        $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);  // folder 'font' should be near assist.php !!!!!!!
        $this->SetFont('DejaVu','',20);                  
        $this->SetLeftMargin(8);
        $this->Cell(0, 10, $title,0, 0, 'C');
        $this->Ln();
        $this->SetFont('DejaVu','',15); 
        $this->Cell(0, 10, 'По заполнению',0, 0, 'C');
        $this->Ln();           
     
            /*           
                примерно так выветедся картинка:
                    $image = assist::image2tmpFile("rwp", '099efa87-853f-4319-96d9-0f61cd2cf7bc', 'vwm');   

                    $colon_pos = strpos($image, ":");
                    $semi_pos = strpos($image, ";");
                    $img_mime = substr($image, $colon_pos + 1, $semi_pos - $colon_pos - 1);

                    $ext = ($img_mime == 'image/png' ? 'png' : 'jpg');           


                    $info = getimagesize($image);
                    $this->Image($image,10,6,30,30, $ext);
                    $this->Image($image,10,30,$info[0], $info[1], 'jpg' );                       
            */           
        }
       
    function footer()
            {
                $this->SetY(-15);
                $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);  // folder 'font' should be near assist.php
                $this->SetFont('DejaVu','',8);

                $this->SetTextColor(112, 128, 144);
                $this->Cell(195,10,''.$this->PageNo().'/{nb}',0,0,'R');

            }
                
    function headerTable($subTitle){
        $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);  // folder 'font' should be near assist.php
        $this->SetFont('DejaVu','',12);

        $this->SetTextColor(0,0,0);
        $this->SetFillColor(245, 245, 245);

        $this->Cell(195, 10, $subTitle, 0, 0,'C',0);
        $this->Ln(); 

        $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);  // folder 'font' should be near assist.php
        $this->SetFont('DejaVu','',10);
        $this->Cell(85, 10, 'Наименование дирекции', 1, 0,'C',1);                   
        $this->Cell(50, 10, 'Выведено в отчет', 1, 0, 'C', 1);
        $this->Cell(30, 10, 'Заполнено',1, 0, 'C', 1);
        $this->Cell(30, 10, 'Не заполнено',1, 0, 'C', 1);
        $this->Ln();
    }
    
    public function count_mdlsAndPdfs($arr){
        $db = new db_depo();
        $result['pdfs'] = 0;
        $result['mlds'] = 0;

        foreach($arr as $row){
           if($db->pdf_pidExists($row['rid']) || $db->docs_pidExists($row['rid'])) $result['pdfs']++;

        $result['mlds']++;
        }   
        unset($db);
        return $result;
    }
    
    public function count_trainsAndPdfs($arr){
        $db = new db_depo();
        $result['pdf'] = 0;
        $result['train'] = 0;

        foreach($arr as $row){
           if($db->docs_pidExists($row['rid'])) $result['pdf']++;

        $result['train']++;
        }   
        unset($db);
        return $result;
    }
}
/*
$db = new db_depo();
$rep = new PDF();

$arr =  $db->get_rid_from_carrPasport_limit_ONE('38400d1f-dc47-4915-b44d-45ed3ae494a0'); 
var_dump($rep->count_mdlsAndPdfs($arr));

*/
?>


