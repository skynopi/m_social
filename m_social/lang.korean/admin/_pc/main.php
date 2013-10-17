<?php
if($my['uid']==1) :  //최고 관리자 체크
$sort	= $sort ? $sort : 'gid';
$orderby= $orderby ? $orderby : 'asc';
$recnum	= $recnum && $recnum < 301 ? $recnum : 15;
$bbsque	= '';

$RCD = getDbArray($table['s_site'],$bbsque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_site'],$bbsque);
$TPG = getTotalPage($NUM,$recnum);


$is_file = $g['path_module'].$module.'/var/'.$uid.'.var.php';
$R = getUidData($table['s_site'],$uid);
if(is_file($is_file)){
	include_once $g['path_module'].$module.'/var/'.$uid.'.var.php';
	$msg = '';
}else{
	include_once $g['path_module'].$module.'/var/var.php';
	$msg = ' <br /><h5> 등록된 소셜연동 설정이 없습니다. 좌측의 사이트를 선택하신 후 등록해 주십시오..</h5><br />';
}
?>




<div id="catebody">
	<div id="category">
		<div class="title">
			<select class="c1" onchange="goHref('<?php echo $g['adm_href']?>&amp;recnum='+this.value);">
			<?php for($i=15;$i<=300;$i=$i+15):?>
			<option value="<?php echo $i?>"<?php if($i==$recnum):?> selected="selected"<?php endif?>>D.<?php echo $i?></option>
			<?php endfor?>
			</select>
			<select class="c2" onchange="goHref('<?php echo $g['adm_href']?>&amp;recnum=<?php echo $recnum?>&amp;p='+this.value);">
			<?php for($i = 1; $i <= $TPG; $i++):?>
			<option value="<?php echo $i?>"<?php if($i==$p):?> selected="selected"<?php endif?>>P.<?php echo $i?></option>
			<?php endfor?>
			</select>

		</div>
		
		<?php if($NUM):?>
		<div class="tree">
			<ul id="bbsorder">
			<?php while($BR = db_fetch_array($RCD)):?>
			<li>
				<a href="<?php echo $g['adm_href']?>&amp;recnum=<?php echo $recnum?>&amp;p=<?php echo $p?>&amp;uid=<?php echo $BR['uid']?>"><span class="name<?php if($BR['uid']==$R['uid']):?> on<?php endif?>"><?php echo $BR['name']?></span></a><span class="id">(<?php echo $BR['id']?>)</span>
			</li>
			<?php endwhile?>
			</ul>
		</div>
		<?php else:?>
		<div class="none">등록된 소셜연동 설정 사이트가 없습니다.</div>
		<?php endif?>

	</div>


	<div id="catinfo">


		<form name="procForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return saveCheck(this);">
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="m" value="<?php echo $module?>" />
		<input type="hidden" name="uid" value="<?php echo $uid?>" />
		<input type="hidden" name="a" value="config" />

		<div class="title">

			<div class="xleft">
				소셜연동 설정 
			</div>
			<div class="xright">

			</div>

		</div>

		<div class="notice">
			<?php echo $msg?>
			소셜네트워크 연동을 위해서는 각각의 SNS의 APP등록을 하셔야 합니다.<br />
			APP 등록을 하면 컨슈머키와 같은 특정 인증키를 받게되며 그 값을 등록해 주시면 됩니다.<br />
			인증키를 등록한 후에는 반드시 각 SNS APP등록페이지에서 콜백주소 및 기타 설정을 해 주세요.
		</div>


		<table>
						
			<tr>
				<td class="td1">사용할 SNS</td>
				<td class="td2 shift">
					
					<input type="checkbox" name="use_t" id="chk_t" value="1"<?php if($d['social']['use_t']):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
					<img src="<?php echo $g['path_module'].$module?>/image/sns_t0.gif" alt="" /> <label for="chk_t">트위터</label>

					<input type="checkbox" name="use_f" id="chk_f" value="1"<?php if($d['social']['use_f']):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
					<img src="<?php echo $g['path_module'].$module?>/image/sns_f0.gif" alt="" /> <label for="chk_f">페이스북</label>

					<input type="checkbox" name="use_m" id="chk_m" value="1"<?php if($d['social']['use_m']):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
					<img src="<?php echo $g['path_module'].$module?>/image/sns_m0.gif" alt="" /> <label for="chk_m">미투데이</label>

					<input type="checkbox" name="use_y" id="chk_y" value="1"<?php if($d['social']['use_y']):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
					<img src="<?php echo $g['path_module'].$module?>/image/sns_y0.gif" alt="" /> <label for="chk_y">다음</label>


					<input type="checkbox" name="use_r" id="chk_r" value="1"<?php if($d['social']['use_r']):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
					<img src="<?php echo $g['path_module'].$module?>/image/sns_r0.gif" alt="" /> <label for="chk_r">플리커</label>

					<input type="checkbox" name="use_g" id="chk_g" value="1"<?php if($d['social']['use_g']):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
					<img src="<?php echo $g['path_module'].$module?>/image/sns_g0.gif" alt="" /> <label for="chk_g">구글</label>

				</td>
			</tr>
			<tr>
				<td class="td1">짧은주소사용</td>
				<td class="td2 shift">
					
					<input type="checkbox" name="use_b" id="chk_b" value="1"<?php if($d['social']['use_b']):?> checked="checked"<?php endif?> onclick="chkSNScheck(this);" />
					<label for="chk_b">bit.ly 주소를 사용합니다.</label>

				</td>
			</tr>
		</table>


		<div id="snsdiv_t"<?php if(!$d['social']['use_t']):?> class="hide"<?php endif?>>
		<table>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_t0.gif" alt="" /> Consumer Key</td>
				<td class="td2">
					<input type="text" name="key_t" value="<?php echo $d['social']['key_t']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_t0.gif" alt="" /> Consumer Secret</td>
				<td class="td2">
					<input type="text" name="secret_t" value="<?php echo $d['social']['secret_t']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_t0.gif" alt="" /> Callback Url</td>
				<td class="td2 b">
					<?php echo $g['url_root'].'/?r='.$r.'&m='.$module.'&a=snscall_direct&type=t'?>
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_t0.gif" alt="" /> APP 등록페이지</td>
				<td class="td2 b">
					<a href="https://dev.twitter.com/apps/new" target="_blank">https://dev.twitter.com/apps/new</a>
				</td>
			</tr>
		</table>
		</div>

		<div id="snsdiv_f"<?php if(!$d['social']['use_f']):?> class="hide"<?php endif?>>
		<table>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_f0.gif" alt="" /> API Key</td>
				<td class="td2">
					<input type="text" name="key_f" value="<?php echo $d['social']['key_f']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_f0.gif" alt="" /> Secret Code</td>
				<td class="td2">
					<input type="text" name="secret_f" value="<?php echo $d['social']['secret_f']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_f0.gif" alt="" /> Callback Url</td>
				<td class="td2 b">
					<?php echo $g['url_root'].'/?r='.$r.'&m='.$module.'&a=snscall_direct&type=f'?>
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_f0.gif" alt="" /> APP 등록페이지</td>
				<td class="td2 b">
					<a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a>
				</td>
			</tr>
		</table>
		</div>

		<div id="snsdiv_m"<?php if(!$d['social']['use_m']):?> class="hide"<?php endif?>>
		<table>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_m0.gif" alt="" /> API Key</td>
				<td class="td2">
					<input type="text" name="key_m" value="<?php echo $d['social']['key_m']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_m0.gif" alt="" /> Callback Url</td>
				<td class="td2 b">
					<?php echo $g['url_root'].'/?r='.$r.'&m='.$module.'&a=snscall_direct&type=m'?>
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_m0.gif" alt="" /> APP 등록페이지</td>
				<td class="td2 b">
					<a href="http://me2day.net/me2/app/get_appkey" target="_blank">http://me2day.net/me2/app/get_appkey</a>
				</td>
			</tr>
		</table>
		</div>

		<div id="snsdiv_y"<?php if(!$d['social']['use_y']):?> class="hide"<?php endif?>>
		<table>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_y0.gif" alt="" /> Consumer Key</td>
				<td class="td2">
					<input type="text" name="key_y" value="<?php echo $d['social']['key_y']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_y0.gif" alt="" /> Consumer Secret</td>
				<td class="td2">
					<input type="text" name="secret_y" value="<?php echo $d['social']['secret_y']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_y0.gif" alt="" /> Callback Url</td>
				<td class="td2 b">
					<?php echo $g['url_root'].'/?r='.$r.'&m='.$module.'&a=snscall_direct&type=y'?>
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_y0.gif" alt="" /> APP 등록페이지</td>
				<td class="td2 b">
					<a href="https://dna.daum.net/myapi/authapi/new" target="_blank">https://dna.daum.net/myapi/authapi/new</a>
				</td>
			</tr>
		</table>
		</div>

		
		<div id="snsdiv_r"<?php if(!$d['social']['use_r']):?> class="hide"<?php endif?>>
		<table>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_r0.gif" alt="" /> Consumer Key</td>
				<td class="td2">
					<input type="text" name="key_r" value="<?php echo $d['social']['key_r']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_r0.gif" alt="" /> Consumer Secret</td>
				<td class="td2">
					<input type="text" name="secret_r" value="<?php echo $d['social']['secret_r']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_r0.gif" alt="" /> Callback Url</td>
				<td class="td2 b">
					<?php echo $g['url_root'].'/?r='.$r.'&m='.$module.'&a=snscall_direct&type=r'?>
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_r0.gif" alt="" /> APP 등록페이지</td>
				<td class="td2 b">
					<a href="http://www.flickr.com/services/apps/create/" target="_blank">http://www.flickr.com/services/apps/create/</a>
				</td>
			</tr>
		</table>
		</div>

		<div id="snsdiv_g"<?php if(!$d['social']['use_g']):?> class="hide"<?php endif?>>
		<table>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_g0.gif" alt="" /> Consumer Key</td>
				<td class="td2">
					<input type="text" name="key_g" value="<?php echo $d['social']['key_g']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_g0.gif" alt="" /> Consumer Secret</td>
				<td class="td2">
					<input type="text" name="secret_g" value="<?php echo $d['social']['secret_g']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_g0.gif" alt="" /> Callback Url</td>
				<td class="td2 b">
					<?php echo $g['url_root'].'/?r='.$r.'&m='.$module.'&a=snscall_direct&type=g'?>
				</td>
			</tr>
			<tr>
				<td class="td1"><img src="<?php echo $g['path_module'].$module?>/image/sns_g0.gif" alt="" /> APP 등록페이지</td>
				<td class="td2 b">
					<a href="http://code.google.com/apis/console" target="_blank">http://code.google.com/apis/console</a>
				</td>
			</tr>
		</table>
		</div>

		

		<div id="snsdiv_b"<?php if(!$d['social']['use_b']):?> class="hide"<?php endif?>>
		<table>
			<tr>
				<td class="td1"> Login ID</td>
				<td class="td2">
					<input type="text" name="key_b" value="<?php echo $d['social']['key_b']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"> API Key</td>
				<td class="td2">
					<input type="text" name="secret_b" value="<?php echo $d['social']['secret_b']?>" size="40" class="input" />
				</td>
			</tr>
			<tr>
				<td class="td1"> APP 등록페이지</td>
				<td class="td2 b">
					<a href="http://bit.ly/a/account/" target="_blank">http://bit.ly/a/account/</a>
				</td>
			</tr>
			<tr>
				<td class="td1"> 관리자 ID</td>
				<td class="td2">
					<input type="text" name="key_id" value="<?php echo $d['social']['key_id']?>" size="40" class="input" />
				</td>
			</tr>
		</table>
		</div>

		<div class="submitbox">
			<input type="submit" class="btnblue" value=" 확인 " />
		</div>

		</form>


		
	</div>
	<div class="clear"></div>
</div>
<?php endif;//최고관리자 체크?>


<script type="text/javascript">
//<![CDATA[
function chkSNScheck(obj)
{
	if (obj.checked == true)
	{
		getId(obj.name.replace('use_','snsdiv_')).style.display = 'block';
	}
	else {
		getId(obj.name.replace('use_','snsdiv_')).style.display = 'none';
	}
}
function saveCheck(f)
{
	return confirm('정말로 실행하시겠습니까?         ');
}

//]]>
</script>
