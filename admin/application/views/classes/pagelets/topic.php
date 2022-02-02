<div class="im-tab-content active">
    <h5>Topics</h5>

    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Section<span class="required">*</span>
        </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input type="text" name="title" class="form-control" value="<?php echo (isset($page_data)) ? $page_data['title'] : set_value('title'); ?>">
            <?php echo form_error('title'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Intro Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <textarea class="form-control" rows="3" name="intro_text"><?php echo (isset($page_data)) ? $page_data['intro_text'] : set_value('intro_text'); ?></textarea>
            <?php echo form_error('intro_text'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Button Text <span class="required">*</span></label>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <input class="form-control" type="text" name="button_text" value="<?php echo (isset($page_data)) ? $page_data['button_text'] : set_value('button_text'); ?>"/>
            <?php echo form_error('button_text'); ?>
        </div>
    </div>
    <input type="hidden" name="action" value="topic">
    <input type="hidden" name="current_id" value="<?php echo (isset($page_data)) ? $page_data['id'] : set_value('id'); ?>">
    <hr/>
    <div class="add-more-items well">
        <?php
        if (isset($page_data['sub_details']) && !empty($page_data['sub_details'])) {
            $count = 0;
            $delete_url=base_url().'classes/delete-sub-page';
            foreach ($page_data['sub_details'] as $value) {
                ?>
                <?php if (!$count) { ?> 
                    <h5>Topic Details  <button class="btn btn-primary pull-right btn-add-more-item btn-sm" type="button"><i class="fa fa-plus"></i> Add more</button></h5>
                <?php } ?>
                <div class="item-details">
                    <?php if ($count > 0) { ?>
                        <button data-msg="topic" data-params='<?php echo json_encode(array('id'=>$value['id'],'page_type'=>'topic'));?>' data-url="<?php echo $delete_url;?>" class='pull-right btn btn-danger btn-remove-item btn-sm' type='button'><i class='fa fa-trash'></i> Remove</button>
                    <?php } ?>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Topic Title<span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" class="form-control topic_title" name="topic_title[<?php echo $count; ?>]" value="<?php echo $value['topic_title'] ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Topic Color <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input type="text" class="select-color form-control col-md-7 col-xs-12 topic_color" name="topic_color[<?php echo $count; ?>]" value="<?php echo $value['topic_color'] ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Topic Text</label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <textarea class="form-control col-md-7 col-xs-12 topic_text" rows="3" name="topic_text[<?php echo $count; ?>]"><?php echo $value['topic_text'] ?></textarea>
                        </div>
                        <input type="hidden" name="sub_id[<?php echo $count; ?>]" value="<?php echo $value['id'];?>">
                    </div>
                </div>

                <?php
                $count++;
            }
        } else {
            ?>
             <h5>Topic Details  <button class="btn btn-primary pull-right btn-add-more-item btn-sm" type="button"><i class="fa fa-plus"></i> Add more</button></h5>        
            <div class="item-details">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Topic Title<span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" class="form-control topic_title" name="topic_title[0]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Topic Color <span class="required">*</span></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input type="text" class="select-color form-control col-md-7 col-xs-12 topic_color" name="topic_color[0]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Topic Text</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <textarea class="form-control col-md-7 col-xs-12 topic_text" rows="3" name="topic_text[0]"></textarea>
                    </div>
                </div>
            </div>
        <?php }
        ?> 
    </div>

</div>