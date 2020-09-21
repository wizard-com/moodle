<?php

/**
 *
 * @package    local_message
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_message_before_footer(){
    //die("Hello");
    \core\notification::add("A test message", \core\output\notification::NOTIFY_SUCCESS);
}