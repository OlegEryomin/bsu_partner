<?php

defined('MOODLE_INTERNAL') || die();

require_once("lib_partner.php");
function get_items_menu_bsu_partner(&$items)
{
    global $CFG, $OUTPUT, $USER;

    $index_items = ['university'];

    $context = context_system::instance();
    if (has_capability('block/bsu_partner:viewalldepartmentpartner', $context) || is_siteadmin() || getDepartmentsManager()){
        array_push($index_items, 'department');
    }
    if (has_capability('block/bsu_partner:viewdepartments', $context) || is_siteadmin()){
        array_push($index_items, 'ref_departments');
    }
    if (has_capability('block/bsu_partner:viewquestions', $context) || is_siteadmin()){
        array_push($index_items, 'questions');
    }


    $name = 'university';
    $icons = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('c/group'), 'class' => 'icon'));
    $items[$name] = html_writer::link($CFG->wwwroot . '/blocks/bsu_partner/partners.php', $icons . 'Партнёры университета');;

    $name = 'department';
    $icons = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/journal'), 'class' => 'icon'));
    $items[$name] = html_writer::link($CFG->wwwroot . '/blocks/bsu_partner/partners_department.php', $icons . 'Дорожная карта');

    $name = 'ref_departments';
    $icons = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/db'), 'class' => 'icon'));
    $items[$name] = html_writer::link($CFG->wwwroot . '/blocks/bsu_partner/departments.php', $icons . 'Подразделения университета');

    $name = 'questions';
    $icons = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/questions'), 'class' => 'icon'));
    $items[$name] = html_writer::link($CFG->wwwroot . '/blocks/bsu_partner/questions.php', $icons . 'Банк вопросов');

    return $index_items;
}
