<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>List of Admin Users</h3>
                    <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('user/add-admin-user') ?>">Add Admin</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" data-opts='{"sAjaxSource":"<?php echo base_url().'user/get-users-data/2';?>","searching": false}'>
                            <thead>
                                <tr>
                                    <th width="5%">Id</th>
                                    <th>User Name</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Organization</th>
                                    <th>Status</th>
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
</div>
<!-- start popup -->
<!-- start popup -->
<div class="modal fade" id="view_user" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <img class="img-circle" alt="" src="<?php echo assets_url('images/default-avatar.png') ?>">
                <h4 class="modal-title" id="exampleModalLabel">John Doe</h4>
            </div>
            <div class="modal-body">
            </div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="edit_course" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-header">
               <h4 class="pull-left"> Select course for assign to Admin</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
            </div>
               <div class="modal-footer">
               <button class='save_course btn btn-primary btn-save btn-sm' data-params='"+ params +"'>Save changes</button>
               <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


