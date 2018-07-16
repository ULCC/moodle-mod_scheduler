var M = M || {};

M.mod_scheduler = {

    restrictbookings  : function (e,maximum) {


        M.mod_scheduler.retrictionenbled(e,maximum);



        $('#id_s_mod_scheduler_maxbookingsenabled').click(function (e) {
            M.mod_scheduler.retrictionenbled(e);
        });



    },


    retrictionenbled    :   function    (e,maximum)     {


        $markingsenabled    =   document.getElementById('id_s_mod_scheduler_maxbookingsenabled');

        if (document.getElementById("id_s_mod_scheduler_maxbookingsenabled").checked  ==  true)   {
            disabled    =   false;
        } else {
            disabled    =   true;
        }

        $(".restrictbookings").each(function (e,elem)    {
            $(elem).attr('disabled',disabled);
        });

        $("#restrictbookings_add").on("click",function()    {
            if (maximum != 0 && $(".restrictbookings_category").length < maximum)

            $(".restrictbookings_category").first().clone().insertAfter($(".restrictbookings_category").last());
        });

        $("#restrictbookings_remove").on("click",function()    {
            if ($(".restrictbookings_category").length > 1)
            $(".restrictbookings_category").last().remove();
        });


    }

}
