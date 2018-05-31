<?php

/**
 * A class for representing a scheduler waiting list instance.
 *
 * @copyright  2014 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scheduler_waiting_list      extends mvc_child_record_model {

    const   LISTED      =   0;
    const   PENDING    =   1;
    const   ACCEPTED    =   2;
    const   DECLINED    =   3;
    const   REMOVED     =   4;


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
     * load scheduler waiting list from the database.
     */
    public static function load_by_id($id, scheduler_instance $scheduler) {
        $scheduler_waiting = new scheduler_waiting_list($scheduler);
        $scheduler_waiting->load($id);
        return $scheduler_waiting;
    }

    /**
     * load a scheduler instance from the database using student id and status
     */
    public static function load_by_student($studentid,$scheduler,$status=self::LISTED)      {
        global  $DB;
        $entry      =       $DB->get_record('scheduler_waiting_list',array('schedulerid'=>$scheduler->get_id(),'studentid'=>$studentid,'status'=>$status));

        $scheduler_waiting  =   null;
        if (!empty($entry))   {
            $scheduler_waiting = new scheduler_waiting_list($scheduler);
            $scheduler_waiting->load($entry->id);
        }

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
        return  ($this->data->status == self::ACCEPTED);
    }

    /**
     * was a slot offered and declined
     *
     * @return bool
     */
    public function     slot_declined()   {
        return  ($this->data->status  ==  self::DECLINED);
    }

    /**
     * did the user remove themself from the waiting list
     */
    public  function    entry_removed()     {
        return  ($this->data->status    ==  self::REMOVED);
    }

    /**
     *  Sets the status of a waiting list entry to removed
     */

    public function     remove_entry()     {
        $this->data->timemodified     =   time();
        $this->data->status     =   self::REMOVED;
         parent::save();
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