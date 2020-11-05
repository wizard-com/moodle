<?php

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/customfield/handler.php');

class coursecat_handler extends core_customfield\handler {

    /**
     * Context that should be used for new categories created by this handler
     *
     * @return \context
     */
    public function get_configuration_context(): \context
    {
        // TODO: Implement get_configuration_context() method.
    }

    /**
     * URL for configuration of the fields on this handler.
     *
     * @return \moodle_url
     */
    public function get_configuration_url(): \moodle_url
    {
        // TODO: Implement get_configuration_url() method.
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
    }

    /**
     * The current user can configure custom fields on this component.
     *
     * @return bool
     */
    public function can_configure(): bool
    {
        // TODO: Implement can_configure() method.
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
    }
}
