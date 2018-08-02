var M = M || {};

M.mod_scheduler = {

    restrictbookings  : function (e,maximum) {


        M.mod_scheduler.retrictionenbled(e,maximum);



        $('#id_s_mod_scheduler_maxbookingsenabled').click(function (e) {
            M.mod_scheduler.retrictionenbled(e);
        });

        $('#id_s_mod_scheduler_maxbookingscourse').click(function (e) {
            M.mod_scheduler.retrictionenbled(e);
        });

        $(".category").each(function (e,elem)    {



            $(elem).on("change",function()  {
                M.mod_scheduler.loadcategorycourses($(this),$(this).siblings(".course").get(0));
            });
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

            clonediv    =   $(".restrictbookings_category").first().clone().insertAfter($(".restrictbookings_category").last());

            $(clonediv).children(".category").on("change",function()  {
                M.mod_scheduler.loadcategorycourses($(this),$(this).siblings(".course").get(0));
            });
;
        });

        $("#restrictbookings_remove").on("click",function()    {
            if ($(".restrictbookings_category").length > 1)
            $(".restrictbookings_category").last().remove();
        });


    },


    loadcategorycourses     :   function    (element,courseselect)  {

        categoryid = $(element).val();

        $.post( "/mod/scheduler/getcategorycourses.php",
            {"categoryid" : categoryid})
            .done(function( data ) {

                    $(courseselect).empty();
                    categorycourses =   JSON.parse(data);

                    options ="";
                    options += '<option value="-1">All courses</option>';
                    $.each(categorycourses, function( k ,v) {
                        options += '<option value="'+ k + '">' + v + '</option>';

                    });

                    $(courseselect).append(options);
            });
    }




}
