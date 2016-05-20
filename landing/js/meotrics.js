//write by conghd

$( document ).ready(function() {
	$("#tologin").click(function() {
		$("[href$='#login']").trigger('click');
	});

	$("#toregister").click(function() {
		$("[href$='#register']").trigger('click');
	});

	
});
