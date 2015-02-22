

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
    if (sortingCol != '')
    {
        console.log('setting order to ' + sortingCol);
        options.order = [ sortingCol, 'asc' ];
    }
    console.log(options);

    tables.DataTable(options);
    //new $.fn.dataTable.FixedHeader( tables );
    //new $.fn.dataTable.KeyTable( tables );
});
