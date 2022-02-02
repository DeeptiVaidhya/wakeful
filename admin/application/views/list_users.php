<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
					<h3>List of Participant Users</h3>
					
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
					</ul>
					
					<a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('user/add-user/'.$course_id.'/'.$study_id) ?>">Add Participant</a>
					<a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('user/csv-file-participant-profile/'.$course_id.'/'.$study_id) ?>">Export</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" data-opts='{"sAjaxSource":"<?php echo base_url().'user/get-users-data/3/'.$course_id.'/'.$study_id;?>","searching": false}'>
                            <thead>
                                <tr>
									<th>User</th>
                                    <th>Unique Id</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Registration Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- start popup -->
<div class="modal fade" id="view_user" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <img class="img-circle" alt="" src="<?php echo assets_url('images/default-avatar.png') ?>">
                <h4 class="modal-title" id="exampleModalLabel">John Does</h4>
            </div>
            <div class="modal-body">
            </div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
