<div class="">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
           <!--  <li role="presentation" class="active"><a href="javascript:void(0)">Exercise</a>
            </li> -->
           <!--  <li role="presentation" class=""><a href="<?php // echo base_url() . 'homework/podcast/' . $course['id'] . '/' . $class_detail['id']; ?>">Podcast</a>
            </li> -->
            <li role="presentation" class=""><a href="<?php echo base_url() . 'homework/reading/' . $course['id'] . '/' . $class_detail['id']; ?>">Reading</a>
            </li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h3>
                                    <?php echo (isset($exercise_detail['exercise_data']) && !empty($exercise_detail['exercise_data'])) ? 'Edit' : 'Add'; ?> Exercise <small>in <b><?php echo $class_detail['title'] . ' (' . $course['title'] . ')'; ?></b></small>
                                </h3>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="row">
                                    <form class="form-horizontal form-label-left"  method="post" id="homework_exercise" enctype="multipart/form-data" name="homework_exercise">
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" name="title" class="form-control col-md-7 col-xs-12" value="<?php echo (isset($exercise_detail)) ? $exercise_detail['title'] : set_value('title'); ?>">
                                                <?php echo form_error('title'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="intro_text" class="control-label col-md-3 col-sm-3 col-xs-12">Intro Text <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <textarea class="form-control col-md-7 col-xs-12" name="intro_text"><?php echo (isset($exercise_detail)) ? $exercise_detail['intro_text'] : set_value('intro_text'); ?></textarea>
                                                <?php echo form_error('intro_text'); ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Practice Category</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <select class="input-file" name="practice_categories_id">
                                                    <option value="">----select-----</option>
                                                    <?php ?>
                                                    <?php foreach ($practice_category as $val) { ?>
                                                        <option value="<?php echo $val['id']; ?>" <?php echo (isset($category_detail) && isset($category_detail['practice_categories_id']) && $category_detail['practice_categories_id'] == $val['id']) ? 'selected' : ''; ?>><?php echo $val['label']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <input type="hidden" name="privious_category_id" value="<?php echo isset($category_detail['practice_categories_id']) ? $category_detail['practice_categories_id']:''; ?>">

                                            </div>
                                        </div>


                                        <?php if (isset($course_homework_exercise) && !empty($course_homework_exercise)) {
                                            ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>Homework Details</h5>
                                                </div>
                                            </div>
                                        
                                        <div class="col-md-12">
											<?php
											
                                            // print_r($course_homework_exercise);
                                            foreach ($course_homework_exercise as $key => $value) {
                                               
                                                if(isset($course_homework) && in_array($value['id'],$course_homework)){
                                                    $checked = " checked";
                                                }else{
                                                    $checked = "";
												}
												
												if(isset($course_homework) && $value['is_meditation_practice'] == 1){
                                                    $pChecked = " checked";
                                                }else{
                                                    $pChecked = "";
                                                }
                                                ?>
                                                <div class="well">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Title</label>

                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <textarea name="homework_title" class="form-control col-md-7 col-xs-12" readonly><?php echo $value['title'] ?></textarea>
                                                        </div>
                                                    </div> 
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Tip</label>

                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <textarea name="homework_tip" class="form-control col-md-7 col-xs-12" readonly><?php echo $value['tip'] ?></textarea>
                                                        </div>
                                                    </div> 
                                                    <?php
                                                    $file = isset($value['files_id']) ? get_file($value['files_id']) : '';
                                                    if ($file) {
                                                        ?>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Script</label>

                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <div class="col-xs-12 col-md-8"><h5 class="c-black title"><?php echo $file; ?></h5></div>
                                                            </div>
                                                        </div>
                                                    <?php } ?> 
                                                    <div class="form-group">
                                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3"> 
														<label class="control-label">
														<input type="checkbox" name="homework_id[]" value="<?php echo $value['id'] ?>" <?php echo $checked; ?> />
                                                            Add homework to exercise</label>
                                                        </div>
													</div> 
													<div class="form-group">
                                                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 col-sm-offset-3"> 
														<label class="control-label">
														<input type="checkbox" name="practice_file_id[<?php echo $value['id']; ?>]" <?php echo $pChecked?> value="1" />
														Included in Practice page</label>
                                                        </div>
													</div> 
													<div class="form-group">
													<label class="control-label col-md-3 col-sm-3 col-xs-12">Practice Title</label>

													<div class="col-md-6 col-sm-6 col-xs-12">
														<input name="practice_title[<?php echo $value['id']; ?>]" placeholder="Practice Title" value="<?php echo $value['meditation_practice_title']; ?>"  class="form-control col-md-7 col-xs-12">
													</div>
                                                    </div> 
                                                </div>
                                            <?php } ?> 
                                            <?php } else { ?>
                                                 <div class="row well">
                                                <div class="col-md-12 text-center">
                                                    <h5>There is no homework exercise in this Course. Click on link for add homework exercise 
                                                    <a href="<?php echo base_url() . 'course/add-homework-exercise/' . $course['id']; ?>">Add Homework Excercise</a></h5>
                                                </div>
                                            </div>
                                            <?php  }?>
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
        </div>
    </div>
</div>
</div>
