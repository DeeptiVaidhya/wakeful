<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Pages <small>in <b><?php echo $class_detail['title']; ?></b></small></h3>
                    <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('classes/add-class/'.$course['id'].'/'.$class_detail['id'])?>">Add Page</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered data-table" data-reorder-url="classes/reorder-pages" data-opts='{"columnDefs": [{ "orderable": false, "targets": 0 },{ "orderable": false, "targets": "_all" }],"rowReorder":{"selector":".row-move"},"aaData":null,"bServerSide":false,"paging":false,"searching": false}' data-classes_id="<?php echo $class_detail['id']?>" data-course_id="<?php echo $course['id']?>">
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Section</th>
                                    <th>Header</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tr = '<tr><td colspan="5">No Page found</td></tr>';
                                if (isset($pages) && !empty($pages)) {
                                    $tr = '';
                                    $view_url = base_url() . 'classes/view-page';
                                    
                                    foreach ($pages as $key => $page) {
                                        $ptype = strtolower($page['page_type']);
                                        $pheader = (isset($page['page_data']['header'])) ? $page['page_data']['header'] : 'N/A';
                                        $section = strtoupper($page['title']);

                                        $tr .= "<tr>"
                                                . "<td>" . ($key + 1) . "</td>"
                                                . "<td>{$section}</td>"
                                                . "<td>{$pheader}</td>"
                                                . "<td>" . ucfirst($ptype) . "</td>"
                                                . "<td><a href='" . base_url("classes/page/{$ptype}/{$course['id']}/{$page['classes_id']}/{$page['id']}") . "' title='Edit'><i class='fa fa-edit'></i></a>"
                                                . "<a class='page-view' href='#' title='Preview' data-type='" . $page['page_type'] . "' data-toggle='modal' data-url='" . $view_url . "' data-target='#view_popup' data-params='" . json_encode(array('type' => $ptype, 'page_id' => $page['id'], 'class_id' => $page['classes_id'])) . "'><i class='fa fa-eye'></i></a>"
                                                . "<a class='row-move' data-page_pos='" . $page['position'] . "' title='Move'><i class='fa fa-arrows'></i></a>"
                                                . "<a href='javascript:void(0)' class='delete' data-msg='page' data-url='" . base_url("classes/delete-page/{$ptype}/{$course['id']}/{$page['classes_id']}/{$page['id']}") . "' title='Delete'><i class='fa fa-trash'></i></a>"
                                                . "<input type='hidden' value='" . $page['id'] . "' name='page_id[]' /></td>"
                                                . "</tr>";
                                    }
                                }
                                echo $tr;
                                ?>

                            </tbody>
                        </table>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
<!-- start popup -->
<div class="modal fade" id="view_popup" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog page-content-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">General</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2 col-sm-3 col-xs-12"><h4>Title:</h4></div>
                    <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black title">Class 1 Welcome</h4></div>
                    <div class="col-md-2 col-sm-3 col-xs-12"><h4> Image:</h4></div>
                    <div class="col-md-10 col-sm-9 col-xs-12"><img class="popup-cont-img" src="<?php echo assets_url('images/logo.png') ?>"></div>
                    <div class="col-md-2 col-sm-3 col-xs-12"><h4>Header:</h4></div>
                    <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black header">Welcome to Class 1</h4></div>
                    <div class="col-md-2 col-sm-3 col-xs-12"><h4>Content:</h4></div>
                    <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black">This class will include various exercises to help you step off of automatic pilot and engage in the practice of mindfulness.</h4></div>
                    <div class="col-md-2 col-sm-3 col-xs-12"><h4>Button Text:</h4></div>
                    <div class="col-md-10 col-sm-9 col-xs-12"><h4 class="c-black">Lets Get Started</h4></div> 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- end popup -->