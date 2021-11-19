<?php
 include_once 'wagon_types_db.php';

$form_data = [];    //Pass back the data

$context = trim($_GET['srch_box']);

$db = new wagon_types_db();
$result = $db->searching_coincidence_mdls($context);
unset($db);

$form_data['query'] = $context;
$form_data['suggestions'] = $result;

//Return the data back
echo json_encode($form_data);
?>
