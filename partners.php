<?php

require_once("../../config.php");
require_once("lib_partner.php");

require_login();

$script_name = "partners.php";

$PAGE->set_url("/blocks/bsu_partner/" . $script_name);
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Партнеры НИУ «БелГУ»");
$PAGE->set_heading($SITE->fullname . " - Партнеры НИУ «БелГУ»");
$PAGE->navbar->add("Партнеры НИУ «БелГУ»", new moodle_url("index.php"));
$PAGE->navbar->add("Партнёры университета");
echo $OUTPUT->header();


require_once('classes/form/partners_form.php');
$context = context_system::instance();
if (is_siteadmin() || getDepartmentsManager() || has_capability('block/bsu_partner:addpartner', $context)) {
    $mform = new classes\form\partners_form();

    if ($mform->is_cancelled()) {

    } else if ($fromform = $mform->get_data()) {


        $DB->insert_record('bsu_partner', $fromform);
        redirect($PAGE->url);

    } else {
        $mform->set_data($toform);

        echo $OUTPUT->box_start("generalbox sitetopic");
        $mform->display();
        echo $OUTPUT->box_end();
    }

}


$table = new html_table();
$table->head = ['ИНН', 'Имя партнёра', 'Адрес сайта', 'Описание', 'Подразделения'];
$partners = $DB->get_records('bsu_partner', null, 'namepartner');

foreach ($partners as $partner) {

    if ($partner->departments){
        $departmentname ='';
        foreach(explode(',', $partner->departments) as $department) {
            $department = (int)$department;
            $departmentname .= '<br>' . getDepartmentName($department);
        }

    }else{
        $departmentname ='-';
    }

    $table->data[] = [$partner->inn,$partner->namepartner, $partner->site_url, $partner->info, $departmentname];
}

echo html_writer::table($table);



echo $OUTPUT->footer();