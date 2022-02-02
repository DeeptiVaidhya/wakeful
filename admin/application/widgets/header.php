<?php
/*
 * Header widget
 */
class Header extends Widget {
    public function display($data) {
        
        if (!isset($data['breadcrumb'])) {
            $data['breadcrumb'] = '';
        }
        $this->view('widgets/header', $data);
    }
    
}