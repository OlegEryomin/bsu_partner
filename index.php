<?php

require_once("../../config.php");
require_once('lib_menu_partner.php');


require_login();

$script_name = "index.php";
$PAGE->set_url("/blocks/bsu_partner/" . $script_name);
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Партнеры НИУ «БелГУ»");
$PAGE->set_heading($SITE->fullname . " - Партнеры НИУ «БелГУ»");
$PAGE->navbar->add("Партнеры НИУ «БелГУ»");
echo $OUTPUT->header();

$index_items = get_items_menu_bsu_partner($items);
$table = new html_table();
$table->align = array ('middle');

if (!empty($index_items))	{
    foreach ($index_items as $index_item)	{
        $table->data[] = array("<strong>{$items[$index_item]}</strong>" );
    }
}
echo $OUTPUT->box_start('generalbox sitetopic');
echo '<div align="center">'.html_writer::table($table).'</div>';
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
