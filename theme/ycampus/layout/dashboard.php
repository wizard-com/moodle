<?php
/**
 * Dashboard page layout for the ycampus theme.
 *
 * @package   theme_ycampus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $PAGE, $OUTPUT;
$renderer = $PAGE->get_renderer('theme_ycampus', 'core_course');

$enrolled_courses = enrol_get_my_courses();
$new_courses = get_new_courses();

$html_block = html_writer::tag('h4', 'My Courses');

if (count($enrolled_courses) >= 1) {
    $html_block .= $renderer->lw_courses($enrolled_courses, 1);
}
if(count($new_courses) >= 1) {
    $html_block .= '<h4>New courses Available</h4>' . $renderer->lw_courses($new_courses, 2);
}
else {
    $html_block = '<div></div>';
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
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'html_block'=> $html_block
];

$nav = $PAGE->flatnav;
$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();

echo $OUTPUT->render_from_template('theme_ycampus/dashboard', $templatecontext);