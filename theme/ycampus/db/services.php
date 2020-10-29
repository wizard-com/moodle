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
 * Manual plugin external functions and service definitions.
 *
 * @package    theme_ycampus
 * @category   webservice
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    // === review function ===
    'add_reviews' => array(
        'classname'   => 'manage_form_submission_external',
        'methodname'  => 'add_review',
        'classpath'   => 'theme/ycampus/externallib.php',
        'description' => 'Add reviews',
        'type'        => 'write',
        'ajax'          => true,
    ),
    'add_notes' => array(
        'classname' => 'manage_form_submission_external',
        'methodname'  => 'add_note',
        'classpath'   => 'theme/ycampus/externallib.php',
        'description' => 'Add notes',
        'type'        => 'write',
        'ajax'          => true,
    )
);
