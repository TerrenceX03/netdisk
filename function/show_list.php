<?php
/*
Function:Create the html by filesets' name
$fileset:filesets' name
$href:None default
*/

function show_folder($fileset,$type,$href) {
    $html_ans = '';
    $html_ans .="<ul>";
    foreach ($fileset as $key => $value) {
        $html_ans .= "<li id=";
        $html_ans .= $value;
        $html_ans .= " class='folder chinese'><img class='folder-icon' src='icons/folder.png'><label>";
        $html_ans .= $value;
        $html_ans .= "</label></li>";
        }
    $html_ans .= "</ul>";
    return $html_ans;
}

/*
Function:Create the html to show the files' table
$name:Files' name
$id:Files' ID
$href:None default
*/
function show_files_table($name, $id, $href) {
    $html_ans = '';
    
    $html_ans .= "<input type='checkbox' id='$id' name='checkbox' href='' \/>";
    $html_ans .= "<label for='$id'>$name</label><br>";
    return $html_ans;
}
?>