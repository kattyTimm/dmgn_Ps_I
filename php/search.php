<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';
// $_SERVER['DOCUMENT_ROOT'] $_SERVER['DOCUMENT_ROOT'] укажет путь до "C:\inetpub\wwwroot"

$form_data = [];    //Pass back the data

$context = trim($_GET['srch_box']);

$db = new db_depo();
$result = $db->search_get_carr_pasport_AllSuggestions($context);
unset($db);

$form_data['query'] = $context;
$form_data['suggestions'] = $result;

//Return the data back
echo json_encode($form_data);
?>
