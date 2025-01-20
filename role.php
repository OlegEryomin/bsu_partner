<?php

require_once("../../config.php");
require_once("lib_partner.php");
require_once('classes/manager_department_selector.php');
require_once('classes/admins_potential_selector.php');

require_login();

$depid = optional_param('depid', null, PARAM_INT);

$script_name = "role.php";

$PAGE->set_url("/blocks/bsu_partner/" . $script_name);
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Партнеры НИУ «БелГУ»");
$PAGE->set_heading($SITE->fullname . " - Партнеры НИУ «БелГУ»");
$PAGE->navbar->add("Партнеры НИУ «БелГУ»", new moodle_url("index.php"));
$PAGE->navbar->add("Подразделения университета", new moodle_url('departments.php'));
$PAGE->navbar->add("Назначение ответственных");
echo $OUTPUT->header();

$context = context_system::instance();
if (is_siteadmin() || has_capability('block/bsu_partner:addrole', $context)){
    $options = ['depid' => $depid];
    $existinguserselector = new manager_department_selector('removeselect', $options);
    $potentialadmisselector = new admins_potential_selector();
    ?>
    <div id="addadmisform">
        <form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
            <div>
                <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>"/>

                <table class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
                    <tr>
                        <td id='existingcell'>
                            <p>
                                <label for="removeselect">Ответственные за заполнение анкет подразделения</label>
                            </p>
                            <?php $existinguserselector->display()  ?>
                        </td>
                        <td id='buttonscell'>
                            <p class="arrow_button">
                                <input name="add" id="add" type="submit"
                                       value="<?php echo $OUTPUT->larrow() . '&nbsp;' . get_string('add'); ?>"
                                       title="<?php print_string('add'); ?>"/><br/>
                                <input name="remove" id="remove" type="submit"
                                       value="<?php echo get_string('remove') . '&nbsp;' . $OUTPUT->rarrow(); ?>"
                                       title="<?php print_string('remove'); ?>"/>
                            </p>
                        </td>
                        <td id='potentialcell'>
                            <p>
                                <label for="addselect"><?php print_string('users'); ?></label>
                            </p>
                            <?php $potentialadmisselector->display() ?>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
    <?php
}

echo $OUTPUT->footer();