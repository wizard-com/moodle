<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * lw_courses block rendrer
 *
 * @package    block_lw_courses
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @copyright  2017 Mathew May <mathewm@hotmail.co.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/externallib.php');
/**
 * lw_courses block rendrer
 *
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_lw_courses_renderer extends plugin_renderer_base {

    /**
     * Construct contents of lw_courses block
     *
     * @param array $courses list of courses in sorted order
     * @return string html to be displayed in lw_courses block
     */
    public function lw_courses($courses) {

        $output = '';

        $courseordernumber = 0;
        $config = get_config('block_lw_courses');
        $total = count($courses);

        $gridsplit = intval(12 / $total); // Added intval to avoid any float.

        $colsize = intval($config->coursegridwidth) > 0 ? intval($config->coursegridwidth) : BLOCKS_LW_COURSES_DEFAULT_COL_SIZE;
        if ($gridsplit < $colsize) {
            $gridsplit = $colsize;
        }

        $courseclass = $config->startgrid == BLOCKS_LW_COURSES_STARTGRID_YES ? "grid" : "list";

        $output .= html_writer::start_div('carousel slide lw_courses_list', array('id'=>'demo', 'data-ride'=>'carousel'));
        $output .= html_writer::start_div('carousel-inner container-fluid');

        $row_count = intval($total/3)+1;

        if($total % 3 == 0){
            $row_count = $total/3;
        }

        $active = ' active';
        for($i = 0; $i < $row_count; $i++){
            $content = '';
            $content .= html_writer::start_tag('div', array('class'=>'carousel-item row row-equal'.$active));
            $sub_array = array_slice($courses, $i*3, 3);
            $length = count($sub_array);
            $colwidth = 12 / $length;


            foreach ($sub_array as $key => $course) {

                $content .= $this->output->box_start(
                    "coursebox col-lg-$colwidth col-md-6 col-sm-6 col-12",
                    "course-{$course->id}");
                $content .= $this->course_image($course);

                $content .= html_writer::start_tag('div', array('class' => 'course_title'));
                // No need to pass title through s() here as it will be done automatically by html_writer.
                $attributes = array('title' => $course->fullname);
                if ($course->id > 0) {
                    $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
                    $coursefullname = format_string(get_course_display_name_for_list($course), true, $course->id);
                    $link = html_writer::link($courseurl, $coursefullname, $attributes);
                    $content .= $this->output->heading($link, 2, 'title');
                }
                $content .= $this->output->box('', 'flush');
                $content .= html_writer::end_tag('div');

                if ($course->id > 0) {
                    $content .= $this->course_description($course);

                    $content .= block_lw_courses_build_progress($course);
                }


                $content .= $this->output->box('', 'flush');
                $content .= $this->output->box_end();
                $courseordernumber++;

            }
            $content .= html_writer::end_tag('div');
            $output .= $content;

            if($active == ' active'){
                $active = '';
            }

        }

        // Wrap course list in a div and return.
        $output .= html_writer::end_div();
        if($row_count > 1){
            $output .= $this->render_control_buttons();
        }
        $output .= html_writer::end_div();
        return $output;
    }


    /**
     * Construct prev and next buttons for slideshow

     * @return string html of the buttons
     */
    private function render_control_buttons(){
        $output = '';
        $span_prev = html_writer::tag('span', '', ['class'=>'carousel-control-prev-icon']);
        $span_next = html_writer::tag('span', '', ['class'=>'carousel-control-next-icon']);
        $output .= html_writer::link('#demo', $span_prev, ['class'=>'carousel-control-prev','data-slide'=>'prev']);
        $output .= html_writer::link('#demo', $span_next, ['class'=>'carousel-control-next','data-slide'=>'next']);

        return $output;
    }

    /**
     * Construct activities overview for a course
     *
     * @param int $cid course id
     * @param array $overview overview of activities in course
     * @return string html of activities overview
     */
    protected function activity_display($cid, $overview) {
        $output = html_writer::start_tag('div', array('class' => 'activity_info'));
        foreach (array_keys($overview) as $module) {
            $output .= html_writer::start_tag('div', array('class' => 'activity_overview'));
            $url = new moodle_url("/mod/$module/index.php", array('id' => $cid));
            $modulename = get_string('modulename', $module);
            $icontext = html_writer::link($url, $this->output->pix_icon(
                'icon', $modulename, 'mod_'.$module, array('class' => 'iconlarge')));
            if (get_string_manager()->string_exists("activityoverview", $module)) {
                $icontext .= get_string("activityoverview", $module);
            } else {
                $icontext .= get_string("activityoverview", 'block_lw_courses', $modulename);
            }

            // Add collapsible region with overview text in it.
            $output .= $this->collapsible_region($overview[$module], '', 'region_'.$cid.'_'.$module, $icontext, '', true);

            $output .= html_writer::end_tag('div');
        }
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Constructs header in editing mode
     *
     * @param int $max maximum number of courses
     * @return string html of header bar.
     */
    public function editing_bar_head($max = 0) {
        $output = $this->output->box_start('notice');

        $options = array('0' => get_string('alwaysshowall', 'block_lw_courses'));
        for ($i = 1; $i <= $max; $i++) {
            $options[$i] = $i;
        }
        $url = new moodle_url('/my/index.php');
        $select = new single_select($url, 'mynumber', $options, block_lw_courses_get_max_user_courses(), array());
        $select->set_label(get_string('numtodisplay', 'block_lw_courses'));
        $output .= $this->output->render($select);

        $output .= $this->output->box_end();
        return $output;
    }

    /**
     * Show hidden courses count
     *
     * @param int $total count of hidden courses
     * @return string html
     */
    public function hidden_courses($total) {
        if ($total <= 0) {
            return;
        }
        $output = $this->output->box_start('notice');
        $plural = $total > 1 ? 'plural' : '';
        $config = get_config('block_lw_courses');
        // Show view all course link to user if forcedefaultmaxcourses is not empty.
        if (!empty($config->forcedefaultmaxcourses)) {
            $output .= get_string('hiddencoursecount'.$plural, 'block_lw_courses', $total);
        } else {
            $a = new stdClass();
            $a->coursecount = $total;
            $a->showalllink = html_writer::link(new moodle_url('/my/index.php',
                array('mynumber' => block_lw_courses::SHOW_ALL_COURSES)),
                get_string('showallcourses'));
            $output .= get_string('hiddencoursecountwithshowall'.$plural, 'block_lw_courses', $a);
        }

        $output .= $this->output->box_end();
        return $output;
    }

    /**
     * Creates collapsable region
     *
     * @param string $contents existing contents
     * @param string $classes class names added to the div that is output.
     * @param string $id id added to the div that is output. Must not be blank.
     * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
     * @param string $userpref the name of the user preference that stores the user's preferred default state.
     *      (May be blank if you do not wish the state to be persisted.
     * @param bool $default Initial collapsed state to use if the user_preference it not set.
     * @return bool if true, return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region($contents, $classes, $id, $caption, $userpref = '', $default = false) {
        $output  = $this->collapsible_region_start($classes, $id, $caption, $userpref, $default);
        $output .= $contents;
        $output .= $this->collapsible_region_end();

        return $output;
    }

    /**
     * Print (or return) the start of a collapsible region, that has a caption that can
     * be clicked to expand or collapse the region. If JavaScript is off, then the region
     * will always be expanded.
     *
     * @param string $classes class names added to the div that is output.
     * @param string $id id added to the div that is output. Must not be blank.
     * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
     * @param string $userpref the name of the user preference that stores the user's preferred default state.
     *      (May be blank if you do not wish the state to be persisted.
     * @param bool $default Initial collapsed state to use if the user_preference it not set.
     * @return bool if true, return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region_start($classes, $id, $caption, $userpref = '', $default = false) {
        // Work out the initial state.
        if (!empty($userpref) and is_string($userpref)) {
            user_preference_allow_ajax_update($userpref, PARAM_BOOL);
            $collapsed = get_user_preferences($userpref, $default);
        } else {
            $collapsed = $default;
            $userpref = false;
        }

        if ($collapsed) {
            $classes .= ' collapsed';
        }

        $output = '';
        $output .= '<div id="' . $id . '" class="collapsibleregion ' . $classes . '">';
        $output .= '<div id="' . $id . '_sizer">';
        $output .= '<div id="' . $id . '_caption" class="collapsibleregioncaption">';
        $output .= $caption . ' ';
        $output .= '</div><div id="' . $id . '_inner" class="collapsibleregioninner">';
        $this->page->requires->js_init_call('M.block_lw_courses.collapsible', array($id, $userpref, get_string('clicktohideshow')));

        return $output;
    }

    /**
     * Close a region started with print_collapsible_region_start.
     *
     * @return string return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region_end() {
        $output = '</div></div></div>';
        return $output;
    }

    /**
     * Creates html for welcome area
     *
     * @param int $msgcount number of messages
     * @return string html string for welcome area.
     */
    public function welcome_area($msgcount) {
        global $CFG, $USER;
        $output = $this->output->box_start('welcome_area');

        $picture = $this->output->user_picture($USER, array('size' => 75, 'class' => 'welcome_userpicture'));
        $output .= html_writer::tag('div', $picture, array('class' => 'profilepicture'));

        $output .= $this->output->box_start('welcome_message');
        $output .= $this->output->heading(get_string('welcome', 'block_lw_courses', $USER->firstname));

        if (!empty($CFG->messaging)) {
            $plural = 's';
            if ($msgcount > 0) {
                $output .= get_string('youhavemessages', 'block_lw_courses', $msgcount);
                if ($msgcount == 1) {
                    $plural = '';
                }
            } else {
                $output .= get_string('youhavenomessages', 'block_lw_courses');
            }
            $output .= html_writer::link(new moodle_url('/message/index.php'),
                get_string('message'.$plural, 'block_lw_courses'));
        }
        $output .= $this->output->box_end();
        $output .= $this->output->box('', 'flush');
        $output .= $this->output->box_end();

        return $output;
    }

    // Custom LearningWorks functions.

    /**
     * Get the image for a course if it exists
     *
     * @param object $course The course whose image we want
     * @return string|void
     */
    public function course_image($course) {
        global $CFG;

        $course = new core_course_list_element($course);
        // Check to see if a file has been set on the course level.
        if ($course->id > 0 && $course->get_course_overviewfiles()) {
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if ($isimage) {
                    $config = get_config('block_lw_courses');
                    if (is_null($config->lw_courses_bgimage) ||
                         $config->lw_courses_bgimage == BLOCKS_LW_COURSES_IMAGEASBACKGROUND_FALSE) {
                        // Embed the image url as a img tag sweet...
                        $image = html_writer::empty_tag('img', array('src' => $url, 'class' => 'course_image'));
                        return html_writer::div($image, 'image_wrap');
                    } else {
                        // We need a CSS soloution apparently lets give it to em.
                        return html_writer::div('', 'course_image_embed',
                            array("style" => 'background-image:url('.$url.'); background-size:cover'));
                    }
                } else {
                    return $this->course_image_defaults();
                }
            }
        } else {
            // Lets try to find some default images eh?.
            return $this->course_image_defaults();
        }
        // Where are the default at even?.
        return print_error('error');
    }

    /**
     * There was no image for a course give a default
     *
     * @return string|void
     */
    public function course_image_defaults() {

        $config = get_config('block_lw_courses');

        if (method_exists($this->output, 'image_url')) {
            // Use the new method.
            $default = $this->output->image_url('default', 'block_lw_courses');
        } else {
            // Still a pre Moodle 3.3 release. Use pix_url because image_url doesn't exist yet.
            $default = $this->output->pix_url('default', 'block_lw_courses');
        }
        if ($courseimagedefault = get_config('block_lw_courses', 'courseimagedefault')) {

            // Return an img element with the image in the block settings to use for the course.
            $imageurl = block_lw_courses_get_course_image_url($courseimagedefault);
        } else {
            // We check for a default image in the lw_courses pix folder named default aka our final hope.
            $imageurl = $default;
        }

        // Do we need a CSS soloution or is a img good enough?.
        if (is_null($config->lw_courses_bgimage) || $config->lw_courses_bgimage == BLOCKS_LW_COURSES_IMAGEASBACKGROUND_FALSE) {
            // Embed the image url as a img tag sweet...
            $image = html_writer::empty_tag('img', array( 'src' => $imageurl, 'class' => 'course_image' ));
            return html_writer::div($image, 'image_wrap');
        } else {
            // We need a CSS solution apparently lets give it to 'em.
            return html_writer::div('', 'course_image_embed',
                    array("style" => 'background-image:url('.$imageurl.'); background-size:cover'));
        }
        // Where are the default at even?.
        return print_error('filenotreadable');
    }

    /**
     * Get the Course description for a given course
     *
     * @param object $course The course whose description we want
     * @return string
     */
    public function course_description($course) {
        $course = new core_course_list_element($course);

        $context = \context_course::instance($course->id);
        $summary = external_format_string($course->summary, $context,
                1, array());
        return html_writer::div($summary, 'course_description');
    }

    /**
     * Cut off the course description at a certain point
     *
     * @param string $s Initial String passed in
     * @param int $l The length to cut it too
     * @param string $e I am unsure
     * @return string
     */
    public function truncate_html($s, $l, $e = '&hellip;') {
        $s = trim($s);
        $e = (strlen(strip_tags($s)) > $l) ? $e : '';
        $i = 0;
        $tags = array();

        preg_match_all('/<[^>]+>([^<]*)/', $s, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        foreach ($m as $o) {
            if ($o[0][1] - $i >= $l) {
                break;
            }
            $t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
            if ($t[0] != '/') {
                $tags[] = $t;
            } else if (end($tags) == substr($t, 1)) {
                array_pop($tags);
            }
            $i += $o[1][1] - $o[0][1];
        }

        $output = substr($s, 0, $l = min(strlen($s), $l + $i)) .
            $e . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');
        return $output;
    }
}
