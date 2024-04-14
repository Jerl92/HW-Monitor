/**
 * hwm.js
 * @fileoverview Javascript for HW Monitor
 * @author PRESSMAN
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 or higher
 */
    var graphs = {};
    var interval_hw;

    function getData() {
        if (document.hidden) {
            // Stop when background.
            return;
        }

        hw_info = document.getElementById("hwm-area");
        if (hw_info) {

            jQuery.ajax({
                type: 'post',
                url: get_c3_ajax_url,
                data: {
                    'action': 'get_c3'
                },
                dataType: 'json',
                success: function(data){
                    $('#test').text(JSON.stringify(data));
                    var len = data.length;
        
                    for (var i = 0; i < len; i++) {
                        var d = data[i];
        
                        if (!$('#' + d.id)[0]) {
                            var $hwmArea = $('#hwm-area'),
                                areaHtml = '' +
                                '<div id="' + d.id + '">' +
                                '  <div id="' + d.id + '-title" class="title">' +
                                '    <h1>' + d.name + '</h1>' +
                                '    <div class="sub" title="' + d.summary + '">' + d.summary + '</div>' +
                                '  </div>' +
                                '  <small>' + $('#sec').val() + '</small>' +
                                '  <div id="' + d.id + '-graph" class="graph"></div>' +
                                '  <div id="' + d.id + '-desc" class="desc"></div>' +
                                '  <div id="' + d.id + '-error" class="hwm-error"></div>' +
                                '</div>';
        
                            if ($hwmArea.children('div').length && !($hwmArea.children('div').length % 2)) {
                                $hwmArea.append('<hr>');
                            }
        
                            $hwmArea.append($(areaHtml));
        
                            graphs[d.id] = c3.generate({
                                bindto: '#' + d.id + '-graph',
                                data: {
                                    columns: [
                                        ['data0'].concat(Array(50).fill(null))
                                    ],
                                    names: { data0: d.name },
                                    types: { data0: 'area' }
                                },
                                axis: { x: { show: false }, y: { min: 0, max: d.max, padding: { top: 0, bottom: 0 } } },
                                grid: { x: { show: true }, y: { show: true } },
                                point: { r: 0 },
                                color: { pattern: [d.color] },
                                legend: { show: false },
                                tooltip: {
                                    format: {
                                        title: function(d) {
                                            return '';
                                        }
                                    }
                                }
                            });
                        }
        
                        graphs[d.id].flow({
                            columns: [
                                ['data0'].concat([d.rate])
                            ]
                        });
                        var $desc = $('#' + d.id + '-desc');
                        $desc.empty();
        
                        for (var j in d.desc) {
                            $desc.append('' +
                                '<div>' +
                                '  <h5>' + j + '</h5>' +
                                '  <div>' + d.desc[j] + '</div>' +
                                '</div>');
                        }
        
                        var $error = $('#' + d.id + '-error');
                        $error.empty();
        
                        for (var j in d.error) {
                            $error.append('<div>' + d.error[j].message + '</div>');
                        }
                    }
                },
                error: function(errorThrown){
                    //error stuff here.text
                }
            });

        } else {
            clearInterval(interval_hw);
        }
    }

    $(function() {
        hw_info = document.getElementById("hwm-area");
        if (hw_info) {
            hw_info.innerHTML = '';
            getData();
            interval_hw = setInterval(getData, parseInt($('#interval').val(), 10) * 1000);
        } else {
            clearInterval(interval_hw);
        }
    });
