$(document).ready(function() {
    GenerateProgressBar();
    main_initStatInfo();
});

/* input the folder name and create the folder added in the table */
function CreateFolder(){
    var true_name = prompt("请输入文件夹名字","");
    var label = document.getElementById("all_path");
    var true_filepath = '/demofs/' + label.innerText + '/';
    $.ajax({
            url: "folder.php?myaction=CREATE_FOLDER",
            dataType: 'json',
            data: {
                foldername:true_name,
                folderpath:true_filepath
            },
            method: 'POST',
            success: function(res) {
                //pass
            }
    });
    $.ajax({
        url: "files.php?myaction=GET&filepath=" + true_filepath + true_name,
        dataType: 'json',
        success:function(res){
            var t = $('#dataTable').DataTable();
                t.row.add({
                    "filename": true_name,
                    "file_size": res.file_size,
                    "creation_time": res.creation_time,
                    "L_mod_time": res.L_mod_time,
                    "storage_pool_name": res.storage_pool_name,
                    "file_path":res.file_path,
                    "folder_path":res.folder_path,
                    "filetype":res.filetype
                }).draw(false);
        }
    });
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
                        "filetype":file.filetype
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
function GenerateProgressBar() {
    $.ajax({
        url: "pools.php",
        method: 'GET',
        success: function(res) {
            $.each(res ,function(i, pool) {
                var poolName = pool["name"];
                var totaldatasize = pool["totaldatasize"];
                var freedatasize = pool["freedatasize"];
                var userdatapercentage = 100 - pool["freedatapercentage"];

                $("#stat_progressbar_content").append(
                    "<tr><td class='stat_progressbar-name english' colspan='2'><label class='left'>" + poolName.toUpperCase() + "</label><label class='right'>FREE: " + main_formatDataSizeWithUnit(freedatasize) + "</label></td></tr>"
                    + "<tr><td class='stat_progressbar-row'><div id='stat_progressbar-" + poolName + "'></div></td><td class='stat_progressbar-label english'><div>" + userdatapercentage + "%</div></td></tr>"
                    + "<tr><td class='stat_progressbar-name english bottom' colspan='2'><label class='left'>USED: " + main_formatDataSizeWithUnit(totaldatasize - freedatasize) + "</label><label class='right'>CAPACITY: " + main_formatDataSizeWithUnit(totaldatasize) + "</label></td></tr>"
                );

                $("#stat_progressbar-" + poolName).progressbar({
                    value: userdatapercentage
                });

                if (userdatapercentage < 60) {
                    $("#stat_progressbar_content .ui-widget-header").css({
                        'background': 'green'
                    });
                } else {
                    $("#stat_progressbar_content .ui-widget-header").css({
                        'background': 'yellow'
                    });
                }
            });
        }
    })
}

/*
    Format the data size to well unit, KB, MB, GB, TB
    var size - unit must be KB
*/
function main_formatDataSizeWithUnit(size) {
    var result;
    if (size >= 1024 && size < 1024 * 1024) {
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

function main_initStatInfo() {
    $("#more").css("width", $("#content").outerWidth(true));
    $('#more .stat_line').on('click', function () {
        if ($("#statistics").css("display") == "block"){
            $("#statistics").css("display", "none");
            $("#more img").attr("src", "images/arrow-up.png");
        } else{
            $("#more").css("width", $("#content").outerWidth(true));
            $("#more img").attr("src", "images/arrow-down.png");
            $("#statistics").css("display", "block");
        }
    } );
}