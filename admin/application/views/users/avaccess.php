<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php if (isset($type) && !empty($type)) {
    			$tr = '';?>
                <div class="x_panel">
					<div class="x_title">
                        <h3>Audio/Video Access</h3>
                        <div class="clearfix"></div>
					</div>
					<div class="x-content">
							<div class="table-responsive">
								<table class="table table-striped table-bordered dt-responsive datatable-list data-table">
									<thead>
										<tr>
											<th>S. No.</th>
											<th>Audio/Video Title</th>
											<th>Access Time</th>
											<th>Duration</th>
										</tr>
									</thead>
									<tbody>
									<?php if (isset($type['avaccess']) && !empty($type['avaccess'])) {
											foreach($type['avaccess'] as $key => $val){ ?>
											<tr>
												<td><?php echo $key+1;?></td>
												<td><?php echo $val['files_name']?></td>
												<td><?php echo date('m/d/Y H:i', strtotime($val['starts_at'])) ?></td>
												<td><?php echo gmdate("H:i", $val['total_elapsed_time'])?></td>
											</tr>			
									<?php } } else { ?>
										<tr>
											<td class="text-center" colspan="4">No record found </td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</div> 
					</div>
                </div>
            <?php }?>
        </div>
    </div>
</div>

