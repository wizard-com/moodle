<?php
/**
 * Dashboard page layout for the ycampus theme.
 *
 * @package   theme_ycampus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

global $OUTPUT, $PAGE;

$core_renderer = $PAGE->get_renderer('theme_ycampus', 'core');
$course_renderer = $PAGE->get_renderer('theme_ycampus', 'core_course');
$header = $core_renderer->full_header();

$enrolled_courses = enrol_get_my_courses();

$new_courses = get_new_courses();

$htmlblock = '';
$enrolled_course_heading = '';

if(count($enrolled_courses) > 0){
    $enrolled_course_heading = '<h5 id="instance-73-header" class="card-title d-inline">My Courses</h5>';
    $htmlblock .= $course_renderer->lw_courses($enrolled_courses, 1);
}
if(count($new_courses) > 0){
    $new_course_heading = html_writer::tag('h5', 'New courses available');
    $htmlblock .= $new_course_heading;
    $htmlblock .= $course_renderer->lw_courses($new_courses, 2);
}


if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'header'=> $header,
    'html' => $htmlblock,
    'heading'=> $enrolled_course_heading,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen
];

$nav = $PAGE->flatnav;
$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();
echo $OUTPUT->render_from_template('theme_ycampus/dashboard', $templatecontext);
