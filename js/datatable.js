/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    return '<ul id=\"more_fileinfo\">'+
        '<li>'+
            '<p class=\"fileinfo_label\">文件路径:</p>'+
            '<p class=\"fileinfo_value\">'+d.file_path+'</p>'+
        '</li>'+
        '<li>'+
            '<p class=\"fileinfo_label\">元数据副本数:</p>'+
            '<p class=\"fileinfo_value\">'+d.metadata_replication+'</p>'+
        '</li>'+
        '<li>'+
            '<p class=\"fileinfo_label\">数据副本数:</p>'+
            '<p class=\"fileinfo_value\">'+d.data_replication+'</p>'+
        '</li>'
    '</ul>';
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
                    if (isNaN(row) && row.type == "0_directory") {
                        return "<img class='data-icon' src='images/folder-min.png' />"
                                + "<span class='data-label'>"
                                + "<input id='new_foldername' type='text' />" 
                                + "<i class=\"fa fa-check fa-lg inline-icon newfolder-yes\"  aria-hidden=\"true\" ></i>"
                                + "<i class=\"fa fa-times fa-lg inline-icon newfolder-no\"  aria-hidden=\"true\" ></i>"
                                + "</span>";
                    } else if (row.type == "directory") {
                        return "<img class='data-icon' src='images/folder-min.png' /><span class='data-label'>" + data + "</span>";
                    } else {
                        return data;
                    }
                },
                "targets": 1  // file name column
            },
            {
                "render": function (data, type, row) {
                    if (row.type == "directory" || row.type == "0_directory") {
                        return " - ";
                    } else {
                        return main_formatDataSizeWithUnit(data);
                    }
                },
                "targets": 2  // file size column
            },
            {
                "render": function (data, type, row) {
                    if (isNaN(row) && row.type == "0_directory") {
                        return " - ";
                    } else {
                        return data;
                    }
                },
                "targets": [3, 4]
            },
            {
                "render": function (data, type, row) {
                    if (isNaN(row) && row.type == "0_directory") {
                        return " - ";
                    } else {
                        return "<label style='color:green'>" + data.toUpperCase() + "</label>";
                    }
                },
                "targets": 5
            },
            {
                "visible": false,
                "searchable": false,
                "targets": 6
            }
        ],
        "columns": [
            {
                "className":      'datatable-checkbox',
                "orderable":      false,
                "data":           null,
                "defaultContent": "<input type='checkbox' />",
                "width": "2%"
            },
            { "data": "filename", "className": "datatable-data-col" },
            { "data": "file_size", "className": "datatable-data-col" },
            { "data": "creation_time", "className": "datatable-data-col" },
            { "data": "L_mod_time", "className": "datatable-data-col" },
            { "data": "storage_pool_name", "className": "datatable-data-col pool-col" },
            { "data": "type", "className": "datatable-data-col" }
        ],
        initComplete: function () {
            this.api().columns([5]).every( function () {
                var column = this;
                
                var poolFilter = $("#poolfilter").empty().append("<li value=\"\">全部存储池</li>");
                column.data().unique().sort().each( function ( d, j ) {
                    poolFilter.append( '<li value="'+d+'">'+d+'&nbsp;存储池</li>' )
                } );

                $('.select').on('click', '.placeholder', function(e) {
                    var parent = $(this).closest('.select');
                    if (!parent.hasClass('is-open')) {
                        parent.addClass('is-open');
                        $('.select.is-open').not(parent).removeClass('is-open');
                    } else {
                        parent.removeClass('is-open');
                    }
                        e.stopPropagation();
                }).on('click', 'ul>li', function() {
                    var parent = $(this).closest('.select');
                    var placeholder = parent.removeClass('is-open').find('.placeholder');
                    placeholder.text($(this).text());
                    var val = $.fn.dataTable.util.escapeRegex($(this).attr("value"));
                    column.search( val ? '^'+val+'$' : '', true, false ).draw();
                });
 
                $('body').on('click', function() {
                    $('.select.is-open').removeClass('is-open');
                });
            } );
        },
        "orderFixed": [ 6, 'asc' ],
        "order": [3, 'desc'],
        // "scrollY":        '65vh', // KEEP THE COMMENTS HERE!
        // "scrollCollapse": true,   // KEEP THIS LINE HERE!
        "paging":         true,
        "lengthChange":   false,
        "pagingType":     "first_last_numbers",
        "pageLength":     15,
        "destroy":        true
    } );

    $('#dataTable th.datatable-checkbox').prop("onclick",null).off("click");

    // remove old event (if have) listener for opening and closing details
    $('#dataTable tbody').prop("onclick",null).off("click");
    
    // Add event listener for opening and closing details
    $('#dataTable tbody').on('click', 'td.datatable-data-col', function () {
        var table = $('#dataTable').DataTable();
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        
        if (row.data().type == "directory"){
            var folderAbsolutePath = main_trim(row.data().file_path);
            var folders = folderAbsolutePath.substr(1).split("/"); // remove the first '/'
            folders.shift(); // remove the first element from Array, which is the storage pool name.
            var folderRelativePath = folders.join("/");
            createFileTable (folderRelativePath);
            folders.pop(); // remove the last element from Array, which is current folder name.
            backfolder = folders.join('/');

            //set classname for return button.
            $('#backpath').attr("class", backfolder);
            $("#navbar li.openfolder").removeClass("openfolder");
        } else if (row.data().type != "directory" && row.data().type != "0_directory") {
            if ( row.child.isShown() ) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data()) ).show();
                tr.addClass('shown');
            }
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