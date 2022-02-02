<div class="">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <!-- <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
            <li role="presentation"><a href="<?php echo base_url() . 'homework/exercise/' . $course['id'] . '/' . $class_detail['id']; ?>">Exercise</a>
            </li>
         <li role="presentation" class=""><a href="<?php // echo base_url() . 'homework/podcast/' . $course['id'] . '/' . $class_detail['id']; ?>">Podcast</a>
            </li> 
            <li role="presentation" class="active"><a href="javascript:void(0)">Reading</a>
            </li>
        </ul> -->

        <div class="tab-content">


            <div role="tabpanel">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h3>
                                    <?php echo (isset($reading_detail['reading_data']) && !empty($reading_detail['reading_data'])) ? 'Edit' : 'Add'; ?> Reading <small>in <b><?php echo $class_detail['title'] . ' (' . $course['title'] . ')'; ?></b></small>
                                </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="row">
                                    <form class="form-horizontal form-label-left" method="post" id="readingForm" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" name="title" class="form-control col-md-7 col-xs-12" value="<?php echo (isset($reading_detail)) ? $reading_detail['title'] : set_value('title'); ?>">
                                                <?php echo form_error('title'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="intro_text" class="control-label col-md-3 col-sm-3 col-xs-12">Intro Text <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <textarea class="form-control col-md-7 col-xs-12" name="intro_text"><?php echo (isset($reading_detail)) ? $reading_detail['intro_text'] : set_value('intro_text'); ?></textarea>
                                                <?php echo form_error('intro_text'); ?>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="add-more-container well">
                                            <div class="add-more-items">
                                                <?php
                                                if (isset($reading_detail['reading_data']) && !empty($reading_detail['reading_data'])) {
                                                    $count = 0;
                                                    $delete_url = base_url() . 'homework/delete-data';
                                                    foreach ($reading_detail['reading_data'] as $value) {
                                                        ?>
                                                        <?php if (!$count) { ?> 
                                                            <h5>Reading Details  <button class="btn btn-primary pull-right btn-add-more-item btn-sm" type="button"><i class="fa fa-plus"></i> Add more</button></h5>
                                                        <?php } ?>
                                                        <div class="item-details">
                                                            <?php if ($count > 0) { ?>
                                                                <button data-msg="reading" data-params='<?php echo json_encode(array('id' => $value['reading_id'], 'type' => 'reading')); ?>' data-url="<?php echo $delete_url; ?>" class='pull-right btn btn-danger btn-remove-item btn-sm' type='button'><i class='fa fa-trash'></i> Remove</button>
                                                            <?php } ?>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Title <span class="required">*</span></label>
                                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                                    <input type="text" name="reading_title[<?php echo $count; ?>]"class="form-control col-md-7 col-xs-12 reading_title" value="<?php echo $value['title']; ?>">
                                                                    <?php echo form_error('reading_title[]'); ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Author <span class="required">*</span></label>
                                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                                    <input type="text" name="reading_author[<?php echo $count; ?>]" class="form-control col-md-7 col-xs-12 reading_author" value="<?php echo $value['author']; ?>">
                                                                    <?php echo form_error('reading_author[]'); ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Reading Detail <span class="required">*</span></label>
                                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                                    <textarea name="reading_detail[<?php echo $count; ?>]" class="form-control col-md-7 col-xs-12 reading_detail text-tiny-mce"><?php echo $value['reading_detail']; ?></textarea>
                                                                    <p class="error"></p>
                                                                    <?php echo form_error('reading_detail[]'); ?>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="sub_id[<?php echo $count; ?>]" value="<?php echo $value['reading_id']; ?>">
                                                        </div>

                                                        <?php
                                                        $count++;
                                                    }
                                                } else {
                                                    ?>
                                                    <h5>
                                                        <div class="col-md-6 col-sm-6 col-xs-6">Reading Details</div>
                                                        <div class="col-md-6 col-sm-6 col-xs-6"><button class="btn btn-primary pull-right btn-add-more-item" type="button"><i class="fa fa-plus"> Add more</i></button></div>
                                                        <div class="clearfix"></div>
                                                    </h5>
                                                    <div class="item-details">
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Title <span class="required">*</span></label>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <input type="text" name="reading_title[0]"class="form-control col-md-7 col-xs-12 reading_title">
                                                                <?php echo form_error('reading_title[]'); ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Author <span class="required">*</span></label>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <input type="text" name="reading_author[0]" class="form-control col-md-7 col-xs-12 reading_author">
                                                                <?php echo form_error('reading_author[]'); ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Reading Detail <span class="required">*</span></label>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <textarea name="reading_detail[0]" class="form-control col-md-7 col-xs-12 reading_detail text-tiny-mce"></textarea>
                                                                <p class="error"></p>
                                                                <?php echo form_error('reading_detail[]'); ?>
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