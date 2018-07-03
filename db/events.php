<?php

$observers = array(

    array(
        'eventname'   => 'mod_scheduler\event\booking_added',
        'callback'    => 'scheduler_waiting_list::booking_added',
        'includefile' => 'mod/scheduler/model/scheduler_waiting_list.php',
    ),

    array(
        'eventname'   => 'mod_scheduler\event\booking_removed',
        'callback'    => 'scheduler_waiting_list::booking_removed',
        'includefile' => 'mod/scheduler/model/scheduler_waiting_list.php',
    ),

    array(
        'eventname'   => 'mod_scheduler\event\slot_added',
        'callback'    => 'scheduler_waiting_list::slot_added',
        'includefile' => 'mod/scheduler/model/scheduler_waiting_list.php',
    ),


    


);
