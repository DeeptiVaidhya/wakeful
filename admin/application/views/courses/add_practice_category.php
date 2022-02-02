<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $subheading; ?></h3>
                    <div class="clearfix"></div>
                </div>
                
                <div class="x_content add-more-container">
                    <form class="form-horizontal form-label-left" id="practiceCategory" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Label<span class="required">*</span>
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <textarea name="label" class="form-control" ><?php echo (isset($edit_category)) ? $edit_category['label'] : set_value('label'); ?></textarea>
                                <?php echo form_error('label'); ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Image 
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="file" class="input-file filebox" name="category_image" />
                                <?php $config = $this->config->item('assets_images'); 
                                  echo '<p><small>Allowed type ( '.str_replace('|',', ', $config['allowed_types']).' )</small>';
                                  ?>
                                <?php echo form_error('category_image'); ?>
                            </div>
                        </div>

                        <?php if (isset($edit_category) && $edit_category['image_name'] != '') { ?>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <?php echo get_file($edit_category['image_name']) ?>
                                </div>
                                <input type="hidden" name="previous_cat_file_id" value="<?php echo $edit_category['image_name']; ?>">
                            </div>
                        <?php } ?>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-xs-12 col-sm-9">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                        <button class="btn btn-primary btn-save">Save</button>
                        <button class="btn btn-default btn-reset" type="button">Reset</button>
                    </div>
                </div>

                    </form>
                </div>
            </div>
           <div class="x_panel">
                <div class="x_title">
                    <h3>List of Practice Category</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                 <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" id="courseList" data-src="<?php echo base_url().'course/get-course-practice-category-data/'.$category_detail['courses_id'];?>">
                            <thead>
                                 <tr>
                                    <th width="5%">S.No.</th>
                                    <th>Label</th>
                                    <th>Action</th> 
                                </tr>
                            </thead>
                        </table>
                    </div> 
                </div>
            </div> 
        </div>
    </div>
</div>

<!-- start popup -->
<div class="modal fade" id="view_homework_excercise" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Homework Excercise</h4>
            </div>
            <div class="modal-body">
            </div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>