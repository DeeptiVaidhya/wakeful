<?php
 /*

 * Copyright (c) 2003-2020 BrightOutcome Inc.  All rights reserved.
 * 
 * This software is the confidential and proprietary information of
 * BrightOutcome Inc. ("Confidential Information").  You shall not
 * disclose such Confidential Information and shall use it only
 * in accordance with the terms of the license agreement you
 * entered into with BrightOutcome.
 * 
 * BRIGHTOUTCOME MAKES NO REPRESENTATIONS OR WARRANTIES ABOUT THE
 * SUITABILITY OF THE SOFTWARE, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT 
 * NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE, OR NON-INFRINGEMENT. BRIGHTOUTCOME SHALL NOT BE LIABLE
 * FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR
 * DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Files extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    var $tables = array();

    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->tables = array('files' => 'files');
    }
    function get_file($id = '') {
//        var_dump($_SERVER['SERVER_ADDR']);
//        if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) {
//            $this->output->set_status_header(400, 'No Remote Access Allowed');
//            exit; //just for good measure
//        }
        $info_array = array('where' => array('id' => $id), 'table' => $this->tables['files']);
        $info_array['fields'] = $this->tables['files'] . '.*';
        $file_detail = $this->db_model->get_data($info_array);
        if ($file_detail['result']) {
            $mime_type_or_return = $file_detail['result'][0]['type'];
            $type = explode('/', $mime_type_or_return)[0] . 's';
            
            $file_path = './assets/uploads/' . $type . '/' . $file_detail['result'][0]['unique_name'];
            //$this->load->helper('file');
            $image_content = file_get_contents($file_path);
            if ($image_content === FALSE) {
                show_error('Image "' . $file_path . '" could not be found.');
                return FALSE;
            }
            // Return the image or output it?
            if ($mime_type_or_return === TRUE) {
                switch ($type) {
                    case 'images':
                        $file = '<img class="img-responsive box-image" src="' . $image_content . '" alt="image" title="image">';
                        break;
                    case 'audios':
                        $file = '<audio controls><source src="' . $image_content . '" type="' . $type . '"></audio>';
                        break;

                    default:
                        break;
                }
                echo $image_content;
            }
            header('Content-Length: ' . strlen($image_content)); // sends filesize header
            header('Content-Type: ' . $mime_type_or_return); // send mime-type header
            header('Content-Disposition: inline; filename="' . basename($file_path) . '";'); // sends filename header
            //header('Location: ' . base_url($file_path));

            exit($image_content);
        }

        // reads and outputs the file onto the output buffer
    }

}
