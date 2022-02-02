<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Add Review</h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form class="form-horizontal form-label-left">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="title">Title <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="title" name="title" required="required" class="form-control col-md-7 col-xs-12">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="intro_text" class="control-label col-md-3 col-sm-3 col-xs-12">Intro Text <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <textarea id="intro_text" class="form-control col-md-7 col-xs-12" name="intro_text"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="button_text">Button Text <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="button_text" name="button_text" required="required" class="form-control col-md-7 col-xs-12">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="course_id" class="control-label col-md-3 col-sm-3 col-xs-12">Course <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="course_id" class="form-control col-md-7 col-xs-12" name="course_id">
                                    <option>Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="class_id" class="control-label col-md-3 col-sm-3 col-xs-12">Class <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="class_id" class="form-control col-md-7 col-xs-12" name="class_id">
                                    <option>Select</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div>
                            <div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">URL</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="button_text" name="button_text" required="required" class="form-control col-md-7 col-xs-12" value="http://imhere.com/assets/uploads/meditation.mp4" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Script <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="script" class="form-control col-md-7 col-xs-12" name="script"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Description <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="description" class="form-control col-md-7 col-xs-12" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">URL</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="button_text" name="button_text" required="required" class="form-control col-md-7 col-xs-12" value="http://imhere.com/assets/uploads/breathing.mp3" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Script <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="script" class="form-control col-md-7 col-xs-12" name="script"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Description <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="description" class="form-control col-md-7 col-xs-12" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-xs-12 col-sm-9">
                                <button class="btn btn-primary">Save</button>
                                <button class="btn btn-default">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>