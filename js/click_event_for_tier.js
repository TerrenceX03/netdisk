
                /* 
                    GenerateProgressBar：Generate the progressbar for tier information such as GPFS
                    tier:COS,GPFS,etc..
                    barID:The ID of progressbar
                    labelClass:Html sign for class
                 */
                function GenerateProgressBar(tier, tier_size, barID, labelClass) {
                    $.ajax({
                        url: "get_progressbar_values.php",
                        data: {
                            tier: tier,
                            tier_size: tier_size
                        },
                        method: 'POST',
                        success: function(res) {
                            var progressbar = $("#" + barID);
                            progressLabel = $("." + labelClass);
                            var val = res.perc;
                            progressbar.progressbar({
                                value: val,
                            });
                            if (val < 60) {
                                $(".ui-widget-header").css({
                                    'background': 'green'
                                });
                            } else {
                                $(".ui-widget-header").css({
                                    'background': 'yellow'
                                });
                            }
                            var label = res.size;
                            label = String(label);
                            progressLabel.text("已用" + label + "M");
                        }
                    })
                }
/*
 ToOtherTier()：Move the checked file to the specified storage tier
 tier:the specified storage tier
*/
//document.write("<script language=javascript src='GenerateProgressBar.js'></script>");
var time=null;
function click_to_movetier(tier){
clearTimeout(time);
//Use setTimeout() to distinguish between click and double click events.
time=setTimeout(function(){
var checkbox = document.getElementsByName("checkbox");
var id=new Array();

  for (var i=0; i<checkbox.length; i++) {
    if(checkbox[i].checked){
      id.push(checkbox[i].id);
    }
  }
$.ajax({
    url:'click_to_movetier.php',
    data:{id:id,tier:tier},
    method:'POST',
    success:function(res) {

        if(res.msg==1){
          $("#log").append("<span style='color:#f00'>" +id+ "</span><b> has been changed to <b><span style='color:#f00'>" +tier+ "</span><br/>");
          GenerateProgressBar("system", 40960, "progressbar1", "progress-label-1");
          GenerateProgressBar("saspool", 51200, "progressbar2", "progress-label-2");
          GenerateProgressBar("satapool", 61440, "progressbar3", "progress-label-3");
        //   $("#progressbar1").remove();
        // $("#progress_bar1").prepend("<div id='progressbar1'><div class='progress-label-1'></div></div>");  
        //   GenerateProgressBar("system", 40960, "progressbar1", "progress-label-1");

        //   $("#progressbar2").remove();
        // $("#progress_bar2").prepend("<div id='progressbar2'><div class='progress-label-2'></div></div>");  
        //   GenerateProgressBar("saspool", 51200, "progressbar2", "progress-label-2");
   
        //  $("#progressbar3").remove();
        // $("#progress_bar3").prepend("<div id='progressbar3'><div class='progress-label-3'></div></div>");  
        //   GenerateProgressBar("satapool", 61440, "progressbar3", "progress-label-3");

        };
    }
})
},300);
}
 /*
 ltfiles()：Select the all the checkbox of files which are in the same storage and folder
 storage：The specific storage db-clicked by you
 */
function dblclick_to_checkfiles(storage){
    clearTimeout(time); 
    var checkbox = document.getElementsByName("checkbox");
    var id=new Array();
    for(i=0;i<checkbox.length;i++){
        id[i]=checkbox[i].id;
    }     
    $.ajax({
    url:'dblclick_to_checkfiles.php',
    data:{storage_:storage,id_:id},
    method:'POST',
    success:function(res) {
        if(res.msg == 1){
            uncheck();
            for(var i= 0;i<res.id.length;i++){
                $("#log").append("<span style='color:#f00'>" +res.id[i]+ "</span><b> belongs to the <b><span style='color:#f00'>" +storage+ "</span><br/>");
                document.getElementById(res.id[i]).checked=true;
            }
        }        
    }
})
}
//uncheck():Before being checked, clear the situation that has been checked before.
function uncheck(){
     var checkbox = document.getElementsByName("checkbox");
     for(i=0;i<checkbox.length;i++){
         checkbox[i].checked=false;
      }
}
