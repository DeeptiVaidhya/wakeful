<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">

        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $subheading; ?></h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form class="form-horizontal form-label-left" name="addUserForm" id="addUserForm" method="post" enctype="multipart/form-data">
                   	<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12">Unique Id*</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" name="unique_id" id="user_unique_id" class="form-control" value="">
							<span class="error"><?php echo form_error('unique_id'); ?></span> 
						</div>
						
					</div>
					<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12">User Access Code*</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" name="user_access_code" readonly="" id="user_access_code" class="form-control" data-access-code="<?php echo $access_code;?>" value="<?php echo $access_code;?>">
							<input type="hidden" name="course_id"  value="<?php echo $course_id;?>">
							<input type="hidden" name="study_id"  value="<?php echo $study_id;?>">
							<span class="error access-code"><?php echo form_error('user_access_code'); ?></span> 
							
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12">Email*</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" name="email" class="form-control" value="">
							<span class="error"><?php echo form_error('email'); ?></span> 
							
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-xs-12 col-sm-9">
							<button id="submit-button" class="btn btn-primary btn-save">Save</button>
						</div>
					</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
