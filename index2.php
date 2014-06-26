<?php
	//phpinfo();
	$value='<p style=\"text-align: center\"><img alt=\"学校大门.jpg\" width=\"600\" height=\"400\" src=\"/d/file/p/2012-08-03/60e7dc027496247cb182356c4754725e.jpg\" /><br />
学校大门</p>';
	preg_match('/src=\\\\\"([\s\S]*)\\\\\"/iU',$value,$changeImageSrc);
	//var_dump($changeImageSrc);
	echo $changeImageSrc[1];
?>