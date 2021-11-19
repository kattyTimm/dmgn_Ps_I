        <div class="modal fade" id="fm_cat_params_train" tabindex="-1" role="dialog" aria-labelledby="fm_cat_params_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;">
            <div class="modal-dialog" id="all">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h5 id="fm_cat_params_ttl" class="y-dgray-text">Реестр по категориям. <i class="y-steel-blue-text">Параметры</i></h5>
                                
                                <div class="ml-auto" >
                                    <button id="all_roads_none" onclick="uncheck_all_click(this);"
                                            class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                            style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                            data-toggle="tooltip" title="Сбросить все категории" data-pref="cdr_param_rwc">
                                        <i class="far fa-square"></i>
                                    </button>
                                    <button id="all_roads_check" onclick="check_all_click(this);"
                                            class="d-inline-block btn y-btn-xs btn-outline-info y-shad"
                                            style="padding-top:1px;padding-bottom:1px;margin-right:3px;"
                                            data-toggle="tooltip" title="Выбрать все категории" data-pref="cdr_param_rwc">
                                        <i class="far fa-check-square"></i>
                                    </button>
                                </div>
                                <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz15">&times;</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form role="form" autocomplete="off" onsubmit="return false">
                            
                            <div class="form-row">
                                <div class="form-group col-md-4 text-center">
                                    <div class='custom-control custom-checkbox' style="padding-top:20px;">
                                        <input id='cat_params_train_1' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                        <label class='custom-control-label y-gray-text' for='cat_params_train_1'>ДП</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 text-center">
                                    <div class='custom-control custom-checkbox' style="padding-top:20px;">
                                        <input id='cat_params_train_3' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                        <label class='custom-control-label y-gray-text' for='cat_params_train_3'>НД</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 text-center">
                                    <div class='custom-control custom-checkbox' style="padding-top:20px;">
                                        <input id='cat_params_train_2' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                        <label class='custom-control-label y-gray-text' for='cat_params_train_2'>ДЧ</label>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="cat_params_train_ok" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>
