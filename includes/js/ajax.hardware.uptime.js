function ajax_get_uptime($)  {
    $.ajax({    
        type: 'post',
        url: get_uptime_ajax_url,
        data: {
            'action': 'get_uptime'
        },
        dataType: 'JSON',
        success: function(data){
            $("#hw-uptime").empty();
            $("#hw-uptime").html(data);
            setTimeout(function(){ ajax_get_uptime($); }, 500);
        },
        error: function(errorThrown){
            //error stuff here.text
        }
    });
}

  jQuery(document).ready(function($) {
    ajax_get_uptime($);  
  });