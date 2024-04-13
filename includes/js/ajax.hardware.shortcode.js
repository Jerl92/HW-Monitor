function ajax_get_hw_shortcode($)  {
    $.ajax({    
        type: 'post',
        url: get_hw_shortcode_ajax_url,
        data: {
            'action': 'get_hw_shortcode'
        },
        dataType: 'JSON',
        success: function(data){
            hw_info = document.getElementById("hw-wraper");
            if (hw_info) {
                $("#hw-system-load").append(data[0]);
                $("#hw-system-load-ram").append(data[1]);
                $("#hw-system-load-bandwitch").append(data[2]);
                setTimeout(function(){ ajax_get_hw_shortcode($); }, 1000);
            }
        },
        error: function(errorThrown){
            //error stuff here.text
        }
    });
}

  jQuery(document).ready(function($) {
    hw_info = document.getElementById("hw-wraper");
    if (hw_info) {
        ajax_get_hw_shortcode($);  
    }
  });