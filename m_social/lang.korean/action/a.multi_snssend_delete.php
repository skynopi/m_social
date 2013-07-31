<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);

foreach ($snssend_members as $val)
{
	$R = getUidData($table[$m.'data'],$val);
	if (!$R['uid']) continue;

	getDbDelete($table[$m.'data'],'uid='.$R['uid']);
}
getLink('reload','parent.','','');
?>