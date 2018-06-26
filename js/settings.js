var M = M || {};

M.mod_scheduler = {

    restrictbookings  : function (e) {

        console.log('test');
        console.log(document.getElementById('id_s_mod_scheduler_maxbookingsenabled'));

        M.mod_scheduler.retrictionenbled(e);



        $('#id_s_mod_scheduler_maxbookingsenabled').click(function (e) {
            M.mod_scheduler.retrictionenbled(e);
        });



    },


    retrictionenbled    :   function    (e)     {

        $markingsenabled    =   document.getElementById('id_s_mod_scheduler_maxbookingsenabled');

        if (document.getElementById("id_s_mod_scheduler_maxbookingsenabled").checked  ==  true)   {

            console.log('enabled');

disabled    =   false;

        } else {

            console.log('disabled');

            disabled    =   true;

        }

        $('#id_s_mod_scheduler_maxbookingsbooking').attr('disabled',disabled);
        $('#id_s_mod_scheduler_maxbookingsperiod').attr('disabled',disabled);

    }

}
