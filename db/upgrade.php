<?php

defined('MOODLE_INTERNAL') || die;
function xmldb_block_bsu_partner_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();


    if ($oldversion < 2025011500) {

        // Define field isnotresident to be added to bsu_partner
        $table = new xmldb_table('bsu_partner');
        $field = new xmldb_field('isnotresident', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'departments');

        // Conditionally launch add field isnotresident
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // bsu_partner savepoint reached
        upgrade_block_savepoint(true, 2025011500, 'bsu_partner');
    }

}