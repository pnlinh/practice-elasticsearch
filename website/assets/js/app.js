$(function() {

    var height= $(".debug-container").height();
    var down=height-45;

    $(".debug-toggle").data("slide","up");
    $(".debug-container").css("bottom","-"+down+"px");

    $(".debug-toggle").click(function () {

        var height= $(".debug-container").height();
        var down=height-45;

        if($(".debug-toggle").data("slide")=="up") {
            $(".debug-toggle").data("slide","down");
            $(".debug-toggle i").removeClass("fa-angle-up");
            $(".debug-toggle i").addClass("fa-angle-down");
            $(".debug-container").css("bottom","0px");
        }
        else
        {
            $(".debug-toggle").data("slide","up");
            $(".debug-container").css("bottom","-"+down+"px");
            $(".debug-toggle i").addClass("fa-angle-up");
            $(".debug-toggle i").removeClass("fa-angle-down");
        }
    });

});
