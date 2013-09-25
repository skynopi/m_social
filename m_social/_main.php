<?php
$g['snseng'] = array('t'=>'Twitter','f'=>'Facebook','m'=>'Me2day','y'=>'Yozm','r'=>'Flickr','g'=>'Google');
$g['snskor'] = array('t'=>'트위터','f'=>'페이스북','m'=>'미투데이','y'=>'요즘','r'=>'플리커','g'=>'구글+');

function get_shortURL($longURL) {  //추가수정
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $longURL);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$shortURL = curl_exec($ch);
	curl_close($ch);
	return $shortURL;
}
?>