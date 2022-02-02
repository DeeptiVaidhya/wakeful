<div class="im-tab-content active">
    <h5>General</h5>
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
            <input class="form-control" type="text" name="header" value="<?php echo (isset($page_data)) ? $page_data['header'] : set_value('header'); ?>" >
            <?php echo form_error('header'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Image
        </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input type="file" class="input-file filebox" name="general_image" >
             <?php $config = $this->config->item('assets_images'); 
              echo '<p><small>Allowed type ( '.str_replace('|',', ', $config['allowed_types']).' )</small>';
              ?>
            <?php echo form_error('image'); ?>
        </div>
    </div>
    <?php 
        if (isset($page_data) && $page_data['files_id'] != '') { ?>
        <div class="form-group item-details">
            <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                <a class="close-btn btn-remove-item" href="javascript:void(0)" data-msg="image permanently" data-params='<?php echo json_encode(array('id' => $page_data['id'], 'file_id' => $page_data['files_id'], 'page_type' => 'general', 'file_type' => 'images','field'=>'files_id')); ?>' data-url="<?php echo base_url() . 'classes/delete-file'; ?>"><i class="fa fa-2x fa-close"></i></a>
                <div class="profile_img">
                    <div id="crop-avatar">
                        <!-- Current avatar -->
                           <?php echo get_file($page_data['files_id']) ?>
                    </div>
                </div>
            </div>
    <input type="hidden" name="previous_file_id" value="<?php echo $page_data['files_id'];?>">
        </div>
    <?php } ?>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Content <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <textarea class="form-control" rows="3" name="content"><?php echo (isset($page_data)) ? $page_data['content'] : set_value('content'); ?></textarea>
            <?php echo form_error('content'); ?>
        </div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="remove_foreground_objects">Remove Foreground Objects </label>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<input class="" id="personal" <?php echo (isset($page_data['remove_foreground_objects']) &&  $page_data['remove_foreground_objects'] == 1 ? 'checked' : ''); ?> value="1"  type="checkbox" name="remove_foreground_objects"> <label for="personal">Yes</label>
			<?php echo form_error('remove_foreground_objects'); ?>
		</div>
	</div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Button Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="button_text" value="<?php echo (isset($page_data)) ? $page_data['button_text'] : set_value('button_text'); ?>">
            <?php echo form_error('button_text'); ?>
        </div>
	</div>

    <input type="hidden" name="action" value="general">
</div>