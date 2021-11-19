<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php'; 
include_once 'wagon_types_db.php';

class assist {
    public static $copyright_str = '&copy;ПКТБ Л, 2019&thinsp;&hellip;&thinsp;2021';
    
    public function __construct() {
        if (strlen(trim(session_id())) == 0)
            session_start();
    }    
    
    public static function siteRootDir() : string {    // site root must have index.php. directory will return with starting slash, ex: /IcmrM
        return _assbase::siteRootDir_($_SERVER['PHP_SELF']);
    }
    
    public function make_pagination(string $pg_id, int $offset, int $rows, int $currpage, int $totalrows) : string {
        $ass = new _assbase();
        $result = $ass->make_pagination($pg_id, $offset, $rows, $currpage, $totalrows);
        unset($ass);
        
        return $result;
    }
       
    public function get_fm(string $fm_id, $sparam = '') : string {
        $db = new wagon_types_db();
        $result = '';
        
        $path = 'forms/' .$fm_id. '.php';
        
        if(file_exists($path)){
            $form = file_get_contents($path);
            
            if($fm_id == 'fm_add_or_edit_mdl'){
               
                $types_list = $db->typesList_Whole();
                              
                $opt_list = "<option style='color:#C9C9C9;' value=''>Выбор типа...</option>".
                            "<option value='1'>Не определено</option>";
                
                foreach($types_list as $row)
                    $opt_list .= "<option value='" . $row['rid'] . "'>" . $row['nm'] . "</option>";                
                
                $form = str_replace('{types:list}', $opt_list, $form);
                
            }else if($fm_id == 'execution_form'){

                $is_exe_exists = $db->is_exe_exists_for_actual_mdl($sparam);
                
                $btn = '<button id="load_exe_data" class="btn btn-secondary y-shad" onclick="load_last_exe();">Загрузить данные последнего исполнения</button>';
                
                if($is_exe_exists)
                   $form = str_replace('{load_last_exe_button}', $btn, $form);
                else $form = str_replace('{load_last_exe_button}', '', $form);
            }
            else if($fm_id == 'select_mdls_by_type'){
                $types_all = $db->typesList_Whole();

                if(count($types_all) > 0)
                    foreach($types_all as $row)
                      $types_str .= '<div class="form-row">' .
                                       '<div class="custom-control custom-radio custom-control-inline">' .                                     
                                            '<input type="radio" class="custom-control-input" id="'.$row['rid'].'" onclick="get_mdls_4_type(this);" name="type_radio_select">'.
                                            '<label class="custom-control-label y-pad-t2" for="'.$row['rid'].'">'.$row['nm'].'</label>'.    
                                       '</div>'.
                                    '</div>'; 
                        
                        $form = str_replace('{items}', $types_str, $form);
            }
            
            else if ($fm_id == 'select_mdls_by_year'){
                $years = $db->get_prod_ys();
                $options_list = '';
                
                if(count($years) > 0)
                   foreach($years as $y) 
                      $options_list .= "<option value='" . $y['prod_ys'] . "'>" . $y['prod_ys'] . "</option>" ;
                
                $form = str_replace('{years_choose}', $options_list, $form);
                
            }
            
            else if($fm_id == 'select_mdls_by_country'){
                $countries = $db->get_countries();
                
                $options_list = '';
                
                if(count($countries) > 0)
                   foreach($countries as $c) 
                      $options_list .= "<option value='" . $c['country'] . "'>" . $c['country'] . "</option>" ;
                
                $form = str_replace('{types:countries_list}', $options_list, $form);
            }
            
            else if($fm_id == 'select_mdls_by_factory'){
                $factories = $db->get_factories();
                
                $options_list = '';
                
                if(count($factories) > 0)
                   foreach($factories as $f) 
                      $options_list .= "<option value='" . $f['factory'] . "'>" . $f['factory'] . "</option>" ;
                
                $form = str_replace('{factories_list}', $options_list, $form);
            }
            
            else if ($fm_id == 'select_mdls_by_multi'){
                $countries = $db->get_countries();
                $years = $db->get_prod_ys();
                $factories = $db->get_factories();
                
                $ys_list = "<option value=''></option>";
                
                if(count($years) > 0)
                   foreach($years as $y) 
                      $ys_list .= "<option value='" . $y['prod_ys'] . "'>" . $y['prod_ys'] . "</option>" ;
                
                $form = str_replace('{years_choose}', $ys_list, $form);
                
                $countries_list = "<option value=''></option>";
                
                if(count($countries) > 0)
                   foreach($countries as $c) 
                      $countries_list .= "<option value='" . $c['country'] . "'>" . $c['country'] . "</option>" ;
                
                $form = str_replace('{types:countries_list}', $countries_list, $form);
                
                $factories_list = "<option value=''></option>";
                
                if(count($factories) > 0)
                   foreach($factories as $f) 
                      $factories_list .= "<option value='" . $f['factory'] . "'>" . $f['factory'] . "</option>" ;
                
                $form = str_replace('{factories_list}', $factories_list, $form);
            }
            
            $result = $form;
        } else{
               $assbase = new _assbase();
                $result = $assbase->get_fm($fm_id, $sparam);
                unset($ass);
        }    
        unset($db);
        return $result;
    }
    
    public function show_types(){
        $db = new wagon_types_db();
        $types_list = $db->typesList_Whole();
        unset($db);
        
        $result = "<div id='types_list'>" .
                        "<table id='ta_type' class='table table-hover table-colored table-striped-alt m-0 y-border-no-t'>" .
                            "<thead class='h-0'>" .
                                "<tr class='y-border-no'>" .
                                    "<th class='y-border-no m-0 p-0'></th>" .
                                "</tr>" .
                            "</thead>" .
                            "<tbody class='y-border-no-t'>".
                                "{REPLASE_THIS}".   
                            "</tbody>" .
                        "</table>" .
                    "</div>";
        
        $rowset = '';
 
        foreach($types_list as $row){
            
             $act_edit   = "<a id='a_edit_type-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='type_edit_click(this);' data-toggle='tooltip' title='Редактировать' data-delay='100'>" .
                              "<img src='/pktbbase/img/edit_32.png'>" . 
                          "</a>";
          
             $act_delete = "<a id='a_delete_type-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='delete_type_click(this);' data-toggle='tooltip' title='Удалить' data-delay='100'>" .
                              "<img src='/pktbbase/img/delete_32.png'>" . 
                          "</a>";
            
               $rowset .= "<tr class='y-border-no' id='tr_type-".$row['rid']."' onclick='type_click(this);'>" . //  data-flg='".$row['flg']."'
                               "<td id='type_rid-".$row['rid']."' class='flt-item h-100 align-middle y-border-no-t y-cur-point y-row-bborder position-relative' style='padding:5px 5px;'>" . //onclick='get_mdls_4_type(this);'
                                   $row['nm'] .
                       
                                    "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                        "<div class='acts-inner' style='height:auto;width:auto;'>". $act_edit  . $act_delete  . "</div>" .  
                                    "</div>" .
                               "</td>" .
                          "</tr>";
        }
        $result = str_replace("{REPLASE_THIS}", $rowset, $result); 
        
        return $result;
    }
    
    
    function show_mdls($offset, $rows, $currpage, $rid_type = '', $from = '', $to = '', $country = '', $factory = ''){
        $mdls_list = [];
        $result = [];
        
        $db = new wagon_types_db();
        $types_all = $db->typesList_Whole();
        $totalrows = $db->mdls_getRowcount($rid_type, $from, $to, $country, $factory);      
        
        if ($offset >= 0 && $offset < $totalrows && $rows > 0 && $rows < $totalrows){
            $mdls_list = $db->mdlsList_Subset($offset, $rows, $rid_type);
            
            if(strlen($from) > 0 || strlen($to) > 0 || mb_strlen($country) > 0 || mb_strlen($factory) > 0)
                $mdls_list = $db->mdlsList_Subset_by_multi($offset, $rows, $country, $factory, $from, $to);
            
          /*  if((strlen($from) > 0 && strlen($to) > 0)){ // || mb_strlen($country) > 0 || mb_strlen($factory) > 0
                // здесь как раз по нескольким параметрам(ноая функция)
                $mdls_list = $db->mdlsList_Subset_by_years($offset, $rows, $from, $to);
            }
            
            else if(mb_strlen($country) > 0){
                $mdls_list = $db->mdlsList_Subset_by_country($offset, $rows, $country);
            }
            
            else if(mb_strlen($factory) > 0){
                $mdls_list = $db->mdlsList_Subset_by_factory($offset, $rows, $factory);
            } 
          
          */
        }
        else {
            $mdls_list = $db->mdlsList_Whole($rid_type);
            
            if(strlen($from) > 0 || strlen($to) > 0 || mb_strlen($country) > 0 || mb_strlen($factory) > 0)
                $mdls_list = $db->mdlsList_whole_by_multi($country, $factory, $from, $to);
            
            /*
            if((strlen($from) > 0 && strlen($to) > 0)){                 
               $mdls_list = $db->mdlsList_Whole_by_years($from, $to);
            }    
            
            else if(mb_strlen($country) > 0){
                $mdls_list = $db->mdlsList_Whole_by_country($country);
            }
            
            else if(mb_strlen($factory) > 0){
                $mdls_list = $db->mdlsList_Whole_by_factory($factory);
            }
            */
       
        }
        // $totalrows - всегда равно количеству записей в таблице
        if($totalrows > 0 ) $result['pagination'] = $this->make_pagination('index', $offset, $rows, $currpage, $totalrows);

        $result['filter'] = '<div id="filter_tmp" class="dropdown">'. 
                                '<img src="img/filter_512.png" id="show_filter"  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" width="24" hieght="30" title="Фильтр">'.                
                                
                              // '<i class="fas fa-filter" id="show_filter" style="color:SlateBlue4;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Фильтр по типу подвижного состава"></i>'.
                                '<div>'.
                                    //'<i class="fas fa-times-circle" title="Сбросить фильтр" onclick="drop_filter();" style="color:Salmon;"></i>'.
                                '</div>' .
                                '<div id="filter" class="dropdown-menu" aria-labelledby="dropdownMenuButton">'.
                                     //'{items}'.
                                     '<a  class="dropdown-item" onclick="get_form_multi(this);">По параметрам</a>'.
                                  //   '<a  class="dropdown-item" onclick="get_form_year(this);">По начальному году производства</a>'.
                                  //   '<a  class="dropdown-item" onclick="get_form_country(this);">По стране производства</a>'.
                               //      '<a  class="dropdown-item" onclick="get_form_factory(this);">По заводу-изготовителю</a>'. 
                                     '<hr style="margin:0;">' . 
                                     '<a class="dropdown-item" onclick="drop_filter();">Все модели (сбросить фильтр)</a>' .  
                                '</div>'.
                                
                           '</div>';
        
                                /*                
        $result['filter'] = str_replace('{items}', $types_str, $result['filter']); */
        
        $result['tbl'] = "<div id='mdls_list'>" .
                        "<table id='ta_mdls' class='table table-hover table-colored table-striped-alt m-0 y-border-no-t'>" .
                            "<thead class='h-0'>" .
                                "<tr class='y-border-no'>" .
                                    "<th class='y-border-no m-0 p-0'></th>" .
                                "</tr>" .
                            "</thead>" .
                            "<tbody class='y-border-no-t'>".
                                "{REPLASE_THIS}".
                            "</tbody>" . 
                        "</table>" .
                    "</div>";
        
        $rows = '';
        
        if(count($mdls_list) > 0){
            foreach($mdls_list as $row){
             //    $type_record = $db->type_record_by_self_rid($row['rid_type']);
            //     $type_nm = $type_record['rid'] == '' ? 'Не определено' : $type_record['nm'];

                 $act_edit   = "<a id='a_edit_mdl-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='mdl_edit_click(this);' data-toggle='tooltip' title='Редактировать' data-delay='100'>" .
                                  "<img src='/pktbbase/img/edit_32.png'>" . 
                              "</a>";

                 $act_delete = "<a id='a_delete_mdl-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='delete_mdl_click(this);' data-toggle='tooltip' title='Удалить' data-delay='100'>" .
                                  "<img src='/pktbbase/img/delete_32.png'>" . 
                              "</a>";

                $rows .= "<tr id='tr_mdls-".$row['rid']."' data-type-rid='".$row['rid_type']."' onclick='mdl_click(this);'>" .  //data-flg='".$row['flg']."' - пока не пользуюсь полем flg

                            "<td id='mdl_rid-".$row['rid']."' class='flt-item h-100 align-middle y-border-no-t y-cur-point y-row-bborder position-relative' style='padding:5px 5px;'>" .
                                 "<span id='mdl_nm_rid-".$row['rid']."'>".$row['nm']. "</span>" .     
                                 "<span class='y-lgray-text y-mrg-lr10'>" .
                                //   "&lt;<span>" .  $type_nm . "</span>&gt;" .
                                "</span>" .           
                                "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                    "<div class='acts-inner' style='height:auto;width:auto;'>". $act_edit  . $act_delete  . "</div>" . 
                                "</div>" . 
                            "</td>" .
                         "</tr>";
            }
            
            $result['tbl'] = str_replace("{REPLASE_THIS}", $rows, $result['tbl']);
        } 
        else $result['tbl'] = '';
        
        unset($db);
       //$result['tbl'] = str_replace("{REPLASE_THIS}", $rows, $result['tbl']);
        return $result;
    }
    
    public function show_execution($mdl_rid){
          
          $db = new wagon_types_db();
          $records = $db->get_executions_list($mdl_rid);
          $rowset = '';
          unset($db);
          $i = 0;
          
          if(count($records) > 0){
              foreach($records as $row){
				if ($i > 0)
					$rowset .= "<div class='text-center m-0 p-0' style='color:silver;font-size:0.7rem;'>&bull;</div>";
				  
                $rowset .=  "<div id='exe_rid-".strval($row['rid'])."' class='k-pad-b10 y-flex-row-nowrap align-items-center position-relative ".($i%2 != 0 ? 'k-bg' : '')."' onclick='execution_click(this);' " .
				 
								" style='padding-top:5px;'" .
                         
                              "data-flg='".$row['flg']."' data-factory='".$row['factory']."' data-country='".$row['country']."' ".
                              "data-spec='".$row['spec_n']."' data-desigDraw-techCon='".$row['desigDraw_techCon']."' ".
                              "data-comm-draw='".$row['comm_draw']."' data-w-base='".$row['w_base']."' data-w-length='".$row['w_length']."' " .
                              "data-b-length='".$row['b_length']."' data-b-width='".$row['b_width']."' data-b-height='".$row['b_height']."' ". 
                              "data-composition='".$row['composition']."' data-layout='".$row['layout']."' data-type-coupDev='".$row['type_coup_dev']."' ".
                              "data-tM-cart='".$row['tM_cart']."' data-weight-tare='".$row['weight_tare']."' data-payload='".$row['payload']."' " .
                              "data-size-bC='".$row['size_bC']."' data-quan-plcs='".$row['quan_sit_plcs']."' data-speed='".$row['constr_speed']."' ".
                              "data-EHTK='".$row['existType_EHTK']."' data-type-brake='".$row['type_brake']."' data-type-transDev='".$row['type_trans_dev']."' ".
                              "data-type_B_dev='".$row['type_B_dev']."' data-systemElectr='".$row['systemElectr']."' data-type_gen='".$row['type_gen']."' " .
                              "data-DGY='".$row['DGY']."' data-syfle='".$row['syfle']."' data-video='".$row['video']."' ".
                              "data-type_drive='".$row['type_drive']."' data-vent_cond='".$row['vent_cond']."' data-air_decon='".$row['air_decon']."' ". 
                              "data-aqua_decon='".$row['aqua_decon']."' data-F_MV_B_C='".$row['F_MV_B_C']."' data-BRISS='".$row['BRISS']."' " . 
                              "data-sys_heat='".$row['sys_heat']."' data-cert='".$row['cert']."' data-serv_life='".$row['serv_life']."' " . 
                              "data-runs='".$row['runs']."' data-prod_ys='".$row['prod_ys']."'>" .

                                    "<div id='exe_nm-".strval($row['rid'])."' class='y-lgray-text k-fz010 y-wdt-col-9 k-pad-lr-5'>" . strval($row['nm'])."</div>" .  
                                    "<div id='exe_editDelete_tmp-".$row['rid']."' class='ml-auto acts-panel position-absolute invisible' style='top:0;right:0;width:auto;'>" .
                                      //  "<img src='/pktbbase/img/edit_24.png' widht='20' height='20' onclick='edit_exe(this);' class='d-inline-block k-pad-lr-2' " .
										//"data-toggle='tooltip' title='Редактировать' data-delay='100'>".
                                        "<div class='acts-inner' style='height:auto;width:auto;'>" .  
                                            "<img id='delete_exe-".$row['rid']."' data-rid-mdl='".$mdl_rid."' src='/pktbbase/img/delete_24.png' widht='20' height='20' onclick='delete_exe_click(this);'" .
										" class='d-inline-block k-pad-lr-2' data-toggle='tooltip' title='Удалить' data-delay='100'>".
                                        "</div>". 
                                    "</div>".
                            "</div>";
                 
                 $i++;
              }
          }
          
            $result = "<div id='executions_list' class='card detail-card y-shad'>" . // y-mrg-b10  y-mrg-t10
                        "<div class='card-header k-bg-header' id='header_executions_list' >".
                            "<h6 style='padding-top:5px;'><b>Исполнения</b></h6>" . //class='y-steel-blue-text'
                            //"<img id='add_execution' onclick='on_add_execution(this);' src='img/add_512.png' width='32' height='32'>".
                            "<img id='add_execution' onclick='on_add_execution(this);' src='img/add_32.png' class='y-cur-point' data-toggle='tooltip' title='Добавить документ' data-delay='100'>".
                        "</div>" .                                        

                            "<div class='card-body p-0'>" .	// y-pad-tb5
                                //"<div class='y-flex-row-wrap file-panel'>". //  - не работает в ИЕ
                    
                                    "<div class='y-mrg-b10 y-wdt-col-12'>".      // y-pad-lr10                                                        
                                       "{executions}" .
                                    "</div>". 
                                                      
                                //"</div>".               
                            "</div>" .                                            
                    "</div>";
            
            $innerText = (strlen($rowset) > 0) ? $rowset : '';
            
            $result = str_replace("{executions}", $innerText, $result);
              
        return $result;
    }                                 
    
    public function show_execution_info($exe_rid){
        
        $db = new wagon_types_db();
        $records = $db->get_execution_record_by_self_rid($exe_rid); 
        
        $mdl_records = $db->mdl_record_by_self_rid($records['rid_mdl']);
        $mdl_nm = $mdl_records['nm'];
        
        $type_records = $db->type_record_by_self_rid($mdl_records['rid_type']);
        $type_nm = $type_records['nm'];
        unset($db);
        
        $rows = '';
        $rows_other = '';
        $name = (strlen($records['nm']) > 0) ? strval($records['nm']) : '';
        
        $td_class_l = "class='text-left align-middle y-border-no-t y-border-b y-gray-text y-fz08 position-relative k-td-title' "; //col-5
        $td_class_r = "class='text-left align-middle y-border-no-t y-border-b y-fz09 y-steel-blue-text k-td-val' ";
        
        $td_style_l = " style='padding:2px 10px 2px 20px;' ";
        $td_style_r = " style='padding:2px 10px;' ";
        
        $act_edit   = "<a id='a_edit_exe-" . $exe_rid . "' href='javascript:;' class='y-mrg-lr5' onclick='edit_exe(this);' data-toggle='tooltip' title='Редактировать' data-delay='100'>" .
                              "<img src='/pktbbase/img/edit_32.png'>" . 
                          "</a>";
        
        $result = "<div id='detail_exe' class='card detail-card y-shad y-mrg-t10 position-relative'>" . //card detail-card
                        "<div class='card-header' id='header_exe_info'>".
                            "<h6 class='y-dgray-text' style='padding-top:5px;'><b>".$name."</b></h6>" . 
                        "</div>".
                
                        "<div class='position-absolute text-center y-cur-point' style='top:10px;right:0;width:auto;'>" . // invisible
                            "<div style='height:auto;width:auto;'>". $act_edit . "</div>" . 
                        "</div>" . 
                   "</div>".
                
                
                    "<div id='main_info' class='card detail-card y-shad'>" .
                       "<div class='card-header y-align-items-center'>" . //y-flex-row-nowrap
                            "<div class='y-steel-blue-text'>Основная информация</div>" .
                        "</div>" .
                
                        "<div>" .                           
                              "<table class='table table-striped y-border-no m-0'>" .
                                "<thead class='h-0'>" .
                                    "<tr class='y-border-no'>" .
                                        "<th class='y-border-no m-0 p-0' colspan='2'></th>" .    //y-wdt-col-12                                 
                                    "</tr>" .
                                "</thead>" .
                
                                "<tbody class='y-border-no'>" .
                                    "<tr>" .
                                        "<td " . $td_class_l . $td_style_l . ">Тип -</td>" . //
                                        "<td " . $td_class_r . $td_style_r . ">" . $type_nm . "</td>" . //
                                    "</tr>".
        
                                    "<tr>" .
                                       "<td " . $td_class_l . $td_style_l . ">Модель - </td>" .
                                        "<td " . $td_class_r . $td_style_r . ">" . $mdl_nm . "</td>" .
                                    "</tr>" .
                                      
                                    "{exe:info}".  
                                "</tbody>" .
                              "</table>" .               
                            "</div>" .  
                
                        "</div>"  . // end of main_info

                 
                /*   "<div class='text-center y-mrg-b10 y-pad-lr20' style='color:silver;font-size:1.0rem;'>&bull;&nbsp;&bull;&nbsp;&bull;</div>" .  */
                
                 "<div id='other_info' class='card detail-card y-shad y-mrg-t10'>" . 
                
                    "<div class='card-header y-align-items-center'>" . //y-flex-row-nowrap
                        "<div class='y-steel-blue-text'>Прочая информация</div>" .
                    "</div>" .

                    "<div id='other_info_body'>" . 
                         "<table class='table table-striped y-border-no m-0'>" .
                           "<thead class='h-0'>" .
                                "<tr class='y-border-no'>" .
                                    "<th class='y-border-no m-0 p-0' colspan='2'></th>" .         //y-wdt-col-12                            
                                "</tr>" .
                            "</thead>" .

                          "<tbody class='y-border-no'>" .
                                "{exe:info_other}".  
                            "</tbody>" .
                          "</table>" .            
                                    
                    "</div>"  .
                
                 "</div>"; // end of other_info
    
        if(count($records) > 0){                     
            $rows .=  "<tr>" .
                          "<td " . $td_class_l . $td_style_l . ">Наименование -</td>" . //
                          "<td " . $td_class_r . $td_style_r . ">" . $name . "</td>" . //
                      "</tr>".

                      $this->get_exe_info_div($records['country'], "Страна производства -") .
                      $this->get_exe_info_div($records['factory'], "Завод-изготовитель -") .
                      $this->get_exe_info_div($records['prod_ys'], "Год начала производства -"); 
   
            $rows_other .=  $this->get_exe_info_div($records['spec_n'],  			"Специализация -") .
                            $this->get_exe_info_div($records['desigDraw_techCon'], 	"Обозначение (номер) чертежа -") .
                            $this->get_exe_info_div($records['comm_draw'], 			"Чертеж общего вида с вариантом окраски -").
                            $this->get_exe_info_div($records['w_base'], 			"База вагона -") .
                            $this->get_exe_info_div($records['w_length'], 			"Длина вагона по осям автосцепок -") .
                            $this->get_exe_info_div($records['b_length'], 			"Длина кузова -") .
                            $this->get_exe_info_div($records['b_width'], 			"Ширина кузова -") .
                            $this->get_exe_info_div($records['b_height'], 			"Высота кузова от рельса до оси автосцепки -") .
                            $this->get_exe_info_div($records['composition'], 		"Составность -") .
                            $this->get_exe_info_div($records['layout'], 			"Планировка с размерами и наименованиями помещений -") .
                            $this->get_exe_info_div($records['type_coup_dev'], 		"Тип сцепного устройства, поглощающего аппарата -") .
                            $this->get_exe_info_div($records['tM_cart'], 			"Тип, модель тележек -") .
                            $this->get_exe_info_div($records['weight_tare'], 		"Mасса тары -") .
                            $this->get_exe_info_div($records['payload'], 			"Грузоподъёмность -") .
                            $this->get_exe_info_div($records['size_bC'], 			"Габарит кузова, тележек, очертание -") .
                            $this->get_exe_info_div($records['quan_sit_plcs'], 		"Количество мест (для пассажиров проводников для сидения) -") .
                            $this->get_exe_info_div($records['constr_speed'], 		"Конструкционная скорость -") .
                            $this->get_exe_info_div($records['existType_EHTK'], 	"Наличие, тип ЭЧТК, емкость -") .
                            $this->get_exe_info_div($records['type_brake'], 		"Тип тормоза -") .
                            $this->get_exe_info_div($records['type_trans_dev'], 	"Тип переходного устройства -") .
                            $this->get_exe_info_div($records['type_B_dev'], 		"Тип буксовых узлов -") .
                            $this->get_exe_info_div($records['systemElectr'], 		"Система электроснабжения (номер схемы, характеристика) -") .
                            $this->get_exe_info_div($records['type_gen'], 			"Тип генератора -") .
                            $this->get_exe_info_div($records['DGY'], 				"Наличие ДГУ -") .
                            $this->get_exe_info_div($records['syfle'], 				"Суфле -") .
                            $this->get_exe_info_div($records['video'], 				"Система видеонаблюдения -") .
                            $this->get_exe_info_div($records['type_drive'], 		"Тип привода -") .
                            $this->get_exe_info_div($records['vent_cond'], 			"Система вентиляции, кондиционирования воздуха -") .
                            $this->get_exe_info_div($records['air_decon'], 			"Наличие обеззараживателя воздуха -") .
                            $this->get_exe_info_div($records['aqua_decon'], 		"Наличие обеззараживателя воды -") .
                            $this->get_exe_info_div($records['F_MV_B_C'], 			"Наличие холодильника, МВ печи, кипятильника, охладителя питьевой воды -") .
                            $this->get_exe_info_div($records['BRISS'], 				"Наличие БРИСС -") .
                            $this->get_exe_info_div($records['sys_heat'], 			"Cистема отопления, наличие системы жидкостного отопления -") .
                            $this->get_exe_info_div($records['cert'], 				"Сертификат соответствия (при наличии) -") .
                            $this->get_exe_info_div($records['serv_life'], 			"Назначенный срок службы -") .
                            $this->get_exe_info_div($records['runs'], 				"Mежремонтные периоды (сроки) и пробеги в соответствии с КД -");
        }
        
        $result = str_replace("{exe:info}", $rows, $result);

        $result = str_replace("{exe:info_other}", (strlen($rows_other) > 0 ? $rows_other : "<div id='no_other_info' class='text-center mt-10 p-0 y-steel-blue-text'>Данные отсутствуют</div>"), $result);          
        
        return $result;  
       
    }
	
    public function get_exe_info_div(string $field, string $title) : string {
        $td_class_l = "class='text-left align-middle y-border-no-t y-border-b y-gray-text y-fz08 position-relative k-td-title' ";
        $td_class_r = "class='text-left align-middle y-border-no-t y-border-b y-fz09 y-steel-blue-text k-td-val' ";
        
        $td_style_l = " style='padding:2px 10px 2px 20px;' ";
        $td_style_r = " style='padding:2px 10px;' ";
        
        return  (mb_strlen($field) > 0 && preg_match('#^-$#', $field) != 1 ?
                            "<tr>" .
                                "<td " . $td_class_l . $td_style_l . ">".$title."</td>" . //
                                "<td " . $td_class_r . $td_style_r . ">" . $field . "</td>" . //
                            "</tr>" : '');
    }
    
    public function receive_docs($pid){
       $db = new wagon_types_db();
       $records = $db->get_docs_by_pid($pid); 
       // $exe_record = $db->get_execution_record_by_self_rid();
        unset($db);  
        
        $panels = '';
           
        $result =  "<div id='docs_block' class='card detail-card y-shad y-mrg-t10' style='min-height:200px;'>" .
                    "<div class='card-header k-bg-header' id='header_docs'>".
                        "<h6 class='y-steel-blue-text' style='padding-top:5px;'><b>Документация</b></h6>" .
                        //"<img id='append_doc' onclick='add_doc(this);' src='img/add_512.png' width='32' height='32' data-pid='".$pid."'>".
                        "<img id='append_doc' onclick='add_doc(this);' src='img/add_32.png' data-pid='".$pid."' class='y-cur-point' data-toggle='tooltip' title='Добавить документ' data-delay='100'>".
                    "</div>".

                    "<div class='card-body y-pad-tb5'>" .
                        "<div class='file-panel'>". //y-flex-row-wrap 
                           "{cards}".
                        "</div>".               
                    "</div>" .
            "</div>";

        if(count($records) > 0){
      
            foreach($records as $row){

                $ftype = _assbase::getFtypeByFname($row['fnm']); 

                $img = $ftype == "unk" ? "" : "<img class='display-block' src='/pktbbase/img/file/" . $ftype . "_64.png'>";

                $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";

                $fnm = "<p class='y-gray-text' " . $fnm_ttip . ">" . _dbbase::shrink_filename($row['fnm'], 30) . "</p>"; 
                
                //substr(строка, откуда, [сколько]);
                //$str_footer = (mb_strlen($row['fnm']) > 7) ?  mb_substr($row['fnm'], 0, 10) . "." : $row['fnm'];
                
                $card_footer =  "<div class='card-footer y-flex-row-nowrap justify-content-between y-align-items-center p-0' style='height:2.5rem;'>" .
                                    "<div style='width:22px;'>&nbsp;</div>" .   // 22px - experimental for org show at footer center                      
                                    //"<div class='p-0 y-llgray-text'>".$str_footer."</div>" .
                                    //"<div>" .	// class='dropdown' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'
                                      //   "<img id='a_delete_doc-" . $row['rid'] . "' src='img/delete_15.png' onclick='delete_doc_click(this);' " .
                                        //    "data-fnm='".$row['fnm']."' data-pid='".$pid."' data-flg='".$row['flg']."' ".
                                          //  " style='margin-right:5px;' class='y-cur-point'>".                                        
                                    //"</div>" .
                                "</div>";
     
                $panels .=  "<div id='doc_rid-'".$row['rid']." class='card file-card y-shad-light' data-pid='".$pid."'>" . //style='min-width:190px;'
                                "<div class='card-body y-pad-tb5 y-pad-lr10 text-center y-cur-point' onclick='doc_view_click(this);' " .
									"data-doc='" .$row['rid']."'>" . 
                        
                                "<img id='a_delete_doc-" . $row['rid'] . "' src='img/delete_15.png' onclick='delete_doc_click(this);' " .
                                            "data-fnm='".$row['fnm']."' data-pid='".$pid."' data-flg='".$row['flg']."' ".
                                            " style='margin-right:5px;' class='y-cur-point'>".  
                                                   
                                   $img . $fnm .                                                 
                                "</div>" .
                             //   $card_footer .
                            "</div>";
            }            
        }
     
 /*        
 "<div class='dropdown-menu dropdown-menu-right y-pad-tb5'>" .
    "<a class='dropdown-item di-mailto y-fz-10' href='javascript:;'><i class='far fa-envelope'></i> &nbsp;<small>Переслать на email</small></a>" .
    "<a class='dropdown-item di-dload y-fz-10' href='javascript:;'><i class='fas fa-download'></i> &nbsp;<small>Скачать</small></a>" .
    "<div class='dropdown-divider'></div>" .
    "<a id='a_delete_doc-" . $row['rid'] . "' onclick='delete_doc_click(this);' class='dropdown-item di-view y-fz-10' href='javascript:;' ".
    "data-fnm='".$row['fnm']."' data-pid='".$mdl_list['rid']."' data-flg='".$row['flg']."'>" .
       "<i class='far fa-file-alt'></i>" . 
        " &nbsp;<small>удалить файл</small>".
    "</a>" .
"</div>" */
        
       $result = str_replace("{cards}", $panels, $result);
       return $result; 
    }
    
    
    
    
    
    /*
   
    public function get_vw_dsrc(string $clnf_id, string $orgs_id, string $sparam, int $iparam) : array {    // iparam - date as yyyy(16..31) mm(8..15) dd(0..7)
        $result = ["head" => ""];   // There no head in dsrc view
        
        $orgs_id_ = trim($orgs_id);

        if (strlen($clnf_id) > 0) { // authorized user
            $db = new _db();
            $clnf_rec = $db->clnf_getRec($clnf_id);
            unset($db);

             // clnf rec found       // is CL user?                    // for the date
            if (count($clnf_rec) > 0 && strlen($clnf_rec['orgs']) == 0 && $iparam != 0) {   // CL user found
                // file_cards is around of file-panel for make it 100% height of dsrc_content (minus dsrc_footer) and overflow-y auto.
                // if oferflow set for file-panel - dropdowns shows inside flex row
                $body = "<div id='file_cards'><div class='y-flex-row-wrap file-panel'>";   //flex-grow:1; (in css)

                $date_ = _db::dates_int2YYYYMMDD($iparam);
                
                $db = new _db();
                $files_lst = $db->dsrc_getFiles4YYYYMMDD($orgs_id_, $sparam, $date_);  // $sparam - current fldr
                if (strlen($orgs_id_) > 0)   // not CL
                    $footer_lst = $db->dsrc_getOrgFiles4CL($orgs_id_, $date_);
                else 
                    $footer_lst = $db->dsrc_getOrgsFiles4Self($date_);
                unset($db);
                
                $panels = "";

                if (strlen($sparam) > 0)    // add folder up panel
                    $panels .=  "<div class='card file-card card-fldr-out y-shad-light'>" .
                                    "<div class='card-body text-center'>" .
                                        "<img src='/nxcomm/img/ftype/folder_up_64.png'>" . 
                                        "<p class='card-text y-llgray-text'>" . $sparam . "</p>" .
                                    "</div>" .
                                    "<div class='card-footer p-0' style='min-height:2.5rem;'>&nbsp;</div>" .
                                "</div>";

                if (count($files_lst) > 0)
                    foreach($files_lst as $row) {
                        if (strlen($row['rid']) > 0) {
                            $card_classes = "card-dsrc";
                            $img_fname = strtolower(pathinfo($row['nm'])['extension']);
                            
                            $di_newtab = _assbase::isNativeFileType($row['nm']) && !_assbase::isMobileOrTablet() ? 
"<a class='dropdown-item di-newtab y-fz-10' href='javascript:;'><i class='fas fa-external-link-alt'></i> &nbsp;<small>Открыть в новой закладке</small></a>" : "";
                            
                            $ext = strtolower(pathinfo($row['nm'])['extension']);
        
                            $di_getlnk = $ext == 'xls' || $ext == 'xlsx' ?
"<a class='dropdown-item di-getlnk y-fz-10' href='javascript:;'><i class='fas fa-link'></i> &nbsp;<small>Получить адрес ссылки</small></a>" : "";
                            
                            $card_footer = //strlen($orgs_id) != 0 ? "" :
                                        "<div class='card-footer y-flex-row-nowrap justify-content-between y-align-items-center p-0' style='height:2.5rem;'>" .
                                            "<div style='width:22px;'>&nbsp;</div>" .   // 22px - experimental for org show at footer center
                                            "<div class='p-0 y-llgray-text'>" . $row['orgsnm'] . "</div>" .
                                            "<div class='dropdown' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" .
                                                "<button class='btn btn-sm btn-light y-lgray-text y-fz-12' style='margin:2px 0;'>&equiv;</button>" .
                                                "<div class='dropdown-menu dropdown-menu-right y-pad-tb5'>" .
                                                    "<a class='dropdown-item di-mailto y-fz-10' href='javascript:;'><i class='far fa-envelope'></i> &nbsp;<small>Переслать на email</small></a>" .
                                                    "<a class='dropdown-item di-dload y-fz-10' href='javascript:;'><i class='fas fa-download'></i> &nbsp;<small>Скачать</small></a>" .
                                                    $di_newtab .
                                                    $di_getlnk .
                                                    "<div class='dropdown-divider'></div>" .
                                                    "<a class='dropdown-item di-view y-fz-10' href='javascript:;'><i class='far fa-file-alt'></i> &nbsp;<small>Просмотр</small></a>" .
                                                "</div>" .
                                            "</div>" .
                                        "</div>";
                        }
                        else {
                            $card_classes = "card-fldr";
                            $card_menu    = "";
                            $img_fname = "folder";
                            
                            $card_footer = "<div class='card-footer p-0' style='min-height:2.5rem;'>&nbsp;</div>";
                        }
                        
                        $dtt_time = "<p class='m-0 mt-auto'><small class='y-gray-text'>" . substr($row['dtt'], 11) . "</small></p>";

                        $panels .=  "<div class='card file-card " . $card_classes . " y-shad-light' data-dsrc='" . $row['rid'] . "'>" .
                                        "<div class='card-body text-center y-pad-tb5 y-pad-lr10 y-flex-column-nowrap y-align-items-center'>" .
                                            "<img src='/nxcomm/img/ftype/" . $img_fname . "_64.png'>" .
                                            "<p class='card-text m-0'>" . pathinfo($row['nm'])['filename'] . "</p>" . //"<br><small class='y-gray-text'>" . 
                                                //substr($row['dtt'], 11) .
                                                //"</small></p>" .
                                                $dtt_time .
                                        "</div>" .
                                        $card_footer .
                                    "</div>";
                    }
                
                $footer = "";
                    
                if (count($footer_lst) > 0) {
                    $footer = "<div id='dsrc_footer' class='mt-auto y-flex-row-nowrap y-pad-t2 y-pad-b5' style='flex-shrink:0;'>";  // style='min-height:10px;'
                    
                    foreach ($footer_lst as $footer_row) {
                        $img_fname = strtolower(pathinfo($footer_row['fnm'])['extension']);

                        if (strlen($orgs_id_) > 0) {    // not CL
                            $pathinf = pathinfo($footer_row['fnm'])['filename'] . "</p>" .
                                                "<p class='m-0 mt-auto'><small class='y-gray-text'>" . substr($footer_row['dtt'], 11) . "</small></p>" .
                            
                            $sec_class = "";
                        }
                        else {
                            $pathinf = pathinfo($footer_row['fnm'])['filename'] . "</p>" .
                                       "<p class='m-0 mt-auto'><small>" . 
                                            "<span style='color:#DF7000;'>" . $footer_row['osnm'] . "</span>" .
                                            "<span class='y-gray-text'>&thinsp;&centerdot;&thinsp;" . substr($footer_row['dtt'], 11) .  "</span></small></p>";
                            
                            $sec_class = " cl-sec";
                        }
                        
                        $footer .=  "<div class='card file-card card-dsrc y-shad-light" . $sec_class . "' data-dsrc='" . $footer_row['rid'] . "'>" .
                                        "<div class='card-body text-center y-pad-tb5 y-pad-lr10 y-flex-column-nowrap y-align-items-center'>" .
                                            "<img src='/nxcomm/img/ftype/" . $img_fname . "_64.png' style='width:48px;height:48px;'>" .
                                            "<p class='card-text m-0'>" .   // End of </p> in the $pathinf declare
                                                $pathinf .
                                        "</div>" .
                                    "</div>";
                    }

                    $footer .= "</div>";
                }
                    
                if (mb_strlen($panels) == 0 && mb_strlen($footer) == 0)
$panels = "<small class='y-llgray-text text-center' style='position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);'>&lsaquo;Нет данных&rsaquo;</small>";
                    
                $body .= $panels . "</div></div>";    // .file-panel   #file-cards

                $result['body'] = $body . $footer;
            }
            else $result['body'] = "";
        }
        else $result['body'] = self::get_guest_btn();   // button

        return $result;
    }
*/
    
}
/*
$ass = new assist();
$db = new wagon_types_db();
$records = $db->get_execution_record_by_self_rid('37497db4-59b1-44a8-b098-e2fc17c67e50'); 

foreach($records as $r){
    if(preg_match('#^-{1}$#', $r) == 1){
         var_dump($r);
    }
}
      
*/

//echo $result;
// End of: assist


//$db->dsrc_getFiles4YYYYMMDD($orgs_id, $sparam, _db::dates_int2YYYYMMDD());

//echo _db::dates_int2YYYYMMDD(132320014) . '<br>';
//echo date('Ymd') . '<br>';



//$db = new wagon_types_db();
//var_dump( $db->mdls_getRowcount_with_years_table('1970', '2000'));

//$country = (mb_strlen(strval('Россия')) > 0) ? strval('Россия') : '';
//var_dump($country);








							/*
                            "<div class='y-mrg-b10 y-pad-lr20'>".   
                               (mb_strlen($records['country']) > 0 ?
                                 "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Страна производства -</b> </span>" .
                                 "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>".$records['country']."</span>" 
                               : '').
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".    
                              (mb_strlen($records['factory']) > 0 ?
                                 "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Завод-изготовитель -</b> </span>" .
                                 "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>".$records['factory']."</span>" 
                               : '').
                            "</div>" .

                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['prod_ys']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Год начала производства -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['prod_ys'].
                                  "</span>"   : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['spec_n']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Специализация -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['spec_n'].
                                  "</span>"  
                               : '').
                            "</div>" .
                          
                           "<div class='y-mrg-b10 y-pad-lr20'>".
                                (mb_strlen($records['desigDraw_techCon']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Обозначение (номер) чертежа -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['desigDraw_techCon'].
                                  "</span>" : ''). 
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>". 
                              (mb_strlen($records['comm_draw']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Чертеж общего вида с вариантом окраски -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz10 y-steel-blue-text y-fs-i'>" . $records['comm_draw'].
                                  "</span>"  : '').
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['w_base']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>База вагона -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['w_base'].
                                  "</span>"  : ''). 
                            "</div>" .
                          
                           "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['w_length']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Длина вагона по осям автосцепок -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['w_length'].
                                  "</span>"  : '').
                            "</div>" .
                          
                          "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['b_length']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Длина кузова -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['b_length'].
                                  "</span>"  : '').
                            "</div>" .
                          
                          "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['b_width']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Ширина кузова -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['b_width'].
                                  "</span>"  : '').
                            "</div>" .
                          
                          "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['b_height']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Высота кузова от рельса до оси автосцепки -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['b_height'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                          "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['composition']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Составность -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['composition'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['layout']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Планировка с размерами и наименованиями помещений -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['layout'].
                                  "</span>"  : '').   
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['type_coup_dev']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Тип сцепного устройства, поглощающего аппарата -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['type_coup_dev'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['tM_cart']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Тип, модель тележек -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['tM_cart'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['weight_tare']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Mасса тары -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['weight_tare'].
                                  "</span>"  : '').  
                            "</div>" .
                                    
                                    
                             "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['payload']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Грузоподъёмность -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['payload'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['size_bC']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Габарит кузова, тележек, очертание -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['size_bC'].
                                  "</span>"  : '').   
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['quan_sit_plcs']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Количество мест (для пассажиров проводников для сидения) -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['quan_sit_plcs'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['constr_speed']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Конструкционная скорость -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['constr_speed'].
                                  "</span>" : '').  
                            "</div>" .    
                                    
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['existType_EHTK']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Наличие, тип ЭЧТК, емкость -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['existType_EHTK'].
                                  "</span>"   : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['type_brake']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Тип тормоза -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['type_brake'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['type_trans_dev']) > 0 ?
                                   "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Тип переходного устройства -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['type_trans_dev'].
                                  "</span>"  : '').   
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['type_B_dev']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Тип буксовых узлов -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['type_B_dev'].
                                  "</span>"  : '').   
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['systemElectr']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Система электроснабжения (номер схемы, характеристика) -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['systemElectr'].
                                  "</span>"  : '').    
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['type_gen']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Тип генератора -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['type_gen'].
                                  "</span>"  : '').  
                            "</div>" .    
                                    
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['DGY']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Наличие ДГУ -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['DGY'].
                                  "</span>"   : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['syfle']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Суфле -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['syfle'].
                                  "</span>"   : '').    
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['video']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Система видеонаблюдения -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['video'].
                                  "</span>"   : '').  
                            "</div>"  .
                          
                          "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['type_drive']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Тип привода -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['type_drive'].
                                  "</span>"   : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>". 
                              (mb_strlen($records['vent_cond']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Система вентиляции, кондиционирования воздуха -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['vent_cond'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['air_decon']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Наличие обеззараживателя воздуха -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['air_decon'].
                                  "</span>"  : '').    
                            "</div>" .    
                                    
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['aqua_decon']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Наличие обеззараживателя воды -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['aqua_decon'].
                                  "</span>"  : '').   
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                                (mb_strlen($records['F_MV_B_C']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Наличие холодильника, МВ печи, кипятильника, охладителя питьевой воды -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['F_MV_B_C'].
                                  "</span>"  : ''). 
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['BRISS']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Наличие БРИСС -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['BRISS'].
                                  "</span>"  : '').  
                            "</div>" .  
                          
                              "<div class='y-mrg-b10 y-pad-lr20'>".  
                               (mb_strlen($records['sys_heat']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Cистема отопления, наличие системы жидкостного отопления -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['sys_heat'].
                                  "</span>"  : ''). 
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>". 
                              (mb_strlen($records['cert']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Сертификат соответствия (при наличии) -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['cert'].
                                  "</span>"  : '').
                            "</div>" .    
                                    
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                           (mb_strlen($records['serv_life']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Назначенный срок службы -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['serv_life'].
                                  "</span>"  : '').  
                            "</div>" .
                          
                            "<div class='y-mrg-b10 y-pad-lr20'>".  
                              (mb_strlen($records['runs']) > 0 ?
                                  "<span class='y-lgray-text y-fz10 y-wdt-col-4'><b>Mежремонтные периоды (сроки) и пробеги в соответствии с КД -</b> </span>" .
                                  "<span class='y-pad-lr5 y-wdt-col-8 y-fz11 y-steel-blue-text y-fs-i'>" . $records['runs'].
                                  "</span>"   : ''). 
                            "</div>" ;
							*/

?>

