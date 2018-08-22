<?php 
include 'common/common.php'; 
include 'function/mysqlFunc.php';
include 'function/show_list.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Demo</title>
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" type="text/css" media="all" href="css/niceforms-default.css">

        <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
        <!-- <script type="text/javascript" src="js/jquery-ui.js"></script> -->
        <script type="text/javascript" src="js/ddaccordion.js"></script>
        <script type="text/javascript" src="js/jquery.form.min.js"></script>
        <script type="text/javascript" src="js/jconfirmaction.jquery.js"></script>
        <script type="text/javascript" src="js/niceforms.js"></script>
        <script type="text/javascript" src="js/upload_files.js"></script>
        <script type="text/javascript" src="js/showtable_for_files.js"></script>
        <script type="text/javascript" src="js/click_event_for_tier.js"></script>
        <script type="text/javascript" src="js/DataTables/datatables.js"></script>
        <script type="text/javascript" src="js/datatable.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jQuery-File-Upload/js/jquery.fileupload.js"></script>
        <script type="text/javascript" src="js/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>

        <link rel="stylesheet" href="css/jquery-ui.css">
        <link rel="stylesheet" href="css/styleui.css">
        <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.css"/>
        <link rel="stylesheet" type="text/css" href="js/jQuery-File-Upload/css/jquery.fileupload.css"/>
        <link rel="stylesheet" type="text/css" href="js/jQuery-File-Upload/css/jquery.fileupload-ui.css"/>
    </head>
    <body>
        <table border= "0" width= "100%">
            <tr id="header">
                <th colspan="3">
                    <img id="gpfslogo" src="images/GPFSlogo.png">
                    <div id="title" class="english">IBM Spectrum Scale</div>
                    <div id="subtitle" class="chinese">针对云计算、大数据、分析、对象等的高级非结构化数据存储管理解决方案</div>
                </th> 
            </tr>
            <tr>
                <td id="navbar" rowspan="3" width="250px">
                    <div id="navbar-title" class="chinese">全部文件夹</div>
                    <?php 
                    // show list of files 
                    $ans=Select_all_filesets(); 
                    echo show_folder($ans,'picture', "display()"); 
                    ?>
                </td>
                <td id="opbar" colspan="2" height="50px">
                    <table>
                        <tr>
                            <td>
                                <div id="progress" class="progress">
                                    <div class="progress-bar progress-bar-success"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="btn btn-success fileinput-button default-button chinese">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span>上传</span>
                                    <input id="fileupload" type="file" name="newFile" multiple>
                                </span>
                            </td>
                            <td><div class="default-button chinese" onclick="CreateFolder()">新建文件夹</div></td>
                            <td><button class="default-button chinese" href = "JavaScript:void(0)" onclick="showTierTable()">迁移</button></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>  
                <td id="content" colspan="2">
                    <table id="dataTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th class="chinese"></th>
                                <th class="chinese">文件名</th>
                                <th class="chinese">文件大小（M）</th>
                                <th class="chinese">创建时间</th>
                                <th class="chinese">修改时间</th>
                                <th class="chinese">存储池</th>
                            </tr>
                        </thead>
                    </table>
                    <img id="more" src="images/menu.jpg" alt="more">
                </td>   
            </tr>
                <tr id='statistics'>
                    <td id="progressbar">
                        <div id='progressbar_title'>资源池统计</div>
                        <div class="all_progress">
                            <div class="line1"></div>
                            <div class="line2"></div>
                            <div id='progressbar1_name'>System</div>
                            <div id=progress_bar1>
                                <div id="progressbar1">
                                    <div class="progress-label-1"></div>
                                </div>
                                <div class='contain1'>40G</div>
                            </div>
                            <div id='progressbar2_name'>Saspool</div>
                            <div id=progress_bar2>
                                <div id="progressbar2">
                                    <div class="progress-label-2"></div>
                                </div>
                                <div class='contain2'>50G</div>
                            </div>
                            <div id='progressbar3_name'>Satapool</div>
                            <div id=progress_bar3>
                                <div id="progressbar3">
                                    <div class="progress-label-3"></div>
                                </div>
                                <div class='contain3'>60G</div>
                            </div>
                            <div class="line1-tag">30%</div>
                            <div class="line2-tag">70%</div>
                        </div>
                    </td>
                    <td id="logs_td">
                        <div id="logs">
                            <div id='log_title'>系统日志</div>
                            <div id='log'><br></div>
                        </div>
                    </td>
                </tr>
        </table>
        <script type="text/javascript">
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
                GenerateProgressBar("system", 40960, "progressbar1", "progress-label-1");
                GenerateProgressBar("saspool", 51200, "progressbar2", "progress-label-2");
                GenerateProgressBar("satapool", 61440, "progressbar3", "progress-label-3");
        </script>
        <script type="text/javascript">
            $('#more').on('click', function () {
                
                var statistics=document.getElementById("statistics");
                if (statistics.style.display=="block"){
                    statistics.style.display="none";
                }
                else{
                    statistics.style.display="block";
                }
            } );
            
        </script>
        <script type="text/javascript" src="js/main.js"></script>
        <script type="text/javascript">
            function CreateFolder(){
                var lists=document.getElementsByTagName("li");
                var count=lists.length;
                for (var i=0;i<count;i++){
                    var list=lists[i];
                    if(list.getAttribute("class") == "folder chinese openfolder"){
                        var folder=list.getAttribute("id");
                        alert(folder);
                            $.ajax({
        url:'CreateFolder.php',
        data:{folder:folder},
        method:'POST',
        success:function(res) {
            if(res.msg==1){
               alert("success");
            };
          }
        });
                    }
                }
            }

        </script>
    </body>
</html>