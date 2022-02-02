<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">
         <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Site Settings </h3>
                    
                     <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <?php if(count($settings['result']) == 0) { ?>
                    <a class="btn btn-primary btn-sm pull-right" href="<?php echo base_url().'settings/add-setting/'.$study_id; ?>">Add Setting</a>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered data-table" data-opts='{"columnDefs": [{ "orderable": false, "targets": 0 },{ "orderable": false, "targets": "_all" }],"rowReorder":{"selector":".row-move"},"aaData":null,"bServerSide":false,"paging":false,"searching": false}'>
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Title</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($settings) && !empty($settings)) {
									foreach ($settings['result'] as $key => $setting) { ?>
                                        <tr>
                                            <td><?php echo $key + 1 ;?></td>
                                            <td><?php echo $setting['title'];?></td>
                                            <td><?php echo $setting['value'];?></td>
											<td><a class="btn btn-primary btn-xs" title="Edit" href="<?php echo base_url().'settings/edit-setting/'.$setting['id'] ?>"><i class="fa fa-pencil"></i>Edit</a></td>
                                        </tr>
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