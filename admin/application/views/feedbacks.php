<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">
         <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3> Feedbacks</h3>
                     <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        
                        

                        
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" id="courseList" data-opts='{"sAjaxSource":"<?php echo  base_url().'course/get-courses-feedback' ?>","columnDefs": [{ "orderable": false, "targets": 0 },{ "orderable": false, "targets": "_all" }]}'>
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Username</th>
                                    <?php if(!empty($feedback_question)){
                                       foreach($feedback_question as $value){ ?>
                                    <th><?php echo $value['question'];?></th>
                                       <?php } 
                                    }?>
                                </tr>
                            </thead>
                            
                        </table>
                       
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>