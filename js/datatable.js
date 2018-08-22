/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    return '<table id="more_fileinfo" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>file path:</td>'+
            '<td>'+d.file_path+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extra info:</td>'+
            '<td>And any further details here (images etc)...</td>'+
        '</tr>'+
    '</table>';
}
function createFileTable ( folderName ) {
    var table = $('#dataTable').DataTable();
    var str = "<input type='checkbox' id=" +folderName +">";
    if (table) {
        // Clear all data under tbody
        table.clear(false);
        // remove the DataTable
        table.destroy();
    }
    table = $('#dataTable').DataTable( {
        "ajax": {
            "url":'files.php?myaction=LIST',
            "type":"POST",
            "data":function(h){
                h.foldername = folderName;
            }
        },
        "columns": [
            {
                "className":      'datatable-checkbox',
                "orderable":      false,
                "data":           null,
                "defaultContent": str
            },
            { "data": "filename", "className": "datatable-data-col",},
            { "data": "file_size", "className": "datatable-data-col" },
            { "data": "creation_time", "className": "datatable-data-col" },
            { "data": "L_mod_time", "className": "datatable-data-col" },
            { "data": "storage_pool_name", "className": "datatable-data-col" }
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
        var row = table.row(tr);
        //Only directory can be opened again
        if (row.data().filetype=="directory"){
            var folder = row.data().file_path.substr(20);
            createFileTable (folder);
        }
        //Click to show more information
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