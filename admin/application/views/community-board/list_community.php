<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>
                        List of Community Board 
                    </h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped data-table" id="communityList" data-opts='{"sAjaxSource":"<?php echo base_url().'dashboard/get-community-data/'.$course_id.'/'.$study_id;?>","iDisplayLength": 20,"order": [[ 0, "desc" ]], "aLengthMenu": [[20, 50, 100, -1],[20, 50, 100, "All"]]}'>
                            <thead>
                                <tr>
                                    <th width="5%">Sno.</th>
                                    <th width="10%">Date/Time</th>
                                    <th width="10%">Unique ID</th>
                                    <th width="50%">Post Message</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>             
                    <!-- end table -->

                </div>
            </div>
        </div>
    </div>
</div>
