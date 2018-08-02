<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

global $CFG, $USER, $DB;

if (is_siteadmin($USER->id))        {

    $categoryid     =    required_param('categoryid',PARAM_INT);

    $courses        =   $DB->get_records('course',array('category'=> $categoryid));

    $coursesarray   =   array();

    foreach($courses    as  $c)   {
        $coursesarray[$c->id]   =   $c->shortname;
    }

    echo json_encode($coursesarray);

}