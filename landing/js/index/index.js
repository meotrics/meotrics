    var domain = "//app." + location.hostname;
    $('.loginlink').prop('href', domain + '/auth/login');
    $('.registerlink').prop('href', domain + '/auth/register');
    $(function() {

        $('#da-slider').cslider();

    });
    
    window.onload = function(e){
        function addcode(){
            setTimeout(function(){
                // var button = document.getElementById("kol_bounce_form_button_135764");
                var submitbtn = $('input[data-dojo-attach-point = "submitButton"]');
                if(submitbtn === null || submitbtn == undefined ){
                    console.log("acaca");
                    addcode();
                }else{
                    console.log(submitbtn);
                    for (var i=0;i<submitbtn.length;i++){
                        addEvent(submitbtn[i], 'click', signup);
                    }
                    // submitbtn.addEventListener("click",signup);
                    //        submitbtn.click(function() {
                    //         console.log("fuck");
                    //         signup();
                    // });
                    function signup(){
                        console.log("fuck");
                        var obj = new Object();
                        // obj.name = document.getElementById("name").value;
                        // obj.email = document.getElementById("email").value;
                        obj.email = $('input[name="EMAIL"]').value;
                        // obj.phone = document.getElementById("phone").value;
                        console.log(obj);
                        mt.registerevent(obj);
                    }
                }
            }, 1000);
        }
        addcode();
    };

