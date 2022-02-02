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
                    <form class="form-horizontal form-label-left" id="courseForm" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="title" value="<?php echo set_value('title') ?>" name="title" class="form-control col-md-7 col-xs-12">
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
                                            if(is_user_has_organization($value['id'])) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo (set_value('organizations_id') == $value['id']) ? 'selected' : ''; ?>><?php echo $value['title']; ?></option>
                                            <?php }
                                        }
                                    }
                                    ?>
                                </select>
                                <?php echo form_error('organizations_id'); ?> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Bell Audio <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="file" class="input-file" name="bell_audio_file">
                                <?php
                                $config = $this->config->item('assets_audios');
                                echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                ?>
                                <?php echo form_error('bell_audio_file'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Closing Audio 1 <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="file" class="input-file closing_file" name="closing_audio_file[0]">
                                <?php
                                $config = $this->config->item('assets_audios');
                                echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                ?>
                                <?php echo form_error('closing_audio_file1'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Closing Audio 2</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="file" class="input-file closing_file" name="closing_audio_file[1]">
                                <?php
                                $config = $this->config->item('assets_audios');
                                echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                ?>
                                <?php echo form_error('closing_audio_file2'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Closing Audio 3</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="file" class="input-file closing_file" name="closing_audio_file[2]">
                                <?php
                                $config = $this->config->item('assets_audios');
                                echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                ?>
                                <?php echo form_error('closing_audio_file3'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="is_published">Is Private</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="checkbox" id="is_published" checked name="is_published" />
                                <?php echo form_error('is_published'); ?>
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
