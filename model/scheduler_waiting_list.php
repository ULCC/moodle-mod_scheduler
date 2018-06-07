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

    /**
     * scheduler_waiting_list constructor.
     * @param scheduler_instance $scheduler
     */
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
     *
     * @param $id
     * @param scheduler_instance $scheduler
     * @return scheduler_waiting_list
     */
    public static function load_by_id($id, scheduler_instance $scheduler) {
        $scheduler_waiting = new scheduler_waiting_list($scheduler);
        $scheduler_waiting->load($id);
        return $scheduler_waiting;
    }

    /**
     * load a scheduler instance from the database using student id and status
     *
     * @param $studentid
     * @param $scheduler
     * @param int $status
     * @return false|scheduler_waiting_list an instance of a waiting list
     */
    public static function load_by_student($studentid,$scheduler,$status=self::LISTED)      {
        global  $DB;
        $entry      =       $DB->get_record('scheduler_waiting_list',array('schedulerid'=>$scheduler->get_id(),'studentid'=>$studentid,'status'=>$status));

        $scheduler_waiting  =   false;
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

    /**
     * Returns the name of the table that scheduler waiting list information is taken from
     * @return string
     */
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
     *
     * @return bool
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
     *  Sets the status of a waiting list entry to removed
     */
    public function     decline_entry()     {
        $this->data->timemodified     =   time();
        $this->data->status     =   self::DECLINED;
        parent::save();
    }


    /**
     * Return the student object linked to a scheduler instance
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

    /**
     * Called when a booking has been removed, this function informs the next student on the waiting list that
     * that they may make a booking
     *
     * @param $eventdetails event details passed by the booking removed event
     */
    public static function booking_removed( $eventdetails)    {

        global  $DB;

        $userid         =       $eventdetails->userid;
        $slotid         =       $eventdetails->objectid;
        $coursemoduleid =       $eventdetails->contextinstanceid;

        //get slot information
        $slot           =       $DB->get_record('scheduler_slots',array('id'=>$slotid));


        if (!empty($slot))   {

            $sql    =   "SELECT     sw.*, s.course as courseid   
                         FROM       {scheduler_waiting_list}  sw,
                                    {scheduler} s
                         WHERE      sw.schedulerid   =   :schedulerid
                         AND        s.id          =   sw.schedulerid
                         AND        status        =   :status
                         ORDER BY   sw.timecreated DESC ";

            $waitinglist        =       $DB->get_records_sql($sql,array('schedulerid'=>$slot->schedulerid,'status'=>self::LISTED));

            if (!empty($waitinglist))   {


                $firstentry     =       array_pop($waitinglist);

                $firstentry->status     =   self::PENDING;

                $DB->update_record('scheduler_waiting_list',$firstentry);


                $acceptparams        =   array('id'=>$coursemoduleid);

                $declineparams        =   array('what'=>'declinewaitinglist','waitinglistid'=>$firstentry->id,'id'=>$coursemoduleid);

                $decisionurls       =   new stdClass();
                $decisionurls->accept  =   new moodle_url('/mod/scheduler/view.php',$acceptparams);
                $decisionurls->decline=   new moodle_url('/mod/scheduler/view.php',$declineparams);

                $htmlmsg    =       html_writer::tag('p',get_string('bookingslotavailablebody','scheduler'));

                $htmlmsg    .=      html_writer::link($decisionurls->accept,get_string('acceptwaitingslot','scheduler'));
                $htmlmsg    .=      html_writer::empty_tag('br');
                $htmlmsg    .=      html_writer::empty_tag('br');
                $htmlmsg    .=      html_writer::link($decisionurls->decline,get_string('declinewaitingslot','scheduler'));

                $visiturl  =       get_string('visiturloptions','scheduler',$decisionurls);
                //$visitdeclineurl  =       get_string('visitdeclineurl','scheduler',$decisionurls->decline);

                $msgdetails             =   new stdClass();
                $msgdetails->studentid      =   $firstentry->studentid;
                $msgdetails->courseid       =   $firstentry->courseid;
                $msgdetails->subject        =   get_string('bookingslotavailablesubject','scheduler');
                $msgdetails->fullmsg        =   get_string('bookingslotavailablebody','scheduler').' '.$visiturl;
                $msgdetails->fullmsghtml        =   $htmlmsg;
                scheduler_waiting_list::waiting_list_message($msgdetails);

            }



        }

    }

    /**
     * Called when a booking is made this function updates the status of a waiting list entry if the booking was made by
     * someone on the waiting list
     *
     * @param $eventdetails event details passed by the booking added event
     */
    public static function booking_added($eventdetails)    {

        global      $DB;

        $userid         =       $eventdetails->userid;
        $slotid         =       $eventdetails->objectid;

        //get slot information
        $slot           =       $DB->get_record('scheduler_slots',array('id'=>$slotid));


        if (!empty($slot))   {
            $params     =   array('studentid'=>$userid,'schedulerid'=>$slot->schedulerid,'status'=>self::PENDING);
            //see if the booking is linked to a waiting list entry if yes then set it to acceptted
            $waitinglistentry   =   $DB->get_record('scheduler_waiting_list',$params);

            if (!empty($waitinglistentry))  {
                $waitinglistentry->status           =   self::ACCEPTED;
                $waitinglistentry->timemodified     =   time();
                $DB->update_record('scheduler_waiting_list',$waitinglistentry);

            }


        }
    }

    /**
     * Called when  a slot is added, this function informs the next student on the waiting list that
     * that they may make a booking
     *
     * @param $eventdetails event details passed by the slot added event
     */
    public static function  slot_added($eventdetails)       {

        scheduler_waiting_list::booking_removed($eventdetails);

    }

    /**
     * Sends a message to the given student from a given user
     *
     * @param $msgdetails
     */
    public static   function waiting_list_message($msgdetails)     {

        global      $DB,$USER;


        $student    =   $DB->get_record('user',array('id'=>$msgdetails->studentid));
        $sender     =   (!empty($msgdetails->teacherid))    ?   $DB->get_record('user',array('id'=>$msgdetails->teacherid)) : $USER;

        $message = new \core\message\message();
        $message->component         = 'mod_scheduler';
        $message->name              = 'bookingspace';
        $message->userfrom          = $sender;
        $message->userto            = $student;
        $message->subject           = $msgdetails->subject;
        $message->fullmessage       = $msgdetails->fullmsg;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = $msgdetails->fullmsghtml;
        $message->smallmessage      = $msgdetails->fullmsg;
        $message->notification      = '0';

        $message->courseid = $msgdetails->courseid;

        $messageid = message_send($message);

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