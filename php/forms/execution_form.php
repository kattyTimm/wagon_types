        <div class="modal fade" id="execution_form" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" ondrop="return false;" ondragover="return false;"
              data-backdrop="static" data-keyboard="false" data-rid="" data-flg="" data-mdl-rid="">
            <!--data-backdrop="static" data-keyboard="false" эти два атрибута не позволять форме свернуться принажатии вне event target -->
            <div class="modal-dialog modal-lg">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_clnf_ttl" class="y-dgray-text">Исполнение.<small><i id="fm_type_ttl_add" class="y-dgray-text"></i></small></h4>
                                <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz15">&times;</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form onsubmit="return false"> <!--role="form" autocomplete="off" --->

                            <div class="form-row">   
                                 <div class="col-12" id="load_last_exe_btn_tmp">
                                    {load_last_exe_button}
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-6">
                                    <label for="nm">Наименование</label>
                                    <input id="nm" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-6">
                                    <label for="factory">Завод-изготовитель</label>
                                    <input id="factory" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>      
                            
                            <div class="form-row">   
                                 <div class="col-6">
                                    <label for="country">Страна производства</label>
                                    <input id="country" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-6">
                                    <label for="spec_n">Специализация</label>
                                    <input id="spec_n" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>      
                            
                            <div class="form-row">   
                                 <div class="col-4">
                                    <!--designation - Обозначение --> 
                                    <label for="desigDraw_techCon">Обозначение (номер) чертежа</label>
                                    <input id="desigDraw_techCon" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4">
                                    <label for="comm_draw">Чертеж общего вида с вариантом окраски</label>
                                    <input id="comm_draw" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="w_base">База вагона</label>
                                    <input id="w_base" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>                               
                            </div>  
                            
                            <div class="form-row">
                                <div class="col-3">
                                    <label for="w_length">Длина вагона по осям автосцепок</label>
                                    <input id="w_length" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                
                                 <div class="col-3" style="margin-top:22px;">
                                    <!--bodywork - кузовостроение --> 
                                    <label for="b_length">Длина кузова</label>
                                    <input id="b_length" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-3" style="margin-top:22px;">
                                    <label for="b_width">Ширина кузова</label>
                                    <input id="b_width" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-3">
                                    <label for="b_height">Высота кузова от рельса до оси автосцепки</label>
                                    <input id="b_height" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4" style="margin-top:25px;">
                                    <!--bodywork - кузовостроение --> 
                                    <label for="composition">Составность</label>
                                    <input id="composition" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="layout">Планировка с размерами и наименованиями помещений</label>
                                    <input id="layout" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4">
                                    <!--coupling device сцепное устройство-->
                                    <label for="type_coup_dev">Тип сцепного устройства, поглащающего аппарата</label>
                                    <input id="type_coup_dev" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4">
                                    <!--cart - тележка --> 
                                    <label for="tM_cart">Тип, модель тележек</label>
                                    <input id="tM_cart" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="weight_tare">масса тары</label>
                                    <input id="weight_tare" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4">                                
                                    <label for="payload">Грузоподъёмность</label>
                                    <input id="payload" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4" style="margin-top:25px;">
                                    <label for="size_bC">Габарит кузова, тележек, очертание</label>
                                    <input id="size_bC" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="quan_sit_plcs">Количество мест (для пассажиров проводников для сидения)</label>
                                    <input id="quan_sit_plcs" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4" style="margin-top:25px;">                                
                                    <label for="constr_speed">Конструкционная скорость</label>
                                    <input id="constr_speed" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4">
                                    <label for="existType_EHTK">Наличие, тип ЭЧТК, емкость</label>
                                    <input id="existType_EHTK" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="type_brake">Тип тормоза</label>
                                    <input id="type_brake" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4">                                
                                    <label for="type_trans_dev">Тип переходного устройства</label>
                                    <input id="type_trans_dev" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4" style="margin-top:25px;">
                                    <label for="type_B_dev">Тип буксовых узлов</label>
                                    <input id="type_B_dev" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="systemElectr">Система электроснабжения (номер схемы, характеристика)</label>
                                    <input id="systemElectr" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4" style="margin-top:25px;">                                
                                    <label for="type_gen">Тип генератора</label>
                                    <input id="type_gen" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4">
                                    <label for="DGY">Наличие ДГУ</label>
                                    <input id="DGY" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="syfle">Суфле</label>
                                    <input id="syfle" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4">                                
                                    <label for="video">Система видеонаблюдения</label>
                                    <input id="video" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4" style="margin-top:25px;">
                                    <label for="type_drive">Тип привода</label>
                                    <input id="type_drive" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="vent_cond">Система вентиляции, кондиционирования воздуха</label>
                                    <input id="vent_cond" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4" style="margin-top:25px;">                                
                                    <label for="air_decon">Наличие обеззараживателя воздуха</label>
                                    <input id="air_decon" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4" style="margin-top:25px;">
                                    <label for="aqua_decon">Наличие обеззараживателя воды</label>
                                    <input id="aqua_decon" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="F_MV_B_C">Наличие холодильника, МВ печи, кипятильника, охладителя питьевой воды</label>
                                    <input id="F_MV_B_C" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4" style="margin-top:25px;">                                
                                    <label for="BRISS">Наличие БРИСС</label>
                                    <input id="BRISS" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                             <div class="form-row">   
                                 <div class="col-6">
                                     <!--heating  - отопление -->
                                    <label for="sys_heat">Cистема отопления, наличие системы жидкостного отопления</label>
                                    <input id="sys_heat" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-6">
                                    <label for="cert">Сертификат соответствия (при наличии)</label>
                                    <input id="cert" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <div class="form-row">   
                                 <div class="col-4" style="margin-top:25px;">
                                    <label for="serv_life">Назначенный срок службы</label>
                                    <input id="serv_life" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col-4">
                                    <label for="runs">Mежремонтные периоды (сроки) и пробеги в соответствии с КД</label>
                                    <input id="runs" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col-4" style="margin-top:25px;">                                
                                    <label for="prod_ys">Год начала производства</label>
                                    <input id="prod_ys" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                        </form>
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="send_exe_ok" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>