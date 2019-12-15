function ajax_get_memory($)  {
    $.ajax({    
        type: 'post',
        url: get_memory_ajax_url,
        data: {
            'action': 'get_memory'
        },
        dataType: 'JSON',
        success: function(data){
            $("#hw-memory").empty();
            $("#hw-memory").html(data);
            setTimeout(function(){ ajax_get_memory($); }, 500);
        },
        error: function(errorThrown){
            //error stuff here.text
        }
    });
}

  jQuery(document).ready(function($) {
    ajax_get_memory($);  
  });