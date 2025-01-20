<?php

defined('MOODLE_INTERNAL') || die();

require_once('lib_menu_partner.php');

class block_bsu_partner extends block_list
{
    public function init()
    {
        $this->title = get_string('pluginname', 'block_bsu_partner');
    }

    function get_content()
    {
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();

        $items = array();
        $this->content->footer = '';

        if (empty($this->instance)) {
            $this->content = '';
        } else {
            $this->load_content();
        }

        return $this->content;
    }

    function load_content() {
        global $CFG;


        $items = array();
        $index_items = get_items_menu_bsu_partner($items);
        if (!empty($index_items))	{
            foreach ($index_items as $index_item)	{
                $this->content->items[] = $items[$index_item];
            }

            $this->content->footer = '<a href="'.$CFG->wwwroot.'/blocks/bsu_partner/index.php">'.get_string('pluginname', 'block_bsu_partner').'</a>'.' ...';
        }
    }
}