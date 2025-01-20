<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'block/bsu_partner:addpartner' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    'block/bsu_partner:viewalldepartmentpartner' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    'block/bsu_partner:viewdepartments' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    'block/bsu_partner:viewquestions' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    'block/bsu_partner:addalldepartmentanketa' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

    'block/bsu_partner:addrole' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
    ),

);