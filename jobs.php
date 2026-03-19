<?php 
	$page = isset($_GET['page']) ? '&page=' . urlencode($_GET['page']) : '';
	$category = isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '';
	echo file_get_contents('https://www.dealbidar.com/?url=facebookposter/post' . $page . $category);
?>
