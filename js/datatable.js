
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

function createFileTable ( folderName ) {
    var table = $('#dataTable').DataTable();
<<<<<<< HEAD
    var str = "<input type='checkbox' id=" +folderName +">" +"<img style='display:none'src='images/migration.gif' />"
=======
>>>>>>> 426f18014c68676cbd35a292bcbc8da0609aa72b
    if (table) {
        // Clear all data under tbody
        table.clear(false);
        // remove the DataTable
        table.destroy();
    }

    table = $('#dataTable').DataTable( {
        "ajax": {
            "url":'listfiles.php',
            "type":"POST",
<<<<<<< HEAD
            "data":function(h){
                h.foldername = folderName;
=======
            "data":function(d){
                d.foldername = folderName;
>>>>>>> 426f18014c68676cbd35a292bcbc8da0609aa72b
            }
        },
        "columns": [
            {
                "className":      'datatable-checkbox',
                "orderable":      false,
                "data":           null,
                "defaultContent": str
            },
            { "data": "filename", "className": "datatable-data-col" },
            { "data": "filesize", "className": "datatable-data-col" },
            { "data": "crtime", "className": "datatable-data-col" },
            { "data": "modtime", "className": "datatable-data-col" }
        ],
        "order": [[1, 'asc']],
        "scrollY":        '65vh',
        "scrollCollapse": true,
        "paging":         false,
        "destroy":        true
    } );

    // remove old event (if have) listener for opening and closing details
    $('#dataTable tbody').prop("onclick",null).off("click");
    // Add event listener for opening and closing details
    $('#dataTable tbody').on('click', 'td.datatable-data-col', function () {
        var table = $('#dataTable').DataTable();
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
}
    
$(document).ready(function() {
    // Open the first fileset by default
    var firstFolder = $("#navbar ul li:first");
    var firstFolderName = firstFolder.attr("id");
    $(firstFolder).addClass("openfolder");
    $("#navbar ul li:first img").attr("src", "icons/folder-open.png");
    createFileTable(firstFolderName);

    $("#navbar li").click(function () {
        if ($(this).hasClass("openfolder")) {
            return;
        }

        $("#navbar li.openfolder img").attr("src", "icons/folder.png");
        $("#navbar li.openfolder").removeClass("openfolder");
        $(this).addClass("openfolder");
        $(this).children("img").attr("src", "icons/folder-open.png");

        createFileTable($(this).attr("id"))
    });  
} );