function ajax_get_cpu($)  {
    $.ajax({    
        type: 'post',
        url: get_cpu_ajax_url,
        data: {
            'action': 'get_cpu'
        },
        dataType: 'JSON',
        success: function(data){
            $("#hw-cpu").empty();
            $("#hw-cpu").html(data);
            setTimeout(function(){ ajax_get_cpu($); }, 500);
        },
        error: function(errorThrown){
            //error stuff here.text
        }
    });
}

  jQuery(document).ready(function($) {
    ajax_get_cpu($);  
  });