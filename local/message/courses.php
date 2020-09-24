<?php

require_once(__DIR__."/../../config.php");
//
$PAGE->set_url(new moodle_url("/local/message/courses.php"));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Courses");

global $DB;
//global $USER;
//
//$currentUserID = $USER->id;

$course = $DB->get_records('course');
//$info = get_fast_modinfo($course);
//print_object($info);

//$records = $DB->get_records('sample1');
//
$templatecontext = (object)[

    "title" => array_values($course)

];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("local_message/courses", $templatecontext);
echo $OUTPUT->footer();