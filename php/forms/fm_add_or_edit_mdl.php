        <div class="modal fade" id="fm_add_or_edit_mdl" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" ondrop="return false;" ondragover="return false;"
             data-backdrop="static" data-keyboard="false" data-rid="" data-flg="">
            <div class="modal-dialog">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_mdl_ttl" class="y-dgray-text">Модель. <small><i id="fm_mdl_ttl_add" class="y-dgray-text"></i></small></h4>
                                <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz15">&times;</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form role="form" autocomplete="off" onsubmit="return false">

                            <div class="form-group">
                                <label for="mdl_nm">Название модели</label>
                                <input id="mdl_nm" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                            </div>                                  
                            
                            <div class="form-group">
                                <small><label for="typs_list">Выбрать тип</label></small>
                                <select class='custom-select d-inline-block m-0' id='typs_list'>
                                  {types:list}
                                </select>
                            </div>  
                            
                        <!--    <div class="form-group">
                                <label for="mdl_type">Тип модели</label>
                                <input id="mdl_type" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                            </div>
                        -->               
                            
                        </form>
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="mdl_ok" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>