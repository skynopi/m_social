<?php
if(!defined('__KIMS__')) exit;

checkAdmin(0);


$snsSet = array('t','f','m','y','r','g','b');
if($uid){
	$_tmpdfile = $g['dir_module'].'var/'.$uid.'.var.php';
}else{
	$_tmpdfile = $g['dir_module'].'var/var.php';
}
$fp = fopen($_tmpdfile,'w');
fwrite($fp, "<?php\n");

foreach ($snsSet as $val)
{
	fwrite($fp, "\$d['social']['use_".$val."'] = \"".${'use_'.$val}."\";\n");
	fwrite($fp, "\$d['social']['key_".$val."'] = \"".${'key_'.$val}."\";\n");
	fwrite($fp, "\$d['social']['secret_".$val."'] = \"".${'secret_'.$val}."\";\n");
}
fwrite($fp, "\$d['social']['key_id'] = \"".${'key_id'}."\";\n");

fwrite($fp, "?>");
fclose($fp);
@chmod($_tmpdfile,0707);


getLink('reload','parent.','','');
?>