<?php 
include 'common/common.php'; 
include 'function/mysqlFunc.php';
include 'function/show_list.php';
?>
<!DOCTYPE html>
<html style="height:100%">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Demo</title>
        <link rel="stylesheet" href="css/base.css">

        <script type="text/javascript" src="js/jQuery/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="js/jQuery/jquery.form.min.js"></script>
        <script type="text/javascript" src="js/DataTables/datatables.js"></script>
        <script type="text/javascript" src="js/DataTables/absolute.js"></script>
        <script type="text/javascript" src="js/datatable.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.12.1/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jQuery-File-Upload/js/jquery.fileupload.js"></script>
        <script type="text/javascript" src="js/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>

        <link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.css">
        <link rel="stylesheet" href="css/styleui.css">
        <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.css"/>
        <link rel="stylesheet" type="text/css" href="js/jQuery-File-Upload/css/jquery.fileupload.css"/>
        <link rel="stylesheet" type="text/css" href="js/jQuery-File-Upload/css/jquery.fileupload-ui.css"/>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body style="height:100%">
        <table border= "0" width= "100%" style="height:100%">
            <tr id="header">
                <th colspan="3">
                    <img id="gpfslogo" src="images/GPFSlogo.png">
                    <div id="title" class="english">IBM Spectrum Scale</div>
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
                    <div>
                        <ul>
                            <li class="uploadbtn">
                                <a>
                                    <span onclick="uploadfile()" class="btn btn-success fileinput-button default-button chinese" href = "JavaScript:void(0)">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span>上传</span>
                                        <input id="fileupload" type="file" name="newFile" multiple>
                                    </span>
                                </a>
                            </li>
                            <li><a href = "JavaScript:void(0)" onclick="CreateFolder()">新建文件夹</a></li>
                            <li><a href = "JavaScript:void(0)" onclick="main_generateMigrationDialog()">迁移</a></li>
                            <li><a href = "JavaScript:void(0)" onclick="deletefiles()">删除</a></li>
                            <li><a id="backpath" onclick="returnback()" href = "JavaScript:void(0)">返回</a></li>
                            <li class="align-right"><span>请选择存储池：</span><div id="poolfilter"></div></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td id="returnpath" colspan="2">
                    <div class="returnlabel">
                         <label id='path' class='chinese'>当前路径：</label>
                    </div>
                    <div class="fullpath chinese" id="all_path"></div>
                </td>
            </tr>
            <tr>  
                <td id="content" colspan="2">
                    <table id="dataTable" class="hover row-border" style="width:100%">
                    	<thead>
                            <tr>
                                <th class="chinese" style="background: none"></th>
                                <th class="chinese">文件名</th>
                                <th class="chinese">文件大小</th>
                                <th class="chinese">创建时间</th>
                                <th class="chinese">修改时间</th>
                                <th class="chinese">存储池</th>
                                <th class="chinese">类型</th>
                            </tr>
                        </thead>
                    </table>
                </td>   
            </tr>
        </table>

        <div colspan="2" id="stat_panel">
            <div id="more">
                <div class="stat_line"><img src="images/arrow-up.png" />查看统计信息</div>
                <div id="statistics">
                    <table width="100%">
                        <tr>
                            <td width="50%">
                                <div id="stat_progressbar">
                                    <table id="stat_progressbar_content" width="100%">
                                        <thead>
                                            <tr><td colspan="3" class="stat_title chinese">资源池统计</td></tr>
                                        </thead>
                                        <tbody></tbody>
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
        </div>

        <div id="migration-dialog" title="Migration..." style="display: none">
            <p>Migrate your selected file(s) to another storage pool</p>
            <p class="selectedFiles"></p>
            <ul></ul>
            <p class="message"></p>
        </div>

        <script type="text/javascript" src="js/main.js"></script>
        <script type="text/javascript">
            function back(folder) {
                 createFileTable (folder);
            }
        </script>
    </body>
</html>