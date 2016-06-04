<html>
<head>
	<script src="//apis.google.com/js/platform.js" async defer></script>
</head>
<body>
<script>
	function onPageLoad(fn) {
		if (window.addEventListener)
			window.addEventListener('load', fn, false);
		else if (window.attachEvent)
			window.attachEvent('onload', fn);
	}
	onPageLoad(function () {

		gapi.load('auth2', function () {
			auth2 = gapi.auth2.init({
				client_id: '102248826764-hvb3ej6gj2cn04upgtfrs8eja7djb6bu.apps.googleusercontent.com',
				scope: 'profile'
			});

			auth2.then(function () {
				auth2.signOut().then(function () {
					location.href = "/auth/logout";
				});
			});
		})
	});
</script>
Please wait, ...
</body>
</html>