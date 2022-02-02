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
                    <form class="form-horizontal form-label-left" name="addAdminForm" id="addAdminForm" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">First Name*</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" name="first_name" class="form-control" value="<?php echo (!empty(set_value('first_name'))) ? set_value('first_name') : (isset($user_info['first_name']) ? $user_info['first_name'] : ''); ?>">
                                            <span class="error"><?php echo form_error('first_name'); ?> </span>
                                        </div>
                                    </div>        
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Last Name*</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" name="last_name" class="form-control" value="<?php echo (!empty(set_value('last_name'))) ? set_value('last_name') : (isset($user_info['last_name']) ? $user_info['last_name'] : ''); ?>">
                                            <span class="error"><?php echo form_error('last_name'); ?></span> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Username*</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" name="username" value="<?php echo (!empty(set_value('username'))) ? set_value('username') : (isset($user_info['username']) ? $user_info['username'] : ''); ?>" class="form-control" value="">
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
                                    <?php if($this->session->userdata('logged_in')->user_type == 1) {?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Assign Organizations to Admin</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                        <ul style="list-style:none;padding:0">
                                            <?php foreach ($course_list as $course => $value) {
                                                $id = $value['id']; 
                                                $checked = in_array($id, $course_has_users) ? 'checked' : '';

                                            echo "<li><h5><input type='checkbox' name='course_has_users[]' ".$checked." id='course_has_users[".$course."]' value='".$id."'/> <label for='course_has_users[".$course."]'>".$value['title']."<label></li></h5>";
                                                
                                            }?>
                                            <span class="error"><?php echo form_error('course_has_users[]'); ?></span> 
                                           </ul> 
                                        </div>
                                    </div>  
                                    <?php } ?>  
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-xs-12 col-sm-9">
                                <button class="btn btn-primary btn-save"><?php echo isset($user_info['id']) ? 'Update' : 'Save' ?></button>
                                <button class="btn btn-default btn-reset" type="button">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
