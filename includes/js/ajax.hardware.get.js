function ajax_get_hw($)  {
    $.ajax({    
        type: 'post',
        url: get_hw_ajax_url,
        data: {
            'action': 'get_hw'
        },
        dataType: 'JSON',
        success: function(data){
            $("#hw-info").empty();
            setTimeout(function(){ ajax_get_hw($); }, 1000);
            $("#hw-info").html(data);
        },
        error: function(errorThrown){
            //error stuff here.text
        }
    });
}

  jQuery(document).ready(function($) {
    hw_info = document.getElementById("hw-info");
    if (hw_info) {
        ajax_get_hw($);  
    }
  });