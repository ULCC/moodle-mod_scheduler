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



echo $output->heading(get_string('waitinglist', 'scheduler'));

if ($scheduler->uses_waiting_list()) {

    $waitinglisttable = new     scheduler_waiting_list_table($scheduler->id, $scheduler->cmid);
    echo $output->render($waitinglisttable);
} else  {

    echo html_writer::div(get_string('waitinglistnotenabled','scheduler'));

}

// Finish the page.
echo $OUTPUT->footer($course);
exit;