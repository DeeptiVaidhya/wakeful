<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">
         <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3> Setting <small>in <b><?php echo $course_detail['name']?></b></small></h3>
                    <!-- <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('/course/add-setting/'.$course_detail['courses_id']) ?>">Add Setting</a> -->
                     <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered data-table" data-opts='{"columnDefs": [{ "orderable": false, "targets": 0 },{ "orderable": false, "targets": "_all" }],"rowReorder":{"selector":".row-move"},"aaData":null,"bServerSide":false,"paging":false,"searching": false}'>
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Feature</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($settings) && !empty($settings)) {
									foreach ($settings as $key => $setting) { 
										$params = array('study_id'=>$course_detail['id'], 'course_id'=>$course_detail['courses_id'],'key'=>$setting['key'], 'description'=>$setting['description']);
										isset($setting['id']) ? ($params['id'] = $setting['id']): '';
										?>
                                        <tr>
                                            <td><?php echo $key + 1 ;?></td>
                                            <td><?php echo str_replace("_"," ",$setting['key']);?></td>
                                            <td><input type="checkbox" <?php echo (isset($setting['value']) && $setting['value']==1) ? 'checked':'' ?> data-params='<?php echo json_encode($params) ?>' class="switch_btn" data-name="<?php echo (isset($setting['value']) && $setting['value']==1) ? 'Active':'Inactive' ?>" data-url='<?php echo base_url()."course/change-setting" ;?>'/></td>
                                    <?php }
                                }else{ ?>
                                    <tr><td colspan="3">No data found</td></tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>