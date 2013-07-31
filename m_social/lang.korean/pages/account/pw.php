<form name="procForm" action="<?php echo $g['s']?>/" method="post" onsubmit="return saveCheck(this);">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="m" value="<?php echo $m?>" />
<input type="hidden" name="a" value="member_config" />
<input type="hidden" name="act" value="<?php echo $vtype?>" />
<input type="hidden" name="stype" value="<?php echo $stype?>" />
<input type="hidden" name="cync" value="" />
<input type="hidden" name="check_id" value="1" />


<table class="configtbl">
<tr>
<td class="td1">현재 비밀번호</td>
<td class="td2">
	<input type="password" name="pw" value="" maxlength="20" />
	<div>현재 비밀번호는 마지막으로 등록된지 <span class="b"><?php echo -getRemainDate($my['last_pw'])?>일</span>이 경과되었습니다.</div>
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">새 비밀번호</td>
<td class="td2">
	<input type="password" name="pw1" value="" maxlength="20" />
	<div>4~12자의 영문과 숫자만 사용할 수 있습니다.</div>
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">한번 더 입력</td>
<td class="td2">
	<input type="password" name="pw2" value="" maxlength="20" />
	<div>비밀번호를 한번 더 입력하세요.<br />비밀번호는 잊지 않도록 주의하시기 바랍니다.</div>
</td>
<td class="td3"></td>
</tr>
</table>

<div class="submit">
<input type="submit" value="비밀번호 변경" class="btnblue" />
</div>

</form>

<script type="text/javascript">
//<![CDATA[
function saveCheck(f)
{

	if (f.pw.value == '')
	{
		alert('현재 비밀번호를 입력해 주세요.');
		f.pw.focus();
		return false;
	}

	if (f.pw1.value == '')
	{
		alert('변경할 비밀번호를 입력해 주세요.');
		f.pw1.focus();
		return false;
	}
	if (f.pw2.value == '')
	{
		alert('변경할 비밀번호를 한번더 입력해 주세요.');
		f.pw2.focus();
		return false;
	}
	if (f.pw1.value != f.pw2.value)
	{
		alert('변경할 비밀번호가 일치하지 않습니다.');
		f.pw1.focus();
		return false;
	}

	if (f.pw.value == f.pw1.value)
	{
		alert('현재 비밀번호와 변경할 비밀번호가 같습니다.');
		f.pw1.value = '';
		f.pw2.value = '';
		f.pw1.focus();
		return false;
	}

	getIframeForAction(f);
	return confirm('정말로 비밀번호를 변경하시겠습니까?       ');	
}
//]]>
</script>
