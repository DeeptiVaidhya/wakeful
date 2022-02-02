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
                    <form class="form-horizontal form-label-left" id="organizationForm" method="post">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="title" name="title" value="<?php echo (!empty($organization_detail)) ? $organization_detail['title'] : set_value('title');?>" class="form-control col-md-7 col-xs-12">
                                <?php echo form_error('title'); ?>
                            </div>
                        </div>
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
                    <h3>List of Organizations</h3>
                     <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive data-table" id="organizationList" data-src="<?php echo base_url().'organization/get-organizations-data';?>">
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Title</th>
                                    <!-- <th>Slug</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                        
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
