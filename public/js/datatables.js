

var tables;
$(document).ready(function() {
    tables = $('.table-datatable')
    if(!tables) {
        return;
    }

    options = {
        "pagingType": "full_numbers",
        "lengthMenu": [ 15, 25, 50, 75, 100 ]
    };

    tableReorder = tables.hasClass('table-reorder');
    if (tableReorder)
    {
        options['rowReorder'] = {
            selector: 'tr'
        };
        console.log('enabling row reordering');
    }

    var sortingCol = tables.data('datatable-sorting-col');
    console.log('sortingcol', sortingCol);
    if (sortingCol != undefined)
    {
        var sortingType = tables.data('datatable-sorting-type');
        if (!sortingType)
        {
            sortingType = 'asc';
        }
        var orders = []
        if(sortingCol.indexOf(','))
        {
            sortingTokens = sortingCol.split(',');
            for(i = 0; i < sortingTokens.length; i++)
            {
                col = sortingTokens[i].split(':')[0];
                sortOption = sortingTokens[i].split(':')[1] || sortingType;
                orders.push([col, sortOption]);
            }
        }
        else
        {
            orders = [ sortingCol, sortingType ];
        }
        console.log('sorting col ' + sortingCol + ' ' + sortingType);

        options['order'] = orders;
    }

    datatable = tables.DataTable(options);
    var buttonUpdateOrder = $("#update-list");

    if (tableReorder)
    {
        console.log('enabling row reordering event');
        $('#update-list').on('click', function()
        {
            //button = $(this);
            trs = tables.find('tbody tr');
            nb_trs = trs.length;
            order = [];
            for(var i = 0; i < nb_trs; i++)
            {
                order.push($(trs[i]).data('racerid'));
            }
            console.log('new order is', order);
            teamid = $('#teamid').val();
            payload = {
                'order': order,
            };
            $.post('/racer/update-order/' + teamid, payload, function(data, textStatus, jqXHR)
            {
            });

        });
        datatable.on('row-reordered', function (e, diff, edit)
        {
            buttonUpdateOrder.removeAttr('disabled');
        });
    }
});
