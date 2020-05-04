$(document).ready(function() {
    $("#menu li").click(function() {
        document.location = $(this).children().attr("href");
    });

    $("#frame").css("height", (document.documentElement.clientHeight - 155) + "px");
});
