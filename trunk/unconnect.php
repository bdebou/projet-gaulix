<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: 'fr'}</script>
	<title>Gaulix</title>
	<link rel="stylesheet" href="./css/styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<?php
	
	//header('Location: ./index.php');
	echo '<script language="javascript">window.location="./";</script>';
	//exit();
?>
</body>
</html>