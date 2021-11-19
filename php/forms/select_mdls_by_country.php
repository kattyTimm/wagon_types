        <div class="modal fade" id="select_mdls_by_country" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" ondrop="return false;" ondragover="return false;"
             data-backdrop="static" data-keyboard="false" data-rid="" data-flg="">
            <div class="modal-dialog">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_mdl_ttl" class="y-dgray-text">Страна производства. <small><i id="fm_mdl_ttl_add" class="y-dgray-text"></i></small></h4>
                                <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz15">&times;</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form role="form" autocomplete="off" onsubmit="return false">                              
                            
                            <div class="form-group">
                                <small><label for="countries_list">Страна производства:</label></small>
                                <select class='custom-select d-inline-block m-0' id='countries_list'>
                                  {types:countries_list}
                                </select>
                            </div>  
                            
                        </form>
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="select_country_ok" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>