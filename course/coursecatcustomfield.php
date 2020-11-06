<?php

/**
 * Manage course category custom fields
 *
 * @package core_course
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('course_customfield');

$handler = \theme_ycampus\customfield\coursecat_handler::create(1);

$output = $PAGE->get_renderer('core_customfield');
$outputpage = new \core_customfield\output\management($handler);

echo $output->header(),
$output->heading('Course category custom fields'),
$output->render($outputpage),
$output->footer();
