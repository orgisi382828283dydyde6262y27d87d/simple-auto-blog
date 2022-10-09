var parts = window.location.search.substr(1).split("&");
var $_GET = {};
for (var i = 0; i < parts.length; i++) {
    var temp = parts[i].split("=");
    $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
}

if ($_GET["path"]){
$.ajax({
url: 'https://script.google.com/macros/s/AKfycby6XK_viaR-FmH-s6-IRh-V3-_yFCcigSDxF86LTDZ3XaN4kC5ovOymg8HZEmf2i4x1/exec?display_thing=success&path='+$_GET["path"],
    type: 'get',
    dataType: 'json',
    success: function(returnData){
        let data = (returnData);
        if (data.status == 'success'){
            let dd = data;
            document.querySelector('.body_box').innerHTML = (dd.message.d);
        }else{
            document.body.querySelector('.body_box').innerText = (data.message);
        }
    },
    error: function(xhr, status, error){
        var errorMessage = xhr.status + ': ' + xhr.statusText
        console.log('Error - ' + errorMessage);
    }
});
}else{
document.querySelector('.body_box').innerHTML = 'No path set!';
}
