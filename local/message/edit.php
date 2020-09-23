<?php
/**
 *
 * @package    local_message
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__."/../../config.php");
require_once ($CFG->dirroot."/local/message/classes/form/edit.php");

global $DB;
$PAGE->set_url(new moodle_url("/local/message/edit.php"));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Edit");

$form = new editform();

//Form processing and displaying is done here
if ($form->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot."/local/message/manage.php");
} else if ($fromform = $form->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $record = new stdClass();
    $record->messagetext = $fromform->email;

    $DB->insert_record('sample1', $record);

}
echo $OUTPUT->header();
$form->display();

echo $OUTPUT->footer();