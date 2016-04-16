<?php require 'header.php';?>
</head>
<body>

HELLO THIS IS A SAMPLE CLIENT SITE
<a href="/login.php"> login</a>
<br/>
<button id="track">GENERATE PURCHASE</button>
<script>
	document.getElementById('track').onclick=function()
	{
		mt.track('purchase', {price: '50', amount: '45'});
	}
</script>
</body>
</html>