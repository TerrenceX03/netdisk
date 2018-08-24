<?php
/*
Function:Create the html by filesets' name
$fileset:filesets' name
$href:None default
*/
// function show_folder($fileset,$type,$href) {
//     $html_ans = '';
//     foreach ($fileset as $key => $value) {
//         if ($type=='text') {
//             if ($value == 'txt' or $value == 'docx' or $value == 'pptx') {
//                 $html = '';
//                 $html.= "<li><a onclick='$href' name='$value'>";
//                 $html.= $value;
//                 $html.= "</a></li>";
//                 $html_ans.= $html;
//         }}
//         elseif ($type=='picture') {
//             if ($value == 'png' or $value == 'jpg' or $value == 'gif' or $value == 'bmp') {
//                 $html = '';
//                 $html.= "<li><a onclick='$href' name='$value'>";
//                 $html.= $value;
//                 $html.= "</a></li>";
//                 $html_ans.= $html;
//         }}
//         elseif ($type=='video') {
//             if ($value == 'mp4' or $value == 'avi' or $value == 'mp3') {
//                 $html = '';
//                 $html.= "<li><a onclick='$href' name='$value'>";
//                 $html.= $value;
//                 $html.= "</a></li>";
//                 $html_ans.= $html;
//         }}
//     }
//     return $html_ans ? '<ul>' . $html_ans . '</ul>' : $html_ans;
// }
function show_folder($fileset,$type,$href) {
    $html_ans = '';
    $html_ans .="<ul>";
    foreach ($fileset as $key => $value) {
        $html_ans .= "<li id=";
        $html_ans .=$value;
        $html_ans .=" class='folder chinese'><img class='folder-icon' src='icons/folder.png'><label>";
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
    
    $html_ans.= "<input type='checkbox' id='$id' name='checkbox' href='' \/>";
    $html_ans.= "<label for='$id'>$name</label><br>";
    return $html_ans;
}
?>