<?php
if(!defined('__KIMS__')) exit;

if ($g['mobile']&&$_SESSION['pcmode']!='Y')
{
	include_once $g['path_module'].$module.'/lang.'.$_HS['lang'].'/admin/_mobile/'.$front.'.php';
}
else {
	include_once $g['path_module'].$module.'/lang.'.$_HS['lang'].'/admin/_pc/'.$front.'.php';
}
?>