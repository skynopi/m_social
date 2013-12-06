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
<td colspan="3" class="td4">
<span class="b">아이디를 변경하시겠습니까?</span><br /><br />


소셜로그인을 통해 접속하셨을 경우 아이디는 임의의 문자코드로 자동생성되며 중복되지 않을 경우 변경등록이 가능합니다.<br />
원하시는 아이디가 있으시면 변경해 주세요.<br /><br />

4~13자 이내에서 영문 대소문자,숫자,_ 만 사용할 수 있습니다.<br /><br /><br />
</td>
</tr>
</table>
<table class="configtbl">
<tr>
<td class="td1">현재 아이디</td>
<td class="td2">
	<input type="text" name="id" value="<?php echo $my['id']?>" size="20" maxlength="13" onblur="sameCheck(this,'hLayerid');" />
	<span class="hmsg" id="hLayerid"></span>
</td>
<td class="td3"></td>
</tr>
</table>
<div class="submit">
<input type="submit" value=" 아이디변경 " class="btnblue" />
</div>


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
			if (obj.value.length < 4 || obj.value.length > 13 || !getTypeCheck(obj.value,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_"))
			{
				obj.form.check_id.value = '0';
				obj.focus();
				getId(layer).innerHTML = '사용할 수 없는 아이디입니다.';
				return false;
			}
		}

		getIframeForAction(document.procForm);
		frames.__iframe_for_action__.location.href = '<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&a=same_check&fname=' + obj.name + '&fvalue=' + obj.value + '&flayer=' + layer;
	}
}
function saveCheck(f)
{

	if (f.id.value.length < 4 || f.id.value.length > 13)
	{
		alert('아이디는 4~14자 이내이어야 합니다.');
		f.id.focus();
		return false;
	}
	if (!getTypeCheck(f.id.value,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_"))
	{
		alert('아이디는 영문 대소문자,숫자,_ 조합으로 만드셔야 합니다.');
		f.id.value = f.id.defaultValue;
		f.id.focus();
		return false;
	}
	if (f.id.value == f.id.defaultValue)
	{
		alert('아이디가 변경되지 않았습니다.');
		f.id.focus();
		return false;
	}
	if (f.check_id.value == '0')
	{
		alert('사용할 수 없는 아이디입니다.');
		f.id.value = f.id.defaultValue;
		f.id.focus();
		return false;
	}

	getIframeForAction(f);
	return confirm('정말로 변경하시겠습니까?       ');
}
//]]>
</script>
