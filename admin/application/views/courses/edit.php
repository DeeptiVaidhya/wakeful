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
                    <form class="form-horizontal form-label-left" id="editCourseForm" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="title" value="<?php echo (!empty(set_value('title'))) ? set_value('title') : (isset($course_detail['title']) ? $course_detail['title'] : ''); ?>" name="title" class="form-control col-md-7 col-xs-12">
                                <?php echo form_error('title'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="course_id" class="control-label col-md-3 col-sm-3 col-xs-12">Organization <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="course_id" class="form-control col-md-7 col-xs-12" name="organizations_id">
                                    <option value="">---Select---</option>

                                    <?php
                                    if (!empty($organizations['result'])) {
                                        foreach ($organizations['result'] as $value) {
                                            if(is_user_has_organization($value['id'])) {
                                            $organizations_id = (!empty($course_detail)) ? $course_detail['organizations_id'] : set_value('organizations_id');
                                            ?>
                                            <option <?php echo $organizations_id; ?> value="<?php echo $value['id']; ?>" <?php echo ($organizations_id == $value['id']) ? 'selected' : ''; ?>><?php echo $value['title']; ?></option>
                                            <?php }
                                        }
                                    }
                                    ?>
                                </select>
                                <?php echo form_error('organizations_id'); ?> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Bell File <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="file" class="input-file" name="bell_audio_file">
                                <?php
                                $config = $this->config->item('assets_audios');
                                echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                ?>
                            </div>
                        </div>
                        <?php if (isset($course_detail['bell_audio'])) { ?>

                            <div class="form-group audio_box">
                                <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                                    <a class="close-btn btn-remove-item" href="javascript:void(0)" data-msg="bell audio" data-params='<?php echo json_encode(array('course_id' => $course_detail['id'], 'file_id' => $course_detail['bell_audio']['bell_file_id'], 'file_type' => 'bell_audio')); ?>' data-url="<?php echo base_url() . 'course/delete-file'; ?>"><i class="fa fa-2x fa-close audio-del-btn"></i></a>
                                    <?php echo get_file($course_detail['bell_audio']['bell_file_id']) ?>
                                </div>
                            <input type="hidden" name="previous_bell_file_id" value="<?php echo $course_detail['bell_audio']['bell_file_id']; ?>">
                            </div>
                        <?php } ?>
                        <?php
                        for ($i = 0; $i < 3; $i++) {
                            ?>
                            <div class = "form-group">
                                <label class = "control-label col-md-3 col-sm-3 col-xs-12">Closing Audio <?php echo $i + 1; ?> <?php echo (!$i) ? '<span class="required">*</span>' : ''; ?></label>
                                <div class = "col-md-6 col-sm-6 col-xs-12">
                                    <input id="closing_file_<?php echo $i+1;?>" type = "file" class = "input-file closing_file" name = "closing_audio_file[<?php echo $i + 1; ?>]">
                                    <?php
                                    $config = $this->config->item('assets_audios');
                                    echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                    ?>
                                </div>
                            </div>
                            <?php if (isset($course_detail['close_audio'][$i])) {
                                ?>
                                <div class="form-group audio_box">
                                    <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                                        <a class="close-btn btn-remove-item" href="javascript:void(0)" data-msg="closing audio" data-params='<?php echo json_encode(array('course_id' => $course_detail['id'], 'file_id' => $course_detail['close_audio'][$i]['close_files_id'], 'file_type' => 'closing_audio')); ?>' data-url="<?php echo base_url() . 'course/delete-file'; ?>"><i class="fa fa-2x fa-close audio-del-btn"></i></a>
                                        <?php echo get_file($course_detail['close_audio'][$i]['close_files_id']) ?>
                                    </div>
                                <input type="hidden" id="previous_closing_audio_file_<?php echo $i + 1; ?>" name="previous_closing_audio_file[<?php echo $i + 1; ?>]" value="<?php echo $course_detail['close_audio'][$i]['close_files_id']; ?>">
                                </div>
                            <?php } ?>
                        <?php }
                        ?>
                        <div class = "form-group">
                            <label class = "control-label col-md-3 col-sm-3 col-xs-12" for = "is_published">Is Private</label>
                            <div class = "col-md-6 col-sm-6 col-xs-12">
                                <input type = "checkbox" id = "is_published" <?php echo (isset($course_detail['is_published']) && $course_detail['is_published'] == 1 || set_value('is_published')) ? 'checked' : '';
                        ?> name="is_published" />
                                       <?php echo form_error('is_published'); ?>
                            </div>
                        </div>
                        <?php if (isset($course_detail['id'])) { ?>
                            <input type="hidden" name="course_id" value="<?php echo $course_detail['id']; ?>">
                        <?php } ?>
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
