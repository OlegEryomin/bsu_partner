<?php

require_once($CFG->dirroot.'/user/selector/lib.php');

class manager_department_selector extends user_selector_base {

    private $depid;

    /**
     * @param $depid
     */
    public function __construct($name, $options) {
        $this->depid  = $options['depid'];
        parent::__construct($name, $options);
    }

    public function find_users($search)
    {
        list($wherecondition, $params) = $this->search_sql($search, '');

        global $DB;

        $fields      = 'SELECT ' . $this->required_fields_sql('');

        $managers = $DB->get_field('bsu_partner_departments', 'managers', ['id' => $this->depid] );

        if (empty($managers)){
            return null;
        }

        if ($wherecondition) {
            $wherecondition = "$wherecondition AND id IN ($managers)";
        } else {
            $wherecondition = "id IN ($managers)";
        }
        $sql = " FROM {user}
                WHERE $wherecondition";
        $order = ' ORDER BY lastname ASC, firstname ASC';


        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);


        if ($availableusers) {
            if ($search) {
                $groupname = get_string('extusersmatching', 'role', $search);
            } else {
                $groupname = get_string('extusers', 'role');
            }
            $result[$groupname] = $availableusers;
        }

        return $result;
    }
}