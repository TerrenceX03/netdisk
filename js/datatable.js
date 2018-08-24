/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    return '<table id="more_fileinfo" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>file path:</td>'+
            '<td>'+d.file_path+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>folder path:</td>'+
            '<td>'+d.folder_path+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>filetype:</td>'+
            '<td>'+d.filetype+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extra info:</td>'+
            '<td>And any further details here (images etc)...</td>'+
        '</tr>'+
    '</table>';
}

function createFileTable ( folderName ) {
    //add the file information to table and modify the foderName to the correct format.
    var elem = document.getElementById('all_path').innerHTML = "";
    var tmp_array = folderName.split('/');
    var new_foldername = '';

    for (var i = 0; i < tmp_array.length; i++) {
        if (tmp_array[i] != '') {
            var tmp_str = '';
            var back_str = '';

            for (var j = 0; j <= i; j++) {
                if (j == i) {
                    back_str += tmp_array[j];  
                } else {
                    back_str += tmp_array[j]; 
                    back_str += '/';
                }
            }

            if (i == (tmp_array.length - 1)) {                
                new_foldername += tmp_array[i]; 
                tmp_str += "<label onclick=\"back_to_click_folder(\'"+back_str+"\')\" class='folder_path'>"+tmp_array[i]+"</label>";
            } else {
                new_foldername += tmp_array[i]; 
                new_foldername += '/'; 
                tmp_str += "<label onclick=\"back_to_click_folder(\'"+back_str+"\')\" class='folder_path'>"+tmp_array[i]+"/</label>";
            }

            $('#all_path').append(tmp_str);
        }
    }
    //create the file information table
    var table = $('#dataTable').DataTable();
    var str = "<input type='checkbox' id=" + new_foldername + ">";
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
        "columnDefs": [
            {
                "render": function (data, type, row) {
                return main_formatDataSizeWithUnit(data);
                },
                "targets": 2  // file size column
            }
        ],
        "columns": [
            {
                "className":      'datatable-checkbox',
                "orderable":      false,
                "data":           null,
                "defaultContent": str,
                "width": "2%"
            },
            { "data": "filename", "className": "datatable-data-col" },
            { "data": "file_size", "className": "datatable-data-col" },
            { "data": "creation_time", "className": "datatable-data-col" },
            { "data": "L_mod_time", "className": "datatable-data-col" },
            { "data": "storage_pool_name", "className": "datatable-data-col" }
        ],
        "order": [[1, 'asc']],
        // "scrollY":        '65vh', // KEEP THE COMMENTS HERE!
        // "scrollCollapse": true,   // KEEP THIS LINE HERE!
        "paging":         true,
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
        if (row.data().filetype == "directory"){
            var folder = row.data().file_path.substr(20);
            createFileTable (folder);
            var backfolder = folder.split("/");
            backfolder.pop();
            backfolder = backfolder.join('/');
            //set classname for return button.
            $('#backpath').attr("class", backfolder);
            $("#navbar li.openfolder").removeClass("openfolder");
        }
        //Click to show more information
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
}

/*Click the "返回" button to return back*/
function returnback(){
    var backfolder = $('#backpath').attr('class');
    createFileTable(backfolder);
    backfolder = backfolder.split("/");
    backfolder.pop();
    backfolder = backfolder.join('/');
    $('#backpath').attr("class", backfolder);
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

/*Delete*/
function deletefiles(){
    var tbodyObj = document.getElementById('dataTable');
    $("table :checkbox").each(function(key, value) {
        if ($(value).prop('checked')) {

            var table = $('#dataTable').DataTable();
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var label=document.getElementById("all_path");

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            //delete the file in information table
            filepath = label.innerText + "/" + tbodyObj.rows[key+1].cells[1].innerHTML;
            tbodyObj.rows[key+1].innerHTML="";

            $.ajax({
                url: "files.php?myaction=DELETE_FILE&filepath=" + filepath,
                dataType: 'json',                
                success: function(res) {
                    if (res.msg == 1) {
                        //pass
                    };
                }
            });
            
        }
    })   
}