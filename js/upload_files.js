// Click the button to upload the files
$(function() {
    $(".upForm input[type='button']").on("click", function() {
        var options = {
            "beforeSubmit" : checkForm,
            "success" : resultForm,
            "resetForm" : true,
            "dataType" : "json"
        };
        $(".upForm").ajaxSubmit(options);
    });
    function checkForm(formData, form, options) {
    }
    function resultForm(data, status) {
        setTimeout("location.reload();",2010);
        alert('success');
        if(data.msg==1){reP();setTimeout("alert('success')",2000)}
        else if(data.msg==2){alert('Failed:file already exist!')}
    }
});
