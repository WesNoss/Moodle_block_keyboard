<?php
 
class block_keyboard_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 
        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', 'Keyboard block title');
        $mform->setDefault('config_title', '');
        $mform->setType('config_title', PARAM_TEXT);
     

        $mform->addElement('text', 'config_text', 'Characters displayed');
        $mform->setDefault('config_text', 'Input characters to display, separated by spaces.');
        $mform->setType('config_text', PARAM_RAW);  
    }
}