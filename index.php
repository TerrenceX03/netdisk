<?php include 'common/common.php'; ?>
    <!DOCTYPE html>
    <html>
        
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>
                Demo
            </title>
            <link rel="stylesheet" href="css/base.css">
            <link rel="stylesheet" href="css/style.css">
            <link rel="stylesheet" type="text/css" media="all" href="css/niceforms-default.css">
            <script type="text/javascript" src="js/jquery-3.3.1.min.js">
            </script>
            <script type="text/javascript" src="js/ddaccordion.js">
            </script>
            <script type="text/javascript" src="js/jquery.form.min.js">
            </script>
            <script type="text/javascript" src="js/jconfirmaction.jquery.js">
            </script>
            <script language="javascript" type="text/javascript" src="js/niceforms.js">
            </script>
            <script type="text/javascript" src="js/upload_files.js">
            </script>
            <script type="text/javascript" src="js/showtable_for_files.js">
            </script>
            <script type="text/javascript" src="js/click_event_for_tier.js">
            </script>
            <script type="text/javascript" src="js/GenerateProgressBar.js">
            </script>
            <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
            <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js">
            </script>
            <link rel="stylesheet" href="http://jqueryui.com/resources/demos/style.css">
            </head>
        
        <body>
            <table border= "1" width= "100%" height="650px">
    <tr height="100px" >
        <th colspan="3"><img src=images/IBMheader.jpg height="100px" width="100%"></th>
       
    </tr>
    <tr height="50px">
        <td rowspan="3" width="250px"></td>
        <td colspan="2"></td>
        
    </tr>
    <tr>
        
        <td colspan="2">
        <table id="myTable" border="1" width="70%">
            <tr>
                <td id="1">111.txt</td>
                <td id="1">10.3M</td>
            </tr>
            <tr>
                <td id="2">123.txt</td>
                <td id="2">1.6M</td>
            </tr>
         </table>
         
        </td>
        
    </tr>
     <tr height="200px">
        <td></td>
        <td></td>
        
    </tr>
</table>

<script type="text/javascript">
    $(document).on("click","td",function DisplayFileInfo(e){
         $("#new").remove();
         var td_id=e.target.id;
    var table=document.getElementById("myTable");
    var row=table.insertRow(td_id);
    row.id="new";
    var cell1=row.insertCell(0);
    
    cell1.colSpan=2;
    row.style.cssText="height:30px";


    var filename = $(e.target).text();

    $.ajax({
        url:'show_files_info_table.php',
        data: {filename: filename},
        method:'POST',
        success: function(res) {
            
          var str = "";
                str+="<table id='infotable'>";
               str+="<tr><th>tier</th><th>creatime</th><th>modifytime</th></tr>"
               str+="<tr><td>"+res.tier+"</td><td>"+res.crtime+"</td><td>"+res.modtime+"</td></tr>" 
               
                str+="</table>";

                cell1.innerHTML=str;
                
        }
        
    })

 }
    )
</script>
<script>

</script>
        </body>
