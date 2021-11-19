        <div class="modal fade" id="categoryes_form" tabindex="-1" role="dialog" aria-labelledby="railwayCarriage_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;" data-rid=""
             data-backdrop="static" data-keyboard="false">   <!-- Prevent close by click outside or by ESC press (else mouseup close form when outside) --> 
            
            <div class="modal-dialog"> <!-- modal-lg -->
                <div class="modal-content y-modal-shadow">
                    
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_train_ttl" class="y-dgray-text modal-header-text">Реестр по дирекции<small><i id="fm_emp_ttl_add" class="y-steel-blue-text"></i></small></h4>                      
                                <div class="p-0 ml-auto">
                                    <!-- onclick="reset_all_categories(this);"-->
                                     <button id="categories_none" 
                                            class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                            style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                            data-toggle="tooltip" title="Сбросить все">
                                        <i class="far fa-square"></i>
                                     </button>

                                    <button id="categories_all" 
                                            class="d-inline-block btn btn-sm btn-outline-info y-shad ml-auto"
                                            style="padding-top:1px;padding-bottom:2px;"
                                            data-toggle="tooltip" title="Отметить все">
                                        <i class="far fa-check-square"></i>
                                    </button>
                                    <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center y-fz12">&times;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-body">
                        <form onsubmit="return false">
                            
                            <div id="all_directs">
                                <div class="form-row">      
                                    <div class='custom-control custom-checkbox'>
                                            <small> <!-- The only way to align box and label vertically -->
                                                <input id='type_fpk' type='checkbox' data-rid='' class='custom-control-input reestr-type'>
                                                <label class='custom-control-label' for='type_fpk'>реестр ФПК</label>
                                            </small>
                                     </div>
                                </div>

                                 <div class="block-separator"></div>

                                <div class="form-row">      
                                    <div class='custom-control custom-checkbox'>
                                            <small> <!-- The only way to align box and label vertically -->
                                                <input id='type_dss' type='checkbox' data-rid class='custom-control-input reestr-type'>
                                                <label class='custom-control-label' for='type_dss'>реестр ДОСС</label>
                                            </small>
                                     </div>
                                </div>

                               <div class="block-separator"></div>

                               <div class="form-row" id="row_cdmv">    
                                    <div class='custom-control custom-checkbox'>
                                            <small> 
                                                <input id='type_cdmv' type='checkbox' data-rid class='custom-control-input reestr-type'>
                                                <label class='custom-control-label' for='type_cdmv'>реестр ЦДМВ</label>
                                            </small>
                                     </div>  
                                </div>    
                            </div>
                            
                            <div class="block-separator"></div>
                         
                            <div class="y-flex-row-nowrap align-items-stretch y-mrg-t15">
                                <div id="all_categories_list" class="card y-shad" style="flex:1 1 48%;-ms-flex:1 1 48%;">
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
                                                <div class='custom-control custom-checkbox y-pad-t5'>
                                                    <input id='cat_params_k' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                                    <label class='custom-control-label y-gray-text' for='cat_params_k'>К</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 text-center">
                                                <div class='custom-control custom-checkbox y-pad-t5'>
                                                    <input id='cat_params_o' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                                    <label class='custom-control-label y-gray-text' for='cat_params_o'>О</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 text-center">
                                                <div class='custom-control custom-checkbox y-pad-t5'>
                                                    <input id='cat_params_s' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                                    <label class='custom-control-label y-gray-text' for='cat_params_s'>С</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 text-center">
                                                <div class='custom-control custom-checkbox y-pad-t5'>
                                                    <input id='cat_params_g' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                                    <label class='custom-control-label y-gray-text' for='cat_params_g'>Г</label>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>    
                                    
                                </div>
                            </div>
                            
                            <div class="y-flex-row-nowrap align-items-stretch y-mrg-t15">
                                <div id="all_params_list" class='card y-shad' style="flex:1 1 48%;-ms-flex:1 1 48%;">                                  
                                    <div class="card-header y-pad-a5 y-flex-row-nowrap align-items-center"> 
                                        <div class="y-gray-text">&nbsp; Доступность</div>
                                        <div class="ml-auto">
                                            <button id="all_categories_none" onclick="uncheck_all_click(this);"
                                                    class="d-inline-block btn y-btn-xs btn-outline-secondary y-shad"
                                                    style="padding-top:1px;padding-bottom:1px;margin-right:7px;"
                                                    data-toggle="tooltip" title="Сбросить все оценки" data-pref="">
                                                <i class="far fa-square"></i>
                                            </button>
                                            <button id="all_categories_check" onclick="check_all_click(this);"
                                                    class="d-inline-block btn y-btn-xs btn-outline-info y-shad"
                                                    style="padding-top:1px;padding-bottom:1px;margin-right:3px;"
                                                    data-toggle="tooltip" title="Выбрать все оценки" data-pref="">
                                                <i class="far fa-check-square"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" style="height:4rem;"> <!---->
                                        <div class="form-row">
                                            <div class="form-group col-md-4 text-center">
                                                <div class='custom-control custom-checkbox'>
                                                    <input id='cat_params_rate_1' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                                    <label class='custom-control-label y-gray-text' for='cat_params_rate_1'>ДП</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4 text-center">
                                                <div class='custom-control custom-checkbox'>
                                                    <input id='cat_params_rate_2' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                                    <label class='custom-control-label y-gray-text' for='cat_params_rate_2'>НД</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4 text-center">
                                                <div class='custom-control custom-checkbox'>
                                                    <input id='cat_params_rate_3' type='checkbox' class='custom-control-input' style="margin-left:15px;">
                                                    <label class='custom-control-label y-gray-text' for='cat_params_rate_3'>ДЧ</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div> 
                        </form>    
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="chosen_category_ok" class="btn btn-info y-shad">Отправить</button>
                         <button id="train_exit" data-dismiss="modal" class="btn btn-light y-shad">Отмена</button>
                    </div>
                </div>
            </div>
        </div>


