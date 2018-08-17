
/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>Full name:</td>'+
            '<td>'+d.name+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extension number:</td>'+
            '<td>'+d.extn+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extra info:</td>'+
            '<td>And any further details here (images etc)...</td>'+
        '</tr>'+
    '</table>';
}
 
$(document).ready(function() {
/*
    var table = $('#dataTable').DataTable( {
        "ajax": "listfiles.php",
        "columns": [
            {
                "className":      'datatable-checkbox',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<input type="checkbox" />'
            },
            { "data": "filename", "className": "datatable-data-col" },
            { "data": "filesize", "className": "datatable-data-col" },
            { "data": "crtime", "className": "datatable-data-col" },
            { "data": "mdtime", "className": "datatable-data-col" }
        ],
        "order": [[1, 'asc']],
        scrollY:        '65vh',
        scrollCollapse: true,
        paging:         false
    } );
*/
    var table = $('#dataTable').DataTable( {
        "ajax": "testdata/data.txt",
        "columns": [
            {
                "className":      'datatable-checkbox',
                "orderable":      false,
                "data":           null,
                "defaultContent": '<input type="checkbox" />'
            },
            { "data": "name", "className": "datatable-data-col" },
            { "data": "position", "className": "datatable-data-col" },
            { "data": "office", "className": "datatable-data-col" },
            { "data": "salary", "className": "datatable-data-col" }
        ],
        "order": [[1, 'asc']],
        scrollY:        '65vh',
        scrollCollapse: true,
        paging:         false
    } );
    
    // Add event listener for opening and closing details
    $('#dataTable tbody').on('click', 'td.datatable-data-col', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
} );