<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">

        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $subheading;?></h3>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form class="form-horizontal form-label-left" id="courseForm" method="post">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="title" value="<?php echo (!empty(set_value('title'))) ? set_value('title') : (isset($course_detail['title']) ? $course_detail['title'] : '');?>" name="title" class="form-control col-md-7 col-xs-12">
                                <?php echo form_error('title'); ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="course_id" class="control-label col-md-3 col-sm-3 col-xs-12">Organization <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="course_id" class="form-control col-md-7 col-xs-12" name="organizations_id">
                                    <option value="">---Select---</option>
                                            
                                    <?php if(!empty($organizations['result'])){ 
                                            foreach($organizations['result'] as $value){ 
                                               $organizations_id =  (!empty($course_detail)) ? $course_detail['organizations_id'] : set_value('organizations_id');?>
                                            <option <?php echo  $organizations_id;?> value="<?php echo $value['id'];?>" <?php echo ($organizations_id==$value['id']) ? 'selected' : '';?>><?php echo $value['title'];?></option>
                                    <?php } }
                                    ?>
                                </select>
                                <?php echo form_error('organizations_id'); ?> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="is_published">Is Published</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="checkbox" id="is_published" <?php echo (isset($course_detail['is_published']) && $course_detail['is_published']==1 || set_value('is_published')) ? 'checked' : '';?> name="is_published" />
                                <?php echo form_error('is_published'); ?>
                            </div>
                        </div>
                        <?php if(isset($course_detail['id'])){ ?>
                        <input type="hidden" name="course_id" value="<?php echo $course_detail['id'];?>">
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-xs-12 col-sm-9">
                                <button class="btn btn-primary">Save</button>
                                <button class="btn btn-default btn-reset" type="button">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>List of Courses</h3>
                     <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" id="courseList" data-src="<?php echo base_url().'course/get-courses-data';?>">
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Title</th>
                                    <th>Organization</th>
                                    <th>Is Published</th>
                                    <th>Unique Name</th>
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