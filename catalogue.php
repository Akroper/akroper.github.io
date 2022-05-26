<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=0.5, maximum-scale=3">
	<title>Наш магазин - Энгельс, мясо и мясная продукция</title>
	<link charset="utf-8" href="css_js/css.css" rel="stylesheet" type="text/css">
	<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
	<?php require_once('components/db.php'); ?>
	<?php require_once('components/header.php'); ?>
	<?php require_once('components/catalogue.php'); ?>
	<script type="text/javascript">
		document.querySelector("header").classList.add("headerDarker");
		document.getElementById("catalogue").classList.add("disabled");
		document.getElementById("catalogue").removeAttribute("href");
	</script>
</body>
</html>