<?php include 'common/common.php'; ?>
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
        <script type="text/javascript" src="js/ddaccordion.js"></script>
        <script type="text/javascript" src="js/jquery.form.min.js"></script>
        <script type="text/javascript" src="js/jconfirmaction.jquery.js"></script>
        <script language="javascript" type="text/javascript" src="js/niceforms.js"></script>
        <script type="text/javascript" src="js/upload_files.js"></script>
        <script type="text/javascript" src="js/showtable_for_files.js"></script>
        <script type="text/javascript" src="js/click_event_for_tier.js"></script>
        <script type="text/javascript" src="js/GenerateProgressBar.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script type="text/javascript" src="js/DataTables/datatables.js"></script>
        <script type="text/javascript" src="js/datatable.js"></script>
        <link rel="stylesheet" href="http://jqueryui.com/resources/demos/style.css">
        <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.css"/>
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
                    <ul>
                        <li class="folder chinese">
                            <img class="folder-icon" src="icons/folder.png">
                            <label>我的文档</label>
                        </li>
                        <li class="folder chinese openfolder">
                            <img class="folder-icon" src="icons/folder-open.png">
                            <label>方案书</label>
                        </li>
                        <li class="folder chinese">
                            <img class="folder-icon" src="icons/folder.png">
                            <label>后督影像</label>
                        </li>
                        <li class="folder chinese">
                            <img class="folder-icon" src="icons/folder.png">
                            <label>数据库日志</label>
                        </li>
                    </ul>
                </td>
                <td id="opbar" colspan="2" height="50px">
                    <table>
                        <tr>
                            <td><div class="default-button chinese">上传</div></td>
                            <td><div class="default-button chinese">新建文件夹</div></td>
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
                                <th class="chinese">文件大小</th>
                                <th class="chinese">创建时间</th>
                                <th class="chinese">修改时间</th>
                            </tr>
                        </thead>
                    </table>
                </td>   
            </tr>
            <tr height="200px">  
                <td id="statistics"></td>  
                <td id="logs"></td> 
            </tr>
        </table>
        <script type="text/javascript">

        </script>
    </body>
</html>