<?php

class   setting_restrictbookings  extends     admin_setting  {



    public function __construct($numberofbookingsname, $periodname, $visiblename, $description, $defaultsetting,$bookingoptions,$periodoptions, $categoryoptions, $maxcat, $courseoptions) {

        $this->numberofbookingsname      =   $numberofbookingsname;
        $this->periodname     =   $periodname;

        $this->bookingoptions   =   $bookingoptions;
        $this->periodoptions    =   $periodoptions;
        $this->categoryoptions  =   $categoryoptions;
        $this->courseoptions    =   $courseoptions;

         $t     =   explode('/', $this->numberofbookingsname);
         $this->numberofbookingsname_number      =  $t[1];
        $this->numberofbookingsname_period      =  $t[1].'_period';
        $this->numberofbookingsname_category  =  $t[1].'_category';
        $this->numberofbookingsname_course  =  $t[1].'_course';
        $this->numberofbookingsname_enabled =   $t[1].'_enabled';
        /*
            $t     =    explode('/', $this->numberofbookingsname.'_period');
        $this->numberofbookingsname_period      =  $t[1];
        $t     =    explode('/', $this->numberofbookingsname.'_category');
         $this->numberofbookingsname_category  =  $t[1];
        $t     =    explode('/', $this->numberofbookingsname.'_enabled');
        $this->numberofbookingsname_enabled =   $t[1];
*/
        $this->maximumcats      =   $maxcat;

        parent::__construct($numberofbookingsname, $visiblename, $description, $defaultsetting);

    }

    function    write_setting($data)     {
        if (!is_array($data)) {
            return '';
        }

        if (isset($data['booking']))    {
                $bookingdata    =   (is_array($data['booking']))    ?   implode(',',$data['booking'])   : $data['booking']  ;
        } else {
                $bookingdata    =   0;
        }

        if (isset($data['period']))    {
            $perioddata    =   (is_array($data['period']))    ?   implode(',',$data['period'])   : $data['period']  ;
        }   else    {
            $perioddata    =    0;
        }

        if (isset($data['category']))    {
            $categorydata    =   (is_array($data['category']))    ?   implode(',',$data['category'])   : $data['category']  ;
        }   else    {
            $categorydata    =    0;
        }

        if (isset($data['course']))    {
            $coursedata    =   (is_array($data['course']))    ?   implode(',',$data['course'])   : $data['course']  ;
        }   else    {
            $coursedata    =    0;
        }


        $result = $this->config_write($this->numberofbookingsname_number, $bookingdata) && $this->config_write($this->numberofbookingsname_period, $perioddata)
            && $this->config_write($this->numberofbookingsname_category, $categorydata) && $this->config_write($this->numberofbookingsname_course, $coursedata)
            && $this->config_write($this->numberofbookingsname_enabled, (int)$data['enabled']);
        return ($result ? '' : get_string('errorsetting', 'admin'));
    }

    function    get_setting()
    {
        $bookings = $this->config_read($this->numberofbookingsname_number);
        $periods = $this->config_read($this->numberofbookingsname_period);
        $categories = $this->config_read($this->numberofbookingsname_category);
        $courses    = $this->config_read($this->numberofbookingsname_course);
        $enabled = $this->config_read($this->numberofbookingsname_enabled);
        if (is_null($bookings) || is_null($periods) || is_null($categories) || is_null($enabled)) {
            return NULL;
        }

        $bookings           =   explode(",",$bookings);
        $periods            =   explode(",",$periods);
        $categories         =   explode(",",$categories);
        $courses         =   explode(",",$courses);


        return array('booking' => $bookings, 'period' => $periods, 'category'=> $categories, 'course' => $courses, 'enabled' => $enabled);
    }

    /**
     * Returns XHTML time select fields
     *
     * @param array $data Must be form 'booking'=>xx, 'period'=>xx, 'category'=>xx
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



        $catgoryrestriction   =   array();

        for($i =  0;$i < count($data['booking']);$i++) {

            $numberofbookings   =   array();

            foreach ($this->bookingoptions as $k => $v) {

                $t = array();

                $t['name'] = $v;
                $t['value'] = $k;
                $t['selected'] = $k == $data['booking'][$i];

                $numberofbookings[] = $t;
            }

            $bookingperiod = array();

            foreach ($this->periodoptions as $k => $v) {

                $t = array();

                $t['name'] = $v;
                $t['value'] = $k;
                $t['selected'] = $k == $data['period'][$i];

                $bookingperiod[] = $t;
            }

            $categoryoptions    =   array();

            foreach ($this->categoryoptions as $k => $v) {

                $t = array();

                $t['name'] = $v;
                $t['value'] = $k;
                $t['selected'] = $k == $data['category'][$i];

                $categoryoptions[] = $t;
            }

            $courseoptions    =   array();

            foreach ($this->courseoptions as $k => $v) {

                $t = array();

                $t['name'] = $v;
                $t['value'] = $k;
                $t['selected'] = $k == $data['course'][$i];

                $courseoptions[] = $t;
            }

            $restrictions   =   new     stdClass();
            $restrictions->numberofbookings     =       $numberofbookings;
            $restrictions->bookingperiod        =       $bookingperiod;
            $restrictions->category             =       $categoryoptions;
            $restrictions->course               =       $courseoptions;

            $catgoryrestriction[]       =       $restrictions;

        }



        $context                    =   new stdClass();
        $context->id                =   $this->get_id();
        $context->name              =   $this->get_full_name();
        $context->cateogoryrestrictions      =   $catgoryrestriction;


        $context->checked           =   $data['enabled'] == 1;
        $context->enablestr               =   'enable';
        $context->coursestr               =   ' in ';
        $context->bookingstr               =   ' allow ';
        $context->periodstr               =   ' bookings in ';
        $context->maximum               =   $this->maximumcats;




        $element = $OUTPUT->render_from_template('mod_scheduler/setting_restrictbookings', $context);

        $module = array(
            'name' => 'mod_scheduler',
            'fullpath' => '/mod/scheduler/js/settings.js',
            'requires' => array(
                'node'
            ));

        $PAGE->requires->js_init_call('M.mod_scheduler.restrictbookings', array('maximum'=>$this->maximumcats),true,$module);

        return format_admin_setting($this, $this->visiblename, $element, $this->description,
            $this->get_id() , '', $defaultinfo, $query);
    }

}
