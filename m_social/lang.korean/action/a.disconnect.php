<?php
if(!defined('__KIMS__')) exit;

if (!$my['uid']) getLink('','','정상적인 접근이 아닙니다.','');

$_rst = '';
$_set = array('t','f','m','y','','');
$_cnt = count($_set);

if ($delete == 'Y')
{
	if (!$my['email'])
	{
		getLink('','','사이트 계정등록을 하셔야 해제하실 수 있습니다.','');
	}

	$_mg = '연결을 끊었습니다.';
	for($i = 0; $i < $_cnt; $i++)
	{
		$_rst .= ($type==$_set[$i]?'':$g['mysns'][$i]).'|';
	}
	getDbUpdate($table['s_mbrdata'],"sns='".$_rst."'",'memberuid='.$my['uid']);
	getDbUpdate($table['s_mbrsns'],'s'.$type."=''",'memberuid='.$my['uid']);
	$R=getDbData($table['s_mbrsns'],'memberuid='.$my['uid'],'*');
	if (!$R['st']&&!$R['sf']&&!$R['sm']&&!$R['sy'])
	{
		getDbDelete($table['s_mbrsns'],'memberuid='.$my['uid']);
	}
}
else {
	if ($connect=='Y')
	{
		$_x1 = 'off,';
		$_x2 = 'on,';
		$_mg = '활성화 되었습니다.';
	}
	else {
		$_x1 = 'on,';
		$_x2 = 'off,';
		$_mg = '비활성화 되었습니다.';
	}
	for($i = 0; $i < $_cnt; $i++)
	{
		$_rst .= ($type==$_set[$i]?str_replace($_x1,$_x2,$g['mysns'][$i]):$g['mysns'][$i]).'|';
	}
	getDbUpdate($table['s_mbrdata'],"sns='".$_rst."'",'memberuid='.$my['uid']);
}

getLink('reload','parent.',$_mg."\\n반영될때까지 잠시만 기다려 주세요.",'');
?>