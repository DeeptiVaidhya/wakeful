<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3 class="">Participant Detail</h3>
                    <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('user/edit-participant/'.$course_id.'/'.$study_id.'/'.$details['id']) ?>">Edit</a>
                    <!-- <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul> -->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form class="form-horizontal form-label-left" name="addUserForm" id="progress" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Unique Id :</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                               <span class="text"> <?php echo  isset($details['unique_id']) ? $details['unique_id'] : ''; ?></span>
                            </div>                            
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Username :</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                               <span class="text"> <?php echo  isset($details['username']) ? $details['username'] : ''; ?></span>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Email :</label>
                            <div class="col-md-9 col-sm-9 col-xs-12 participant_email">
                                <span><?php echo  isset($details['email']) ? $details['email'] : ''; ?></span>
								<?php //if(is_null($details['password']) && is_null($details['salt'])){ ?>
									<!-- <a class="btn btn-primary btn-sm edit_email" href="javascript:void(0)">Edit</a> -->
								<?php //} ?>
                            </div>
							<div class="col-md-9 col-sm-9 col-xs-12 participant_email_update">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="email" name="email" class="form-control" value="<?php echo  isset($details['email']) ? $details['email'] : ''; ?>">
								</div>
								<div class="col-md-3">
									<button id="submit-button" class="btn btn-primary btn-save">Update</button>
								</div>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Registration Date :</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                               <span class="text"> <?php echo  (!is_null($details['registered_at']) && !is_null($details['is_authorized']) &&  !is_null($details['is_active'])) ? date("m/d/Y", strtotime($details['registered_at'])) : date("m/d/Y", strtotime($details['created_at'])); ?></span>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Active :</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                               <span> <?php echo  isset($details['is_active']) ? 'Yes' : 'No' ?></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
           <div class="col-xs-12 link-bg">
				  <ul class="profile-tab">
				  	<li> <a class="small-link text-left"  href="<?php echo base_url() . "user/participants-detail/" . $details['id'] . "/progress"; ?>">Progress</a></li>
					  <li> <a class="small-link "  href="<?php echo base_url() . "user/participants-detail/" . $details['id'] . "/avaccess"; ?>">A/V Access</a>	</li>
					  <li> <a class="small-link dropdown-toggle"  data-toggle="dropdown" href="javascript:void(0)">Homework</a>
					  <ul class="dropdown-menu homework-dropdown">
							<li> <a href="<?php echo base_url() . "user/participants-detail/" . $details['id'] . "/homework/exercise"; ?>">Exercise</a></li>
							<!-- <li> <a href="<?php echo base_url() . "user/participants-detail/" . $details['id'] . "/homework/podcast"; ?>">Podcast</a>	</li> -->
							<li> <a href="<?php echo base_url() . "user/participants-detail/" . $details['id'] . "/homework/reading"; ?>">Reading</a>	</li>
						</ul>
						</li>
					  <li> <a class="small-link "  href="<?php echo base_url() . "user/participants-detail/" . $details['id'] . "/meditation"; ?>">Meditation</a>	</li>
				  </ul>
            </div>
            <?php
				if (isset($type)) {
					if (isset($type['progress'])) {
						echo $this->template->partial->view('users/progress', $type);
					} elseif (isset($type['avaccess'])) {
						echo $this->template->partial->view('users/avaccess', $type);
					} elseif (isset($type['homework'])) {
						echo $this->template->partial->view('users/homework', $type);
					} elseif (isset($type['meditation'])) { 
						echo $this->template->partial->view('users/meditation', $type);	
					}
				}
			?>
        </div>
    </div>
</div>

