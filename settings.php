<?php

/**
 * Global configuration settings for the scheduler module.
 *
 * @package    mod_scheduler
 * @copyright  2011 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    global  $DB;

    require_once($CFG->dirroot.'/mod/scheduler/lib.php');

    require_once $CFG->dirroot.'/mod/scheduler/local_adminlib.php';

    $settings->add(new admin_setting_configcheckbox('mod_scheduler/allteachersgrading',
                     get_string('allteachersgrading', 'scheduler'),
                     get_string('allteachersgrading_desc', 'scheduler'),
                     0));

    $settings->add(new admin_setting_configcheckbox('mod_scheduler/showemailplain',
                     get_string('showemailplain', 'scheduler'),
                     get_string('showemailplain_desc', 'scheduler'),
                     0));

    $settings->add(new admin_setting_configcheckbox('mod_scheduler/groupscheduling',
                     get_string('groupscheduling', 'scheduler'),
                     get_string('groupscheduling_desc', 'scheduler'),
                     1));

    $settings->add(new admin_setting_configcheckbox('mod_scheduler/mixindivgroup',
                     get_string('mixindivgroup', 'scheduler'),
                     get_string('mixindivgroup_desc', 'scheduler'),
                     1));

    $settings->add(new admin_setting_configtext('mod_scheduler/maxstudentlistsize',
                     get_string('maxstudentlistsize', 'scheduler'),
                     get_string('maxstudentlistsize_desc', 'scheduler'),
                     200, PARAM_INT));

    $settings->add(new admin_setting_configtext('mod_scheduler/uploadmaxfiles',
                     get_string('uploadmaxfilesglobal', 'scheduler'),
                     get_string('uploadmaxfilesglobal_desc', 'scheduler'),
                     5, PARAM_INT));

    $bookingoptions        =   array();
    for($i=0;$i<11;$i++)    {
        $bookingoptions[$i]    =   $i;
    }


    $periodoptions  =   array();
    $periodoptions[86400]   =   '1 day';
    $periodoptions[172800]   =   '2 days';
    $periodoptions[604800]   =   '1 week';
    $periodoptions[1209600]   =   '2 weeks';

    $categories         =   $DB->get_records('course_categories');
    $categoryoptions    =   array();



    foreach($categories     as      $c) {
        $categoryoptions[$c->id]    =   $c->name ;

    }

    $maxcategorys       =   (!empty($categories))   ?   count($categories) :   0;

    $courses            =   $DB->get_records('course');

    $courseoptions      =   array(-1=>get_string('allcourses','scheduler'),
        -2=>get_string('eachcourse','scheduler'));

    foreach($courses     as      $c) {
        if ($c->id != 1)     $courseoptions[$c->id]    =   $c->shortname ;
    }

    $settings->add(new setting_restrictbookings('mod_scheduler/maxbookings',
        'mod_scheduler/bookingperiod',
        get_string('maxbookings', 'scheduler'),
        get_string('maxbookings_desc', 'scheduler'),
        5, $bookingoptions, $periodoptions, $categoryoptions, $maxcategorys, $courseoptions));

   //$settings->add(new )

}
