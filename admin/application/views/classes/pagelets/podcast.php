<div class="im-tab-content active">
    <h5>Podcast</h5>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Section<span class="required">*</span>
        </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input type="text" name="title" class="form-control" value="<?php echo (isset($page_data)) ? $page_data['title'] : set_value('title'); ?>">
            <?php echo form_error('title'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Podcast  Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <textarea class="form-control" rows="3" name="audio_text"><?php echo (isset($page_data)) ? $page_data['audio_text'] : set_value('audio_text'); ?></textarea>
            <?php echo form_error('audio_text'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Audio <span class="required">*</span>
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
    <?php if (isset($page_data) && $page_data['practice_audio_file_id'] != '') { ?>
        <div class="form-group">
            <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                <?php echo get_file($page_data['practice_audio_file_id']) ?>
            </div>
        </div>
        <input type="hidden" name="previous_audio_id" value="<?php echo $page_data['practice_audio_file_id']; ?>">
    <?php } ?>

   <!--  <?php if (isset($page_data) && $page_data['files_id'] != '') { ?>
        <div class="form-group">
            <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                <?php echo get_file($page_data['files_id']) ?>
            </div>
        </div>
        <input type="hidden" name="previous_audio_id" value="<?php echo $page_data['files_id']; ?>">
    <?php } ?> -->

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Script <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">

            <textarea class="form-control" rows="3" name="script"><?php echo (isset($page_data)) ? $page_data['script'] : set_value('script'); ?></textarea>
            <?php echo form_error('script'); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Button Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="button_text" value="<?php echo (isset($page_data)) ? $page_data['button_text'] : set_value('button_text'); ?>">
            <?php echo form_error('button_text'); ?>
        </div>
    </div>

    <!-- type  -->

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
    
    <input type="hidden" name="action" value="podcast">
</div>