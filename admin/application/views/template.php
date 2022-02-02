<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $this->template->title->default("Wakeful"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="<?php echo assets_url('images/favicon.ico'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo $this->template->description; ?>">
        <meta name="author" content="">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/toastr.css'); ?>"/>
        <!--<link rel="stylesheet" href="<?php // echo assets_url('css/admin-template.css'); ?>"/ >-->
        <!--<link rel="stylesheet" href="<?php // echo assets_url('css/admin.css'); ?>"/ >-->
        <!--<link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">-->
        <!-- Font Awesome -->
        <!--<link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">-->
        <!-- NProgress -->
        <!--<link href="../vendors/nprogress/nprogress.css" rel="stylesheet">-->
        <!-- Theme Style -->
        <link href="<?php echo assets_url('css/theme.css')?>" rel="stylesheet"/>
        <link href="<?php echo assets_url('css/admin.css')?>" rel="stylesheet"/>
        
        
        <?php echo $this->template->meta; ?>
        <?php echo $this->template->stylesheet; ?>
    </head>
    <body class="nav-md">
        <div class="main_container">
            <div class="container body">
                <div class="col-md-3 left_col">
                    <?php 
                        // This is an example to show that you can load stuff from inside the template file
                        echo $this->template->widget("leftsection", array('base_url' => base_url()));
                    ?>
                </div>
                <div class="top_nav">
                    <?php 
                        // This is an example to show that you can load stuff from inside the template file
                        echo $this->template->widget("header", isset($breadcrumb) ? $breadcrumb : '');
                    ?>
                </div>
                <div class="right_col" role="main">
                    <?php
                        // This is the main content partial
                        echo $this->template->content;
                    ?>
                </div>
                <footer>

                    <p>
                        <?php 
                            // Show the footer partial, and prepend copyright message
                            //echo $this->template->footer->prepend("All rights reserved ");
                            //&copy;

                        ?>
                        All rights reserved - Wakeful &copy; <?php echo date('Y');?>
                    </p>
                </footer>

            </div>
        </div>
        
        <script>var BASE_URL="<?php echo base_url();?>";</script>
        <script src="<?php echo assets_url('js/jquery-3.4.1.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/theme.js'); ?>"></script>
        <script src="<?php echo assets_url('js/admin.js'); ?>"></script>
        <script src="<?php echo assets_url('js/toastr.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootbox.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/jquery.validate.js'); ?>"></script>
        <script src="<?php echo assets_url('js/form_validation.js'); ?>"></script>
        
            
        <script type="text/javascript">
            $(document).ready(function(){
                toastr.options = {closeButton: true}
                var success = '<?php echo $this->session->flashdata("success");?>',
                    error = '<?php echo $this->session->flashdata("error");?>';
                if(success!=''){
                    toastr.success(success);
                } else if(error!=''){
                    toastr.error(error); 
                }
            });
             
        </script>
        <?php echo $this->template->javascript; ?>
    </body>
</html>