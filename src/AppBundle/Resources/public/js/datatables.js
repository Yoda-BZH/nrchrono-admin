

var tables;
$(document).ready(function() {
    tables = $('.table-datatable')
    if(!tables) {
        return;
    }
    options = {
        //stateSave: true,
        "pagingType": "full_numbers",
    };
    sortingCol = tables.data('datatable-sorting-col');
    if (sortingCol != undefined)
    {
        sortingType = tables.data('datatable-sorting-col');
        if (!sortingType)
        {
            sortingType = 'asc';
        }
        
        options.order = [ sortingCol, sortingType ];
    }

    tables.DataTable(options);
    //new $.fn.dataTable.FixedHeader( tables );
    //new $.fn.dataTable.KeyTable( tables );
});
