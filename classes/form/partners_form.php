<?php


namespace classes\form;
use moodleform;

require_once("$CFG->libdir/formslib.php");
defined('MOODLE_INTERNAL') || die();
class partners_form extends moodleform
{
    public function definition()
    {

        $mform = $this->_form;
        $mform->addElement('radio', 'isnotresident', '', 'Резидент РФ', 0);
        $mform->addElement('radio', 'isnotresident', '', 'Нерезидент РФ', 1);
        $mform->addElement('text', 'inn', 'ИНН /Регистрационный номер');
        $mform->addRule('inn', 'Обязательное поле для заполнения', 'required', null, 'client');
        $mform->addElement('text', 'namepartner', 'Имя партнёра');
        $mform->addRule('namepartner', 'Обязательное поле для заполнения', 'required', null, 'client');
        $mform->addElement('text', 'site_url', 'Адрес сайта');
        $mform->addElement('textarea', 'info', 'Описание');

        $mform->addElement('submit', 'button', 'Добавить');

    }

    function validation($data, $files)
    {
        global $DB;

        if ($data['isnotresident'] == 0) {
            if (strlen($data['inn']) != 10) {
                $err['inn'] = 'В ИНН должно быть 10 цифр';
            }

            if ($DB->record_exists('bsu_partner', ['inn' => $data['inn'], 'isnotresident' => $data['isnotresident']])) {
                $err['inn'] = 'Партнёр с заданным ИНН уже существует';
            }

            if (!is_numeric($data['inn'])) {
                $err['inn'] = 'ИНН должен состоять из цифр';
            }

        }

        if ($data['isnotresident'] == 1) {
            if ($DB->record_exists('bsu_partner', ['inn' => $data['inn'], 'isnotresident' => $data['isnotresident']])) {
                $err['inn'] = 'Партнёр с заданным регистрационным номером уже существует';
            }
        }



        return $err;
    }
}