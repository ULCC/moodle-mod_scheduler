<?php
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class waitinglist_form extends moodleform {


    protected   $scheduler;

    public function __construct($action = null, $scheudler)  {


        $this->scheduler    =   $scheudler;

        parent::__construct($action);
    }

    //Add elements to form
    public function definition() {
        global $CFG, $USER;

        $mform = $this->_form;

        $mygroupsforscheduling = groups_get_all_groups($this->scheduler->courseid, $USER->id, $this->scheduler->bookingrouping, 'g.id, g.name');
        $groupchoice = array();

        if ($this->scheduler->is_individual_scheduling_enabled()) {
            $groupchoice[0] = get_string('myself', 'scheduler');
        }

        foreach ($mygroupsforscheduling as $group) {
            $groupchoice[$group->id] = $group->name;
        }

        $mform->addElement('select', 'appointgroup', get_string('joinwaitinglistfor', 'scheduler'), $groupchoice);

        $mform->addElement('hidden','what','joinwaitinglist');

        $mform->setType('what',PARAM_RAW);

        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('joinwaitinglist', 'scheduler'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}