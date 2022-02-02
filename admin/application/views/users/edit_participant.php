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
						<label class="control-label col-md-3 col-sm-3 col-xs-12">User Name</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" name="username" id="username" class="form-control" value="<?php echo (!empty(set_value('username'))) ? set_value('username') : (isset($user_info['username']) ? $user_info['username'] : ''); ?>">
							<span class="error"><?php echo form_error('username'); ?></span> 
						</div>
						
					</div>
					<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12">Email*</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" name="email" class="form-control" value="<?php echo (!empty(set_value('email'))) ? set_value('email') : (isset($user_info['email']) ? $user_info['email'] : ''); ?>">
							<span class="error"><?php echo form_error('email'); ?></span> 
							
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-xs-12 col-sm-9">
							<button id="submit-button" class="btn btn-primary btn-save">Update</button>
						</div>
					</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
