<?php


namespace classes\form;
use moodleform;

require_once("$CFG->libdir/formslib.php");

defined('MOODLE_INTERNAL') || die();

class department_form extends moodleform
{
    public function definition()
    {
        global $USER;


        $mform = $this->_form;

        $departments = getDepartments();

        if ($departments) {
            $mform->addElement('select', 'depid', 'Подразделение', $departments);
            $mform->addElement('html', \html_writer::link('partners.php', 'Если партнер отсутствует в списке,
            его необходимо добавить, перейдя по данной ссылке'));
            $mform->addElement('select', 'partnerid', 'Партнёр', getPartners());


            $data = getQuestions();


            // Построение дерева
            $tree = buildTree($data);

            // Вывод дерева
            displayTree($tree, 0, $mform);


            $mform->addElement('submit', 'button', 'Сохранить');
        }

    }

    function validation($data, $files)
    {

    }
}