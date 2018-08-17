//display()：Click on the folder to display a list of files
// $(function display() {  
//     $('li').click(function(e) {  
        // var name=$(e.target).attr('name');
        // $.ajax({
        //     url:'show_files_table.php',
        //     data: {
        //         Name: name
        //     },
        //     method:'POST',
        //     success: function(res) {
//                 if (res.msg == 1) {                               
//                     $("table").remove();
//                     $('#close_table').remove();
//                     //Table head:'Filename'
//                     var str = "";
//                     str += "<table id='table1'><thead><tr><th>Filename</th></tr></thead><tbody>";
//                     //Generate file name
//                     str += "<tr><td class='text-left' id='long'><div id='longer'>"+res.html+"</div></td></tr>";                    
//                     str +="</tbody></table>";
//                     //Append table to <div>
//                     $('#wrap').append(str);
//                     $("#log").append("<b> Display a list of all files in the <span style='color:#f00'>" +name+ "</span><br/>");
//                 } 
//                     else {
//                     alert('failed')
//                 }
//             }
//         }) 
//     });
// });
// DisplayFileInfo():Click the file and show the information, change the circle's color as meanwhile.
    $(document).on("click","input",function DisplayFileInfo(e){    
    var filename = $(e.target).next().text();
        $.ajax({
        url:'show_files_info_table.php',
        data: {filename: filename},
        method:'POST',
        success: function(res) {
            if (res.msg==1) { 
                if(res.tier=="system"){
                    $(".circle1").css("background-color","yellow");
                    setTimeout(function() {$(".circle1").css("background-color","green")},1500);
                }
                if(res.tier=="saspool"){
                    $(".circle2").css("background-color","yellow");
                    setTimeout(function() {$(".circle2").css("background-color","green")},1500);
                }
                if(res.tier=="satapool"){
                    $(".circle3").css("background-color","yellow");
                    setTimeout(function() {$(".circle3").css("background-color","green")},1500);
                }
                $("#table2").remove();
                //Add the files' information to the html
                var str = "";
                str+="<table id='table2'>";
                str+="<tr><th><span style='color:#f00'>filename</span>  :  "+filename+"</th></tr>";
                str+="<tr><th><span style='color:#f00'>tier</span>  :  "+res.tier+"</th></tr>";
                str+="<tr><th><span style='color:#f00'>filepath</span>  :  "+res.filepath+"</th></tr>";
                str+="<tr><th><span style='color:#f00'>Createtime</span>  :  "+res.crtime+"</th></tr>";
                str+="<tr><th><span style='color:#f00'>Modifytime</span>  :  "+res.modtime+"</th></tr>";
                str+="</table>";
                // str1="<div class='close_table' id='close_table'><button onclick='deinfo()'>关闭信息</button></div>";
                $('#wrap').append(str);
                $("#log").append("<b> Display <b><span style='color:#f00'>" +filename+ "</span><b> related information <b><br/>");
            } else {
                alert('failed');
            }
        }

    }) 
 })
// showche():click the button and then show the checkbox
function showche(){
    var box=document.getElementsByName("checkbox");
    for(i=0;i<box.length;i++){  
        box[i].style.display="";
    }
}



