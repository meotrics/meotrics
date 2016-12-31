//write by conghd

$(function(){
	if(window.location.pathname.length < 3){
      	$("#includedContent").load("menu.html"); 
		$("#footer").load("footer.html"); 
  		$('#checking').load("tracking.html");

	}
  	else{
  		$("#includedContent").load("../menu.html"); 
  		$("#footer").load("../footer.html"); 
  		$('#checking').load("../tracking.html");

  	}

    });