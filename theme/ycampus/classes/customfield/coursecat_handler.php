<?php

/**
 * Course category handler for custom fields
 *
 * @package   theme_ycampus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_ycampus\customfield;

defined('MOODLE_INTERNAL') || die;

use core_customfield\field_controller;

class coursecat_handler extends \core_customfield\handler {

    /**
     * @var coursecat_handler
     */
    static protected $singleton;
    /** @var int Field is displayed in the course listing, visible to everybody */
    const VISIBLETOALL = 2;
    /** @var int Field is displayed in the course listing but only for teachers */
    const VISIBLETOTEACHERS = 1;
    /** @var int Field is not displayed in the course listing */
    const NOTVISIBLE = 0;

    /**
     * Returns a singleton
     *
     * @param int $itemid
     * @return \theme_ycampus\customfield\coursecat_handler
     */
    public static function create(int $itemid = 0) : \core_customfield\handler {
        if (static::$singleton === null) {
            self::$singleton = new static(0);
        }
        return self::$singleton;
    }


    /**
     * Context that should be used for new categories created by this handler
     *
     * @return \context
     */
    public function get_configuration_context(): \context
    {
        // TODO: Implement get_configuration_context() method.
        return \context_system::instance();

    }

    /**
     * URL for configuration of the fields on this handler.
     *
     * @return \moodle_url
     */
    public function get_configuration_url(): \moodle_url
    {
        // TODO: Implement get_configuration_url() method.
        return new \moodle_url('/course/coursecatcustomfield.php');
    }

    /**
     * Context that should be used for data stored for the given record
     *
     * @param int $instanceid id of the instance or 0 if the instance is being created
     * @return \context
     */
    public function get_instance_context(int $instanceid = 0): \context
    {
        // TODO: Implement get_instance_context() method.
        if ($instanceid > 0) {
            return \context_coursecat::instance($instanceid);
        } else {
            return \context_system::instance();
        }
    }

    /**
     * The current user can configure custom fields on this component.
     *
     * @return bool
     */
    public function can_configure(): bool
    {
        // TODO: Implement can_configure() method.
        return has_capability('moodle/category:manage', $this->get_configuration_context());
    }

    /**
     * The current user can edit given custom fields on the given instance
     *
     * Called to filter list of fields displayed on the instance edit form
     *
     * Capability to edit/create instance is checked separately
     *
     * @param \core_customfield\field_controller $field
     * @param int $instanceid id of the instance or 0 if the instance is being created
     * @return bool
     */
    public function can_edit(\core_customfield\field_controller $field, int $instanceid = 0): bool
    {
        // TODO: Implement can_edit() method.
        if ($instanceid) {
            $context = $this->get_instance_context($instanceid);
            return has_capability('moodle/category:manage', $context);
        } else {
            return false;
        }
    }

    /**
     * Returns the parent context for the course category
     *
     * @return \context
     */
    protected function get_parent_context() : \context {
        return \context_system::instance();
    }

    /**
     * The current user can view the value of the custom field for a given custom field and instance
     *
     * Called to filter list of fields returned by methods get_instance_data(), get_instances_data(),
     * export_instance_data(), export_instance_data_object()
     *
     * Access to the instance itself is checked by handler before calling these methods
     *
     * @param \core_customfield\field_controller $field
     * @param int $instanceid
     * @return bool
     */
    public function can_view(\core_customfield\field_controller $field, int $instanceid): bool
    {
        // TODO: Implement can_view() method.
        $visibility = $field->get_configdata_property('visibility');
        if ($visibility == self::NOTVISIBLE) {
            return false;
        } else if ($visibility == self::VISIBLETOTEACHERS) {
            return has_capability('moodle/category:viewcourselist', $this->get_instance_context($instanceid));
        } else {
            return true;
        }
    }

    /**
     * Set up page customfield/edit.php
     *
     * @param field_controller $field
     * @return string page heading
     */
    public function setup_edit_page(field_controller $field) : string {
        global $CFG, $PAGE;
        require_once($CFG->libdir.'/adminlib.php');

        $title = parent::setup_edit_page($field);
        $PAGE->navbar->add($title);
        return $title;
    }
    /**
     * Allows to add custom controls to the field configuration form that will be saved in configdata
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform) {
        $mform->addElement('header', 'course_handler_header', get_string('customfieldsettings', 'core_course'));
        $mform->setExpanded('course_handler_header', true);

        // If field is locked.
        $mform->addElement('selectyesno', 'configdata[locked]', get_string('customfield_islocked', 'core_course'));
        $mform->addHelpButton('configdata[locked]', 'customfield_islocked', 'core_course');

        // Field data visibility.
        $visibilityoptions = [self::VISIBLETOALL => get_string('customfield_visibletoall', 'core_course'),
            self::VISIBLETOTEACHERS => get_string('customfield_visibletoteachers', 'core_course'),
            self::NOTVISIBLE => get_string('customfield_notvisible', 'core_course')];
        $mform->addElement('select', 'configdata[visibility]', get_string('customfield_visibility', 'core_course'),
            $visibilityoptions);
        $mform->addHelpButton('configdata[visibility]', 'customfield_visibility', 'core_course');
    }

}
