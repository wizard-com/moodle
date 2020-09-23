<?php

/**
 *
 * @package    local_message
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__."/../../config.php");

$PAGE->set_url(new moodle_url("/local/message/manage.php"));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Manage messages");

global $DB;

$records = $DB->get_records('sample1');

$templatecontext = (object)[

    "textstodisplay" => array_values($records),
    "url" => new moodle_url('/local/message/edit.php')

];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("local_message/manage", $templatecontext);
echo $OUTPUT->footer();