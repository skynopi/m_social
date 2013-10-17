<?php 
include_once $g['path_module'].'m_social/var/'.$s.'.var.php';
$_snsuse1=explode(',',$g['mysns'][0]);
$_snsuse2=explode(',',$g['mysns'][1]);
$_snsuse3=explode(',',$g['mysns'][2]);
$_snsuse4=explode(',',$g['mysns'][3]);
$_snsuse5=explode(',',$g['mysns'][4]);
$_snsuse6=explode(',',$g['mysns'][5]);
?>


<input type="hidden" name="snsCallBack" value="m_social/lang.korean/action/a.snssend.php" />

<img src="<?php echo $g['img_core']?>/_public/ico_notice.gif" alt="" />SNS 동시등록
<?php if($m=='comment'):?>
<a href="#." onclick="snsAccount_cmt('');"><strong>[설정]</strong></a>&nbsp;
<?php else:?>
<a href="#." onclick="snsAccount('');"><strong>[설정]</strong></a>&nbsp;
<?php endif?>

<?php if($d['social']['use_t']):?>
<input type="checkbox" name="sns_t" id="snsInp_t" value="1"<?php if($_snsuse1[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse1[0]?>',0);" />
<img id="snsImg_t" src="<?php echo $g['path_module']?>/m_social/image/sns_t0.gif" alt="twitter" title="트위터" />
<?php endif?>

<?php if($d['social']['use_f']):?>
<input type="checkbox" name="sns_f" id="snsInp_f" value="1"<?php if($_snsuse2[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse2[0]?>',1);" />
<img id="snsImg_f" src="<?php echo $g['path_module']?>/m_social/image/sns_f0.gif" alt="facebook" title="페이스북" />
<?php endif?>

<?php if($d['social']['use_m']):?>
<input type="checkbox" name="sns_m" id="snsInp_m" value="1"<?php if($_snsuse3[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse3[0]?>',2);" />
<img id="snsImg_m" src="<?php echo $g['path_module']?>/m_social/image/sns_m0.gif" alt="me2day" title="미투데이" />
<?php endif?>

<?php if($d['social']['use_y']):?>
<input type="checkbox" name="sns_y" id="snsInp_y" value="1"<?php if($_snsuse4[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse4[0]?>',3);" />
<img id="snsImg_y" src="<?php echo $g['path_module']?>/m_social/image/sns_y0.gif" alt="daum blog" title="다음블로그" />
<?php endif?>

<?php if($d['social']['use_r']):?>
<input type="checkbox" name="sns_r" id="snsInp_r" value="1"<?php if($_snsuse5[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse5[0]?>',4);" />
<img id="snsImg_r" src="<?php echo $g['path_module']?>/m_social/image/sns_r0.gif" alt="Flickr" title="플리커" />
<?php endif?>


<script type="text/javascript">
//<![CDATA[
function snsAccount(ex)
{
	if (memberid == '')
	{
		alert('로그인해 주세요.  ');
		return false;
	}	
	getLayerBox('<?php echo $g['s']?>/?r=<?php echo $r?>&m=m_social&page=account','소셜계정',450,400,ex,false,'r');
}

function snsAccount_cmt(ex)
{
	if (memberid == '')
	{
		alert('로그인해 주세요.  ');
		return false;
	}
	getLayerBox('<?php echo $g['s']?>/?r=<?php echo $r?>&m=m_social&page=account','소셜계정',450,400,ex,false,'l');
}
function snsCheck1(obj,token,n)
{
	if (token == '')
	{
		var result = getHttprequest(rooturl+'/?r='+raccount+'&m=m_social&page=ajax/logcheck&n='+n);
		if(getAjaxFilterString(result,'RESULT')=='')
		{
			alert('소셜계정이 설정되지 않았습니다.  ');
			obj.checked = false;
		}
	}
}
//]]>
</script>
