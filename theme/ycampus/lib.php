<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.
function theme_ycampus_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_ycampus', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/ycampus/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/ycampus/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
}

/**
 * Serve the files from the theme_ycampus file areas
 * @param string $filearea the name of the file area
 * @return moodle_url
 */
function theme_ycampus_pluginfile($filearea) {
    global $CFG;
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.

    // Make sure the filearea is one of those used by the plugin.
    if (strpos($filearea, 'coursecat') === false) {
        echo "Invlid filearea";
        return new moodle_url($CFG->wwwroot.'/theme/ycampus/infocomm.png');
    }

    $id = (int) substr($filearea, -1);
    $coursecat_img = get_config('theme_ycampus', 'categoryimage'.$id);
    // Retrieve the file from the Files API.

    if(empty($coursecat_img)){
        return new moodle_url($CFG->wwwroot.'/theme/ycampus/infocomm.png');
    }
    $fs = get_file_storage();
    $area_files = $fs->get_area_files(1, 'theme_ycampus', $filearea, 1596696920);
    foreach ($area_files as $file){
        $isimage = $file->is_valid_image();
        $url = "$CFG->wwwroot/pluginfile.php".'/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
            $file->get_filearea(). $file->get_filepath(). $file->get_filename();
        if ($isimage) {
            echo $url.'<br/>';
        }
    }


    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    return moodle_url::make_pluginfile_url(1, 'theme_ycampus', $filearea, 1596696920, '', $coursecat_img);
}

/**
 * Query db to get avg rating value
 *
 * @return array
 */
function get_average_rating_value(){
    global $DB;

    $id = optional_param('id', 0, PARAM_INT);
    $query = "SELECT ROUND(AVG(rating), 1) AS average_rating FROM {course_reviews} WHERE courseid = $id";
    $average_rating = $DB->get_records_sql($query);

    foreach ($average_rating as $rating){
        if(empty($rating->average_rating)){
            return array();
        }
    }
    return $average_rating;

}

/**
 * Query db to get rating breakdown
 * @return array
 */
function get_rating_breakdown(){
    global $DB;

    $id = optional_param('id', 0, PARAM_INT);
    $query = "SELECT rating, COUNT(id) AS rating_count, (rating*20) AS percent FROM {course_reviews}  WHERE courseid = $id GROUP BY rating ORDER BY rating DESC";
    $rating_breakdown = $DB->get_records_sql($query);

    $count = count($rating_breakdown);

    if($count == 0){
        return array();
    }

    while($count < 5){
        $obj = (object) new stdClass();
        $obj->rating = 5 - $count;
        $obj->rating_count = 0;
        $obj->percent = $obj->rating * 20;
        array_push($rating_breakdown, $obj);
        $count++;
    }

    return $rating_breakdown;
}
function get_course_reviews(){
    global $DB, $CFG;
    $id = 0;
    try {
        $id = optional_param('id', 0, PARAM_INT);
    } catch (coding_exception $e) {

    }
    $query = 'SELECT mdl_course_reviews.id, mdl_course_reviews.comment, mdl_course_reviews.rating, mdl_course_reviews.timecreated, mdl_user.username, mdl_user.id AS user_id FROM `mdl_course_reviews` INNER JOIN mdl_user ON mdl_course_reviews.userid = mdl_user.id WHERE mdl_course_reviews.courseid = '.$id;

    $reviews = $DB->get_records_sql($query);


    foreach ($reviews as $review){
        $review->timecreated = date('M d, Y', $review->timecreated);
        $review->url = $CFG->wwwroot."/user/profile.php?id=".$review->user_id;
        $review->grey_block_count = array_fill(0, 5-$review->rating, 0);
        $review->gold_block_count = array_fill(0, $review->rating, 0);
    }

    $course_reviews = [];
    $related_courses = get_related_courses();
    $average_rating = get_average_rating_value();
    $rating_breakdown = get_rating_breakdown();

    $course_reviews['reviews'] = array_values($reviews);
    $course_reviews['related_courses'] = array_values($related_courses);

    if(count($average_rating) > 0){
        $course_reviews['average_ratings'] = array_values($average_rating);
    }

    if(count($rating_breakdown) > 0){
        $course_reviews['rating_breakdowns'] = array_values($rating_breakdown);
        $course_reviews['header_text'] = 'Rating breakdown';
    }

    return $course_reviews;
}

/**
 * Query db to get related courses for a course
 *
 * @return array
 */
function get_related_courses(){
    global $DB, $COURSE;

    $category_id = $COURSE->category;
    $query = "SELECT * FROM {course} WHERE category = $category_id AND id != $COURSE->id";
    $related_courses = $DB->get_records_sql($query);

    foreach ($related_courses as $related) {
        $related->img_url = course_image($related);
    }

    return $related_courses;
}

/**
 * Query db to get popular courses
 *
 * @return array
 */
function get_popular_courses(){
    global $DB;

    $fields = "id, category, sortorder, fullname, shortname, idnumber, summary, summaryformat, format, showgrades, newsitems, startdate, enddate, relativedatesmode, marker, maxbytes, legacyfiles, showreports, visible, visibleold, groupmode, groupmodeforce, defaultgroupingid, lang, calendartype, theme, timecreated, timemodified, requested, enablecompletion, completionnotify, cacherev";
    $sql = "SELECT $fields, COUNT(*) AS enrolments FROM mdl_course c JOIN (SELECT DISTINCT e.courseid, ue.id AS userid FROM {user_enrolments} ue JOIN {enrol} e ON e.id = ue.enrolid) ue ON ue.courseid = c.id GROUP BY c.id, c.fullname HAVING enrolments > 1 ORDER BY enrolments DESC, c.fullname";
    $popular_courses = $DB->get_records_sql($sql);

    if(empty($popular_courses)){
        return array();
    }

    foreach ($popular_courses as $popular) {
        $popular->img_url = course_image($popular);
    }

    $popular_courses = array_values($popular_courses);

    return $popular_courses;
}


/**
 * Query db to get categories
 *
 * @return array
 * @throws dml_exception
 */
function get_course_categories(){
    global $DB;

    $categories = $DB->get_records('course_categories', null, 'name');
    $categories = array_values($categories);

    return $categories;
}

/**
 * Get the image for a course if it exists
 *
 * @param object $course The course whose image we want
 * @return string|void
 */
function course_image($course) {
    global $CFG;

    $course = new core_course_list_element($course);
    $url = "";
    // Check to see if a file has been set on the course level.
    // Check to see if a file has been set on the course level.
    if ($course->id > 0 && $course->get_course_overviewfiles()) {
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = "$CFG->wwwroot/pluginfile.php".'/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                $file->get_filearea(). $file->get_filepath(). $file->get_filename();
            if ($isimage) {
                return $url;
            }
        }
    }
    $courseimagedefault = get_config('block_lw_courses', 'courseimagedefault');
    $url = get_image_url($courseimagedefault);
    return $url;
}
/**
 * Build the Image url for course category
 *
 * @param string $filename Name of the image
 * @param string $filearea file area name
 * @return string
 */
function get_course_cat_img_url($filearea){

    return theme_ycampus_pluginfile($filearea);
}
/**
 * Build the Image url
 *
 * @param string $fileorfilename Name of the image
 * @return moodle_url|string
 */
function get_image_url($fileorfilename) {
    // If the fileorfilename param is a file.
    if ($fileorfilename instanceof stored_file) {
        // Separate each component of the url.
        $filecontextid  = $fileorfilename->get_contextid();
        $filecomponent  = $fileorfilename->get_component();
        $filearea       = $fileorfilename->get_filearea();
        $filepath       = $fileorfilename->get_filepath();
        $filename       = $fileorfilename->get_filename();

        // Generate a moodle url to the file.
        $url = new moodle_url("/pluginfile.php/{$filecontextid}/{$filecomponent}/{$filearea}/{$filepath}/{$filename}");

        // Return an img element containing the file.
        return $url;
    }

    // The fileorfilename param is not a stored_file object, assume this is the name of the file in the blocks file area.
    // Generate a moodle url to the file in the blocks file area.
    return new moodle_url("/pluginfile.php/1/block_lw_courses/courseimagedefault{$fileorfilename}");
}

/**
 * Build the headingImage url
 * @return moodle_url|string
 * @throws dml_exception
 */
function get_default_heading_image_url(){
    $courseimagedefault = get_config('block_lw_courses', 'courseimagedefault');
    $url = get_image_url($courseimagedefault);
    return $url;
}

/**
 * Query db to get user notes
 * @throws dml_exception
 * @throws coding_exception
 * @return array
 */
function get_notes(){

    global $DB, $PAGE, $USER;

    $user_id = $USER->id;
    $context = $PAGE->context->contextlevel;

    if($context == CONTEXT_MODULE){
        $id = optional_param('id', 0, PARAM_INT);
        $query = "SELECT * FROM {course_module_notes} WHERE modid = $id AND userid = $user_id";
        $notes = $DB->get_records_sql($query);
        $notes = array_values($notes);
        return $notes;
    }

    return array();

}

/**
 * Get current userid and moduleid
 * @return object
 * @throws coding_exception
 */
function get_current_user_and_mod(){
    global $USER;

    $data = (object) array();

    $id = optional_param('id', 0, PARAM_INT);

    $data->userid = $USER->id;

    $data->modid = $id;

    return $data;

}

