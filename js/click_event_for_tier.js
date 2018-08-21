//migrate to other pools.
function gettier() {
    var radio = document.getElementsByName("tier");
    for (i = 0; i < radio.length; i++) {
        if (radio[i].checked) {
            var TargetTier = radio[i].value;
        }
    }
    $("#Tiers").remove();
    var input_html = "<input type='checkbox' id=" + TargetTier + ">";
    var fileid = new Array();
    var folderid = new Array();
    var tbodyObj = document.getElementById('dataTable');
    $("table :checkbox").each(function(key, value) {
        if ($(value).prop('checked')) {
            fileid.push(tbodyObj.rows[key + 1].cells[1].innerHTML);
            folderid.push($(value).attr('id'));
            var time = (tbodyObj.rows[key + 1].cells[2].innerHTML / (1024 * 1014)) * 1000
            //migrated action
            var orignal_checkbox_str = "<input type='checkbox' id=" + folderid[0] + ">"
            var pool_str = "<td class='datatable-data-col'>" + TargetTier + "</td>"
            tbodyObj.rows[key + 1].cells[0].innerHTML = "<img src='images/migration.gif'/><span>migrating</span>";
            migration_success = setTimeout(function() {
                tbodyObj.rows[key + 1].cells[0].innerHTML = "<img src='images/migration_success.png'/>";
            },
            time);
            migration_pool = setTimeout(function() {
                tbodyObj.rows[key + 1].cells[5].innerHTML = pool_str;
            },
            time + 20);
            migration_orignal = setTimeout(function() {
                tbodyObj.rows[key + 1].cells[0].innerHTML = orignal_checkbox_str;
            },
            time + 3000);
        }
    })
    $.ajax({
        url: 'click_to_movetier.php',
        data: {
            id: fileid,
            tier: TargetTier,
            folder: folderid
        },
        method: 'POST',
        success: function(res) {
            if (res.msg == 1) {
                $("#log").append("<span style='color:#f00'>" + fileid + "</span><b> has been changed to <b><span style='color:#f00'>" + TargetTier + "</span><br/>");
                // $("#Tiers").remove();
            };
        }
    });
}
function showTierTable() {
    var str = "";
    str += "<div id='Tiers'>"+
    "<label class='pool_name'><input type='radio' name='tier' value='system'> system</label><br><br>"+
    "<label class='pool_name'><input type='radio' name='tier' value='saspool'> saspool</label><br><br>"+
    "<label class='pool_name'><input type='radio' name='tier' value='satapool'> satapool</label><br><br>"+
    "<input class='pool_check' type='submit' value='' onclick='gettier()'>"+
    "<input class='pool_close' type='submit' value='' href = 'javascript:void(0)' onclick = 'closeDialog()'></div>";
    $('#content').prepend(str);
    document.getElementById('Tiers').style.display = 'block';
}
function closeDialog() {
    document.getElementById('Tiers').style.display = 'none';
}