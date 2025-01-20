<?php

require_once("../../config.php");
require_once("lib_partner.php");

require_login();

$script_name = "departments.php";

$PAGE->set_url("/blocks/bsu_portfolio/iot/" . $script_name);
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Партнеры НИУ «БелГУ»");
$PAGE->set_heading($SITE->fullname . " - Партнеры НИУ «БелГУ»");
$PAGE->navbar->add("Партнеры НИУ «БелГУ»", new moodle_url("index.php"));
$PAGE->navbar->add("Банк вопросов");
echo $OUTPUT->header();

$context = context_system::instance();
if (is_siteadmin() || has_capability('block/bsu_partner:viewquestions', $context)) {
    $table = new html_table();
    $table->attributes['style'] = 'width:80%';
    $table->attributes['align'] = 'center';
    $table->head = ['ID', 'Наименование', 'Родитель'];
    $table->data = getAllQuestion();
    echo html_writer::table($table);
}else{
    $OUTPUT->notification('Недостаточно прав для совершения действия');
}




echo $OUTPUT->footer();