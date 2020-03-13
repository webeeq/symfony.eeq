$(document).ready(function() {
    $("#menu li").click(function() {
        document.location = $(this).children().attr("href");
    });

    $("#frame").css("height", (document.documentElement.clientHeight - 155) + "px");
});

function ajaxData(id, file) {
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById(id).innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET", file, true);
    xmlhttp.send();
}
