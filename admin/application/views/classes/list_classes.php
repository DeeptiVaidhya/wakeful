<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>
                        Classes <small>in <b><?php echo $course_detail['result'][0]['title']?></b></small>
                    </h3>
                        <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('classes/add-class/'.$cid)?>">Add Class</a>
                    
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped data-table" data-reorder-url="classes/reorder-classes" data-opts='{"columnDefs": [{ "orderable": false, "targets": 0 },{ "orderable": false, "targets": "_all" }],"rowReorder":{"selector":".row-move"},"aaData":null,"bServerSide":false,"paging":false,"searching": false}' data-classes_id="<?php echo $course_detail['result'][0]['id']?>" data-course_id="<?php echo $cid ?>">
                            <thead>
                                <tr>
                                    <th>Sno.</th>
                                    <th>Title</th>
                                    <!--<th>Duration (in days)</th>-->
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(!empty($classes['result'])){
                                    $classes = $classes['result'];
                                    $sno=1;$tr='';
                                    $base=base_url();
                                    foreach($classes as $cls){
                                        $tr.='<tr><td>'.$sno++.'</td>
                                                <td>'.$cls['title'].'</td>
                                                <td><input type="checkbox" '.($cls['is_active']==1?'checked':'').' data-params=\'{"cid":"'.$cls['id'].'"}\' class="switch_btn" data-url="'.$base.'classes/change-status"/></td>
                                                <td>
                                                <a class="row-move" data-page_pos=" '. $cls['position'] . '" title="Move"><i class="fa fa-arrows"></i></a>
                                                <a class="btn btn-primary btn-xs" title="Edit" href="'.$base.'classes/add-class/'.$cid.'/'.$cls['id'].'"><i class="fa fa-pencil"></i>Edit</a>'
                                                . '<a class="btn btn-primary btn-xs" title="Pages" href="'.$base.'classes/list-pages/'.$cid.'/'.$cls['id'].'"><i class="fa fa-file-text"></i>Pages</a>'
                                                // . '<a class="btn btn-primary btn-xs" title="Review" href="'.$base.'review/review-detail/'.$cid.'/'.$cls['id'].'"><i class="fa fa-clipboard"></i>Review</a>'
                                                . '<a class="btn btn-primary btn-xs" title="Readings" href="'.$base.'homework/reading/'.$cid.'/'.$cls['id'].'"><i class="fa fa-book"></i>Readings</a>'
                                                . "<input type='hidden' value='" . $cls['id'] . "' name='page_id[]' /></td>". '</td>';
                                        
                                    }
                                } else {
                                    $tr = '<tr><td>No Records Found</td></tr>';
                                }
                                echo $tr;
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->

                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- start popup -->
<div class="modal fade" id="edit_user" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Class 1</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 col-xs-6">
                        <div class="classes-bg">
                            <span><i class="fa fa-folder"></i></span>
                            <p>General</p>
                        </div>
                        
                    </div>
                    <div class="col-md-4 col-xs-6">
                        <div class="classes-bg">
                            <span><i class="fa fa-music"></i></span>
                            <p>Audio</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6">
                        <div class="classes-bg">
                            <span><i class="fa fa-play-circle-o"></i></span>
                            <p>Video</p>
                        </div>
                    </div>

                    <div class="col-md-4 col-xs-6">
                        <div class="classes-bg">
                            <span><i class="fa fa-tasks"></i></span>
                            <p>Topics</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6">
                        <div class="classes-bg">
                            <span><i class="fa fa-address-card"></i></span>
                            <p>Testimonials</p>
                        </div>
                    </div>

                </div>
            
                
            </div>
  
        </div>
    </div>
</div>

