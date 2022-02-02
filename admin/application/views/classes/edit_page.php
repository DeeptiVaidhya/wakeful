<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Edit Page <small>in <b><?php echo $class_detail['title'] ?></b></small></h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-xs-12">
                            <form class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" id="editPageForm">

                                <div class="row">
                                    <div class="add-more-container col-xs-12">
                                        <?php echo (isset($page)) ? $page : ''; ?>  
                                    </div>
                                </div>
                                <div class="col-md-9 col-sm-9 col-md-offset-3 col-xs-12 col-sm-offset-3">
                                    <div class="form-group">
                                        <input type="hidden" value="<?php echo $course['id'] ?>"/>
                                        <input type="hidden" value="<?php echo (isset($class['id']) ? '/' . $class['id'] : '') ?>"/>
                                        <button class="btn btn-primary btn-save" value="Validate">Save</button>
                                        <button class="btn btn-default btn-reset" type="button">Reset</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <?php if (isset($previous['id']) && $previous['id']) :?>
                                            <a href="<?php echo base_url("classes/page/".strtolower($previous['page_type'])."/{$course['id']}/{$class_id}/{$previous['id']}")?>" class="btn btn-primary" name="button_previous">Previous</a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <?php if (isset($next['id']) && $next['id']) :?>
                                            <a href="<?php echo base_url("classes/page/".strtolower($next['page_type'])."/{$course['id']}/{$class_id}/{$next['id']}")?>" class="btn btn-primary" name="button_next">Next</a>
                                        <?php endif; ?>
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