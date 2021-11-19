<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        if (strlen(trim(session_id())) == 0) session_start();
        ?>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">

        <title>ЦБ ППС Оператор</title>

        <!-- Favicon: https://stackoverflow.com/questions/30824294/mobile-favicons -->
        <link rel="apple-touch-icon" sizes="128x128" href="img/favicon/train_128.png"> <!--railwaycarriage_128-->
        <link rel="icon" type="image/png" href="img/favicon/train_64.png" sizes="48x48">
        <link rel="shortcut icon" href="img/favicon/train_64.ico">
        <meta name="msapplication-TileImage" content="img/favicon/train_128.png">
        <meta name="msapplication-TileColor" content="#FFFFFF">
        
        <link href="/site_lib/lib/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        
        <link href="/pktbbase/css/bootstrap.min.css" rel="stylesheet">
        <link href="/pktbbase/css/mprogress-gr.css" rel="stylesheet">
        <link href="/pktbbase/css/colorbox.css" rel="stylesheet"/>

        <link href="/pktbbase/css/_indx_root.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_comm.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_drag.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_srch.css" rel="stylesheet"/> 
        <link href="../orgcomm/css/bootstrap-datepicker3.css" rel="stylesheet">

        <link href="css/index.css" rel="stylesheet">
    </head>
    <body spellcheck="false" ondrop='return false;' ondragover='return false;'>
        
        <?php
        include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
        include_once 'php/assist.php';
        
        $current_browser = get_browser(null, true);
        if (strcasecmp($current_browser['browser'], 'ie') == 0 && intval($current_browser['majorver']) < 10) {
            echo "<div class='text-center align-middle' style='padding:20px;color:white;background:red;line-height:40px;'>Версии браузера Internet Explorer ниже 10 не поддерживаются!</div>";
            exit();
        }
        ?>

        <nav id="navbar_wrap" class="navbar navbar-expand navbar-light y-fixtop" style="background-color: rgba(82, 179, 217, 0.2);">  <!-- fixed-top -->
			<!--
            <div>
                <span id="favicon">
                   <img src="img/favicon/train_128.png" width="92" hieght="92">
                </span> &emsp;
                <span id="main_title" class="navbar-brand "><b>ЦБ ППС</b> <i class="y-steel-blue-text">Оператор</i></span> 
              
            </div>
			-->

            <span id="main_title" class="navbar-brand d-inline-block">
               <img src="/wagon_types/img/favicon/train_56.png" width="auto" height="36" style="margin-bottom:3px;">
			   &nbsp;
	       <b>ЦБ ППС</b> <i class="y-steel-blue-text y-ver">Оператор</i>
	    </span>
            
            <div class="input-group-k">
              <!--  <div id="tmp_filter_items"></div>
                        <img src="img/filter_128.png" width="24" hieght="20" title="Фильтр по типу подвижного состава" > onclick="show_filter_by_types(this);" -->

                <div id="search_tmp">
                    <input id="srch_box" type="text" placeholder="Поиск <по модели>" ondrop="return false;" ondragover="return false;" class="form-control">
                </div>
            </div>
        </nav>
        
        <div class="content-wrapper">
            <div id="content_lists">   <!-- class="y-flex-column-nowrap" -->
                <div id="content_types" class="card flt-card flt-card-rw y-shad">
                    <div id="content_types_head" class="card-header k-bg-header"> <!-- y-steel-blue-text -->
                        <div id="tmp_filter_h6">
                            <div id="tmp_filter_items"></div>
                            <h6><b>Типы подвижного состава</b></h6> 
                        </div>
                        <img id="new_type" onclick="add_new_type(this);" class="y-cur-point" data-toggle='tooltip' title='Добавить тип' data-delay='100' src="img/add_512.png" width="32" hieght="32">  <!-- /pktbbase/img/add_3_32.png" -->
                    </div>
                    <div id="content_types_body"></div>    	<!-- y-flex-column-nowrap  -->
                </div>
                <div id="content_mdls" class="card flt-card flt-card-rw y-shad">
                    <div id="content_mdls_head" class="card-header k-bg-header">   
                      
                            <h6 id="header_mdl_p"><b>Модели</b></h6>
                            <img id="new_mdl" onclick="add_new_mdl(this);" class="y-cur-point" data-toggle='tooltip' title='Добавить модель' data-delay='100' src="img/add_512.png" width="32" hieght="32">
                       
                    </div>
                    <div id="content_mdls_body"></div>
                 
                </div>               
            </div>  
			
			    <!-- class="y-flex-column-nowrap noselect" Катя с этими классами будь аккуратна!!! в ИЕ они могут некорректно работать-->
            
            <div id="executions_block"></div>
            
            <div id="content_info" class="d-none">               <!--noselect-->
                <div id="exe_info"></div>
                <div id="docs"></div>
            </div>
        </div>

	<div id="pagination"></div>
  <!--     
        <div id="sideout" class="noselect">
            <div id="sideout_title">
                <span id="sideout_who" class="d-inline-block y-pad-lr10">&nbsp;</span>  
                <div id="mm_inout_block" class="y-flex-column-nowrap y-cur-point ml-auto" onclick="mm_inout_click();">
                    <div><img id="mm_inout_btn" src=""></div>
                    <small id="mm_inout_ttla" class="d-inline-block y-fz07">&nbsp;</small>
                    <small id="mm_inout_ttlb" class="d-inline-block y-fz07">&nbsp;</small>
                </div>
              
            </div>
            <div id="sideout_body" class="p-0 position-relative" style="overflow-y:auto;">
                <div id="mm_main_grp">

                </div>
                <div class="sideout-strike back"><span>&sdot; Быстрые настройки &sdot;</span></div>
                <div id="mm_qck_settings" class="y-pad-lr20">
                </div>
                <div id="mm_opts_grp">
                    <div class="sideout-strike"><span>&bull; &bull; &bull;</span></div>
                    <a id="mm_opts_cswd" class="sideout-menuitem y-a-flat d-none" href="javascript:;" onclick="mm_opts_cswd_click();"><img src='/nxcomm/img/swd_chg_32.png'>&nbsp; Изменение пароля</a>
                </div>
            </div>
            <div id="sideout_footer">
            </div>
        </div>


        
        <div id="fst" class="noselect">
            <div id="fst_body" class="noselect">
            </div>
            <div id='fst_footer' class='y-flex-row-nowrap align-items-center'>
                <div id='current_fsfo' class='y-pad-lr10 y-fz10 y-gray-text'></div>
            </div>
        </div>
     -->   
        <div id="foot_fst"><div id="fst_show" class="d-none p-0"></div></div>

        
        <footer class="sticky-footer y-flex-row-nowrap justify-content-between align-items-center bg-dark">
            <div id='foot_left_section'>
                <span id='foot_left_info' class="y-gray-text">
                    <span id='foot_partname' class='d-inline-block'><!--<i class="far fa-window-restore" style="color:#A6A653;"></i> -->&nbsp;
                        <span id="type_partname" data-app="type" class="y-steel-blue-text">
                            <img src="img/train_white_right_512_2.png" width="32" height="32">
                        </span> <!--Оператор-->           
                    </span> 
                    <span id="nx_clnf_fio" class="y-lgray-text"></span>                       
                </span>
            </div>
            <div id='foot_client_info' class="y-lgray-text y-fz08"></div>
            <div id='foot_right'>
               
                <span class="y-whitegray-text foot-copyright d-inline-block"><span id='foot_copyright_str' class="y-steel-blue-text"> &nbsp;<?php echo assist::$copyright_str; ?> </span>
                    <span id='foot_copyright_float'></span>
                </span>
                
            </div>
        </footer>
        
        
        <div class="y-ajax-wait"></div>   <!-- Waiting cursor -->
        
        <!--<img src='/nxcomm/img/control/win8_24.gif' class="y-dsrc-wait d-none">-->

        <div id="div_tmp"></div>
        <div id="div_tmpx"></div>
        
        <a id='a_download' href='javascript:;' class='d-none' download></a>
        
        <div id="pktb_appid" class="d-none" data-appnm="wagon_types" data-applvl="O" data-oneid="151" data-helpid="151"><?php echo _assbase::app_id('wagon_types');?></div> 
        <!--_assbase include via assist-->
        
        <?php _assbase::checkSttv('wagon_types'); ?>        
        
        <script src="/pktbbase/js/jquery.min.js"></script>
        <script src="/pktbbase/js/bootstrap.bundle.min.js"></script>

        <script src="/pktbbase/js/purl.min.js"></script>
        <script src="/pktbbase/js/jquery.noty.packaged.min.js"></script>
        <script src="/pktbbase/js/mprogress.min.js"></script>
        <script src="/pktbbase/js/bootstrap4-toggle.min.js"></script>
      
        <script src="/pktbbase/js/md5.min.js"></script>
        <script src="/pktbbase/js/jquery.colorbox-min.js"></script>
  
        <script src="/pktbbase/js/bootstrap-datepicker.min.js"></script>
        <script src="/pktbbase/js/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
        <script src="/pktbbase/js/jquery.touchSwipe.min.js"></script>

        <script src="/pktbbase/js/jquery.autocomplete.corrected-me.min.js"></script>
        
        <script src="../orgcomm/js/bootstrap-datepicker.min.js"></script>
        <script src="../orgcomm/js/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
       
        <script src="js/index.js"></script>
    </body>
</html>
