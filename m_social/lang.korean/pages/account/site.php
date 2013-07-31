
<div class="tops">
<?php if(!$my['email']):?>
계정을 등록하시면 사이트 회원으로 접속하실 수 있습니다.
<?php else:?>
회원님은 사이트 회원으로 등록되어 있습니다.
<?php endif?>
</div>



<form name="procForm" action="<?php echo $g['s']?>/" method="post"  onsubmit="return saveCheck(this);">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="m" value="<?php echo $m?>" />
<input type="hidden" name="a" value="member_config" />
<input type="hidden" name="act" value="<?php echo $my['email']?'info':'snslogin'?>" />
<input type="hidden" name="check_nic" value="1" />
<input type="hidden" name="check_email" value="1" />

<?php if($my['email']):?>
<table class="configtbl">
<tr>
<td class="td1">사용자명</td>
<td class="td2">
	<input type="text" name="nic" value="<?php echo $my['nic']?>" maxlength="8" onblur="sameCheck(this,'hLayernic');" />
	<span class="hmsg" id="hLayernic"></span>
	<div>웹사이트에서 사용하고 싶은 이름을 입력해 주세요 (8자이내 중복불가)</div>
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">실명</td>
<td class="td2">
	<input type="text" name="name" value="<?php echo $my['name']?>" maxlength="12" />
	<div>회원님의 실제이름을 입력해 주세요.</div>
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">성별</td>
<td class="td2">
	<select name="sex">
	<option value="0"<?php if(!$my['sex']):?> selected="selected"<?php endif?>>------</option>
	<option value="1"<?php if($my['sex']==1):?> selected="selected"<?php endif?>>남성</option>
	<option value="2"<?php if($my['sex']==2):?> selected="selected"<?php endif?>>여성</option>
	</select>
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">생일</td>
<td class="td2">
	<select name="birth_1">
	<option value="">년도</option>
	<?php for($i = substr($date['today'],0,4); $i > 1930; $i--):?>
	<option value="<?php echo $i?>"<?php if($my['birth1']==$i):?> selected="selected"<?php endif?>><?php echo $i?></option>
	<?php endfor?>
	</select>
	<select name="birth_2">
	<option value="">월</option>
	<?php $birth_2=substr($my['birth2'],0,2)?>
	<?php for($i = 1; $i < 13; $i++):?>
	<option value="<?php echo sprintf('%02d',$i)?>"<?php if($birth_2==$i):?> selected="selected"<?php endif?>><?php echo $i?></option>
	<?php endfor?>
	</select>
	<select name="birth_3">
	<option value="">일</option>
	<?php $birth_3=substr($my['birth2'],2,2)?>
	<?php for($i = 1; $i < 32; $i++):?>
	<option value="<?php echo sprintf('%02d',$i)?>"<?php if($birth_3==$i):?> selected="selected"<?php endif?>><?php echo $i?></option>
	<?php endfor?>
	</select>
	<input type="checkbox" name="birthtype" value="1"<?php if($my['birthtype']):?> checked="checked"<?php endif?> />음력
</td>
<td class="td3"></td>
</tr>
</table>

<div class="submit">
<input type="submit" value="변경내용 저장" class="btnblue" />
</div>

<?php else:?>

<table class="configtbl">
<tr>
<td class="td1">사용자명</td>
<td class="td2">
	<input type="text" name="nic" value="<?php echo $my['nic']?>" maxlength="8" onblur="sameCheck(this,'hLayernic');" />
	<span class="hmsg" id="hLayernic"></span>
	<div>웹사이트에서 사용하고 싶은 이름을 입력해 주세요 (8자이내 중복불가)</div>
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">이메일</td>
<td class="td2">
	<input type="text" name="email" value="" size="40" onblur="sameCheck(this,'hLayeremail');" />
	<span class="hmsg" id="hLayeremail"></span>
	<div>주로 사용하는 이메일 주소를 입력해 주세요.<br />비밀번호 잊어버렸을 때 확인 받을 수 있습니다.</div>
	<div><input type="checkbox" name="remail" value="1" />뉴스레터나 공지이메일을 수신받겠습니다.</div>
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">비밀번호</td>
<td class="td2">
	<input type="password" name="pw1" value="" maxlength="20" />
</td>
<td class="td3"></td>
</tr>
<tr>
<td class="td1">한번 더 입력</td>
<td class="td2">
	<input type="password" name="pw2" value="" maxlength="20" />
</td>
<td class="td3"></td>
</tr>
</table>
<div class="submit">
<input type="submit" value="사이트 계정등록" class="btnblue" />
</div>
<?php endif?>
</form>


<script type="text/javascript">
//<![CDATA[
function sameCheck(obj,layer)
{

	if (!obj.value)
	{
		eval('obj.form.check_'+obj.name).value = '0';
		getId(layer).innerHTML = '';
	}
	else
	{
		if (obj.name == 'id')
		{
			if (obj.value.length < 4 || obj.value.length > 12 || !chkIdValue(obj.value))
			{
				obj.form.check_id.value = '0';
				obj.focus();
				getId(layer).innerHTML = '사용할 수 없는 아이디입니다.';
				return false;
			}
		}
		if (obj.name == 'email')
		{
			if (!chkEmailAddr(obj.value))
			{
				obj.form.check_email.value = '0';
				obj.focus();
				getId(layer).innerHTML = '이메일형식이 아닙니다.';
				return false;
			}
		}

		getIframeForAction(document.procForm);
		frames.__iframe_for_action__.location.href = '<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&a=same_check&fname=' + obj.name + '&fvalue=' + obj.value + '&flayer=' + layer;
	}
}
function saveCheck(f)
{
	getIframeForAction(f);

	<?php if($my['email']):?>
	if (f.check_nic.value == '0')
	{
		alert('사용자명을 입력해 주세요.');
		f.nic.focus();
		return false;
	}
	if (f.sex.value == '0')
	{
		alert('성별을 지정해 주세요.');
		f.sex.focus();
		return false;
	}
	if (f.birth_1.value == '')
	{
		alert('생년월일을 지정해 주세요.');
		f.birth_1.focus();
		return false;
	}
	if (f.birth_2.value == '')
	{
		alert('생년월일을 지정해 주세요.');
		f.birth_2.focus();
		return false;
	}
	if (f.birth_3.value == '')
	{
		alert('생년월일을 지정해 주세요.');
		f.birth_3.focus();
		return false;
	}
	return confirm('정말로 변경하시겠습니까?       ');
	<?php else:?>
	if (f.nic.value == '')
	{
		alert('사용자명을 입력해 주세요.');
		f.nic.focus();
		return false;
	}
	if (f.email.value == '')
	{
		alert('이메일을 확인해 주세요.');
		f.email.focus();
		return false;
	}
	if (f.pw1.value == '')
	{
		alert('비밀번호를 입력해 주세요.');
		f.pw1.focus();
		return false;
	}
	if (f.pw2.value == '')
	{
		alert('비밀번호를 한번더 입력해 주세요.');
		f.pw2.focus();
		return false;
	}
	if (f.pw1.value != f.pw2.value)
	{
		alert('비밀번호가 일치하지 않습니다.');
		f.pw1.focus();
		return false;
	}
	<?php endif?>
}
//]]>
</script>


