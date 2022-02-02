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
                    <form class="form-horizontal form-label-left" id="courseHomeworkExercise" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Practice Title<span class="required">*</span>
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <textarea name="title" class="form-control" ><?php echo (isset($homework_detail)) ? $homework_detail['title'] : set_value('title'); ?></textarea>
                                <?php echo form_error('title'); ?>
                            </div>
                        </div>

                         <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Practice Text<span class="required">*</span>
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <textarea name="tip" class="form-control" ><?php echo (isset($homework_detail)) ? $homework_detail['tip'] : set_value('tip'); ?></textarea>
                                <?php echo form_error('tip'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Practice Category</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="input-file" name="practice_categories_id">
                                    <?php foreach ($practice_category as $val) { ?>
                                        <option value="<?php echo $val['id']; ?>" <?php echo (isset($homework_detail) && isset($homework_detail['practice_categories_id']) && $homework_detail['practice_categories_id'] == $val['id']) ? 'selected' : ''; ?>><?php echo $val['label']; ?></option>
                                    <?php } ?>
                                </select>
                               <!--  <input type="hidden" name="practice_categories_id" value="<?php echo isset($homework_detail['practice_categories_id']) ? $homework_detail['practice_categories_id']:''; ?>"> -->

                            </div>
                        </div>

                       
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Audio 
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="file" class="input-file" name="audio">
                                <?php
                                    $config = $this->config->item('assets_audios');
                                    echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                ?>
                                <?php echo form_error('audio'); ?>
                            </div>
                        </div>

                        <?php if (isset($homework_detail) && $homework_detail['practice_audio_file_id'] != '') { ?>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <?php echo get_file($homework_detail['practice_audio_file_id']) ?>
                                </div>
                                <input type="hidden" name="previous_audio_id" value="<?php echo $homework_detail['practice_audio_file_id']; ?>">
                            </div>
                        <?php } ?>


                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Poem
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="file" class="input-file" name="poem">  
                                <?php 
                                    echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
                                    echo form_error('poem'); 
                                ?>
                            </div>
                        </div>

                        <?php if (isset($homework_detail) && $homework_detail['poem_file_id'] != '') { ?>
                            <div class="form-group audio_box">
                                <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                                    <?php echo get_file($homework_detail['poem_file_id']) ?>
                                    <a class="close-btn btn-remove-item" href="javascript:void(0)" data-msg="poem" data-params='<?php echo json_encode(array('id' => $homework_detail['id'], 'file_id' => $homework_detail['poem_file_id'])); ?>' data-url="<?php echo base_url() . 'course/delete-file'; ?>"><i class="fa fa-2x fa-close audio-del-btn"></i></a>
                                    <input type="hidden" name="previous_poem_id" value="<?php echo $homework_detail['poem_file_id']; ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Closing Audio</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="input-file" name="closing_file">
                                    <?php foreach ($course_detail['close_audio'] as $val) { ?>
                                        <option value="<?php echo $val['close_files_id']; ?>" <?php echo (isset($homework_detail) && isset($homework_detail['closing_file_id']) && $homework_detail['closing_file_id'] == $val['close_files_id']) ? 'selected' : ''; ?>><?php echo $val['close_file_name']; ?></option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" name="previous_closing_id" value="<?php echo isset($homework_detail['closing_file_id']) ? $homework_detail['closing_file_id']:''; ?>">

                            </div>
                        </div>
                        
                         <?php if (isset($homework_detail) && $homework_detail['files_id'] != '') { ?>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Generated Audio</label>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <?php echo get_file($homework_detail['files_id']) ?>
                                </div>
                                <input type="hidden" name="previous_file_id" value="<?php echo $homework_detail['files_id']; ?>">
                            </div>
                        <?php } ?>
        
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Script</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <textarea class="form-control" rows="3" name="script"><?php echo (isset($homework_detail)) ? $homework_detail['script'] : set_value('script'); ?></textarea>
                        <?php echo form_error('script'); ?>
                    </div>
                </div>
                                    
                <div class="form-group">
                    <div class="col-sm-offset-3 col-xs-12 col-sm-9">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <input type="hidden" name="homework_id" value="<?php echo $homework_id; ?>">
                        <button class="btn btn-primary btn-save">Save</button>
                        <button class="btn btn-default btn-reset" type="button">Reset</button>
                    </div>
                </div>

                    </form>
                </div>
            </div>
            <div class="x_panel">
                <div class="x_title">
                    <h3>List of Practice</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            <div class="x_content add-more-container">
                     <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" data-opts='{"sAjaxSource":"<?php echo base_url().'course/get-course-homework-excercise-data/'.$course_detail['id'];?>","searching": false}'>
                            <thead>
                                <tr>
                                    <th width="5%">S.No.</th>
                                    <th>Title</th>
                                    <th>Tip</th>
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
<div class="modal fade" id="view_homework_excercise" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Homework Excercise</h4>
            </div>
            <div class="modal-body">
            </div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>