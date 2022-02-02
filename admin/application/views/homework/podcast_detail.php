<div class="">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation"><a href="<?php echo base_url() . 'homework/exercise/' . $course['id'] . '/' . $class_detail['id']; ?>">Exercise</a>
            </li>
            <!-- <li role="presentation" class="active"><a href="javascript:void(0)">Podcast</a>
            </li> -->
            <li role="presentation" class=""><a href="<?php echo base_url() . 'homework/reading/' . $course['id'] . '/' . $class_detail['id']; ?>">Reading</a>
            </li>
        </ul>
        <div class="tab-content">


            <div role="tabpanel">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h3>
                                    <?php echo (isset($podcast_detail['podcast_data']) && !empty($podcast_detail['podcast_data'])) ? 'Edit' : 'Add'; ?> Podcast <small>in <b><?php echo $class_detail['title'] . ' (' . $course['title'] . ')'; ?></b></small>
                                </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="row">
                                    <form class="form-horizontal form-label-left" method="post" id="podcastForm" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" name="title" class="form-control col-md-7 col-xs-12" value="<?php echo (isset($podcast_detail)) ? $podcast_detail['title'] : set_value('title'); ?>">
                                                <?php echo form_error('title'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="intro_text" class="control-label col-md-3 col-sm-3 col-xs-12">Intro Text <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <textarea class="form-control col-md-7 col-xs-12" name="intro_text"><?php echo (isset($podcast_detail)) ? $podcast_detail['intro_text'] : set_value('intro_text'); ?></textarea>
                                                <?php echo form_error('intro_text'); ?>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="add-more-container well">
                                            <div class="add-more-items">
                                                <?php
                                                if (isset($podcast_detail['podcast_data']) && !empty($podcast_detail['podcast_data'])) {
                                                    $count = 0;
                                                    $delete_url = base_url() . 'homework/delete-data';
                                                    foreach ($podcast_detail['podcast_data'] as $value) {
                                                        ?>
                                                        <?php if (!$count) { ?> 
                                                            <h5>Podcast Details  <button class="btn btn-primary pull-right btn-add-more-item btn-sm" type="button"><i class="fa fa-plus"></i> Add more</button></h5>
                                                        <?php } ?>
                                                        <div class="item-details">
                                                            <?php if ($count > 0) { ?>
                                                                <button data-msg="podcast" data-params='<?php echo json_encode(array('id' => $value['podcast_id'], 'type' => 'podcast')); ?>' data-url="<?php echo $delete_url; ?>" class='pull-right btn btn-danger btn-remove-item btn-sm' type='button'><i class='fa fa-trash'></i> Remove</button>
                                                            <?php } ?>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Title <span class="required">*</span></label>
                                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                                    <input type="text" name="podcast_title[<?php echo $count; ?>]"class="form-control col-md-7 col-xs-12 podcast_title" value="<?php echo $value['title']; ?>">
                                                                    <?php echo form_error('podcast_title[]'); ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Author <span class="required">*</span></label>
                                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                                    <input type="text" name="podcast_author[<?php echo $count; ?>]" class="form-control col-md-7 col-xs-12 podcast_author" value="<?php echo $value['author']; ?>">
                                                                    <?php echo form_error('podcast_author[]'); ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Script <span class="required">*</span></label>
                                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                                    <input type="text" name="podcast_script[<?php echo $count; ?>]" class="form-control col-md-7 col-xs-12 podcast_script" value="<?php echo $value['script']; ?>">
                                                                    <?php echo form_error('podcast_script[]'); ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Podcast Detail <span class="required">*</span></label>
                                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                                    <input type="file" name="podcast_link[<?php echo $count; ?>]" class="form-control col-md-7 col-xs-12 podcast_link input-file" value="<?php echo $value['files_id']; ?>">
                                                                    <?php echo form_error('podcast_link[]'); ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group not-req-elems">
                                                                <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                                                                    <div class="profile_img">
                                                                        <div id="crop-avatar">
                                                                            <?php echo get_file($value['files_id']) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <input type="hidden" name="previous_file_id[<?php echo $count; ?>]" value="<?php echo $value['files_id'] ?>"> 
                                                            <input type="hidden" name="sub_id[<?php echo $count; ?>]" value="<?php echo $value['podcast_id']; ?>">
                                                        </div>

                                                        <?php
                                                        $count++;
                                                    }
                                                } else {
                                                    ?>
                                                    <h5>
                                                        <div class="col-md-6 col-sm-6 col-xs-6">Podcast Details</div>
                                                        <div class="col-md-6 col-sm-6 col-xs-6"><button class="btn btn-primary pull-right btn-add-more-item" type="button"><i class="fa fa-plus"> Add more</i></button></div>
                                                        <div class="clearfix"></div>
                                                    </h5>
                                                    <div class="item-details">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Title <span class="required">*</span></label>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <input type="text" name="podcast_title[0]"class="form-control col-md-7 col-xs-12 podcast_title">
                                                                <?php echo form_error('podcast_title[]'); ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Author <span class="required">*</span></label>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <input type="text" name="podcast_author[0]" class="form-control col-md-7 col-xs-12 podcast_author">
                                                                <?php echo form_error('podcast_author[]'); ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Script <span class="required">*</span></label>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <input type="text" name="podcast_script[0]" class="form-control col-md-7 col-xs-12 podcast_script">
                                                                <?php echo form_error('podcast_script[]'); ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Podcast Detail <span class="required">*</span></label>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <input type="file" name="podcast_link[0]" class="form-control col-md-7 col-xs-12 podcast_link input-file">
                                                                <?php echo form_error('podcast_link[]'); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }
                                                ?> 
                                            </div></div>
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
            </div>
        </div>
    </div>

</div>