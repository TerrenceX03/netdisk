function gettier(){
    var radio = document.getElementsByName("tier");
    for (i=0; i<radio.length; i++) {
    if (radio[i].checked) {
        var TargetTier=radio[i].value;
        }
     }
    var input_html="<input type='checkbox' id=" +TargetTier +">";
    var fileid=new Array();
    var folderid = new Array();
    var tbodyObj = document.getElementById('dataTable');

        $("table :checkbox").each(function(key,value){
            if($(value).prop('checked')){
            var progress_html=(tbodyObj.rows[key+1].cells[0].innerHTML).toString();
                progress_html +="='<span>success</span>'";
            // alert(progress_html);

                // alert(tbodyObj.rows[key+1].cells[0].innerHTML);
                tbodyObj.rows[key+1].cells[0].innerHTML="<img src='images/migration.gif' />";
                // setTimeout(progress_html,4000);
                // setTimeout(tbodyObj.rows[key+1].cells[0].innerHTML=input_html,80000);
                fileid.push(tbodyObj.rows[key+1].cells[1].innerHTML);
                folderid.push($(value).attr('id'));
            }
        })
  
    $.ajax({
    url:'click_to_movetier.php',
    data:{id:fileid,tier:TargetTier,folder:folderid},
    method:'POST',
    success:function(res) {
        if(res.msg==1){
        $("#log").append("<span style='color:#f00'>" +fileid+ "</span><b> has been changed to <b><span style='color:#f00'>" +TargetTier+ "</span><br/>");
        $("#Tiers").remove();
        };
    }
});
    }
  function showTierTable(){
    var str="";
    str+="<div id='Tiers' style='display: none; position: absolute;  top: 25%;  left: 25%;  width: 20%;  height: 20%;  padding: 20px;  border: 10px solid orange;  background-color: white;  z-index:1002;  overflow: auto; '><input type='radio' name='tier' value='system'> system<br><input type='radio' name='tier' value='saspool'> saspool<br><input type='radio' name='tier' value='satapool'> satapool<br><input type='submit' value='确定' onclick='gettier()'><button href = 'javascript:void(0)' onclick = 'closeDialog()'>点这里关闭本窗口</button></div>";
    $('#content').prepend(str);
    document.getElementById('Tiers').style.display='block';

  }
  function closeDialog(){
            document.getElementById('Tiers').style.display='none';
        }