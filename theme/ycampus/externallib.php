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

class manage_reviews_external extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function add_review_parameters() {
        return new external_function_parameters(
            array(
                'review' =>
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'The course where review is submitted'),
                            'userid' => new external_value(PARAM_INT, 'The user who submitted review'),
                            'time_created' => new external_value(PARAM_INT, 'The review submission time'),
                            'comment' => new external_value(PARAM_TEXT, 'The comment written by user'),
                            'rating' => new external_value(PARAM_INT, 'Timestamp when the enrolment end')
                    )
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

        $record = (object) $params->keys->review->keys;
        $reviews = array();

        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->insert_record('course_reviews', $record);

            $transaction->allow_commit();

            $reviews = $DB->get_records()

        } catch(Exception $e) {
            $transaction->rollback($e);
        }

        return $reviews;
    }

}

