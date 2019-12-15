function ajax_get_bandwidth($)  {
    $.ajax({    
        type: 'post',
        url: get_bandwidth_ajax_url,
        data: {
            'action': 'get_bandwidth'
        },
        dataType: 'JSON',
        success: function(data){
            $("#hw-bandwidth").empty();
            $("#hw-bandwidth").html(data);
            setTimeout(function(){ ajax_get_bandwidth($); }, 1000);
        },
        error: function(errorThrown){
            //error stuff here.text
        }
    });
}

  jQuery(document).ready(function($) {
    ajax_get_bandwidth($);  
  });