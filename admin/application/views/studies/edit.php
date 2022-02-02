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
                <div class="x_content add-more-container">
                    <form class="form-horizontal form-label-left" id="studyForm" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="name" value="<?php echo (!empty(set_value('name'))) ? set_value('name') : (isset($study_detail['name']) ? $study_detail['name'] : ''); ?>" name="name" class="form-control col-md-7 col-xs-12">
                                <?php echo form_error('name'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="cc_email">CC Email <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="email" id="cc_email" value="<?php echo (!empty(set_value('cc_email'))) ? set_value('cc_email') : (isset($study_detail['cc_email']) ? $study_detail['cc_email'] : ''); ?>" name="cc_email" class="form-control col-md-7 col-xs-12">
                                <?php echo form_error('cc_email'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="course_id" class="control-label col-md-3 col-sm-3 col-xs-12">Course <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="course_id" class="form-control col-md-7 col-xs-12 select-course" name="courses_id">
                                    <?php
                                    if (!empty($courses['result'])) {
                                        foreach ($courses['result'] as $value) {
                                            if(is_user_has_organization($value['organizations_id']) || !$value['is_published']) {
                                            $courses_id = (!empty($study_detail)) ? $study_detail['courses_id'] : set_value('courses_id');
                                            ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo ($courses_id == $value['id']) ? 'selected' : ''; ?>><?php echo $value['title']; ?></option>
                                            <?php }
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="error"><?php echo form_error('courses_id'); ?> </span>
                                <span class="error"><?php echo form_error('class_id[]'); ?></span> 
                            </div>
                        </div>

                        <div class="form-group page_content_div">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="class">Classes</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <?php if (!empty($class)) {
                                        foreach ($class['result'] as $i => $res) { ?>
                                        <input type="checkbox" id="class<?php echo $res['id']?>" name="class_id[]" <?php echo in_array($res['id'], $study_detail['class']) ? "checked" : "" ?>  value="<?php echo $res['id']?>" >&nbsp;
                                        <label for="class<?php echo $res['id']?>"><?php echo $res['title']?> </label><br>
                                    <?php } }?>
                                </div>
                        </div>
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
