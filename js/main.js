$(document).ready(function() {
    initNavBar();
    generateProgressBar();
    initStatInfo();
    initFilter();

    $("#allcheckbox").on("click", function() {
        if ($(this).prop("checked")) {
            $("#dataTable td.datatable-checkbox input[type=checkbox]").prop("checked", true);
        } else {
            $("#dataTable td.datatable-checkbox input[type=checkbox]").prop("checked", false);
        }
        
    });
});

function initNavBar() {
    $.ajax({
        url: "filesets.php?myaction=LIST",
        method: "GET",
        success: function (res) {
            var filesetContainer = $("#navbar-fileset");
            $("#navbar-fileset").empty();

            $.each(res, function(i, fileset) {
                $("#navbar-fileset").append("<li id='" + fileset["name"] + "' path='" + fileset["path"] + "' class='folder chinese " + fileset["status"].toLowerCase() + "'><img class='folder-icon' src='images/folder.png'><label>" + fileset["name"] + "</label></li>");
            });

            // Open the first fileset by default
            var firstFolder = $("#navbar ul li:first");
            var firstFolderName = firstFolder.attr("id");
            $(firstFolder).addClass("openfolder");
            $("#navbar ul li:first img").attr("src", "images/folder-open.png");
            createFileTable(firstFolderName);

            $("#navbar li").click(function () {
                if ($(this).hasClass("openfolder")) {
                    return;
                }

                $("#navbar li.openfolder img").attr("src", "icons/folder.png");
                $("#navbar li.openfolder").removeClass("openfolder");
                $(this).addClass("openfolder");
                $(this).children("img").attr("src", "images/folder-open.png");

                $(".placeholder").text("全部存储池");
                $('.select.is-open').removeClass('is-open');

                createFileTable($(this).attr("id"))
            });
        }
    });  
}

function initFilter() {
    var creationdateSince = $("#creationdate_since").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd"
    }).on("change", function () {
        creationdateTo.datepicker("option", "minDate", main_getDate(this));
    });

    var creationdateTo = $("#creationdate_to").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd"
    }).on("change", function () {
        creationdateSince.datepicker("option", "maxDate", main_getDate(this));
    });

    var changedateSince = $("#changedate_since").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd"
    }).on("change", function () {
        changedateTo.datepicker("option", "minDate", main_getDate(this));
    });

    var changedateTo = $("#changedate_to").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd"
    }).on("change", function () {
        changedateSince.datepicker("option", "maxDate", main_getDate(this));
    });

    $("#btn_filter").on("click", function () {
        $("#dataTable").DataTable().draw();
    });

    $("#btn_clear").on("click", function () {
        $("#size_since, #size_to, #creationdate_since, #creationdate_to, #changedate_since, #changedate_to").val("");
        creationdateSince.datepicker("option", "maxDate", "");
        creationdateTo.datepicker("option", "minDate", "");
        changedateSince.datepicker("option", "maxDate", "");
        changedateTo.datepicker("option", "minDate", "");
        $("#dataTable").DataTable().draw();
    });
}

/* input the folder name and create the folder added in the table */
function CreateFolder(){
    if ($("#new_foldername").length > 0) {
        $("#new_foldername").css("border", "1px solid red");
    } else {
        var t = $('#dataTable').DataTable();
        t.row.add({
            "filename": "-",
            "file_size": "-",
            "creation_time": "-",
            "L_mod_time": "-",
            "storage_pool_name": "-",
            "file_path": "-",
            "folder_path": "-",
            "filetype": "directory",
            "type": "0_directory",
            "action": ""
        }).draw(false);

        $("#dataTable i.newfolder-yes").on("click", function () {
            var foldername = $("#new_foldername").val();
            if (main_trim(foldername) == "") {
                $("#new_foldername").css("border", "1px solid red");
            } else {
                var tr = $(this).closest('tr');
                var label = document.getElementById("all_path");
                var true_filepath = '/' + label.innerText + '/';
                $.ajax({
                    url: "folder.php?myaction=POST",
                    dataType: 'json',
                    data: {
                        foldername: foldername,
                        folderpath: true_filepath
                    },
                    method: 'POST',
                    success: function(res) {
                        if (res.result == 1) {
                            t.row(tr).data(res.files);
                            t.draw(false);
                        } else {
                            alert(res.error);
                        }
                    }
                });
            }
        });

        $("#dataTable i.newfolder-no").on("click", function () {
            var tr = $(this).closest('tr');
            t.row(tr).remove().draw(false);
        });
    }
}

/* add the new uploaded files in table */
function uploadfile(){
    var label = document.getElementById("all_path");
    var true_filepath = label.innerText;
    $(document).ready(function() {
        $('#fileupload').fileupload({
            url: "files.php?myaction=POST&parent=" + true_filepath,
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    var t = $('#dataTable').DataTable();
                    t.row.add({
                        "filename": file.name,
                        "file_size": file.size,
                        "creation_time": file.creation_time,
                        "L_mod_time": file.L_mod_time,
                        "storage_pool_name": file.storage_pool_name,
                        "file_path":file.file_path,
                        "folder_path":file.folder_path,
                        "filetype":file.filetype,
                        "type":file.type,
                        "metadata_replication":file.metadata_replication,
                        "data_replication":file.data_replication
                    }).draw(false);

                    $("#messageBar-icon i").removeClass("fa-times-circle").removeClass("fa-exclamation-triangle").addClass("fa-check");
                    $("#messageBar-msg span").empty().append("æ–‡ä»¶ä¸Šä¼ æˆåŠŸ: " + file.name + "!");
                    $("#messageBar").css({"display":"block", "color": "#33a451", "border-color": "#33a451"});
                    $("#log").append("<span>" + file.name + " has been uploaded sucessfully!");
                });
            },
            fail: function (e, data) {
                $("#messageBar-icon i").removeClass("fa-check").addClass("fa-times-circle");
                $("#messageBar-msg span").empty().append("è­¦å‘Šï¼šæ–‡ä»¶ä¸Šä¼ å¤±è´? " + file.name + "ï¼?" + data);
                $("#messageBar").css({"display":"block", "color": "#ea4335", "border-color": "#ea4335"});
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css('width',progress + '%');
            }
        });
    });
}

/* Delete files */
function deletefiles(){
    main_generateDeleteDialog();
}

/*
    return to display file information of clicked floder
*/
function back_to_click_folder(folder) {
    createFileTable (folder);
}

/* 
GenerateProgressBarï¼šGenerate the progressbar for tier information such as GPFS
tier:COS,GPFS,etc..
barID:The ID of progressbar
labelClass:Html sign for class
*/
function generateProgressBar() {
    $.ajax({
        url: "pools.php",
        method: 'GET',
        success: function(res) {
            $("#stat_progressbar_content tbody").empty();

            $.each(res ,function(i, pool) {
                if (pool["type"] == "internal") {
                    var poolName = pool["name"];
                    var totaldatasize = pool["totaldatasize"];
                    var freedatasize = pool["freedatasize"];
                    var userdatapercentage = 100 - pool["freedatapercentage"];

                    $("#stat_progressbar_content tbody").append(
                        "<tr><td class='stat_progressbar-name english' colspan='2'><label class='left'>" + poolName.toUpperCase() + "</label><label class='right'>FREE: " + main_formatDataSizeWithUnit(freedatasize) + "</label></td></tr>"
                        + "<tr><td class='stat_progressbar-row'><div id='stat_progressbar-" + poolName + "'></div></td><td class='stat_progressbar-label english'><div>" + userdatapercentage + "%</div></td></tr>"
                        + "<tr><td class='stat_progressbar-name english bottom' colspan='2'><label class='left'>USED: " + main_formatDataSizeWithUnit(totaldatasize - freedatasize) + "</label><label class='right'>CAPACITY: " + main_formatDataSizeWithUnit(totaldatasize) + "</label></td></tr>"
                    );

                    $("#stat_progressbar-" + poolName).progressbar({
                        value: userdatapercentage
                    });

                    var backgroundColor = "green";
                    if (userdatapercentage > 50 && userdatapercentage <= 90) {
                        backgroundColor = "yellow";
                    } else if (userdatapercentage > 90) {
                        backgroundColor = "red";
                    }
                    $("#stat_progressbar-" + poolName + " .ui-widget-header").css({
                        'background': backgroundColor
                    });
                }
            });
        }
    })
}

function main_generateDeleteDialog() {
    var table = $("#dataTable").DataTable();
    var checkedLines = $("#dataTable tbody input[type=checkbox]:checked");

    if (checkedLines.length == 0) {
        $("#delete-dialog i").removeClass("fa-info-circle").addClass("fa-exclamation-triangle");
        $("#delete-dialog p.messageType").empty().append("Warning!");
        $("#delete-dialog p.selectedFiles").empty().append("You must select at least one file!");

        var dialog = $("#delete-dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function() {
                    $("#delete-dialog p").empty();
                    $(this).dialog("destroy");
                }
            }
        });
    } else {
        $("#delete-dialog i").removeClass("fa-exclamation-triangle").addClass("fa-info-circle");
        $("#delete-dialog p.messageType").empty().append("Information!");
        $("#delete-dialog p.selectedFiles").empty().append("You have selected " + checkedLines.length + " files.<br/>Do you confirm to delete these files?");

        var dialog = $("#delete-dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "Confirm": function() {
                    var trs = new Array();
                    var filepaths = new Array();

                    $.each(checkedLines, function (i, line) {
                        var tr = $(line).closest('tr');
                        trs.push(tr);
                        filepaths.push(table.row(tr).data().file_path);
                        $(tr).children(".pool-col").append("<img src='images/recycling.gif' style='margin-left:10px;height:17px'/>");
                        $(tr).find("input[type=checkbox]").prop("disabled", true);
                    });

                    $("#delete-dialog p").empty();
                    $(this).dialog("destroy");

                    $.ajax({
                        url: "files.php?myaction=DELETE",
                        dataType: 'json',
                        data: {
                            files: JSON.stringify(filepaths)
                        },
                        method: 'POST',
                        success: function(res) {
                            var failed = 0;
                            var successful = 0;

                            $.each(res, function(i, j) {
                                var row = table.row(trs[i]);
                                if (j.result == 1) { // migrate sucessfully
                                    $("#log").append("<span>" + row.data().filename + " has been deleted</span><br/>");
                                    successful ++;
                                    row.remove();
                                } else {
                                    $(trs[i]).children(".pool-col").children("img").remove();
                                    $(trs[i]).find("input[type=checkbox]").prop("disabled", false);
                                    $("#log").append("<span>" + row.data().filename + " hasn't been deleted with error: " + j.error + "</span><br/>");
                                    failed ++;
                                }
                            });

                            if (failed > 0 && successful > 0) {
                                $("#messageBar-icon i").removeClass("fa-check").addClass("fa-exclamation-triangle");
                                $("#messageBar-msg span").empty().append("Warning! Successfully deleted " + successful + " files, and failed to delete " + failed + " files!");
                                $("#messageBar").css({"display":"block", "color": "#fbbc05", "border-color": "#fbbc05"});
                            } else if (failed > 0 && successful == 0) {
                                $("#messageBar-icon i").removeClass("fa-check").addClass("fa-times-circle");
                                $("#messageBar-msg span").empty().append("Warning! Successfully deleted " + successful + " files, and failed to delete " + failed + " files!");
                                $("#messageBar").css({"display":"block", "color": "#ea4335", "border-color": "#ea4335"});
                            } else {
                                $("#messageBar-icon i").removeClass("fa-times-circle").removeClass("fa-exclamation-triangle").addClass("fa-check");
                                $("#messageBar-msg span").empty().append("Good! Successfully deleted " + successful + " files!");
                                $("#messageBar").css({"display":"block", "color": "#33a451", "border-color": "#33a451"});
                            }

                            table.draw(false);
                            generateProgressBar();
                        }
                    });
                },
                Cancel: function() {
                    $("#delete-dialog p").empty();
                    $("#delete-dialog ul").empty();
                    $(this).dialog("destroy");
                }
            }
        });
    }
}

function main_generateMigrationDialog() {
    var table = $("#dataTable").DataTable();
    var checkedLines = $("#dataTable tbody input[type=checkbox]:checked");

    if (checkedLines.length == 0) {
        $("#migration-dialog i").removeClass("fa-info-circle").addClass("fa-exclamation-triangle");
        $("#migration-dialog p.messageType").empty().append("Warning!");
        $("#migration-dialog p.selectedFiles").empty().append("You must select at least one file!");

        var dialog = $("#migration-dialog").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function() {
                    $("#migration-dialog p").empty();
                    $("#migration-dialog ul").empty();
                    $(this).dialog("destroy");
                }
            }
        });
    } else {
        var files = new Array();
        files["pool"] = new Array();

        $.each(checkedLines, function (i, line) {
            var tr = $(line).closest('tr');
            var row = table.row(tr);
            var poolName = row.data().storage_pool_name;
            var filePath = row.data().file_path;

            if (!files[poolName]) {
                files[poolName] =  new Array();
                files[poolName + "_tr"] = new Array();
                files["pool"].push(poolName);
            }

            files[poolName].push(main_trim(filePath));
            files[poolName + "_tr"].push(tr);
        });

        $("#migration-dialog i").removeClass("fa-exclamation-triangle").addClass("fa-info-circle");
        $("#migration-dialog p.messageType").empty().append("Information!");
        $("#migration-dialog p.selectedFiles").empty().append("You have selected: <br/>");
        $.each(files["pool"], function (j, poolName) {
            $("#migration-dialog p.selectedFiles").append(files[poolName].length + " files in pool " + poolName + ".<br/>");
        });
        $("#migration-dialog p.selectedFiles").append("Please select target pool for your migration:");

        $.ajax({
            url: "pools.php",
            method: 'GET',
            success: function(res) {
                $("#migration-dialog ul").empty();
                $.each(res ,function(i, pool) {
                    var poolName = pool["name"];
                    $("#migration-dialog ul").append("<li><input class='" + pool["type"] + "' type='radio' name='targetPool' value='" + poolName + "' /><label class='pool " + pool["type"] + " " + poolName + "'>" + poolName + "</label></li>");
                });

                var dialog = $("#migration-dialog").dialog({
                    resizable: false,
                    height: "auto",
                    width: 400,
                    modal: true,
                    buttons: {
                        "Migrate": function() {
                            var selectedTarget = $("#migration-dialog input[type=radio]:checked");

                            if (selectedTarget.length == 0) {
                                $("#migration-dialog p.message").append("Please select your target pool!");
                            } else {
                                var target = selectedTarget[0].value;
                                var targetpooltype = $(selectedTarget).hasClass("external") ? "external" : "internal";
                                var filepaths = new Array();
                                var trs = new Array();

                                $.each(files["pool"], function (j, poolName) {
                                    if (poolName != target) {
                                        filepaths = filepaths.concat(files[poolName]);
                                        trs = trs.concat(files[poolName + "_tr"]);
                                    }
                                });

                                $.each(trs, function (i, r) {
                                    $(r).children(".pool-col").append("&nbsp;&nbsp;<img src='images/arrow-migrate2.gif' style='height:12px' />&nbsp;&nbsp;<label style='color:green'>" + target.toUpperCase() + "</label>");
                                    $(r).find("input[type=checkbox]").prop("disabled", true);
                                });

                                $("#migration-dialog p").empty();
                                $("#migration-dialog ul").empty();
                                $(this).dialog("destroy");

                                $.ajax({
                                    url: "files.php?myaction=MIGRATE",
                                    dataType: 'json',
                                    data: {
                                        files: JSON.stringify(filepaths),
                                        target: target,
                                        targetpooltype: targetpooltype
                                    },
                                    method: 'POST',
                                    success: function(res) {
                                        var successful = 0;
                                        var failed = 0;

                                        $.each(res, function(i, j) {
                                            var tmp = $(trs[i]).children(".pool-col");
                                            var tmp2 = $(trs[i]).find("input[type=checkbox]");
                                            var tmp3 = table.row(trs[i]);
                                            tmp.empty();
                                            tmp2.prop("checked", false);
                                            tmp2.prop("disabled", false);

                                            var f = tmp3.data();
                                            if (j.result == 1) { // migrate sucessfully
                                                if (targetpooltype == "external") {
                                                    f.state = "Non-resident";
                                                    f.external_storage_pool_name = target;
                                                } else {
                                                    f.storage_pool_name = target;
                                                }
                                                
                                                $("#log").append("<span>" + tmp3.data().filename + " has been migrated to " + target + " pool.</span><br/>");
                                                successful ++;
                                            } else {
                                                $("#log").append("<span>" + tmp3.data().filename + " hasn't been migrated to " + target + " pool with error: " + j.error + "</span><br/>");
                                                failed ++;
                                            }
                                            tmp3.data(f);
                                        });

                                        if (failed > 0 && successful > 0) {
                                            $("#messageBar-icon i").removeClass("fa-check").addClass("fa-exclamation-triangle");
                                            $("#messageBar-msg span").empty().append("Warning! Successfully migrated " + successful + " files, and failed to migrate " + failed + " files!");
                                            $("#messageBar").css({"display":"block", "color": "#fbbc05", "border-color": "#fbbc05"});
                                        } else if (failed > 0 && successful == 0) {
                                            $("#messageBar-icon i").removeClass("fa-check").addClass("fa-times-circle");
                                            $("#messageBar-msg span").empty().append("Warning! Successfully migrated " + successful + " files, and failed to migrate " + failed + " files!");
                                            $("#messageBar").css({"display":"block", "color": "#ea4335", "border-color": "#ea4335"});
                                        } else {
                                            $("#messageBar-icon i").removeClass("fa-times-circle").removeClass("fa-exclamation-triangle").addClass("fa-check");
                                            $("#messageBar-msg span").empty().append("Good! Successfully migrated " + successful + " files!");
                                            $("#messageBar").css({"display":"block", "color": "#33a451", "border-color": "#33a451"});
                                        }
                                        $("#messageBar").css("display", "block");

                                        table.draw(false);
                                        generateProgressBar();
                                    }
                                });
                            }
                        },
                        Cancel: function() {
                            $("#migration-dialog p").empty();
                            $("#migration-dialog ul").empty();
                            $(this).dialog("destroy");
                        }
                    }
                });
            }
        })
    }
}

/*
    Format the data size to well unit, KB, MB, GB, TB
    var size - unit must be KB
*/
function main_formatDataSizeWithUnit(size) {
    var result;
    if (size <= 0) {
        result = size + "KB";
    } else if (size >= 1024 && size < 1024 * 1024) {
        result = (size / 1024).toFixed(2) + "MB";
    } else if (size >= 1024 * 1024 && size < 1024 * 1024 * 1024) {
        result = (size / 1024 / 1024).toFixed(2) + "GB";
    } else if (size >= 1024 * 1024 * 1024) {
        result = (size / 1024 / 1024 / 1024).toFixed(2) + "TB";
    } else {
        result = (size).toFixed(2) + "KB";
    }

    return result;
}

function initStatInfo() {
    // $("#more").css("width", $("#content").outerWidth(true));
    $('#more .stat_line').on('click', function () {
        if ($("#statistics").css("display") == "block"){
            $("#statistics").css("display", "none");
            $("#more img").attr("src", "images/arrow-up.png");
        } else{
            // $("#more").css("width", $("#content").outerWidth(true));
            $("#more img").attr("src", "images/arrow-down.png");
            $("#statistics").css("display", "block");
        }
    } );
}

function main_trim( string ) {
    return string.replace(/(^\s*)|(\s*$)/g, "");
}

function main_getDate ( string ) {
    var date;
    try {
        date = $.datepicker.parseDate("yy-mm-dd", string.value);
    } catch (error) {
        date = null;
    }

    return date;
}

String.prototype.startWith = function (s) {
    if (s == null || s == "" || this.length == 0 || s.length > this.length) {
        return false;
    }

    if (this.substr(0, s.length) == s) {
        return true;
    } else {
        return false;
    }
}

String.prototype.endWith = function(str){
    if(str==null||str==""||this.length==0||str.length>this.length) {
        return false;
    }

    if(this.substring(this.length - str.length) == str) {
        return true;
    } else {
        return false;
    }
}
