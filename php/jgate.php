<?php
// gate for .js ajax queries
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_dbbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/orgcomm/php/_jgcomm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';

include_once 'wagon_types_db.php';
include_once 'assist.php';

$form_data = [];    //Pass back the data
$form_data['success'] = false;

$assist = new assist();
$db_depo = new db_depo();
$db_type = new wagon_types_db();

$part = strval($_POST['part']);

if($part == 'clnf_get_rec_by_ip'){
    $ip = _dbbase::get_currentClientIP(); // '10.144.94.77'
    //$ip = '2';
        $form_data['ip']  = $ip;
        $result = $db_depo->clnf_getRecByIP($ip);

        if (count($result) > 0) {
            $form_data['rid']  = $result['rid'];
            $form_data['org']  = $result['org'];
            $form_data['flg']   = $result['flg'];
            $form_data['ip']   = $result['ip'];
            $form_data['rem']  = $result['rem'];
            $form_data['success'] = true;
        }
}
else if ($part == 'load_types_tbl' || $part == 'load_mdls_tbl'){
    
   if($part == 'load_types_tbl'){  
     $result = $assist->show_types();
   }
   else if ($part == 'load_mdls_tbl'){
     
     $rid = strval($_POST['rid_type']);
     $offset = intval($_POST['offset']);  
     $rows = intval($_POST['rows']);    
     $currpage = intval($_POST['currpage']);    
     
     $from = (strlen(strval($_POST['from'])) > 0) ? strval($_POST['from']) : '';
     $to = (strlen(strval($_POST['to'])) > 0) ? strval($_POST['to']) : '';
     
     $country = (mb_strlen(strval($_POST['country'])) > 0) ? strval($_POST['country']) : '';
     
     $factory = (mb_strlen(strval($_POST['factory'])) > 0) ? strval($_POST['factory']) : '';
     
     $result = $assist->show_mdls($offset, $rows, $currpage, $rid, $from, $to, $country, $factory); 
   }
   
   if($part == 'load_types_tbl'){
    if(strlen($result) > 0){
        $form_data['body'] = $result;
        $form_data['success'] = true;
    }
   }else{
        if(count($result) > 0){
            $form_data['filter'] = $result['filter'];
            $form_data['body'] = $result['tbl']; 
            $form_data['pagination'] = $result['pagination'];
            $form_data['success'] = true;
        }
    }
}

else if($part == 'load_exe_info'){ 
    $rid = strval($_POST['rid']);
    
    $result = $assist->show_execution_info($rid);
    
    if(strlen($result) > 0){
       $form_data['body'] = $result;
       $form_data['success'] = true;
   }
}

else if($part == 'load_execution'){
    $rid = strval($_POST['rid_mdl']);
    
    $result = $assist->show_execution($rid);
    
    if(strlen($result) > 0){
       $form_data['body'] = $result;
       $form_data['success'] = true;
   }
}

else if($part == 'add_new_exe'){
    $nm =  strval($_POST['nm']);
    $factory =  strval($_POST['factory']);
    $country =  strval($_POST['country']);
    $flg = intval($_POST['flg']);
    $rid_mdl = strval($_POST['rid_mdl']);
    $rid = strval($_POST['rid']);
    $spec_n = strval($_POST['spec_n']);
    $desigDraw_techCon = strval($_POST['desigDraw_techCon']);
    $comm_draw = strval($_POST['comm_draw']);
    $w_base = strval($_POST['w_base']);
    $w_length = strval($_POST['w_length']);
    $b_length = strval($_POST['b_length']);
    $b_width = strval($_POST['b_width']);
    $b_height = strval($_POST['b_height']);
    $composition = strval($_POST['composition']);
    
    $layout = strval($_POST['layout']);
    $type_coup_dev = strval($_POST['type_coup_dev']);
    $tM_cart = strval($_POST['tM_cart']);
    $weight_tare = strval($_POST['weight_tare']);
    $payload = strval($_POST['payload']);
    
    $size_bC = strval($_POST['size_bC']);
    $quan_sit_plcs = strval($_POST['quan_sit_plcs']);
    $constr_speed = strval($_POST['constr_speed']);
    $existType_EHTK = strval($_POST['existType_EHTK']);
    $type_brake = strval($_POST['type_brake']);
    $type_trans_dev = strval($_POST['type_trans_dev']);
       
    $type_B_dev = strval($_POST['type_B_dev']);
    $systemElectr = strval($_POST['systemElectr']);
    $type_gen = strval($_POST['type_gen']);
    
    $DGY = strval($_POST['DGY']);
    $syfle = strval($_POST['syfle']);
    $video = strval($_POST['video']);
    
    $type_drive = strval($_POST['type_drive']);
    $vent_cond = strval($_POST['vent_cond']);
    $air_decon = strval($_POST['air_decon']);
    
    $aqua_decon = strval($_POST['aqua_decon']);
    $F_MV_B_C = strval($_POST['F_MV_B_C']);
    $BRISS = strval($_POST['BRISS']);
    
    $sys_heat = strval($_POST['sys_heat']);
    $cert = strval($_POST['cert']);
    $serv_life = strval($_POST['serv_life']);
    $runs = strval($_POST['runs']);
    $prod_ys = strval($_POST['prod_ys']);

    $result = $db_type->add_orEdit_execution($rid, $rid_mdl, $nm,$factory,$country, $flg, $spec_n, $desigDraw_techCon, $comm_draw, $w_base, $w_length,
                                             $b_length, $b_width, $b_height,$composition,  $layout, $type_coup_dev, $tM_cart, $weight_tare, 
                                             $payload, $size_bC, $quan_sit_plcs, $constr_speed, $existType_EHTK, $type_brake, $type_trans_dev,
                                             $type_B_dev, $systemElectr, $type_gen, $DGY, $syfle, $video,
                                             $type_drive, $vent_cond, $air_decon, $aqua_decon, $F_MV_B_C, $BRISS,
                                             $sys_heat, $cert, $serv_life, $runs, $prod_ys);
    
    if(strlen($result) > 0){
        $form_data['success'] = true;
        $form_data['rid'] = $result;
    }     
}

else if ($part == 'get_last_exe'){
    $rid_mdl = strval($_POST['rid_mdl']);
    
    $result = $db_type->get_last_exe_by_mdl($rid_mdl);

    if(count($result) > 0){
       $form_data['success'] = true;
       $form_data['nm'] = $result['nm'];
       $form_data['factory'] = $result['factory'];
       $form_data['country'] = $result['country'];
       $form_data['spec_n'] = $result['spec_n'];
       $form_data['desigDraw_techCon'] = $result['desigDraw_techCon'];
       $form_data['comm_draw'] = $result['comm_draw'];
       $form_data['w_base'] = $result['w_base'];
       $form_data['w_length'] = $result['w_length'];
       $form_data['b_length'] = $result['b_length'];
       $form_data['b_width'] = $result['b_width'];
       $form_data['b_height'] = $result['b_height'];
       $form_data['composition'] = $result['composition'];
       
       $form_data['layout'] = $result['layout'];
       $form_data['type_coup_dev'] = $result['type_coup_dev'];
       $form_data['tM_cart'] = $result['tM_cart'];
       $form_data['weight_tare'] = $result['weight_tare'];
       $form_data['payload'] = $result['payload'];
       
       $form_data['size_bC'] = $result['size_bC'];
       $form_data['quan_sit_plcs'] = $result['quan_sit_plcs'];
       $form_data['constr_speed'] = $result['constr_speed'];
       $form_data['existType_EHTK'] = $result['existType_EHTK'];
       $form_data['type_brake'] = $result['type_brake'];
       $form_data['type_trans_dev'] = $result['type_trans_dev'];
       
       $form_data['type_B_dev'] = $result['type_B_dev'];
       $form_data['systemElectr'] = $result['systemElectr'];
       $form_data['type_gen'] = $result['type_gen'];
       $form_data['DGY'] = $result['DGY'];
       $form_data['syfle'] = $result['syfle'];
       $form_data['video'] = $result['video'];
       
       $form_data['type_drive'] = $result['type_drive'];
       $form_data['vent_cond'] = $result['vent_cond'];
       $form_data['air_decon'] = $result['air_decon'];
       $form_data['aqua_decon'] = $result['aqua_decon'];
       $form_data['F_MV_B_C'] = $result['F_MV_B_C'];
       $form_data['BRISS'] = $result['BRISS'];
       
       $form_data['sys_heat'] = $result['sys_heat'];
       $form_data['cert'] = $result['cert'];
       $form_data['serv_life'] = $result['serv_life'];
       $form_data['runs'] = $result['runs'];
       $form_data['prod_ys'] = $result['prod_ys'];
    }
}

else if ($part == 'get_fm') {
    $fm_id = trim(strval($_POST['fm_id']));
    $sparam = '';
    
    if($fm_id == 'execution_form')
        $sparam = strval($_POST['mdl_rid']); 
   
    $result = $assist->get_fm($fm_id, $sparam);

    $form_data['html'] = $result;
    if (strlen($result) > 0)
        $form_data['success'] = true;
}

else if($part == 'type_data' || $part == 'mdl_data'){
    $nm =  strval($_POST['nm']);
    $flg = intval($_POST['flg']);
    $rid = strval($_POST['rid']);
    
    if($part == 'type_data'){
       $result = $db_type->add_orEdit_type($rid, $nm, $flg);

    }else if($part == 'mdl_data'){
       $rid_type = strval($_POST['rid_type']);
       $result = $db_type->add_orEdit_mdl($rid, $nm, $rid_type, $flg);    
    }
    
    if(strlen($result) > 0 ){ // || $result > 0
        $form_data['success'] = true;
        $form_data['rid'] = $result;  
    }    
}

else if($part == 'verify_mdl_name'){
    $nm = strval(trim($_POST['nm']));
    
    $result = $db_type->verify_nm_mdl($nm);
    
    if(!$result){      
        $form_data['success'] = true;
         $form_data['nm'] = '';
    }else{
        $form_data['nm'] = $result;
    }
}

else if ($part == 'search_get_mdls_row'){
    $rid = trim(strval($_POST['rid']));
    $result = $db_type->table_getRidRowNumber('mdls', 'nm', $rid);

    $form_data['rownum'] = $result;
    $form_data['success'] = true;
}

else if ($part == 'delete_type' || $part == 'delete_mdl' || $part == 'delete_exe'){  
    $rid = strval($_POST['rid']);
    
    if($part == 'delete_type')
        $result = $db_type->delete_type_by_rid($rid);
    else if ($part == 'delete_mdl')
        $result = $db_type->delete_mdl_by_rid($rid);
    else if($part == 'delete_exe')
        $result = $db_type->delete_exe_by_rid($rid);
    
    if($result) $form_data['success'] = true;
}


else if ($part == 'get_docs'){
    $pid = strval($_POST['pid']);
    
    $result = $assist->receive_docs($pid);
    
    $form_data['success'] = true;
    $form_data['body'] = $result;
}

else if($part == 'add_docs'){
        $tbl = trim(strval($_POST['tbl']));
        $pid = trim(strval($_POST['pid']));
        $fnm = _dbbase::shrink_filename(trim(strval($_POST['fnm'])), 50);
        $nm  = mb_substr(trim(strval($_POST['nm'])), 0, 50);
        $flg = intval($_POST['doc_flg']);
        $rdat = strval($_POST['rdat']);
        
        $result = $db_type->docs_addFile($tbl, $pid, $fnm, $nm, $flg, $rdat);
        
        if (strlen($result) > 0) {
            $form_data['pid'] = $pid;
            $form_data['docs_rid'] = $result;
            $form_data['success'] = true;
        }            
}

else if($part == 'delete_doc'){
    $rid = strval($_POST['rid']);
    
    $result = $db_type->delete_doc_by_rid($rid);
    
    if($result) $form_data['success'] = true;
}

else if($part == 'file_put_tmp'){
        $rid = trim(strval($_POST['val']));

        $result = $db_type->get_document($rid);

        if (count($result) > 0) {
            $fname = _assbase::dataUri2tmpFile($_SERVER['DOCUMENT_ROOT'] . assist::siteRootDir() . '/tmp', $result['fnm'], $result['rdat']);

            if (mb_strlen($fname) > 0) {
                $form_data['frelname'] = assist::siteRootDir() . '/tmp/' . $result['fnm'];
                $form_data['success'] = true;
            }
        }
}

else if ($part == 'remove_tmp_file') {
    $jg = new _jgcomm();
    $jg->jg_remove_tmp_file($form_data);   // $form_data pass by reference
    unset($jg);
    
    /* RESULT
        --SUCCESS:
        $form_data['success'] = true;
        --ERROR:
        $form_data['success'] = false;
    */
}

//var_dump(_dbbase::get_currentClientIP());
unset($assist);
unset($db_depo);
unset($db_type);

//Return the data back
echo json_encode($form_data);
?>
