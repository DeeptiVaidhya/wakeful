<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="<?php echo assets_url('images/favicon.ico'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Wakeful Admin Login</title>

        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/toastr.css'); ?>"/>


        <!-- Custom Theme Style -->
        <link rel="stylesheet" href="<?php echo assets_url('css/theme.css'); ?>"/> 
        <script src="<?php echo assets_url('js/jquery-3.4.1.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/toastr.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/jquery.validate.js'); ?>"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                toastr.options = {"closeButton": true}
                var success = '<?php echo $this->session->flashdata("success"); ?>';
                var error = '<?php echo $this->session->flashdata("error"); ?>';
                if (success != '') {
                    toastr.success(success);
                } else if (error != '') {
                    toastr.error(error);
                }


                $("#resetPasswordForm").validate({
                    rules: {
                        password:{required: true},
                        confirm_password: {
                            equalTo: "#password",
                            required:true 
                        }
                    }
                });
            });

        </script>
    </head>
    <style>
        /*start login*/

        .login-page{
            margin: 35px auto;
            width: 400px;
        }
        .login-bg{
            background-color: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 4px;
            padding: 0 10px 20px;
        }
        .login-page .col-md-12.form-group.has-feedback{padding-bottom: 10px;}
        .login-logo{
            text-align: center;
            padding-bottom: 20px;
        }
        .login-logo img{width: 85px;}
        .login-bg h3{
            color: #8d8d8d;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        /*end login*/

    </style>
    <body>
        <div class="login-page">
            <div class="login-logo">
                <img src="<?php echo assets_url('images/full_logo.png') ?>">
            </div>
            <div class="row login-bg">
                <div><h3 class="text-center"><?php echo $heading; ?></h3></div>
                <form class="form-horizontal form-label-left input_mask" method="post" action="" id="resetPasswordForm">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                        <input class="form-control has-feedback-left" type="Password" placeholder="Password" name="password" id="password">
                        <span aria-hidden="true" class="fa fa-lock form-control-feedback left"></span>
                        <?php echo form_error('password'); ?>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                        <input class="form-control has-feedback-left" type="Password" placeholder="Confirm Password" name="confirm_password">
                        <span aria-hidden="true" class="fa fa-lock form-control-feedback left"></span>
                        <?php echo form_error('confirm_password'); ?>
                    </div>
                    <input type="hidden" name="forgotten_code" value="<?php echo $code; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                    <div class="clerfix"></div>
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <button class="btn btn-primary btn-block m-b-15" type="submit">Submit</button>
                        <a href="<?php echo base_url() . 'auth/login'; ?>" type="button">Sign in</a>
                    </div>
                </form>    
            </div>    
        </div>

    </body>
</html>
