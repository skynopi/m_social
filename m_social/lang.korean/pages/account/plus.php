<form name="procForm" action="<?php echo $g['s']?>/" method="post" onsubmit="return saveCheck(this);">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="m" value="<?php echo $m?>" />
<input type="hidden" name="a" value="member_config" />
<input type="hidden" name="act" value="<?php echo $vtype?>" />
<input type="hidden" name="stype" value="<?php echo $stype?>" />
<input type="hidden" name="cync" value="" />
<input type="hidden" name="check_id" value="1" />


<?php 
if($_SESSION['plussns']):
$PM=getDbData($table[$m.'mbrsns'],'memberuid='.$_SESSION['plussns'],'*'); 
?>
<table class="configtbl">
<tr>
<td class="td4">
연결하시려는 계정에는 다음의 소셜계정이 포함되어 있습니다.<br />
이 계정(들)을 사이트계정으로 통합해 주세요.<br /><br />

<span class="b">계정을 통합하면 모든 소셜계정과 사이트계정이 하나로 연결됩니다.</span><br /><br />
</td>
</tr>
<tr>
<td class="td2">
	<br />
	<?php foreach($g['snskor'] as $_key => $_val):?>
	<?php if(!$PM['s'.$_key])continue?>
	<div class="snsx">
	<img src="<?php echo $g['img_module_skin']?>/sns_<?php echo $_key?>.gif" alt="" title="<?php echo $_val?>" />
	<span><?php echo $_val?></span>
	</div>
	<?php endforeach?>
	<div class="clear"></div>
</td>
</tr>
</table>
<div class="submit" style="padding-left:10px;">
<input type="hidden" name="plus" value="Y" />
<input type="hidden" name="hplus" value="" />
<input type="button" value=" 취소 " class="btngray" onclick="if(confirm('정말로 취소하시겠습니까?   ')){this.form.hplus.value='1';this.form.submit();}" />
<input type="submit" value=" 계정통합 " class="btnblue" />
</div>

<?php else:?>
<table class="configtbl">
<tr>
<td colspan="3" class="td4">
<span class="b">혹시, 여러개의 계정을 가지고 계시나요?</span><br /><br />

소셜계정을 이용해서 로그인을 하신 경우 SNS마다 독립된 계정이 생성될 수 있습니다.<br />
여러개의 계정을 가지고 계시다면 사이트계정으로 통합해 주세요.<br />
계정을 통합하면 모든 소셜계정과 사이트계정이 하나로 연결됩니다.<br /><br />

이 계정에 통합할 계정의 이메일주소와 비밀번호를 입력해 주세요.<br />
이메일이 등록되지 않은 소셜계정을 가지고 계시다면 소셜계정에서 연결버튼을 클릭해 주세요.<br />
그러면 이메일/비밀번호 입력없이 계정을 통합하실 수 있습니다.<br /><br />
통합대상 계정의 포인트/등급등의 정보는 합산되지 않습니다.<br /><br /><br />
</td>
</tr>
</table>

<table class="configtbl">
<tr>
<td class="td1">이메일</td>
<td class="td2">
	<input type="text" name="email" value="" size="40" />
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">비밀번호</td>
<td class="td2">
	<input type="password" name="pw" value="" size="20" />
</td>
<td class="td3"></td>
</tr>
<tr>
</table>
<div class="submit">
<input type="submit" value=" 계정통합 " class="btnblue" />
</div>
<?php endif?>

</form>

<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{
	<?php if(!$_SESSION['plussns']):?>
	<?php if(!$my['email']):?>
	alert('계정을 통합하려면 먼저 사이트계정에 등록하셔야 합니다.  ');
	location.href = rooturl + '/?r='+raccount+'&m='+moduleid+'&page=account&vtype=site';
	return false;
	<?php endif?>
	if (f.email.value == '')
	{
		alert('이메일을 입력해 주세요.');
		f.email.focus();
		return false;
	}
	if (f.pw.value == '')
	{
		alert('비밀번호를 입력해 주세요.');
		f.pw.focus();
		return false;
	}
	<?php endif?>
	getIframeForAction(f);
	return confirm('정말로 통합하시겠습니까?       ');
}
//]]>
</script>
