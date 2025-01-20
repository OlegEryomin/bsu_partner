<?php

require_once("../../config.php");
require_once("lib_partner.php");

require_login();

$script_name = "departments.php";

$PAGE->set_url("/blocks/bsu_partner/" . $script_name);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'block_bsu_partner'));
$PAGE->set_heading($SITE->fullname . " - " . get_string('pluginname', 'block_bsu_partner'));
$PAGE->navbar->add(get_string('pluginname', 'block_bsu_partner'), new moodle_url("index.php"));
$PAGE->navbar->add(get_string('departmentsuni', 'block_bsu_partner'));
echo $OUTPUT->header();

$context = context_system::instance();
if (is_siteadmin() || has_capability('block/bsu_partner:viewdepartments', $context)) {
    $table = new html_table();
    $table->attributes['style'] = 'width:80%';
    $table->attributes['align'] = 'center';
    $head = ['ID', 'Наименование', 'guid'];
    if (is_siteadmin() || has_capability('block/bsu_partner:addrole', $context)){
        array_push($head, 'Действия');
    }
    $table->head = $head;
    $departments = getAllDepartment();

    foreach ($departments as $department) {

        $icons = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/roles'), 'class' => 'icon'));
        $url = new moodle_url('role.php', ['depid' => $department->id]);
        $link = html_writer::link( $url, $icons);

        $data = [$department->id, $department->namedepartment, $department->guid];
        if (is_siteadmin() || has_capability('block/bsu_partner:addrole', $context)){
            array_push($data, $link);
        }

        $table->data[] = $data;


    }


    echo html_writer::table($table);
}else{
    $OUTPUT->notification('Недостаточно прав для совершения действия');
}




echo $OUTPUT->footer();