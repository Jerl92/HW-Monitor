!function ($) {
    let adminAjaxUrl = $('#admin-ajax-url').val(),
        phmData = {},
        phmGraphs = {};

    function getData() {
        $.ajax({
            url: adminAjaxUrl,
            type: 'post',
            data: {'action': 'pmhm'}
        }).done(function (data) {
            $('#test').text(JSON.stringify(data));
            let len = data.length;

            for (let i = 0; i < len; i++) {
                let d = data[i];

                if (!$('#' + d.id)[0]) {
                    let areaHtml = '' +
                            '<div id="' + d.id + '">' +
                            '  <h3>' + d.name + '</h3>' +
                            '  <small>' + $('#sec').val() + '</small>' +
                            '  <div id="' + d.id + '-graph"></div>' +
                            '  <div id="' + d.id + '-desc"></div>' +
                            '</div>';
                    $('#pmhm-area').append($(areaHtml));

                    phmData[d.id] = Array(25).fill(null);
                    phmGraphs[d.id] = c3.generate({
                        bindto: '#' + d.id + '-graph',
                        data: {
                            columns: [['data0'].concat(phmData[d.id])],
                            names: {data0: d.name},
                            types: {data0: 'area'}
                        },
                        axis: {x: {show: false}, y: {min: 0, max: 100, padding: {top: 0, bottom: 0}}},
                        grid: {x: {show: true}, y: {show: true}},
                        point: {r: 0},
                        color: {pattern: [d.color]},
                        legend: {show: false}
                    });
                }

                phmData[d.id].shift();
                phmData[d.id].push(d.rate);
                phmGraphs[d.id].load({columns: [['data0'].concat(phmData[d.id])]});
                //graphs[d.id].flow({columns: [['data0'].concat([d.rate])]});
            }
        });
    }

    $(function () {
        getData();
        setInterval(getData, parseInt($('#interval').val(), 10) * 1000);
    });
}(jQuery);