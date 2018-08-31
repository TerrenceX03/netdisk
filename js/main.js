$(document).ready(function() {
    generateProgressBar();
    initStatInfo();
});

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
                        console.log(res);
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
                        "type":file.type
                    }).draw(false);
                });
            },
            fail: function (e, data) {
                console.log(data);
            },
            progressall: function (e, data) {
    　           var progress = parseInt(data.loaded / data.total * 100, 10);
    　           $('#progress .progress-bar').css('width',progress + '%');
    　      }
        });
    });
}

/*
    return to display file information of clicked floder
*/
function back_to_click_folder(folder) {
    createFileTable (folder);
}

/* 
GenerateProgressBar：Generate the progressbar for tier information such as GPFS
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
            });
        }
    })
}

function main_generateMigrationDialog() {
    var table = $("#dataTable").DataTable();
    var checkedLines = $("#dataTable input[type=checkbox]:checked");

    if (checkedLines.length == 0) {
        $("#migration-dialog p.selectedFiles").append("You must select at least one file!");

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

        $("#migration-dialog p.selectedFiles").append("You have selected ");
        $.each(files["pool"], function (j, poolName) {
            $("#migration-dialog p.selectedFiles").append(files[poolName].length + " files in pool " + poolName + ", ");
        });
        $("#migration-dialog p.selectedFiles").append("please select target pool:");

        $.ajax({
            url: "pools.php",
            method: 'GET',
            success: function(res) {
                $.each(res ,function(i, pool) {
                    var poolName = pool["name"];
                    $("#migration-dialog ul").append("<li><label><input type='radio' name='targetPool' value='" + poolName + "' />" + poolName + "</label></li>");
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
                                        target: target
                                    },
                                    method: 'POST',
                                    success: function(res) {
                                        $.each(res, function(i, j) {
                                            var tmp = $(trs[i]).children(".pool-col");
                                            var tmp2 = $(trs[i]).find("input[type=checkbox]");
                                            var tmp3 = table.row(trs[i]);
                                            tmp.empty();
                                            tmp2.prop("checked", false);
                                            tmp2.prop("disabled", false);

                                            if (j.result == 1) { // migrate sucessfully
                                                var f = tmp3.data();
                                                f.storage_pool_name = target;
                                                tmp3.data(f);
                                                $("#log").append("<span>" + tmp3.data().filename + " has been migrated to " + target + " pool.</span><br/>");
                                            } else {
                                                $("#log").append("<span>" + tmp3.data().filename + " hasn't been migrated to " + target + " pool with error.</span><br/>");
                                                alert(tmp3.data().filename + " migrated failed, because of " + j.error);
                                            }
                                        });

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