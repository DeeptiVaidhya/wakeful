<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $subheading; ?></h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content add-more-container">
                    <form class="form-horizontal form-label-left" id="siteSettingForm" method="post" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title"><?php echo isset($setting_detail['title']) ? $setting_detail['title'] : 'Access Token' ?> <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="title" value="<?php echo (!empty(set_value('value'))) ? set_value('value') : (isset($setting_detail['value']) ? $setting_detail['value'] : ''); ?>" name="value" class="form-control col-md-7 col-xs-12 value">
                                <?php echo form_error('value'); ?>
                                <span class="value_div error"></span>
                            </div>
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