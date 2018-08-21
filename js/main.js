$(document).ready(function() {
    var openfolder = $("#navbar li.openfolder").attr("id");
    $('#fileupload').fileupload({
        url: "files.php?myaction=POST&parent=" + openfolder,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                var t = $('#dataTable').DataTable();
                t.row.add({
                    "filename": file.name,
                    "file_size": file.size,
                    "creation_time": file.creation_time,
                    "L_mod_time": file.L_mod_time,
                    "storage_pool_name": file.storage_pool_name
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
} );