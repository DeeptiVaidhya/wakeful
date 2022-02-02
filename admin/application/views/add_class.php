<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Add Class</h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- Smart Wizard -->
                    <div id="wizard" class="form_wizard wizard_horizontal">
                        <ul class="wizard_steps">
                            <li>
                                <a href="#step-1">
                                    <span class="step_no">1</span>
                                    <span class="step_descr">Class Info</span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-2">
                                    <span class="step_no">2</span>
                                    <span class="step_descr">Pages</span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-3">
                                    <span class="step_no">3</span>
                                    <span class="step_descr">Reorder Pages</span>
                                </a>
                            </li>
                        </ul>
                        <div id="step-1">
                            <form class="form-horizontal form-label-left">

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Title <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="first-name" required="required" class="form-control col-md-7 col-xs-12">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Duration <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="duration" class="form-control col-md-7 col-xs-12" type="text" name="duration">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Course <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select id="duration" class="form-control col-md-7 col-xs-12" type="text" name="duration">
                                            <option>Select</option>
                                        </select>

                                    </div>
                                </div>

                            </form>

                        </div>
                        <div id="step-2">
                            <div class="col-md-8 col-sm-8"> 
                                <form class="form-horizontal form-label-left">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Title<span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" required="required" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Image <span class="required">*</span>
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="file" id="exampleInputFile" class="input-file">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Header <span class="required">*</span></label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Content <span class="required">*</span></label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <textarea class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12">Button Text <span class="required">*</span></label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input class="form-control" type="text">
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="step-2-right">
                                    <h4>Add Page</h4>
                                    <ul>
                                        <li><a href="#" class="active">General</a></li>
                                        <li><a href="#">Practice Audio</a></li>
                                        <li><a href="#">Educational Video</a></li>
                                        <li><a href="#">Reflection Question</a></li>
                                        <li><a href="#">Topics</a></li>
                                        <li><a href="#">Testimonials</a></li>
                                    </ul>
                                </div>
                            </div>  
                        </div>
                        <div id="step-3">
                            <div class="col-md-5 col-sm-4 col-md-offset-3">
                                <div class="step-3-right">
                                    <h4>Sort and preview pages</h4>
                                    <ul>
                                        <li><a href="#" data-toggle="modal" data-target="#reorder_popup">General <i class="fa fa-exchange fa-rotate-90"></i></a></li>
                                        <li><a href="#">Practice Audio <i class="fa fa-exchange fa-rotate-90"></i></a></li>
                                        <li><a href="#">Educational Video <i class="fa fa-exchange fa-rotate-90"></i></a></li>
                                        <li><a href="#">Reflection Question <i class="fa fa-exchange fa-rotate-90"></i></a></li>
                                        <li><a href="#">Topics <i class="fa fa-exchange fa-rotate-90"></i></a></li>
                                        <li><a href="#">Testimonials <i class="fa fa-exchange fa-rotate-90"></i></a></li>
                                    </ul>
                                </div>
                            </div>


                        </div>

                    </div>
                    <!-- End SmartWizard Content -->
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- start popup -->
    <div class="modal fade" id="reorder_popup" role="dialog" aria-labelledby="exampleModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="exampleModalLabel">General</h4>
          </div>
          <div class="modal-body">
            <div class="row reorder-popup">
              <div class="col-md-2 col-sm-3 col-xs-12"><h4>Title:</h4></div>
              <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black">Class 1 Welcome</h4></div>
              <div class="col-md-2 col-sm-3 col-xs-12"><h4> Image:</h4></div>
              <div class="col-md-10 col-sm-9 col-xs-12"><img class="popup-cont-img" src="<?php echo assets_url('images/logo.png')?>"></div>
              <div class="col-md-2 col-sm-3 col-xs-12"><h4>Header:</h4></div>
              <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black">Welcome to Class 1</h4></div>
              <div class="col-md-2 col-sm-3 col-xs-12"><h4>Content:</h4></div>
              <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black">This class will include various exercises to help you step off of automatic pilot and engage in the practice of mindfulness.</h4></div>
              <div class="col-md-2 col-sm-3 col-xs-12"><h4>Button Text:</h4></div>
              <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black">Lets Get Started</h4></div> 
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-sm">Edit</button>
            <button type="button" class="btn btn-danger btn-sm">Delete</button>
          </div>
        </div>
      </div>
    </div>
    <!-- end popup -->
