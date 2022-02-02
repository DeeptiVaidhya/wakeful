<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php if (isset($type) && !empty($type)) {
    			$tr = '';?>
                <div class="x_panel">
					<div class="x_title">
                        <h3>Homework <small>></small> <?php 
                        	if(isset($type['homework']['reading'])){
                        		echo "Reading";
                        	}elseif(isset($type['homework']['podcast'])){
                        		echo "Podcast";
                        	}else{
                        		echo "Exercise";
                        	}
                        ?></h3>
                        <div class="clearfix"></div>
					</div>
					<div class="x-content">
					<div class="table-responsive">
					<?php if(isset($type['homework']['reading'])) { ?>
							<table class="table table-striped table-bordered dt-responsive datatable-list data-table">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Class</th>
										<th>Title</th>
										<th>Access Time</th>
										<th>Duration</th>
									</tr>
								</thead>
								<tbody>
								<?php if (isset($type['homework']['reading']) && !empty($type['homework']['reading'])) {
									   foreach($type['homework']['reading'] as $key => $val){ ?>
										<tr>
											<td><?php echo $key+1;?></td>
											<td><?php echo $val['class_title']?></td>
											<td><?php echo $val['article_title']?></td>
											<td><?php echo date('m/d/Y H:i', strtotime($val['start_time'])) ?></td>
											<td><?php echo gmdate("H:i", $val['TotalTimeSpentInMinutes']) ?></td>
										</tr>			
									<?php } } else { ?>
										<tr>
											<td class="text-center" colspan="5">No record found </td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						<?php } elseif(isset($type['homework']['podcast'])) { ?>
							<table class="table table-striped table-bordered dt-responsive datatable-list data-table">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Class</th>
										<th>Title</th>
										<th>Access Time</th>
										<th>Duration</th>
									</tr>
								</thead>
								<tbody>
								<?php if (isset($type['homework']['podcast']) && !empty($type['homework']['podcast'])) {
									  	foreach($type['homework']['podcast'] as $key => $val){ ?>
										<tr>
											<td><?php echo $key+1;?></td>
											<td><?php echo $val['class_title']?></td>
											<td><?php echo $val['home_pod_recording_title']?></td>
											<td><?php echo date('m/d/Y H:i', strtotime($val['created_at']))?></td>
											<td><?php echo gmdate("H:i", $val['total_elapsed_time']) ?></td>
										</tr>			
									<?php } } else { ?>
										<tr>
											<td class="text-center" colspan="5">No record found </td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						<?php } elseif(isset($type['homework']['exercise'])) { ?>
							<table class="table table-striped table-bordered dt-responsive datatable-list data-table">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Category</th>
										<!-- <th>Title</th> -->
										<th>Access Time</th>
										<th>Duration</th>
										

									</tr>
								</thead>
								<tbody>
								<?php if (isset($type['homework']['exercise']) && !empty($type['homework']['exercise'])) {
									  	foreach($type['homework']['exercise'] as $key => $val){ ?>
										<tr>
											<td><?php echo $key+1;?></td>
											<td><?php echo $val['class_title']?></td>
											<!-- <td><?php //echo $val['exercise_title']?></td> -->
											<td><?php echo date('m/d/Y H:i', strtotime($val['created_at'])) ?></td>
											<td><?php echo gmdate("H:i", $val['total_elapsed_time']) ?></td>
										</tr>			
									<?php } } else { ?>
										<tr>
											<td class="text-center" colspan="5">No record found </td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						<?php } ?>
						</div> 
					</div>
                </div>
            <?php }?>
        </div>
    </div>
</div>

