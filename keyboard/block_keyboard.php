
<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form for editing HTML block instances.
 *
 * @package   block_keyboard
 * @copyright 1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_keyboard extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_keyboard');
        $this->chars = explode(" ", "a b c");
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        if(isset($this->config)) {
        	if(!empty($this->config->text)) {
        		$this->chars = explode(" ", $this->config->text);
        	}
        	if (empty($this->config->title)) {
            	$this->title = get_string('pluginname', 'block_keyboard');            
	        } else {
	            $this->title = $this->config->title;
	        }
        }
    }

    function instance_allow_multiple() {
        return true;
    }

    public function get_content() {
	 	$this->content =  new stdClass;

	    $newChars = "[";
	    for ($x = 0; $x < count($this->chars)-1; $x++) {
	    	if($this->chars[$x]!='') {
	    		$newChars = $newChars."\"".$this->chars[$x]."\",";
	    	}
	    }
	    $newChars = $newChars."\"".$this->chars[count($this->chars)-1]."\""."]";

	    $charFile = fopen("/Moodle3.6/server/moodle/blocks/keyboard/includes/Chars.php", "w") or die("Can't open file");
	    fwrite($charFile, $newChars);
	    fclose($charFile);
	    $charFile = fopen("/Moodle3.6/server/moodle/blocks/keyboard/includes/Chars.php", "r");
	    $myFile1 = fopen("/Moodle3.6/server/moodle/blocks/keyboard/includes/Intro_html.php", "r") or die("Cant open file");
	    $myFile2 = fopen("/Moodle3.6/server/moodle/blocks/keyboard/includes/Keyboard_html.php", "r") or die("Cant open file");

	    $this->content->text = fread($myFile1, filesize("/Moodle3.6/server/moodle/blocks/keyboard/includes/Intro_html.php")).fread($charFile, filesize("/Moodle3.6/server/moodle/blocks/keyboard/includes/Chars.php")).fread($myFile2, filesize("/Moodle3.6/server/moodle/blocks/keyboard/includes/Keyboard_html.php"));

	    fclose($myFile1);
	    fclose($myFile2);
	    fclose($charFile);
	 
	    return $this->content;
	}

	public function instance_config_save($data,$nolongerused =false) {
		if(get_config('keyboard', 'Allow_HTML') == '1') {
		  $data->text = strip_tags($data->text);
		}
		// And now forward to the default implementation defined in the parent class
		return parent::instance_config_save($data,$nolongerused);
	}

    
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_keyboard');
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        $fromcontext = context_block::instance($fromid);
        $fs = get_file_storage();
        // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
        if (!$fs->is_area_empty($fromcontext->id, 'block_keyboard', 'content', 0, false)) {
            $draftitemid = 0;
            file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_keyboard', 'content', 0, array('subdirs' => true));
            file_save_draft_area_files($draftitemid, $this->context->id, 'block_keyboard', 'content', 0, array('subdirs' => true));
        }
        return true;
    }

    function content_is_trusted() {
        global $SCRIPT;

        if (!$context = context::instance_by_id($this->instance->parentcontextid, IGNORE_MISSING)) {
            return false;
        }
        //find out if this block is on the profile page
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                // this is exception - page is completely private, nobody else may see content there
                // that is why we allow JS here
                return true;
            } else {
                // no JS on public personal pages, it would be a big security issue
                return false;
            }
        }

        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

    /*
     * Add custom html attributes to aid with theming and styling
     *
     * @return array
     */
    function html_attributes() {
        global $CFG;

        $attributes = parent::html_attributes();

        if (!empty($CFG->block_keyboard_allowcssclasses)) {
            if (!empty($this->config->classes)) {
                $attributes['class'] .= ' '.$this->config->classes;
            }
        }

        return $attributes;
    }
}
