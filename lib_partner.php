<?php

defined('MOODLE_INTERNAL') || die();


function getAllDepartmentsPartners()
{
    global $DB;

    $partners = $DB->get_records_select('bsu_partner', 'departments IS NOT NULL');

    return $partners;
}

function getDepartmentsManager()
{
    global $DB, $USER;

    $departments = $DB->get_records('bsu_partner_departments');

    foreach ($departments as $department) {


        foreach (explode(',', $department->managers) as $manager) {
            $manager = (int)$manager;
            if ($manager) {
                if ($manager == $USER->id) {
                    $dep = $department->id;
                }
            }
        }

        if ($dep) {
            $depids[] = $dep;
            unset($dep);
        }
    }
    $depids = implode(',', $depids);

    return $depids;
}

function getPartnerDepartments($depids)
{
    global $DB;

    foreach (explode(',', $depids) as $department) {
        $department = (int)$department;
        if ($department) {
            $partner = $DB->get_field_select('bsu_partner', 'id', "departments LIKE '%$department%'");
            if ($partner) {
                $partnerids[] = $partner;
            }
        }
    }

    $departments = implode(',', $partnerids);

    if ($departments) {
        $partners = $DB->get_records_select('bsu_partner', "id IN($departments)");
    }


    return $partners;
}


function getDepartmentName($id)
{
    global $DB;

    return $DB->get_field('bsu_partner_departments', 'namedepartment', ['id' => $id]);

}

function getDepartmentsName($ids) {
    foreach (explode(',', $ids) as $id) {
         $depnames[] = getDepartmentName($id);
    }

    $depnames = implode(',', $depnames);

    return $depnames;
}


/**
 * @return array Возвращает массив id => наименование подразделений на которые есть права на добавления партнера
 * @throws dml_exception
 */
function getDepartments()
{
    global $DB, $USER;

    $context = context_system::instance();
    if (has_capability('block/bsu_partner:addalldepartmentanketa', $context) || is_siteadmin()) {
        $departments = $DB->get_records_menu('bsu_partner_departments', null, 'namedepartment', 'id,namedepartment');
    } else {
        $departments = $DB->get_records_select_menu('bsu_partner_departments', "managers LIKE '%$USER->id%'", null, 'namedepartment', 'id,namedepartment');
    }


    return $departments;
}


function getQuestions()
{
    global $DB, $OUTPUT;

    try {
        $result = $DB->get_records('bsu_partner_questions');
    } catch (dml_read_exception $e) {
        echo $OUTPUT->notification($e->getMessage());
    }

    return $result;
}

function getChildrenQuestions($id)
{
    global $DB, $OUTPUT;

    try {
        $result = $DB->get_records('bsu_partner_questions', ['parentid' => $id]);
    } catch (dml_read_exception $e) {
        echo $OUTPUT->notification($e->getMessage());
    }

    return $result;
}


/**
 * @param $id
 * @return bool возвращает true если есть потомки в иерархии
 */
function isExistChildrenQuestions($id)
{
    global $DB, $OUTPUT;

    try {
        return $DB->record_exists('bsu_partner_questions', ['parentid' => $id]);
    } catch (dml_read_exception $e) {
        echo $OUTPUT->notification($e->getMessage());
    }
}

// Функция для преобразования массива в дерево
function buildTree(array $data, $parentId = 0)
{
    $tree = [];
    foreach ($data as $item) {
        if ($item->parentid == $parentId) {
            $children = buildTree($data, $item->id);
            if ($children) {
                $item->children = $children;
            }
            $tree[] = $item;
        }
    }
    return $tree;
}


// Функция для отображения дерева
function displayTree(array $tree, $level, $mform)
{

    foreach ($tree as $node) {
        if (isset($node->children)) {

            if ($node->description){
                $mform->addElement('html', '<span class="alert alert-info">' . $node->description. '</span>');
            }

            switch ($node->typequestion) {
                case 'radio':
                    $mform->addElement($node->typequestion,  $node->parentid, '', $node->namequestion, $node->id);
                    $mform->setDefault($node->parentid, $node->id);
                    break;
                case 'checkbox':
                    $mform->addElement($node->typequestion, $node->id, '', $node->namequestion, $node->id);
                    break;
                case 'textarea':
                case 'header':
                    $mform->addElement($node->typequestion, 'h'. $node->id, $node->namequestion);
                    break;
            }


            displayTree($node->children, $level + 1, $mform);
        } else {
            if ($node->description){
                $mform->addElement('html', '<span class="alert alert-info">'. $node->description. '</span>');
            }

            switch ($node->typequestion) {
                case 'selectyear':
                    $startyear = get_config('core', 'calendar_startyear');
                    $stopyear = get_config('core', 'calendar_stopyear');

                    // Если значения не заданы, задаем диапазон по умолчанию.
                    if (!$startyear) {
                        $startyear = 1970;
                    }
                    if (!$stopyear) {
                        $stopyear = 2100;
                    }

                    $years = array_combine(range($startyear, $stopyear), range($startyear, $stopyear));
                    $mform->addElement('select', $node->id, $node->namequestion, $years);
                    $mform->setDefault($node->id, date('Y'));
                    break;
                case 'radio':
                    $mform->addElement($node->typequestion,  $node->parentid, '', $node->namequestion, $node->id);
                    $mform->setDefault($node->parentid, $node->id);
                    break;
                default:
                    $mform->addElement($node->typequestion, $node->id, $node->namequestion, ['style' => 'width: 100% !important;', 'rows' => 3, 'wrap' => 'virtual']);
            }


            $parentid = getParentid($node->id);
            if ($parentid) {
                $hid = getParentid($parentid);
                $mform->disabledIf($node->id,  $hid, 'neq', $parentid);
                 $mform->disabledIf($node->id, $parentid, 'notchecked', $parentid);
            }

        }
    }
}



function getParentid($id)
{
    global $DB;
    return $DB->get_field('bsu_partner_questions', 'parentid', ['id' => $id]);
}


function getPartners()
{
    global $DB, $OUTPUT;

    try {
        return $DB->get_records_menu('bsu_partner', null, 'namepartner', 'id, namepartner');
    } catch (dml_read_exception $e) {
        echo $OUTPUT->notification($e->getMessage());
    }
}

function addAnketa($fromform)
{
    global $DB, $OUTPUT;

    $partner = $DB->get_record('bsu_partner', ['id' => $fromform->partnerid]);

    foreach (explode(',', $partner->departments) as $department) {
        $department = (int)$department;
        if ($department) {
            $departments[$department] = $department;
        }
    }

    $departments[$fromform->depid] = $fromform->depid;

    $DB->set_field('bsu_partner', 'departments', implode(',', $departments), ['id' => $fromform->partnerid]);

    $depid = $fromform->depid;
    unset($fromform->depid);
    $partnerid = $fromform->partnerid;
    unset($fromform->partnerid);
    unset($fromform->button);

    foreach ($fromform as $id => $value) {
        $content = new stdClass();
        $content->partnerid = $partnerid;
        $content->depid = $depid;
        $content->questionid = $id;
        $content->value = $value;
        if ($idrecord = $DB->get_field('bsu_partner_anketa', 'id', ['depid' => $depid, 'partnerid' => $partnerid, 'questionid' => $id])) {
            $content->id = $idrecord;
            $DB->update_record('bsu_partner_anketa', $content);
        } else {
            $DB->insert_record('bsu_partner_anketa', $content);
        }

    }

}

function getAllDepartment()
{
    global $DB;

    return $DB->get_records('bsu_partner_departments');
}

function getAllQuestion()
{
    global $DB;

    return $DB->get_records('bsu_partner_questions');
}

function getAnketa($partnerid, $depid)
{
    global $DB, $OUTPUT;

    return $DB->get_records_menu('bsu_partner_anketa', ['partnerid' => $partnerid, 'depid' => $depid], null, 'questionid,	value');
}

function getDepartmentsPartner($partnerid) {
    global $DB;

    return $DB->get_field('bsu_partner', 'departments', ['id' => $partnerid]);
}