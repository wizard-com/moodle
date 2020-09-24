<?php

require_once(__DIR__."/../../config.php");
//
$PAGE->set_url(new moodle_url("/local/message/course_details"));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title("Course details");

global $DB;
//global $USER;
//
//$currentUserID = $USER->id;

$courses = $DB->get_records_sql('SELECT fullname, enddate FROM {course} WHERE id != 1;');
//$info = get_fast_modinfo($course);
//print_object($info);


foreach ($courses as $course){
    $course->enddate = substr(date('d-M-Y', $course->enddate), 0, 6);
}
//$records = $DB->get_records('sample1');
//
$templatecontext = (object)[

    "title" => array_values($courses)

];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("local_message/course_details", $templatecontext);
echo $OUTPUT->footer();