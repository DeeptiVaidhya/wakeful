<div class="im-tab-content active">
    <h5>Educational Video</h5>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Section<span class="required">*</span>
        </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input type="text" name="title" class="form-control" value="<?php echo (isset($page_data)) ? $page_data['title'] : set_value('title'); ?>">
            <?php echo form_error('title'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Header <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="header" value="<?php echo (isset($page_data)) ? $page_data['header'] : set_value('header'); ?>">
            <?php echo form_error('header'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Pretext <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
<!--            <input class="form-control" type="text" name="pretext" value="<?php echo (isset($page_data)) ? $page_data['pretext'] : set_value('pretext'); ?>">-->
            <textarea class="form-control" rows="3" name="pretext"><?php echo (isset($page_data)) ? $page_data['pretext'] : set_value('pretext'); ?></textarea>
            <?php echo form_error('pretext'); ?>
        </div>
    </div>
    <!-- <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Background Image<span class="required">*</span>
        </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input type="file" class="input-file filebox" name="image" >
            <?php
            $config = $this->config->item('assets_images');
            echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small>';
            ?>
            <?php echo form_error('image'); ?>
        </div>
    </div> -->

    <?php
    if (isset($page_data) && $page_data['backgound_image_unique_name'] != '') {
        ?>

        <div class="form-group">
            <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                <div class="profile_img">
                    <div id="crop-avatar">
                        <?php $background_image = ($page_data['backgound_image_unique_name'] == '') ? 'assets/images/default-.jpg' : 'assets/uploads/images/' . $page_data['backgound_image_unique_name'] ?>
                        <img class="img-responsive box-image" src="<?php echo base_url() . $background_image; ?>" alt="<?php echo $page_data['backgound_image_name']; ?>" title="<?php echo $page_data['backgound_image_name']; ?>">
                    </div>
                </div>
            </div>
            <input type="hidden" name="previous_image" value="<?php echo $page_data['backgound_image_unique_name']; ?>">
        </div>
    <?php } ?>
    <hr/>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Script <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
<!--            <input class="form-control" type="text" name="script" value="<?php echo (isset($page_data)) ? $page_data['script'] : set_value('script'); ?>"> -->
            <textarea class="form-control" rows="3" name="script"><?php echo (isset($page_data)) ? $page_data['script'] : set_value('script'); ?></textarea>
            <?php echo form_error('script'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Video <span class="required">*</span>
        </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input type="file" class="input-file" name="video">
            <?php
            $config = $this->config->item('assets_videos');
            echo '<p><small>Allowed types ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
            echo '<small>File max size upto ( ' . ($config['max_size'] / 1000) . ' MB )</small></p>';
            ?>
            <?php echo form_error('video'); ?>
        </div>
    </div>

    <?php if (isset($page_data) && $page_data['unique_name'] != '') { ?>
        <div class="form-group">
            <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                <?php echo get_file($page_data['files_id']) ?>
            </div>
        </div>
        <input type="hidden" name="previous_file_id" value="<?php echo $page_data['files_id']; ?>">
    <?php } ?>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Post Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <textarea class="form-control" rows="3" name="post_text"><?php echo (isset($page_data)) ? $page_data['post_text'] : set_value('post_text'); ?></textarea>
            <?php echo form_error('post_text'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Button Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="button_text" value="<?php echo (isset($page_data)) ? $page_data['button_text'] : set_value('button_text'); ?>">
            <?php echo form_error('button_text'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 col-sm-3 col-xs-12 control-label">Type</label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <ul class="list-inline ul-review-practice-type">
                <li class="radio">
                    <label>
                        <input type="radio" name="practice_type" 
                        <?php echo (
                                !isset($page_data['practice_type']) || 
                                (isset($page_data['practice_type']) && $page_data['practice_type']!='practice')
                            ) ? 'checked' : ''?> value="review"> Review
                    </label>
                </li>
                <li class="radio">
                    <label>
                        <input type="radio" name="practice_type" <?php echo isset($page_data['practice_type']) && $page_data['practice_type']=='practice'?'checked' : ''?> value="practice"> Practice
                    </label>
                </li>
            </ul>            
        </div>
    </div> 
    
    <div class="practice-detail-container">
        <div class="form-group practice-category <?php echo (!isset($page_data['practice_type']) || (isset($page_data['practice_type']) && $page_data['practice_type']!='practice'))?'hide' : ''?>">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">Practice Category</label>
            <div class="col-md-9 col-sm-9 col-xs-12">
                <select class="input-file" name="practice_categories_id">
                    <option value="">----Select-----</option>
                    <?php foreach ($practice_category as $val) { ?>
                        <option <?php echo (isset($page_data['practice_categories_id']) && $page_data['practice_categories_id']==$val['id']) ? 'selected="selected"' : ''?> value="<?php echo $val['id']; ?>" ><?php echo $val['label']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                <span class="practice-type-label"><?php echo (!isset($page_data['practice_type']) || (isset($page_data['practice_type']) && $page_data['practice_type']!='practice'))?'Review' : 'Practice'?></span> Title 
                <span class="required">*</span>
            </label>
            <div class="col-md-9 col-sm-9 col-xs-12">              

                <input class="form-control" type="text" name="practice_title" value="<?php echo (isset($page_data)) ? $page_data['practice_title'] : set_value('practice_title'); ?>"/>
                <?php echo form_error('practice_title'); ?>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">
                <span class="practice-type-label"><?php echo (!isset($page_data['practice_type']) || (isset($page_data['practice_type']) && $page_data['practice_type']!='practice'))?'Review' : 'Practice'?></span> Text 
                <span class="required">*</span>
            </label>
            <div class="col-md-9 col-sm-9 col-xs-12">
                 <textarea class="form-control" rows="3" name="practice_text"><?php echo (isset($page_data)) ? $page_data['practice_text'] : set_value('practice_text'); ?></textarea>                
                <?php echo form_error('practice_text'); ?>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="action" value="video">
</div>