<?php include 'common/common.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Demo</title>
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/style.css">
        
        <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/DataTables/datatables.js"></script>
        <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.css"/>
        
    </head>
    <body>
        <table id="example" class="display" style="width:100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Office</th>
                    <th>Salary</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Office</th>
                    <th>Salary</th>
                </tr>
            </tfoot>
        </table>

        <script type="text/javascript">
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
    var table = $('#example').DataTable( {
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
        scrollY:        '50vh',
        scrollCollapse: true,
        paging:         false
    } );
     
    // Add event listener for opening and closing details
    $('#example tbody').on('click', 'td.datatable-data-col', function () {
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
        </script>
    </body>
</html>