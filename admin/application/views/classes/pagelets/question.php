<div class="im-tab-content active">
    <h5>Reflection Question</h5>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Section<span class="required">*</span>
        </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input type="text" name="title" class="form-control" value="<?php echo (isset($page_data)) ? $page_data['title'] : set_value('title');?>">
            <?php echo form_error('title'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Number <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="question_number" value="<?php echo (isset($page_data)) ? $page_data['question_number'] : set_value('question_number');?>">
            <?php echo form_error('question_number'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Color <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control select-color" type="text" name="question_color" value="<?php echo (isset($page_data)) ? $page_data['question_color'] : set_value('question_color');?>">
            <?php echo form_error('question_color'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Question Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <textarea class="form-control" rows="3" name="question_text"><?php echo (isset($page_data)) ? $page_data['question_text'] : set_value('question_text');?></textarea>
             <?php echo form_error('question_text'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Button Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="button_text" value="<?php echo (isset($page_data)) ? $page_data['button_text'] : set_value('button_text');?>"/>
              <?php echo form_error('button_text'); ?>
        </div>
    </div>
    <input type="hidden" name="action" value="question">
</div>