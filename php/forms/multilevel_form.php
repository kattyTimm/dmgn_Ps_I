        <div class="modal fade" id="multilevel_form" tabindex="-1" role="dialog" aria-labelledby="fm_cdr_params_ttl" aria-hidden="true" 
             ondrop="return false;" ondragover="return false;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h5 id="fm_cdr_params_ttl" class="y-dgray-text">Реестр по многоуровневым параметрам. 
                                    <small><i class="y-steel-blue-text">Выбор элементов</i></small>
                                </h5>
                                <div class="p-0 ml-auto">
                                    <button id="total_drop" onclick="drop_all(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                                data-toggle="tooltip" title="Сбросить все выбранные флажки">
                                            <i class="far fa-square"></i>
                                    </button> 
                                    <button id="total_check" onclick="check_all(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-info y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:3px;"
                                                data-toggle="tooltip" title="Выбрать все флажки">
                                            <i class="far fa-check-square"></i>
                                    </button>
                                    <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz12">&times;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="y-flex-row-nowrap align-items-stretch">
                
                            <div id="all_orgs_list" class='card y-shad' style="height:15rem;flex:1 1 48%;-ms-flex:1 1 48%;margin-right:10px;">
                                <div class="card-header y-pad-a5 y-flex-row-nowrap align-items-center">
                                    <div class="y-gray-text">&nbsp; Организации</div>
                                    <div class="ml-auto">
                                        <button id="all_orgs_none" onclick="uncheck_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                                data-toggle="tooltip" title="Сбросить все организации" data-pref="cdr_param_rwc">
                                            <i class="far fa-square"></i>
                                        </button>
                                        <button id="all_orgs_check" onclick="check_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-info y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:3px;"
                                                data-toggle="tooltip" title="Выбрать все организации" data-pref="cdr_param_rwc">
                                            <i class="far fa-check-square"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body y-pad-tb5 overflow-y-auto">
                                    {orgs:options}
                                </div>
                                <div id="foot_cdr_param_rwc" class="card-footer y-pad-a5 y-fz07 y-gray-text" 
                                     style="min-height:30px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                            </div>
                            
                            <div id="all_roads_list" class='card y-shad' style="height:15rem;flex:1 1 48%;-ms-flex:1 1 48%;">
                                <div class="card-header y-pad-a5 y-flex-row-nowrap align-items-center">
                                    <div class="y-gray-text">&nbsp; Дороги</div>
                                    <div class="ml-auto">
                                        <button id="all_roads_none" onclick="uncheck_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                                data-toggle="tooltip" title="Сбросить все дороги" data-pref="cdr_param_rwc">
                                            <i class="far fa-square"></i>
                                        </button>
                                        <button id="all_roads_check" onclick="check_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-info y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:3px;"
                                                data-toggle="tooltip" title="Выбрать все дороги" data-pref="cdr_param_rwc">
                                            <i class="far fa-check-square"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body y-pad-tb5 overflow-y-auto">
                                    {roads:options}
                                </div>
                                <div id="foot_cdr_param_rwc" class="card-footer y-pad-a5 y-fz07 y-gray-text" 
                                     style="min-height:30px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                            </div>
                        
                        </div>
                        
                        <div class="y-flex-row-nowrap align-items-stretch y-mrg-t15">
                
                            <div id="all_categories_list" class="card y-shad" style="flex:1 1 48%;-ms-flex:1 1 48%;margin-right:10px;">
                                <div class="card-header y-pad-a5 y-flex-row-nowrap align-items-center">
                                    <div class="y-gray-text">&nbsp; Категории</div>
                                    <div class="ml-auto">
                                        <button id="all_categories_none" onclick="uncheck_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                                data-toggle="tooltip" title="Сбросить все категории" data-pref="">
                                            <i class="far fa-square"></i>
                                        </button>
                                        <button id="all_categories_check" onclick="check_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-info y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:3px;"
                                                data-toggle="tooltip" title="Выбрать все категории" data-pref="">
                                            <i class="far fa-check-square"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="height:4rem;">

                                    <div class="form-row">
                                        <div class="form-group col-md-3 text-center">
                                            <div class='custom-control custom-checkbox'>
                                                <input id='param-K' type='checkbox' class='custom-control-input' style="margin-left:15px;" data-shrt="К">
                                                <label class='custom-control-label y-gray-text' for='param-K'>К</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 text-center">
                                            <div class='custom-control custom-checkbox'>
                                                <input id='param-O' type='checkbox' class='custom-control-input' style="margin-left:15px;" data-shrt="О">
                                                <label class='custom-control-label y-gray-text' for='param-O'>О</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 text-center">
                                            <div class='custom-control custom-checkbox'>
                                                <input id='param-S' type='checkbox' class='custom-control-input' style="margin-left:15px;" data-shrt="С">
                                                <label class='custom-control-label y-gray-text' for='param-S'>С</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3 text-center">
                                            <div class='custom-control custom-checkbox'>
                                                <input id='param-G' type='checkbox' class='custom-control-input' style="margin-left:15px;"data-shrt="Г">
                                                <label class='custom-control-label y-gray-text' for='param-G'>Г</label>
                                            </div>
                                        </div>
                                    </div>
                            
                                </div>
                                <div id="foot_cdr_param_cat" class="card-footer y-pad-a5 y-fz07 y-gray-text" style="min-height:30px;"></div>
                            </div>
                
                            <div id="all_params_list" class='card y-shad' style="flex:1 1 48%;-ms-flex:1 1 48%;">
                                <div class="card-header y-pad-a5 y-flex-row-nowrap align-items-center">
                                    <div class="y-gray-text">&nbsp; Доступность</div>
                                    <div class="ml-auto">
                                        <button id="all_params_none" onclick="uncheck_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                                data-toggle="tooltip" title="Сбросить все оценки" data-pref="">
                                            <i class="far fa-square"></i>
                                        </button>
                                        <button id="all_params_check" onclick="check_all_click(this);"
                                                class="d-inline-block btn y-btn-xs btn-outline-info y-shad"
                                                style="padding-top:1px;padding-bottom:1px;margin-right:3px;"
                                                data-toggle="tooltip" title="Выбрать все оценки" data-pref="">
                                            <i class="far fa-check-square"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="height:4rem;">
                            
                                    <div class="form-row">
                                        <div class="form-group col-md-4 text-center">
                                            <div class='custom-control custom-checkbox'>
                                                <input id='cdr_param_dst-1' type='checkbox' class='custom-control-input' style="margin-left:15px;" data-shrt="ДП">
                                                <label class='custom-control-label y-gray-text' for='cdr_param_dst-1'>ДП</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4 text-center">
                                            <div class='custom-control custom-checkbox'>
                                                <input id='cdr_param_dst-2' type='checkbox' class='custom-control-input' style="margin-left:15px;" data-shrt="НД">
                                                <label class='custom-control-label y-gray-text' for='cdr_param_dst-2'>НД</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4 text-center">
                                            <div class='custom-control custom-checkbox'>
                                                <input id='cdr_param_dst-3' type='checkbox' class='custom-control-input' style="margin-left:15px;" data-shrt="ДЧ">
                                                <label class='custom-control-label y-gray-text' for='cdr_param_dst-3'>ДЧ</label>
                                            </div>
                                        </div>
                                    </div>
                            
                                </div>
                                <div id="foot_cdr_param_dst" class="card-footer y-pad-a5 y-fz07 y-gray-text" style="min-height:30px;"></div>
                            </div>
                        
                        </div>
                        
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="multilevel_params_ok" class="btn btn-primary y-shad">Ok</button>
                    </div>
                </div>
            </div>
        </div>
