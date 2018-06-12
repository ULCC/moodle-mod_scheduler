<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/scheduler/locallib.php');

$taburl = new moodle_url('/mod/scheduler/view.php', array('id' => $scheduler->cmid,
    'what' => 'viewwaitinglist'));

$PAGE->set_url($taburl);

echo $OUTPUT->header();

// Display navigation tabs.

echo $output->teacherview_tabs($scheduler, $taburl, '');

echo $output->mod_intro($scheduler);

$waitinglisttable   =   new     scheduler_waiting_list_table($scheduler->id,$scheduler->cmid);

echo $output->heading(get_string('waitinglist', 'scheduler'));

echo $output->render($waitinglisttable);

// Finish the page.
echo $OUTPUT->footer($course);
exit;