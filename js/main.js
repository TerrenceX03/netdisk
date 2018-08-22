//input the folder name and create the folder added in the table
function CreateFolder(){
    var true_name=prompt("请输入文件夹名字","");
    var label=document.getElementById("folder_path");
    var true_filepath='/demofs/'+label.innerText + '/';
    $.ajax({
        url:'CreateFolder.php',
        data:{foldername:true_name,folderpath:true_filepath},
        method:'POST',
        success:function(res) {
            //pass
        }
    });
    $.ajax({
        url: "files.php?myaction=GET&filepath=" + true_filepath+true_name,
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
//add the new uploaded files in table
function uploadfile(){
    var label=document.getElementById("folder_path");
    var true_filepath=label.innerText;
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