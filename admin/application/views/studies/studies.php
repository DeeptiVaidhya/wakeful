<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">
        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>List of Studies</h3>
                    <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('study/add-study') ?>">Add Study</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" id="studyList" data-src="<?php echo base_url() . 'study/get-studies-data'; ?>">
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Title</th>
                                    <!-- <th>URL</th> -->
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
