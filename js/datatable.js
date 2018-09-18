/* Formatting function for row details - modify as you need */
function format ( d ) {
    // `d` is the original data object for the row
    var result = "<ul class='more_fileinfo'>" + 
                "<li>" +
                    "<p class='fileinfo_label'>文件路径</p>" +
                    "<p class='fileinfo_value'>" + d.file_path + "</p>" +
                "</li>" +
                "<li>" +
                    "<p class='fileinfo_label'>元数据副本:数据副本</p>" +
                    "<p class='fileinfo_value'>" + d.metadata_replication + "; " + d.data_replication + "</p>" +
                "</li>";

    if(d.misc_attributes && d.misc_attributes == "ARCHIVE OFFLINE") {
        result = result + 
                "<li class='cloudinfo'>" +
                    "<input id='cloudinfo_btn' type='button' value='查看云信息'' />" + 
                "</li>";
    }

    return result + '</ul>';
}

function createFileTable ( folderName ) {
    // Close the past opened folder, and restore the pool filter
    $("#navbar li.openfolder img").attr("src", "icons/folder.png");
    $("#navbar li.openfolder").removeClass("openfolder");
    if ($("li.folder[path='" + folderName + "']").length > 0) {
        $("li.folder[path='" + folderName + "']").addClass("openfolder").children("img").attr("src", "images/folder-open.png");
    } else { // Open the nearest parent folder
        var tmp = folderName.split("/");
        for (var i = 0; i < tmp.length - 2; i ++) {
            tmp.pop();
            var tmppath = tmp.join("/");
            if ($("li.folder[path='" + tmppath + "']").length > 0) {
                $("li.folder[path='" + tmppath + "']").addClass("openfolder").children("img").attr("src", "images/folder-open.png");
                break;
            }
        }
    }
    $(".placeholder").text("全部存储池");
    $('.select.is-open').removeClass('is-open');

    // Control operation buttons
    $(".exportbtn").remove();
    $(".importbtn").remove();
    if ($("#navbar li.openfolder").hasClass("cleversafe-new sharing")) {
        $("#opbar-btn-container").append("<li class='exportbtn button'><a href = 'JavaScript:void(0)' onclick='exportfiles()'>导出</a></li>");
        $("#opbar-btn-container").append("<li class='importbtn button'><a href = 'JavaScript:void(0)' onclick='importfiles()'>导入</a></li>");
    }

    // Generate return path and action
    folderName = folderName.endWith("/") ? folderName.substr(0, folderName.length - 1) : folderName; // remove the last '/'
    var folders = folderName.substr(1).split("/"); // remove the first '/'

    $("#all_path").empty().attr("path", folderName);
    if (folders.length == 1){
        $("#all_path").append("<label class='folder_path firstpath currentpath'>HOME</label>");
    } else {
        $("#all_path").append("<label onclick=\"createFileTable('/" + folders[0] + "')\" class='folder_path firstpath'>HOME</label><label>></label>");
        var tmpPath = "/" + folders[0];
        
        for (var i = 1; i < folders.length - 1; i ++) {
            tmpPath += ("/" + folders[i]);
            $("#all_path").append("<label onclick=\"createFileTable('" + tmpPath + "')\" class='folder_path'>" + folders[i] + "</label><label>></label>");
        }

        $("#all_path").append("<label class='folder_path currentpath'>" + folders[folders.length - 1] + "</label>");
    }

    //create the file information table
    var table = $('#dataTable').DataTable();
    if (table) {
        // Clear all data under tbody
        table.clear(false);
        // remove the DataTable
        table.destroy();
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var sizeSince = parseInt($("#size_since").val(), 10);
        var sizeTo = parseInt($("#size_to").val(), 10);
        var size = parseFloat(data[2]) || 0;
        var sizeMatched = false;
        if ((isNaN(sizeSince) && isNaN(sizeTo)) || (isNaN(sizeSince) && size <= sizeTo) || (sizeSince <= size && isNaN(sizeTo)) || (sizeSince <= size && size <= sizeTo)) {
            sizeMatched = true;
        }

        var creationdateSince = new Date($.datepicker.formatDate("yy/mm/dd", $("#creationdate_since").datepicker("getDate")));
        var creationdateTo = new Date($.datepicker.formatDate("yy/mm/dd", $("#creationdate_to").datepicker("getDate")));
        var creationDate = new Date(data[3].substr(0, 10).replace(/\-/g, "\/"));
        var creationDateMatched = false;
        if ((isNaN(creationdateSince) && isNaN(creationdateTo)) || (isNaN(creationdateSince) && creationDate <= creationdateTo) || (creationdateSince <= creationDate && isNaN(creationdateTo)) || (creationdateSince <= creationDate && creationDate <= creationdateTo)) {
            creationDateMatched = true;
        }

        var changedateSince = new Date($.datepicker.formatDate("yy/mm/dd", $("#changedate_since").datepicker("getDate")));
        var changedateTo = new Date($.datepicker.formatDate("yy/mm/dd", $("#changedate_to").datepicker("getDate")));
        var changeDate = new Date(data[4].substr(0, 10).replace(/\-/g, "\/"));
        var changeDateMatched = false;
        if ((isNaN(changedateSince) && isNaN(changedateTo)) || (isNaN(changedateSince) && changeDate <= changedateTo) || (changedateSince <= changeDate && isNaN(changedateTo)) || (changedateSince <= changeDate && changeDate <= changedateTo)) {
            changeDateMatched = true;
        }

        if (sizeMatched && creationDateMatched && changeDateMatched) {
            return true;
        }

        return false;
    });

    table = $('#dataTable').DataTable( {
        "ajax": {
            "url":'files.php?myaction=LIST&foldername=' + folderName,
            "type":"GET"
        },
        "columnDefs": [
            {
                "render": function (data, type, row) {
                    if (isNaN(row) && row.type == "0_directory") {
                        return "<i class='fa fa-folder-o fa-lg type-icon' aria-hidden='true'></i>"
                                + "<span class='data-label'>"
                                + "<input id='new_foldername' type='text' />" 
                                + "<i class=\"fa fa-check fa-lg inline-icon newfolder-yes\"  aria-hidden=\"true\" ></i>"
                                + "<i class=\"fa fa-times fa-lg inline-icon newfolder-no\"  aria-hidden=\"true\" ></i>"
                                + "</span>";
                    } else if (row.type == "directory") {
                        return "<i class='fa fa-folder-o fa-lg type-icon' aria-hidden='true'></i><span class='data-label'>" + data + "</span>";
                    } else if (data.toLowerCase().endWith("pdf")) {
                        return "<i class='fa fa-file-pdf-o fa-lg type-icon' aria-hidden='true'></i><span class='data-label'>" + data + "</span>";
                    } else if (data.toLowerCase().endWith("png") || data.toLowerCase().endWith("jpg")) {
                        return "<i class='fa fa-file-image-o fa-lg type-icon' aria-hidden='true'></i><span class='data-label'>" + data + "</span>";
                    } else if (data.toLowerCase().endWith("html")) {
                        return "<i class='fa fa-file-code-o fa-lg type-icon' aria-hidden='true'></i><span class='data-label'>" + data + "</span>";
                    } else if (data.toLowerCase().endWith("ppt") || data.toLowerCase().endWith("pptx")) {
                        return "<i class='fa fa-file-powerpoint-o fa-lg type-icon' aria-hidden='true'></i><span class='data-label'>" + data + "</span>";
                    } else if (data.toLowerCase().endWith("doc") || data.toLowerCase().endWith("docx")) {
                        return "<i class='fa fa-file-word-o fa-lg type-icon' aria-hidden='true'></i><span class='data-label'>" + data + "</span>";
                    } else {
                        return "<i class='fa fa-file-text-o fa-lg type-icon' aria-hidden='true'></i><span class='data-label'>" + data + "</span>";
                    }
                },
                "targets": 1  // file name column
            },
            {
                "data": function(row, type, val, meta) {
                    if (type == "set") {
                        row.file_size = val;
                        return;
                    } else if (type == "display") {
                        if (row.type == "directory" || row.type == "0_directory") {
                            return " - ";
                        } else {
                            return  main_formatDataSizeWithUnit(row.file_size);
                        }
                    } 
                    
                    return row.file_size; // sort, filter, undefined,
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
                        if(row.misc_attributes && row.misc_attributes == "ARCHIVE OFFLINE") {
                            var externalPoolAccount = $("#navbar li.openfolder").attr("account");
                            return "<label class='pool external " + externalPoolAccount + "'>" + externalPoolAccount.toUpperCase() + "</label>";
                        } else {
                            return "<label class='pool internal " + data + "'>" + data.toUpperCase() + "</label>"; 
                        }
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
                "defaultContent": "<label><input class='checkbox' type='checkbox'><span></span></label>",
                "width": "2%"
            },
            { "data": "file_name", "className": "datatable-data-col" },
            { "className": "datatable-data-col" }, // file_size, the data info has been defined in columnDefs
            { "data": "creation_time", "className": "datatable-data-col" },
            { "data": "l_mod_time", "className": "datatable-data-col" },
            { "data": "storage_pool_name", "className": "datatable-data-col pool-col" },
            { "data": "type", "className": "datatable-data-col" }
        ],
        initComplete: function () {
            this.api().columns([5]).every( function () {
                var column = this;
                
                $('.select').off('click', '.placeholder');
                $('.select').off('click', 'ul > li');

                var poolFilter = $("#poolfilter").empty().append("<li class='pool internal' value=\"\">全部存储池</li>");
                // column.data().unique().sort().each( function ( d, j ) {
                //     poolFilter.append( '<li class="' + d + '" value="'+d+'">'+d+'&nbsp;存储池</li>' );
                // } );
                $.ajax({
                    url: "pools.php",
                    method: 'GET',
                    success: function(res) {
                        var tctTieringEnabled = $("#navbar li.openfolder").hasClass("cleversafe-new tiering");

                        $.each(res ,function(i, pool) {
                            var poolName = pool["name"];
                            // If the fileset hasn't enable tct tiering, the COS pool will be exluded from the pool filter list.
                            if (!tctTieringEnabled &&  (!pool["cloudtype"] || pool["cloudtype"] != "cleversafe-new")) {
                                poolFilter.append( "<li class='pool " + pool["type"] + " " + poolName + "' value='" + poolName + "'>" + poolName + "&nbsp;存储池</li>" );
                            } else if (tctTieringEnabled) {
                                if (pool["cloudtype"] && pool["cloudtype"] == "cleversafe-new") {
                                    poolFilter.append( "<li class='pool " + pool["type"] + " " + pool["cloudtype"] + " " + poolName + "' value='" + poolName + "'>" + poolName + "&nbsp;存储池</li>" ); 
                                } else {
                                    poolFilter.append( "<li class='pool " + pool["type"] + " " + poolName + "' value='" + poolName + "'>" + poolName + "&nbsp;存储池</li>" );
                                }
                            }    
                        });
                    }
                });

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
        "pageLength":     10,
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
            createFileTable(main_trim(row.data().file_path));
        } else if (row.data().type != "directory" && row.data().type != "0_directory") {
            if ( row.child.isShown() ) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(row.data()) ).show();
                tr.addClass('shown');
                $(tr).next().find("#cloudinfo_btn").on("click", function () {
                    var moreFileInfo = $(this).closest(".more_fileinfo");
                    var cloudInfo = $(this).parent(".cloudinfo");
                    $.ajax({
                        url: 'cloudgateway.php?myaction=GET&key=FILE&filepath=' + row.data().file_path,
                        method: "GET",
                        success: function (res) {
                            $(cloudInfo).remove();
                            $(moreFileInfo).append('<li>' +
                                    '<p class=\"fileinfo_label\">占用的block数</p>' +
                                    '<p class=\"fileinfo_value\">' + res.used_blocks + '</p>' +
                                '</li>' + 
                                '<li>' +
                                    '<p class=\"fileinfo_label\">元数据版本：数据版本</p>' +
                                    '<p class=\"fileinfo_value\">' + res.meta_version + '; ' + res.data_version + '</p>' +
                                '</li>' +
                                '<li>' +
                                    '<p class=\"fileinfo_label\">数据状态</p>' +
                                    '<p class=\"fileinfo_value\">' + res.state + '</p>' +
                                '</li>' +
                                '<li>' +
                                    '<p class=\"fileinfo_label\">云端数据索引</p>' +
                                    '<p class=\"fileinfo_value\">' + res.base_name + '</p>' +
                                '</li>'
                            );
                        }
                    });
                });
            }
        }
    } );
}
