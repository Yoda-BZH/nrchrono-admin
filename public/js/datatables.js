

var tables;
$(document).ready(function() {
    tables = $('.table-datatable')
    if(!tables) {
        return;
    }

    options = {
        //stateSave: true,
        "pagingType": "full_numbers",
        "lengthMenu": [ 15, 25, 50, 75, 100 ]
    };

    tableReorder = tables.hasClass('table-reorder');
    if (tableReorder)
    {
        options['rowReorder'] = {
            selector: 'td i'
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

    if (tableReorder)
    {
        console.log('enabling row reordering event');
        //datatable.on('row-reorder-changed', function (e, { insertPlacement, insertPoint }) {
        //    console.log('Row moved ' + insertPlacement + ' the ' + insertPoint + '. row');
        //});
        datatable.on('row-reordered', function (e, diff, edit)
        {
            console.log('e', e);
            console.log('diff', diff);
            console.log('edit', edit);
            for (var i = 0, ien = diff.length; i < ien; i++)
            {
                $(diff[i].node).addClass('reordered').css('border', '1px solid red');
                jqObj = edit[i];
                racerId = edit.nodes[i].dataset['racerid'];
                payload = {
                    'oldPosition': diff[i]['oldPosition'] + 1,
                    'newPosition': diff[i]['newPosition'] + 1
                };
                console.log('updated racer id', racerId, 'with', payload);
                console.log('obj', $(edit.nodes[i]), $(edit.nodes[i]).find('td.racer-position'), $(edit.nodes[i]).find('td.racer-position').text());
                $(edit.nodes[i]).find('td.racer-position').text(payload['newPosition']);

                $.post('/racer/update-order/' + racerId, payload, function(data, textStatus, jqXHR)
                {
                    console.log('racer id', racerId, 'updated');
                });
            }

            return true;
        });
    }
});
