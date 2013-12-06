<?php
if(!defined('__KIMS__')) exit;

if (!$fname || !$fvalue) exit;

include_once $g['path_module'].'member/var/var.join.php';
if ($my['admin'])
{
	$resultnum = 1;
	$resultmsg = '<span class="ok">OK!</span>';
}
else {
	if ($fname == 'id')
	{
		if (strstr(','.$d['member']['join_cutid'].',',','.$fvalue.','))
		{
			$resultnum = 0;
			$resultmsg = '사용할 수 없는 아이디입니다';
		}
		else 
		{
			$isId = getDbRows($table['s_mbrid'],"id='".$fvalue."' and id<>'".$my['id']."'");
			if (!$isId)
			{
				if(!$d['member']['join_rejoin'])
				{
					if(is_file($g['path_tmp'].'out/'.$fvalue.'.txt'))
					{
						$resultnum = 0;
						$resultmsg = '사용할 수 없는 아이디입니다';
					}
					else {
						$resultnum = 1;
						$resultmsg = '<span class="ok">OK!</span>';
					}
				}
				else {
					$resultnum = 1;
					$resultmsg = '<span class="ok">OK!</span>';
				}
			}
			else {
				$resultnum = 0;
				$resultmsg = '사용할 수 없는 아이디입니다';
			}
		}
	}
	if ($fname == 'email')
	{
		if ($my['uid'])
		{
			$isId = getDbRows($table['s_mbrdata'],"email='".$fvalue."' and email <> '".$my['email']."'");
		}
		else {
			$isId = getDbRows($table['s_mbrdata'],"email='".$fvalue."'");
		}
		if (!$isId)
		{
			$resultnum = 1;
			$resultmsg = '<span class="ok">OK!</span>';
		}
		else {
			$resultnum = 0;
			$resultmsg = '이미 존재하는 이메일입니다';
		}
	}
	if ($fname == 'nic')
	{

		if (strstr(','.$d['member']['join_cutnic'].',',','.$fvalue.',') && !$my['admin'])
		{
			$resultnum = 0;
			$resultmsg = '이미 존재하는 사용자명입니다';
		}
		else 
		{
			if ($my['admin'])
			{
				$resultnum = 1;
				$resultmsg = '<span class="ok">OK!</span>';
			}
			else {
				if($my['uid'])
				{
					$isId = getDbRows($table['s_mbrdata'],"nic='".$fvalue."' and nic<>'".$my['nic']."'");
				}
				else {
					$isId = getDbRows($table['s_mbrdata'],"nic='".$fvalue."'");
				}
				if (!$isId)
				{
					$resultnum = 1;
					$resultmsg = '<span class="ok">OK!</span>';
				}
				else {
					$resultnum = 0;
					$resultmsg = '이미 존재하는 사용자명입니다';
				}
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $_HS['lang']?>" xml:lang="<?php echo $_HS['lang']?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title></title>
<script type="text/javascript">
//<![CDATA[
<?php if(!$resultnum):?>
//parent.document.procForm.<?php echo $fname?>.value = '';
parent.document.procForm.<?php echo $fname?>.focus();
<?php endif?>
parent.document.procForm.check_<?php echo $fname?>.value = "<?php echo $resultnum?>";
parent.getId('<?php echo $flayer?>').innerHTML = "<?php echo addslashes($resultmsg)?>";
//]]>
</script>
</head>
<body></body>
</html>
<?php exit?>