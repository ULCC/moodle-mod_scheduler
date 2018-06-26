<?php

class   setting_restrictbookings  extends     admin_setting  {



    public function __construct($numberofbookingsname, $periodname, $visiblename, $description, $defaultsetting,$bookingoptions,$periodoptions) {

        $this->numberofbookingsname      =   $numberofbookingsname;
        $this->periodname     =   $periodname;

        $this->bookingoptions   =   $bookingoptions;
        $this->periodoptions    =   $periodoptions;

         $t     =   explode('/', $this->numberofbookingsname);
         $this->numberofbookingsname_number      =  $t[1];
            $t     =    explode('/', $this->numberofbookingsname.'_period');
         $this->numberofbookingsname_period  =  $t[1];
        $t     =    explode('/', $this->numberofbookingsname.'_enabled');
        $this->numberofbookingsname_enabled =   $t[1];


        parent::__construct($numberofbookingsname, $visiblename, $description, $defaultsetting);

    }

    function    write_setting($data)     {
        if (!is_array($data)) {
            return '';
        }

        $bookingdata    =       (isset($data['booking']))   ?    (int) $data['booking']    :   0;

        $perioddata    =       (isset($data['period']))   ?    (int) $data['period']    :   0;

        $result = $this->config_write($this->numberofbookingsname_number, $bookingdata) && $this->config_write($this->numberofbookingsname_period, $perioddata)
            && $this->config_write($this->numberofbookingsname_enabled, (int)$data['enabled']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    function    get_setting()
    {
        $result1 = $this->config_read($this->numberofbookingsname_number);
        $result2 = $this->config_read($this->numberofbookingsname_period);
        $result3 = $this->config_read($this->numberofbookingsname_enabled);
        if (is_null($result1) or is_null($result2) or is_null($result3)) {
            return NULL;
        }

        return array('booking' => $result1, 'period' => $result2, 'enabled' => $result3);
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'h'=>xx, 'm'=>xx
     * @param string $query
     * @return string XHTML time select fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT, $PAGE;

        $default = $this->get_defaultsetting();
        if (is_array($default)) {
            $defaultinfo = $default['booking'].':'.$default['period'];
        } else {
            $defaultinfo = NULL;
        }


        $numberofbookings   =   array();

        foreach($this->bookingoptions   as      $k => $v)   {

            $t      =   array();

            $t['name']      =   $v;
            $t['value']     =   $k;
            $t['selected']  =   $k == $data['booking'];

            $numberofbookings[]     =   $t;
        }

        $bookingperiod      =   array();

        foreach($this->periodoptions   as      $k => $v)   {

            $t      =   array();

            $t['name']      =   $v;
            $t['value']     =   $k;
            $t['selected']  =   $k == $data['period'];

            $bookingperiod[]     =   $t;
        }

        $context                    =   new stdClass();
        $context->id                =   $this->get_id();
        $context->name              =   $this->get_full_name();
        $context->numberofbookings  =   $numberofbookings;
        $context->bookingperiod     =   $bookingperiod;
        $context->checked           =   $data['enabled'] == 1;
        $context->enablestr               =   'enable';
        $context->bookingstr               =   ' allow ';
        $context->periodstr               =   ' bookings in ';




        $element = $OUTPUT->render_from_template('mod_scheduler/setting_restrictbookings', $context);

        $module = array(
            'name' => 'mod_scheduler',
            'fullpath' => '/mod/scheduler/js/settings.js',
            'requires' => array(
                'node'
            ));

        $PAGE->requires->js_init_call('M.mod_scheduler.restrictbookings', array($this->get_id()),true,$module);

        return format_admin_setting($this, $this->visiblename, $element, $this->description,
            $this->get_id() , '', $defaultinfo, $query);
    }

}
