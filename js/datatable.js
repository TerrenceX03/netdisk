
/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>tier name:</td>'+
            '<td>'+d.tier+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>file path:</td>'+
            '<td>'+d.filepath+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extra info:</td>'+
            '<td>And any further details here (images etc)...</td>'+
        '</tr>'+
    '</table>';
}
    
$(document).ready(function() {
    $("li").click(function () {
        // ('#dataTable_wrapper').remove() ; 
        var name_=$(this).attr("id");
        var str = '';
        alert(name_); 
        var table = $('#dataTable').DataTable( {

        "ajax": {
            "url":'listfiles.php',
            "type":"POST",
            "data":function(d){
                d.foldername = name_;
            }
        },
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
            { "data": "modtime", "className": "datatable-data-col" }
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


    });
    
} );