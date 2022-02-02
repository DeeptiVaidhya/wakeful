<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php if (isset($type) && !empty($type)) {
    			$tr = '';?>
                <div class="x_panel">
					<div class="x_title">
                        <h3>Progress</h3>
                        <div class="clearfix"></div>
					</div>
					<div class="x-content">
						<?php if (isset($type['progress']) && !empty($type['progress'])) {?>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<form id="progress" class="form-horizontal form-label-left" method="post">
									<div class="form-group">
										<label class="control-label col-md-3 col-sm-3 col-xs-12">Practice Minute : </label>
										<div class="col-md-9 col-sm-9 col-xs-12">
											<span><?php echo isset($type['progress']['meditation_minutes']) ? $type['progress']['meditation_minutes'] : '' ?></span>
										</div>                                  
									</div>
									<div class="form-group">
										<label class="control-label col-md-3 col-sm-3 col-xs-12">Consecutive days of practice : </label>
										<div class="col-md-9 col-sm-9 col-xs-12">
											<span><?php echo isset($type['details']['consecutive_days']) ? $type['details']['consecutive_days'] : '' ?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3 col-sm-3 col-xs-12">Completed Classes : </label>
										<div class="col-md-9 col-sm-9 col-xs-12">
											<span><?php echo (isset($type['progress']['completed_class']) && $type['progress']['completed_class'] > 0) ? ($type['progress']['completed_class']-1) : '0'?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3 col-sm-3 col-xs-12">Where you are in the course : </label>
										<div class="col-md-9 col-sm-9 col-xs-12">
											<span><?php echo isset($type['progress']['current_class']->title) ? $type['progress']['current_class']->title .' :' : ''?>  <strong><?php echo isset($type['progress']['current_page']->title) ? $type['progress']['current_page']->title : 'N/A' ?></span>
										</div>

									</div>
								</form>
								<div class="table-responsive">
								<table class="table table-striped table-bordered dt-responsive datatable-list data-table">
									<thead>
										<tr>
											<th>S. No.</th>
											<th>Class Title</th>
											<th>Class Open</th>
											<th>Participant Start</th>
											<th>End Date</th>
										</tr>
									</thead>
									<tbody>
									<?php if (isset($type['progress']['class_list']) && !empty($type['progress']['class_list'])) {
											foreach($type['progress']['class_list'] as $key => $val){ ?>
											<tr>
												<td><?php echo $key+1;?></td>
												<td><?php echo $val['title']?></td>
												<td><?php echo ($val['start_at']) ? date("m/d/Y", strtotime($val['start_at'])) : 'N/A' ?></td>
												<td> <?php echo ($val['class_start_at']) ? date("m/d/Y", strtotime($val['class_start_at'])) : 'N/A' ?></td>
												<td>
													<?php echo ($val['completed_at']) ? date("m/d/Y", strtotime($val['completed_at'])) : (($val['end_at'] != '') ? '<span style="color:grey">('. date("m/d/Y", strtotime($val['end_at'])).')</span>' : 'N/A'); ?>
												</td>
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
							
						<?php	}?>
					</div>
                </div>
            <?php }?>
        </div>
    </div>
</div>

