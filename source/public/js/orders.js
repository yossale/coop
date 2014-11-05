$(document).ready(function() {

    $("#users").combobox();
    
    $(".acinput").keyup(function(event) {
        if (event.which == 13)
        {
           var id = $("#users").val();
           window.location = public_path + "/duty/new-order/id/" + id;
        }
    });
    
    $(".acinput").focus();
        
});