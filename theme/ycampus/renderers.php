<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/renderer.php');

class theme_ycampus_core_course_renderer extends core_course_renderer{

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        $output = '';
        // We return empty string (because course module will not be displayed at all)
        // if:
        // 1) The activity is not visible to users
        // and
        // 2) The 'availableinfo' is empty, i.e. the activity was
        //     hidden in a way that leaves no info, such as using the
        //     eye icon.
        if (!$mod->is_visible_on_course_page()) {
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }

        $output .= html_writer::start_tag('div');

        if ($this->page->user_is_editing()) {
            $output .= course_get_cm_move($mod, $sectionreturn);
        }

        $output .= html_writer::start_tag('div', array('class' => 'mod-indent-outer w-100'));

        // This div is used to indent the content.
        $output .= html_writer::div('', $indentclasses);

        // Start a wrapper for the actual content to keep the indentation consistent
        $output .= html_writer::start_tag('div');

        // Display the link to the module (or do nothing if module has no url)
        $cmname = $this->course_section_cm_name($mod, $displayoptions);

        if (!empty($cmname)) {
            // Start the div for the activity title, excluding the edit icons.
            $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));
            $output .= $cmname;


            // Module can put text after the link (e.g. forum unread)
            $output .= $mod->afterlink;

            // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
            $output .= html_writer::end_tag('div'); // .activityinstance
        }

        // If there is content but NO link (eg label), then display the
        // content here (BEFORE any icons). In this case cons must be
        // displayed after the content so that it makes more sense visually
        // and for accessibility reasons, e.g. if you have a one-line label
        // it should work similarly (at least in terms of ordering) to an
        // activity.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);
        $url = $mod->url;
        if (empty($url)) {
            $output .= $contentpart;
        }

        $modicons = '';
        if ($this->page->user_is_editing()) {
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            $modicons .= ' '. $this->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            $modicons .= $mod->afterediticons;
        }

        $modicons .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        if (!empty($modicons)) {
            $output .= html_writer::div($modicons, 'actions');
        }

        // Show availability info (if module is not available).
        $output .= $this->course_section_cm_availability($mod, $displayoptions);

        // If there is content AND a link, then display the content here
        // (AFTER any icons). Otherwise it was displayed before
        if (!empty($url)) {
            $output .= $contentpart;
        }

        $output .= html_writer::end_tag('div'); // $indentclasses

        // End of indentation div.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');


        return $output;
    }

    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode
     *
     * This function calls {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */


    public function course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $USER;

        $output = '';
        $modinfo = get_fast_modinfo($course);
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // check if we are currently in the process of moving a module with JavaScript disabled
        $ismoving = $this->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one)
        $moduleshtml = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // do not display moving mod
                    continue;
                }

                if ($modulehtml = $this->course_section_cm_list_item($course,$completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li', html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)), array('class' => 'movehere'));

                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li', html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)), array('class' => 'movehere'));
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));


        return $output;
    }

    /**
     * Returns HTML to display course summary.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_summary(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';
        //$content .= html_writer::start_tag('div', array('class' => 'row'));
        if ($course->has_summary()) {
//            $content .= html_writer::start_tag('div', ['class' => 'summary']);
//            $content .= $chelper->get_course_formatted_summary($course,
//                array('overflowdiv' => true, 'noclean' => true, 'para' => false));
//            $content .= html_writer::end_tag('div');

        }

        $content .= 'Some text';
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = '';
        $classes = trim('coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'info'));
        $content .= $this->course_name($chelper, $course);
        $content .= $this->course_enrolment_icons($course);
        $content .= html_writer::end_tag('div');

        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div');

        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }

    /**
     * Returns HTML to print list of available courses for the frontpage
     *
     * @return string
     */
    public function frontpage_available_courses() {
        global $CFG;

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
        set_courses_display_options(array(
            'recursive' => true,
            'limit' => $CFG->frontpagecourselimit,
            'viewmoreurl' => new moodle_url('/course/index.php'),
            'viewmoretext' => new lang_string('fulllistofcourses')));

        $chelper->set_attributes(array('class' => 'row'));
        $courses = core_course_category::top()->get_courses($chelper->get_courses_display_options());
        $totalcount = core_course_category::top()->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }
        return $this->coursecat_courses($chelper, $courses, $totalcount);
    }

    /**
     * Returns HTML to display course name.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_name(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $nametag = 'div';
        }
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
            $coursename, ['class' => $course->visible ? 'aalink' : 'aalink dimmed']);
        $content .= html_writer::tag($nametag, $coursenamelink, ['class' => 'coursename']);
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', ['class' => 'moreinfo']);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()
                || $course->has_custom_fields()) {
                $url = new moodle_url('/course/info.php', ['id' => $course->id]);
                $image = $this->output->pix_icon('i/info', $this->strings->summary);
                $content .= html_writer::link($url, $image, ['title' => $this->strings->summary]);
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('div');
        return $content;
    }

        /**
     * Returns HTML to print list of courses user is enrolled to for the frontpage
     *
     * Also lists remote courses or remote hosts if MNET authorisation is used
     *
     * @return string
     */
    public function frontpage_my_courses() {
        global $USER, $CFG, $DB;

        if (!isloggedin() or isguestuser()) {
            return '';
        }

        $output = '';
        $courses  = enrol_get_my_courses('summary, summaryformat');
        $rhosts   = array();
        $rcourses = array();
        if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode==='strict') {
            $rcourses = get_my_remotecourses($USER->id);
            $rhosts   = get_my_remotehosts();
        }

        if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

            $chelper = new coursecat_helper();
            $totalcount = count($courses);
            if (count($courses) > $CFG->frontpagecourselimit) {
                // There are more enrolled courses than we can display, display link to 'My courses'.
                $courses = array_slice($courses, 0, $CFG->frontpagecourselimit, true);
                $chelper->set_courses_display_options(array(
                    'viewmoreurl' => new moodle_url('/my/'),
                    'viewmoretext' => new lang_string('mycourses')
                ));
            } else if (core_course_category::top()->is_uservisible()) {
                // All enrolled courses are displayed, display link to 'All courses' if there are more courses in system.
                $chelper->set_courses_display_options(array(
                    'viewmoreurl' => new moodle_url('/course/index.php'),
                    'viewmoretext' => new lang_string('fulllistofcourses')
                ));
                $totalcount = $DB->count_records('course') - 1;
            }
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
            set_attributes(array('class' => 'frontpage-course-list-enrolled'));
            $output .= $this->coursecat_courses($chelper, $courses, $totalcount);

            // MNET
            if (!empty($rcourses)) {
                // at the IDP, we know of all the remote courses
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rcourses as $course) {
                    $output .= $this->frontpage_remote_course($course);
                }
                $output .= html_writer::end_tag('div'); // .courses
            } elseif (!empty($rhosts)) {
                // non-IDP, we know of all the remote servers, but not courses
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rhosts as $host) {
                    $output .= $this->frontpage_remote_host($host);
                }
                $output .= html_writer::end_tag('div'); // .courses
            }
        }
        return $output;
    }

}