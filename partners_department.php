<?php

require_once("../../config.php");
require_once("lib_partner.php");
require_once('classes/form/department_form.php');

require_login();

$id = optional_param('id', null, PARAM_INT);
$depid = optional_param('depid', null, PARAM_INT);

$script_name = "partners_department.php";

$PAGE->set_url("/blocks/bsu_partner/" . $script_name);
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Партнеры НИУ «БелГУ»");
$PAGE->set_heading($SITE->fullname . " - Партнеры НИУ «БелГУ»");
$PAGE->navbar->add("Партнеры НИУ «БелГУ»", new moodle_url("index.php"));
$PAGE->navbar->add("Дорожная карта");
echo $OUTPUT->header();



echo $OUTPUT->heading('ДОРОЖНАЯ КАРТА ПАРТНЕРСТВА');

$context = context_system::instance();
if (has_capability('block/bsu_partner:viewalldepartmentpartner', $context) ||  getDepartmentsManager() || is_siteadmin()){


    if (has_capability('block/bsu_partner:viewalldepartmentpartner', $context) || is_siteadmin()){
        $partners =  getAllDepartmentsPartners();
    }else{
        $depManager = getDepartmentsManager();
        $partners = getPartnerDepartments($depManager);
    }


    foreach ($partners as $partner) {

        $table = new html_table();
        $head = [$partner->inn . ' ' . $partner->namepartner];
        if (has_capability('block/bsu_partner:addalldepartmentanketa', $context) || getDepartmentsManager() || is_siteadmin() ){
            array_push($head, 'Действие');
        }

        $table->head = $head;
        foreach(explode(',', $partner->departments) as $department) {
            $department = (int)$department;
            $url = new moodle_url($PAGE->url, ['id' =>$partner->id, 'depid' => $department]);
            $link = html_writer::link($url, 'Редактировать');
            $data =[getDepartmentName($department)];
            if (has_capability('block/bsu_partner:addalldepartmentanketa', $context) || getDepartmentsManager() || is_siteadmin()){
                array_push($data, $link);
            }
            $table->data[] = $data;
        }
        echo html_writer::table($table);

    }



    $mform = new classes\form\department_form();

    if ($mform->is_cancelled()) {

    } else if ($fromform = $mform->get_data()) {

        addAnketa($fromform);
        redirect($PAGE->url);

    } else {

        if ($id){
            $toform =  getAnketa($id, $depid);
            $toform['depid'] =  $depid;
            $toform['partnerid'] =  $id;
        }

        $mform->set_data($toform);

        echo $OUTPUT->box_start("generalbox sitetopic");
        $mform->display();
        echo $OUTPUT->box_end();
    }

}


echo $OUTPUT->footer();