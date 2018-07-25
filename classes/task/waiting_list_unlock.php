<?php

/**
 * Scheduled background task for sending automated messages to users on waiting lists
 *
 * @package    mod_scheduler
 * @copyright  2016 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_scheduler\task;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/../../model/scheduler_instance.php');
require_once(dirname(__FILE__).'/../../model/scheduler_waiting_list.php');
require_once(dirname(__FILE__).'/../../model/scheduler_slot.php');
require_once(dirname(__FILE__).'/../../model/scheduler_appointment.php');


/**
 *  Scheduled background task for sending automated messages to users on waiting lists
 *
 * @package    mod_scheduler
 * @copyright  2016 Henning Bostelmann and others (see README.txt)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class waiting_list_unlock extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('waitinglistunlocktask', 'mod_scheduler');
    }

    public function execute() {
        \scheduler_instance::unlocked_schedulers();
    }



}