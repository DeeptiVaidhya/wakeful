<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$CI = & get_instance();

$config = array(
    'courseHomeworkExercise' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required'
        ),
        array(
            'field' => 'tip',
            'label' => 'tip',
            'rules' => 'required'
        ),
    ),
    'loginForm' => array(
        array(
            'field' => 'username',
            'label' => 'username',
            'rules' => 'required'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required'
        ),
    ),

    'registerForm' => array(
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'trim|required|valid_email|is_unique[users.email]'
        ),
        array(
            'field' => 'username',
            'label' => 'username',
            'rules' => 'trim|required|is_unique[users.username]'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|matches[confirm_password]|is_valid_password'
        ),
        /*array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'trim|required'
		),*/

        array(
            'field' => 'confirm_password',
            'label' => 'confirm password',
            'rules' => 'required'
        ),
    ),


    'socialLoginForm' => array(
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'trim|required|valid_email|is_unique[users.email]'
        ),
        array(
            'field' => 'id',
            'label' => 'id',
            'rules' => 'required'
        )
    ),

    'resetPasswordForm' => array(
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|matches[confirm_password]|is_valid_password'
        ),
        array(
            'field' => 'confirm_password',
            'label' => 'confirm password',
            'rules' => 'required'
        ),
    ),

    'resetPassUser' => array(
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|matches[confirm_password]'
        ),
        array(
            'field' => 'confirm_password',
            'label' => 'confirm password',
            'rules' => 'required'
        ),
    ),
    
    'organizationForm' => array(
        array(
            'field' => 'title',
            'label' => 'title',
            'rules' => 'required|is_unique[organizations.title]'
        ),
    ),
    'siteSettingForm' => array(
        array(
            'field' => 'value',
            'label' => 'value',
            'rules' => 'required'
        ),
    ),
    'courseForm' => array(
        array(
            'field' => 'organizations_id',
            'label' => 'organization',
            'rules' => 'required'
        )
    ),
    'basicInfoForm' => array(
        array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'required'
        ),
    ),
    'contactUsForm' => array(
        array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'trim|required|valid_email'
        ),
        array(
            'field' => 'message',
            'label' => 'message',
            'rules' => 'trim|required'
        ),
    ),
    'addAdminForm' => array(
        array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'trim|required|valid_email|is_unique[users.email]'
        )
    ),
	'addUserForm' => array(
        array(
            'field' => 'unique_id',
            'label' => 'unique id',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'user_access_code',
            'label' => 'user access code',
            'rules' => 'trim|required|is_unique[users.register_token]'
        ),
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'trim|required|valid_email|is_unique[users.email]'
        )
    ),
    'editUserForm' => array(
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'trim|required|valid_email|is_unique[users.email]'
        ),
        array(
            'field' => 'username',
            'label' => 'username',
            'rules' => 'trim|required'
        )
    ),
    'studyForm' => array(
        array(
            'field' => 'name',
            'label' => 'name',
            'rules' => 'required'
        ),
        array(
            'field' => 'courses_id',
            'label' => 'course',
            'rules' => 'required'
        ),
        array(
            'field' => 'cc_email',
            'label' => 'CC email',
            'rules' => 'required'
        ),
        array(
            'field' => 'class_id[]',
            'label' => 'class',
            'rules' => 'required'
        )
    ),
    'practiceCategory' => array(
        array(
            'field' => 'label',
            'label' => 'label',
            'rules' => 'required'
        )
    ),
);
