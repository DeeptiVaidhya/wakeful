<div class="">
    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>
                        <?php echo (isset($review_detail['review_data']) && !empty($review_detail['review_data'])) ? 'Edit' : 'Add'; ?> Review <small>in <b><?php echo $class_detail['title'] . ' (' . $course['title'] . ')'; ?></b></small>
                    </h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-down"></i></a></li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <form class="form-horizontal form-label-left" method="post" id="reviewForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" name="title" class="form-control col-md-7 col-xs-12" value="<?php echo (isset($review_detail)) ? $review_detail['title'] : set_value('title'); ?>">
                                    <?php echo form_error('title'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="intro_text" class="control-label col-md-3 col-sm-3 col-xs-12">Intro Text <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea class="form-control col-md-7 col-xs-12" name="intro_text"><?php echo (isset($review_detail)) ? $review_detail['intro_text'] : set_value('intro_text'); ?></textarea>
                                    <?php echo form_error('intro_text'); ?>
                                </div>
                            </div>
                            <hr/>
                            <div class="add-more-container well">
                                <div class="add-more-items">
                                    <?php
                                    if (isset($review_detail['review_data']) && !empty($review_detail['review_data'])) {
                                        ?>
                                        <div class="row">
                                            <h5>Review Details</h5>
                                        </div>
                                        <?php
                                        $i = 0;
                                        foreach ($review_detail['review_data'] as $value) {
                                            ?>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Pretext<span class="required">*</span></label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <textarea class="form-control pretext" rows="3" name="pretext[<?php echo $i;?>]"><?php echo $value['pretext'] ?></textarea>
                                                    <?php echo form_error('pretext[]'); ?>
                                                </div>
                                            </div>
                                            <div class="item-details">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> <?php echo $value['page_type'] ?>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="profile_img">
                                                            <div id="crop-avatar">
                                                                <?php $files_id = ($value['page_type'] == 'AUDIO') ? $value['audio_id'] : $value['video_id']; ?>
                                                                <?php echo get_file($files_id) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="sub_id[<?php echo $i;?>]" value="<?php echo $value['id']; ?>">
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
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
</div>