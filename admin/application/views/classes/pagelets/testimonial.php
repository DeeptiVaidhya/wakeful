<div class="im-tab-content active">
    <h5>Testimonial</h5>
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
            <input type="text" name="header" class="form-control" value="<?php echo (isset($page_data)) ? $page_data['header'] : set_value('header'); ?>">
            <?php echo form_error('header'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Button Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="button_text" value="<?php echo (isset($page_data)) ? $page_data['button_text'] : set_value('button_text'); ?>"/>
            <?php echo form_error('button_text'); ?>
        </div>
    </div>
    <input type="hidden" name="action" value="testimonial">
    <input type="hidden" name="current_id" value="<?php echo (isset($page_data)) ? $page_data['id'] : set_value('id'); ?>">
    <hr/>
    <div class="add-more-items well">
        <?php
        if (isset($page_data['sub_details']) && !empty($page_data['sub_details'])) {
            $count = 0;
            $delete_url = base_url() . 'classes/delete-sub-page';
            foreach ($page_data['sub_details'] as $value) {
                ?>
                <?php if (!$count) { ?> 
                    <h5>Testimonial Details  <button class="btn btn-primary pull-right btn-add-more-item btn-sm" type="button"><i class="fa fa-plus"></i> Add more</button></h5>
                <?php } ?>
                <div class="item-details">
                    <?php if ($count > 0) { ?>
                        <button class='pull-right btn btn-danger btn-remove-item btn-sm' type='button' data-msg="tesimonial" data-params='<?php echo json_encode(array('id' => $value['id'], 'file_id' => $value['files_id'], 'page_type' => 'testimonial', 'file_type' => 'images')); ?>' data-url="<?php echo $delete_url; ?>"><i class='fa fa-trash'></i> Remove</button>
                    <?php } ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Name<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" class="form-control topic_title" name="name[<?php echo $count; ?>]" value="<?php echo $value['name'] ?>">
                            <?php echo form_error('name[]'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Photo<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="file" class="form-control photo input-file" name="photo[<?php echo $count; ?>]" />
                            <?php
                            $config = $this->config->item('assets_images');
                            echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small>';
                            ?>
                            <?php echo form_error('photo[]'); ?>
                        </div>
                        <input type="hidden" name="previous_file_id[<?php echo $count; ?>]" value="<?php echo $value['files_id'] ?>"> 
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
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Quote <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea class="form-control quote" rows="3" name="quote[<?php echo $count; ?>]"><?php echo $value['quote'] ?></textarea>
                            <?php echo form_error('quote[]'); ?>
                        </div>
                    </div>
                    <input type="hidden" name="sub_id[<?php echo $count; ?>]" value="<?php echo $value['id']; ?>">
                </div>

                <?php
                $count++;
            }
        } else {
            ?>
            <h5>Testimonial Details <button class="btn btn-primary pull-right btn-add-more-item btn-sm" type="button"><i class="fa fa-plus"></i> Add more</button></h5>
            <div class="item-details">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Name<span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" class="form-control name" name="name[0]">
                        <?php echo form_error('name[]'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Photo<span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="file" class="form-control photo input-file" name="photo[0]">
                        <?php
                        $config = $this->config->item('assets_images');
                        echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small>';
                        ?>
                        <?php echo form_error('photo[]'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Quote <span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <textarea class="form-control quote" rows="3" name="quote[0]"></textarea>
                        <?php echo form_error('quote[]'); ?>
                    </div>
                </div>
            </div>
        <?php }
        ?> 
    </div>
</div>