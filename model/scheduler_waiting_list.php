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
    const   REMOVED_BY_SYSTEM       =   5;
    const   REMOVED_BY_ADMIN      =   6;

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
     * by system
     */

    public function     system_entry_removeal()     {
        $this->data->timemodified     =   time();
        $this->data->status     =   self::REMOVED_BY_SYSTEM;
        parent::save();
    }

    /**
     *  Sets the status of a waiting list entry to removed
     * by teacher
     */

    public function     admin_entry_removeal()     {
        $this->data->timemodified     =   time();
        $this->data->status     =   self::REMOVED_BY_ADMIN;
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
        if ($this->data->studentid) {
            return $DB->get_record('user', array('id' => $this->data->studentid), '*', MUST_EXIST);
        } else {
            return new stdClass();
        }
    }

    /**
     * Return the id of the group linked with this waiting list entry
     *
     * @return mixed
     */
    public  function    get_group() {
        global  $DB;
        return  $this->data->groupid;
   }


    /**
     * Returns the id of the waiting list
     *
     * @return int| bool the id of the waiting list or false
     */
    public function get_id()    {
        return  (!empty($this->data->id))   ? $this->data->id  :   false;
    }

    /**
     * Returns the timestamp of time the waiting list entry was created
     *
     * @return int| bool the id of the waiting list or false
     */
    public function get_date_created()     {
        return  (isset($this->data->timecreated))   ? $this->data->timecreated  :   false;
    }

    /**
     * returns the text of the current entry status
     */
    public  function get_status()       {
        return  (isset($this->data->status))   ? $this->data->status  :   false;
    }


    /**
     * Returns the name of the group associated with the students waiting list entry
     *
     * @return string
     */
    public  function get_group_name()        {
        global  $DB;

        $groupname      =   '';

        if (!empty($this->data->groupid))   {
            $group     =   $DB->get_record('groups',array('id'=>$this->data->groupid));
            $groupname  =  (!empty($group))    ?   $group->name    :   '';
        }

        return  $groupname;
    }


    /**
     * returns the text of the current entry status
     */
    public  function get_status_text()       {

        $status =   '';

        if (isset($this->data->status)) {
            switch ($this->data->status) {

                case scheduler_waiting_list::LISTED   :

                    $status = get_string('waitinglisted', 'scheduler');
                    break;

                case scheduler_waiting_list::PENDING   :

                    $status = get_string('waitingpending', 'scheduler');
                    break;

                case scheduler_waiting_list::ACCEPTED   :

                    $status = get_string('waitingaccepted', 'scheduler');
                    break;

                case scheduler_waiting_list::DECLINED   :

                    $status = get_string('waitingdeclined', 'scheduler');
                    break;

                case scheduler_waiting_list::REMOVED   :

                    $status = get_string('waitingremoved', 'scheduler');
                    break;

                case scheduler_waiting_list::REMOVED_BY_SYSTEM  :

                    $status = get_string('waitingremovedbysystem', 'scheduler');
                    break;

                case scheduler_waiting_list::REMOVED_BY_ADMIN   :

                    $status = get_string('waitingremovedbyadmin', 'scheduler');
                    break;

            }
        }

        return  $status;
    }

    /**
     * Creates the message to be sent to inform a waiting list user that a slot has become available
     *
     * @param $coursemoduleid
     * @param $waitinglistid
     * @param $studentid
     * @param $courseid
     * @return stdClass a object populared with the message information
     */
    public static function slot_available_message($coursemoduleid,$waitinglistid,$studentid,$courseid,$schedulername)         {

        $acceptparams        =   array('id'=>$coursemoduleid);

        $declineparams        =   array('what'=>'declinewaitinglist','waitinglistid'=>$waitinglistid,'id'=>$coursemoduleid);

        $tempurl             =   new moodle_url('/mod/scheduler/view.php',$acceptparams);

        $msgstrings       =   new stdClass();
        $msgstrings->accept   =   $tempurl->__toString();
        $tempurl             =new moodle_url('/mod/scheduler/view.php',$declineparams);
        $msgstrings->decline  =   $tempurl->__toString();
        $msgstrings->schedulername  =    $schedulername;

        $message                =   new     stdClass();
        $message->studentid      =  $studentid;
        $message->courseid       =  $courseid;
        $message->subject       =   get_string('bookingslotavailablesubject','scheduler',$msgstrings->schedulername);
        $message->fullmsg       =   get_string('bookingslotavailablebody','scheduler',$msgstrings);
        $message->fullmsghtml   =   get_string('bookingslotavailablebodyhtml','scheduler',$msgstrings);

        return  $message;

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
                         ORDER BY   sw.timecreated ASC ";

            $waitinglist        =       $DB->get_records_sql($sql,array('schedulerid'=>$slot->schedulerid,'status'=>self::LISTED));

            if (!empty($waitinglist))   {

                $scheduler      =   scheduler_instance::load_by_id($slot->schedulerid);

                //entry will only be set if we find a slot that can accomadate a indidivual or group on thw waiting list
                $entry  =   false;

                //if this is a scheduler with groups enabled then we have to check to make sure that those in the waiting list
                //are individuals or if they are group make sutre a slot in the waiting list exists with the required capacity
                if ($scheduler->is_group_scheduling_enabled())   {

                    //get all of the capacities of slots in this scheduler
                    $schedulerslotscapacities      =   array();
                    $slots      =   $scheduler->get_slots();

                    foreach($slots  as  $s)   {
                        $schedulerslotscapacities[] = $s->count_remaining_appointments();
                    }

                    foreach($waitinglist    as  $wl) {

                        if (!empty($wl->groupid))   {

                            $group      =   $DB->get_records('groups',array('id'=>$wl->groupid));

                            //find the group size
                            $groupmembers = $scheduler->get_available_students($group);
                            $requiredcapacity = count($groupmembers);

                            //get the available slots
                           foreach($schedulerslotscapacities  as  $slotremainingcapacity)   {

                                if ($slotremainingcapacity > 0 && $requiredcapacity <= $slotremainingcapacity)   {
                                    $entry = $wl;
                                    break 2;
                                }

                            }
                        }  else {
                            $entry = array_shift($waitinglist);
                            break;

                        }



                    }
                } else {
                    $entry = array_shift($waitinglist);
                }

                if (!empty($entry)) {
                    $entry->status = self::PENDING;

                    $DB->update_record('scheduler_waiting_list', $entry);

                    scheduler_waiting_list::waiting_list_message(scheduler_waiting_list::slot_available_message($coursemoduleid, $entry->id, $entry->studentid, $entry->courseid,$scheduler->get_name()));
                }
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


    /**
     * Returns all students on the waiting list with status listed
     *
     * @return array
     */
    public static function get_students_with_listed($schedulerid)     {

        global      $DB;

        $sql        =       "SELECT     w.*, u.firstname, u.lastname
                             FROM       {scheduler_waiting_list}    w,
                                        {user}    u
                             WHERE      w.studentid    =   u.id
                             AND        w.schedulerid  =   :schedulerid
                             AND        w.status       =   :status
                             AND        w.declined     =   :declined";

        return      $DB->get_records_sql($sql,array('schedulerid'=>$schedulerid,'status'=>0,'declined'=>0));
    }

    /**
     * Sends emails to users on a waiting list once the time for scheduler unlock has been reached
     *
     * @param $scheduler a scheduler db record
     */
    public static   function    send_unlock_messages($scheduler)      {

        global  $DB;

        $module         =   $DB->get_record('modules',array('name'=>'scheduler'));

        $coursemodule   =   $DB->get_record('course_modules',array('course'=>$scheduler->course,'module'=>$module->id,'instance'=>$scheduler->id));

        $waitingliststudents   =   scheduler_waiting_list::get_students_with_listed($scheduler->id);

        foreach($waitingliststudents   as  $ws) {

            scheduler_waiting_list::waiting_list_message(scheduler_waiting_list::slot_available_message($coursemodule->id,$ws->id,$ws->studentid,$scheduler->course,$scheduler->name));

            $ws->status     =   scheduler_waiting_list::PENDING;

            $DB->update_record('scheduler_waiting_list',$ws);

        }

        $scheduler->waitinglistunlock   =   0;

        $DB->update_record('scheduler',$scheduler);

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