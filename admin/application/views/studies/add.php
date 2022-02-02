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
                    <form class="form-horizontal form-label-left" id="studyForm" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="name" value="<?php echo set_value('name') ?>" name="name" class="form-control col-md-7 col-xs-12">
                                <?php echo form_error('name'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cc_email">CC Email <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="email" id="cc_email" value="<?php echo set_value('cc_email') ?>" name="cc_email" class="form-control col-md-7 col-xs-12">
                                <?php echo form_error('cc_email'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="course_id" class="control-label col-md-3 col-sm-3 col-xs-12">Course <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="course_id" class="form-control col-md-7 col-xs-12 select-course" name="courses_id">
                                    <option value="">---Select---</option>

                                    <?php

                                    if (!empty($courses['result'])) {

                                        foreach ($courses['result'] as $value) {
                                            ?>
                                          <?php if(is_user_has_organization($value['organizations_id']) || !$value['is_published']) { ?>
                                           <option value="<?php echo $value['id']; ?>" <?php echo (set_value('courses_id') == $value['id']) ? 'selected' : ''; ?>><?php echo $value['title']; ?></option> 
                                            <?php }
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="error"><?php echo form_error('courses_id'); ?></span>
                                <span class="error"><?php echo form_error('class_id[]'); ?></span> 
                            </div>
                        </div>

                        

                        <div class="form-group page_content_div"></div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-xs-12 col-sm-9">
                                <button class="btn btn-primary btn-save">Save</button>
                                <button class="btn btn-default btn-reset" type="button">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
