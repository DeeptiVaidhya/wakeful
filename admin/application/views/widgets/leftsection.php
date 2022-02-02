
<div class="left_col scroll-view">
    <div class="navbar nav_title">
        <a href="<?php echo $base_url ?>" class="site_title"><img src="<?php echo assets_url('images/logo.png') ?>" style="margin-left: 10px; width: 35px;"> <span>Wakeful</span></a>
    </div>

    <div class="clearfix"></div>
    <?php $login_user_detail = $this->session->userdata('logged_in'); 
    ?>

    <!-- menu profile quick info -->
    <div class="profile clearfix">
        <div class="profile_pic">
            <!-- <?php $profile_image = (empty($login_user_detail) || $login_user_detail->profile_picture == '') ? 'assets/images/default-avatar.png' : 'assets/uploads/images/' . $login_user_detail->profile_picture ?>
            <img src="<?php echo base_url().$profile_image; ?>" alt="..." class="img-circle profile_img"> -->
        </div>
        <div class="profile_info">
            <span>Welcome,</span>
            <?php if(!empty($login_user_detail)){ ?>
            <h2><?php echo ucwords($login_user_detail->first_name . ' ' . $login_user_detail->last_name); ?></h2>
            <?php } ?>
        </div>
    </div>
    <!-- /menu profile quick info -->

    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <div class="menu_section">
            <ul class="nav side-menu">
            <?php if($login_user_detail->user_type == 2) {?>
                <li><a href="<?php echo $base_url ?>course"><i class="fa fa-graduation-cap"></i> Courses</a></li>
                <li><a href="<?php echo $base_url ?>study"><i class="fa fa-wpforms"></i> Studies</a></li>
                <?php } ?> 
                <li><a href="<?php echo $base_url ?>user/list-admin"><i class="fa fa-user-circle-o"></i>Admins</a></li>
                <?php if($login_user_detail->user_type == 1) {?>
                <li><a href="<?php echo $base_url ?>organization"><i class="fa fa-building-o"></i> Organization</a></li>
                <li><a href="<?php echo $base_url ?>course/feedback"><i class="fa fa-comments-o"></i> Feedback</a></li>
                <?php }?>
                <li><a href="<?php echo $base_url ?>auth/profile"><i class="fa fa-address-card"></i> Profile</a></li>
                <li><a href="<?php echo $base_url ?>auth/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- /sidebar menu -->
</div>
