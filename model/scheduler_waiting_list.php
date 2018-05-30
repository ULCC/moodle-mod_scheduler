<?php

/**
 * A class for representing a scheduler waiting list instance.
 *
 * @copyright  2014 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scheduler_waiting_list      extends mvc_child_record_model {


    public function __construct(scheduler_instance $scheduler) {
        global $USER;

        parent::__construct();
        $this->data = new stdClass();
        $this->set_parent($scheduler);
        $this->data->schedulerid    = $scheduler->get_id();
        $this->data->studentid      = $USER->id;
        $this->data->timecreated    = $this->data->timemodified =   time();
    }

    /**
     * Create a scheduler waiting list from the database.
     */
    public static function load_by_id($id, scheduler_instance $scheduler) {
        $scheduler_waiting = new scheduler_waiting_list($scheduler);
        $scheduler_waiting->load($id);
        return $scheduler_waiting;
    }

    /**
     * Save any changes to the database
     */
    public function save() {
        $this->data->schedulerid = $this->get_parent()->get_id();

        if (isset($this->data->id)) {
            $this->data->timemodified     =   time();
        }

        parent::save();
    }

    public function     get_table() {
        return  'scheduler_waiting_list';
    }

    /**
     * was a slot offered and accepted
     *
     * @return bool
     */
    public function     slot_accepted()   {
        return  (bool)  $this->data->accepted;
    }

    /**
     * was a slot offered and declined
     *
     * @return bool
     */
    public function     slot_declined()   {
        return  (bool)  $this->data->declined;
    }

    /**
     * Return the student object
     *
     * @return stdClass
     */
    public function get_student() {
        global $DB;
        if ($this->data->teacherid) {
            return $DB->get_record('user', array('id' => $this->data->studentid), '*', MUST_EXIST);
        } else {
            return new stdClass();
        }
    }

}


/**
 * A factory class for scheduler slots.
 *
 * @copyright  2011 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scheduler_waiting_list_entry_factory extends mvc_child_model_factory {
    public function create_child(mvc_record_model $parent) {
        return new scheduler_waiting_list($parent);
    }
}