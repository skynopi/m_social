<?php
if(!defined('__KIMS__')) exit;

$iframe = 'Y';
$page = $page ? $page : 'main';

$g['dir_module_skin'] = $g['dir_module'].'lang.'.$_HS['lang'].'/pages/';
$g['url_module_skin'] = $g['url_module'].'/lang.'.$_HS['lang'].'/pages';
$g['img_module_skin'] = $g['url_module_skin'].'/image';

$g['dir_module_mode'] = $g['dir_module_skin'].$page;
$g['url_module_mode'] = $g['url_module_skin'].'/'.$page;

$g['sociallink'] = $g['s'].'/?r='.$r.'&m='.$m;

if ($page == 'account')
{
	if (!$my['uid']) getLink('','parent.getLayerBoxHide();','','');
	$vtype = $vtype ? $vtype : 'sns';
}

$g['main'] = $g['dir_module_mode'].'.php';
?>