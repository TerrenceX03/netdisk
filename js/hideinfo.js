$('#more').on('click', function () {
    var statistics=document.getElementById("statistics");
    if (statistics.style.display=="block"){
        statistics.style.display="none";
    }
    else{
        statistics.style.display="block";
    }
} );