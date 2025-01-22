<?php
require_once("../../config.php");
require_once("lib_partner.php");
require_once($CFG->libdir . '/excel/Worksheet.php');
require_once($CFG->libdir . '/excel/Workbook.php');

set_time_limit(0);
ini_set('memory_limit', -1);

require_login();

$context = context_system::instance();
if (has_capability('block/bsu_partner:viewalldepartmentpartner', $context) || is_siteadmin()) {


    $downloadfilename = 'Отчет_по_заполнению_ДК.xls';

    // Заголовки для скачивания
    header("Content-type: application/vnd.ms-excel; charset=windows-1251");
    header("Content-Disposition: attachment; filename=\"$downloadfilename\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");

    // Создаем объект Workbook (рабочая книга)
    $workbook = new Workbook('-');

    // Формат заголовков таблицы
    $header_format = $workbook->add_format();
    $header_format->set_bold();
    $header_format->set_align('center');
    $header_format->set_text_wrap();
    $header_format->set_border(1);

    // Формат обычного текста
    $text_format = $workbook->add_format();
    $text_format->set_align('left');
    $text_format->set_text_wrap(); //автоперенос
    $text_format->set_border(1);

    global $DB;
    $DB->execute('CALL bsu_get_partners_cards()');

    // Итерация по партнерам
    foreach (getPartners() as $id => $partner) {

        // Создаем новый лист
        $worksheet = $workbook->add_worksheet($id);

        $partnername = mb_convert_encoding($partner, 'Windows-1251', 'UTF-8');
        $worksheet->write_string(0, 1, $partnername, $header_format);

        // Получаем данные о подразделениях
        $depids = getDepartmentsPartner($id);
        $depnames = getDepartmentsName($depids);

        // Заголовки таблицы
        $head = ['ID', 'Анкета'];
        foreach (explode(',', $depnames) as $depname) {
            $head[] = $depname;
        }

        // Конвертируем заголовки в Windows-1251
        $head = array_map(function ($title) {
            return mb_convert_encoding($title, 'Windows-1251', 'UTF-8');
        }, $head);

        // Записываем заголовки в первую строку
        foreach ($head as $col => $title) {
            $worksheet->write_string(1, $col, $title, $header_format);

            switch ($col) {
                case 0: $worksheet->set_column(1, $col, 10);
                break;
                case 1: $worksheet->set_column(1, $col, 60);
                break;
                default: $worksheet->set_column(1, $col, 40);
            }
        }

        // Получаем данные из временной таблицы
        try {
            $records = $DB->get_records_sql('SELECT * FROM temp_' . $id);
        } catch (dml_read_exception $e) {
            $records = [];
        }

        // Записываем данные в таблицу
        $row = 2;
        foreach ($records as $record) {
            $col = 0;
            foreach ($record as $field) {
                $value = mb_convert_encoding($field, 'Windows-1251', 'UTF-8');
                $worksheet->write_string($row, $col++, $value, $text_format);
            }
            $row++;
        }
    }

    // Закрываем книгу и выводим данные
    $workbook->close();
}

