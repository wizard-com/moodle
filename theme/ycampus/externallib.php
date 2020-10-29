<?php
/**
 * External course participation api.
 *
 * This api is mostly read only, the actual enrol and unenrol
 * support is in each enrol plugin.
 *
 * @package    theme_ycampus
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class manage_form_submission_external extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function add_review_parameters() {
        return new external_function_parameters(
            array(
                'review' => array(
                    'courseid' => new external_value(PARAM_INT, 'The course where review is submitted'),
                    'userid' => new external_value(PARAM_INT, 'The user who submitted review'),
                    'time_created' => new external_value(PARAM_INT, 'The review submission time'),
                    'comment' => new external_value(PARAM_TEXT, 'The comment written by user'),
                    'rating' => new external_value(PARAM_INT, 'Timestamp when the enrolment end')
                )
            )
        );
    }

    public static function add_review_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'courseid' => new external_value(PARAM_INT, 'The course where review is submitted'),
                    'userid' => new external_value(PARAM_INT, 'The user who submitted review'),
                    'time_created' => new external_value(PARAM_INT, 'The review submission time'),
                    'comment' => new external_value(PARAM_TEXT, 'The comment written by user'),
                    'rating' => new external_value(PARAM_INT, 'Timestamp when the enrolment end')
                )
            )
        );
    }

    /**
     * Adds a review
     * @param array
     * @return array of newly created groups
     * @throws invalid_parameter_exception
     */
    public static function add_review($review){
        global $CFG, $DB;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(self::add_review_parameters(), array('review' => $review));

        $transaction = $DB->start_delegated_transaction();

        $context = context_course::instance($review->courseid);
        self::validate_context($context);

        $record = new stdClass();

        $reviews = array();

        try {
            $DB->insert_record('course_reviews', $record);
            $transaction->allow_commit();
            return get_course_reviews();
        }
        catch (Exception $e){
            $transaction->rollback($e);
        }
         //   $reviews = $DB->get_records()
        return $reviews;
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function add_note_parameters() {
        return new external_function_parameters(
            array(
                'note' => array(
                    'modid' => new external_value(PARAM_INT, 'The course module where notes are submitted'),
                    'userid' => new external_value(PARAM_INT, 'The user who added notes'),
                    'time_created' => new external_value(PARAM_INT, 'The notes creation time'),
                    'note_content' => new external_value(PARAM_TEXT, 'The comment written by user')
                )
            )
        );
    }

    public static function add_note_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'modid' => new external_value(PARAM_INT, 'The course module where notes are submitted'),
                    'userid' => new external_value(PARAM_INT, 'The user who added notes'),
                    'time_created' => new external_value(PARAM_INT, 'The notes creation time'),
                    'note_content' => new external_value(PARAM_TEXT, 'The comment written by user')
                )
            )
        );
    }

    /**
     * Adds a review
     * @param array
     * @return array of newly created groups
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function add_note($note){
        global $CFG, $DB;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(self::add_review_parameters(), array('note' => $note));
        $transaction = $DB->start_delegated_transaction();

        $context = context_module::instance($note->modid);
        self::validate_context($context);

        $record = new stdClass();
        $notes = array();

        try {
            $DB->insert_record('course_module_notes', $record);
            $transaction->allow_commit();
        }
        catch (Exception $e){
            $transaction->rollback($e);
        }

        //   $reviews = $DB->get_records()
        return $notes;
    }

}

