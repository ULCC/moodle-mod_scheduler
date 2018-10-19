<?php

/**
 * Defines message providers (types of messages being sent)
 *
 * @package mod_scheduler
 * @copyright  2016 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$messageproviders = array (

    // Invitations to make a booking.
    'invitation' => array(
    ),

    // Notifications about bookings (to teachers or students).
    'bookingnotification' => array(
    ),

    // Automated reminders about upcoming appointments.
    'reminder' => array(
    ),

    // Notification that there is a booking space available for a user on the waiting list.
    'bookingspace' => array(
    ),

    // Notification that a user has joined the waiting list.
    'waitinglistnotification' => array(
    ),

);
