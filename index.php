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

        <script type="text/javascript" src="js/jQuery/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery.form.min.js"></script>
        <script type="text/javascript" src="js/click_event_for_tier.js"></script>
        <script type="text/javascript" src="js/DataTables/datatables.js"></script>
        <script type="text/javascript" src="js/datatable.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery-ui.js"></script>
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
                        <div class="nav_menu3">
                            <ul>
                                <li class='nav-has-sub'>
                                    <a>
                                        <span onclick="uploadfile()" class="btn btn-success fileinput-button default-button chinese" href = "JavaScript:void(0)">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span style='color: white'>上传</span>
                                        <input id="fileupload" type="file" name="newFile" multiple>
                                        </span>
                                    </a>
                                </li>
                                <li class='nav-has-sub'>
                                    <a href = "JavaScript:void(0)" onclick="CreateFolder()">新建文件夹</a>
                                </li>
                                <li class='nav-has-sub'>
                                    <a href = "JavaScript:void(0)" onclick="showTierTable()">迁移</a>
                                </li>
                                <li class='nav-has-sub'>
                                    <a href = "JavaScript:void(0)" onclick="deletefiles()">删除</a>
                                </li>
                                <li class='nav-has-sub'>
                                    <a id="backpath" onclick="returnback()" href = "JavaScript:void(0)">返回</a>
                                </li>
                            </ul>
                        </div>
                    </table>
                    <div class="nav_menu3" style="float: left;font-size:16px;color: #eee;">
                         <label id='path' class='folder_path'>当前路径：</label>
                    </div>
                    <div class="nav_menu3" id="all_path" style="float: left;font-size:16px;color: #eee;"></div>
                </td>
            </tr>
            <tr>  
                <td id="content" colspan="2">
                    <table id="dataTable" class="display" style="width:100%">
                    	<thead>
                            <tr>
                                <th class="chinese"></th>
                                <th class="chinese">文件名</th>
                                <th class="chinese">文件大小</th>
                                <th class="chinese">创建时间</th>
                                <th class="chinese">修改时间</th>
                                <th class="chinese">存储池</th>
                            </tr>
                        </thead>
                    </table>
                </td>   
            </tr>
            <tr>
                <td colspan="2" id="stat_panel">
                    <div id="more">
                        <!-- <img src="images/more.jpg" /> -->
                        <div class="stat_line"><img src="images/arrow-up.png" />查看统计信息</div>
                    
                        <div id="statistics">
                            <table width="100%">
                                <tr>
                                    <td width="50%">
                                        <div id="stat_progressbar">
                                            <table id="stat_progressbar_content" width="100%">
                                                <tr><td colspan="3" class="stat_title chinese">资源池统计</td></tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td width="50%">
                                        <div id="logs_td">
                                            <div id="logs">
                                                <div id='log_title' class="chinese">系统日志</div>
                                                <div id='log'><br></div>
                                          </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <script type="text/javascript" src="js/main.js"></script>
        <script type="text/javascript">
            function back(folder) {
                 createFileTable (folder);
            }
        </script>
    </body>
</html>