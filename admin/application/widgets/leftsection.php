<?php
/*
 * Header widget
 */
class Leftsection extends Widget {
    public function display($data) {
        
        if (!isset($data['items'])) {
            $data['items'] = array('Home', 'About', 'Contact');
        }
        $this->view('widgets/leftsection', $data);
    }
    
}