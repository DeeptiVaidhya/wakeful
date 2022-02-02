<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $heading;?> <small>in <b><?php echo $course['title'] ?></b></small></h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-xs-12">
                            <form class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" id="classForm">
                                <h4>Class Info <small>class title for showing in APP</small></h4>
                                <div class="row">
                                    <div class="col-md-8 col-sm-8 col-xs-10 im-tab">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Class Title<span class="required">*</span>
                                            </label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <input data-validation="required"  type="text" class="form-control" name="class_title" value="<?php echo ($class) ? $class['title'] : set_value('class_title'); ?>" />
                                                <?php echo form_error('class_title'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($is_edit){ ?>
                                    <div class="col-md-2 col-sm-6 col-xs-2">
                                        <button type="button" class="btn btn-primary btn-sm" id="update_class" data-id="<?php echo $class['id'];?>" data-url="<?php echo base_url().'classes/update_class';?>">Update</button> 
                                    </div>
                                    <?php } ?>

                                    <div class="col-md-8 col-sm-8 col-xs-10 im-tab">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Tile Image
                                            </label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">                                              
                                                <input class="input-file filebox" type='file' name='tile_image' id='tile_image'>
                                                <?php $config = $this->config->item('assets_images'); 
                                                  echo '<p><small>Allowed type ( '.str_replace('|',', ', $config['allowed_types']).' )</small>';
                                                  ?>
                                                <?php echo form_error('tile_image'); ?>
                                            </div>
                                        </div>
                                        <?php 
                                        if (isset($class['tile_image']) && $class['tile_image'] != '') { ?>
                                        <input type="hidden" name="previous_tile_file_id" value="<?php echo $class['tile_image'];?>">                                            
                                        <div class="form-group">
                                            <div class="col-md-3 col-md-offset-3 col-sm-3 col-sm-offset-3 col-xs-12">
                                                <div class="class_tile_img">
                                                    <div id="crop-avatar">
                                                           <?php echo get_file($class['tile_image']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        </div>
                                        <?php } ?>
                                    </div>

                                     <?php if($is_edit_img){ ?>
                                        <div class="col-md-2 col-sm-6 col-xs-2">
                                           <button class="btn btn-primary btn-sm" type="button" id="update_image" data-id="<?php echo $class['tile_image'];?>" class-id="<?php echo $class['id'];?>"  data-url="<?php echo base_url().'classes/update_image';?>"><i class="fa fa-upload" ></i></button> 
                                        </div>
                                    <?php } ?>
                               
                                </div>

                                <h4>Add Page <small>add a new page</small></h4>
                                <div class="row">
                                    <div class="col-md-8 col-sm-8 col-xs-10 im-tab page_content_div add-more-container">
                                        <?php echo (isset($page)) ? $page : ''; ?>  
                                    </div>
                                    <div class="col-md-offset-1 col-md-3 col-sm-4 col-xs-2">
                                        <div class="step-2-right">
                                            <h4><i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;Add Page</span></h4>
                                            <div class="list-group" data-course-id="<?php echo $course['id'];?>">
                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'general') ? 'active' : ''; ?>" data-type="general">
                                                    <i class="fa fa-folder"></i><span class="hidden-xs">General</span>
                                                </a>
                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'audio') ? 'active' : ''; ?>" data-type="audio">
                                                    <i class="fa fa-music"></i><span class="hidden-xs">Practice Audio</span>
                                                </a>

                                                 <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'podcast') ? 'active' : ''; ?>" data-type="podcast">
                                                    <i class="fa fa-podcast"></i><span class="hidden-xs">Podcast</span>
                                                </a>

                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'video') ? 'active' : ''; ?>" data-type="video">
                                                    <i class="fa fa-play-circle-o"></i><span class="hidden-xs">Educational Video</span>
                                                </a>
                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'question') ? 'active' : ''; ?>" data-type="question">
                                                    <i class="fa fa-question-circle"></i><span class="hidden-xs">Reflection Question</span>
                                                </a>
                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'topic') ? 'active' : ''; ?>" data-type="topic">
                                                    <i class="fa fa-tasks"></i><span class="hidden-xs">Topics</span>
                                                </a>
                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'testimonial') ? 'active' : ''; ?>" data-type="testimonial">
                                                    <i class="fa fa-address-card"></i><span class="hidden-xs">Testimonials</span>
                                                </a>
                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'intention') ? 'active' : ''; ?>" data-type="intention">
                                                    <i class="fa fa-info-circle"></i><span class="hidden-xs">Intention</span>
                                                </a>
                                                <a href="javascript:void(0)" class="list-group-item <?php echo ($type == 'numbered_general') ? 'active' : ''; ?>" data-type="numbered_general">
                                                    <i class="fa fa-info-circle"></i><span class="hidden-xs">Numbered General</span>
                                                </a>
                                            </div>

                                        </div>
                                    </div>  
                                </div>
                                <div class="col-md-10 col-sm-10 col-md-offset-2 col-xs-12 col-sm-offset-2">
                                    <div class="form-group">
                                        <input type="hidden" value="<?php echo $course['id'] ?>"/>
                                        <input type="hidden" value="<?php echo (isset($class['id']) ? '/' . $class['id'] : '') ?>"/>
                                        <button class="btn btn-primary btn-save" value="Validate"   >Save</button>
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