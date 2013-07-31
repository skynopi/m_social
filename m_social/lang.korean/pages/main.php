<?php
if(!defined('__KIMS__')) exit;

$typeset = array
(
	't' => '트위터',
	'f' => '페이스북',
	'm' => '미투데이',
	'y' => '요즘',
);
$urlset = array
(
	't' => 'www.twitter.com',
	'f' => 'www.facebook.com',
	'm' => 'www.me2day.net',
	'y' => 'yozm.daum.net',
);
?>

<div id="snsbox">
	<div class="header">
		<a href="http://www.biz79.net/" target="_blank"><img src="<?php echo $g['img_core']?>/_public/ico_rb.gif" alt="biz79" title="오픈 비지니스 웹 플랫폼" /></a>
		<h1>소셜링크</h1>
	</div>
	<div class="body">
		<p>
			<img src="<?php echo $g['img_core']?>/_public/sns_<?php echo $type?>1.gif" alt="" />
			<?php echo $my['uid']?$my[$_HS['nametype']]:'손'?>님께서는 <span class="b"><?php echo $typeset[$type]?></span>(<a href="http://<?php echo $urlset[$type]?>/" target="_blank"><?php echo str_replace('www.','',$urlset[$type])?></a>) 연결을 요청하실 수 있습니다.<br />
			연결요청후 수락해 주시면 선택한 SNS에도 동시에 글을 게시할 수 있습니다. 
		</p>	
	</div>
	<div class="footer">
		<input type="button" value=" 취소 " class="btngray" onclick="top.close();" />
		<input type="button" value="<?php echo $typeset[$type]?>에 연결요청 보내기" class="btnblue" onclick="snsCall('<?php echo $type?>','<?php echo $_SESSION[$type.'_token']?>');" />
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
function snsCall(type,nowcall)
{
	if (nowcall!='')
	{
		alert('이미 연결되어 있습니다.');
		opener.getId('snsInp_<?php echo $type?>').checked = true;
		opener.getId('snsImg_<?php echo $type?>').style.filter = 'alpha(opacity=100);';
		opener.getId('snsImg_<?php echo $type?>').style.opacity = '1';
		top.close();
	}
	else {
		getId('snsbox').style.display = 'none';

		var w;
		var h;

		switch(type) 
		{
			case 't':
				w = 810;
				h = 550;
				break;
			case 'f':
				w = 1024;
				h = 680;
				break;
			case 'm':
				w = 990;
				h = 800;
				break;
			case 'y':
				w = 450;
				h = 450;
				break;
		}
		var url = "<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $m?>&a=snscall&type="+ type;
		location.href = url;
		top.resizeTo(w,h);
	}
}
window.onload = function()
{
	document.title = '<?php echo $typeset[$type]?> 연결요청';
	top.resizeTo(300,270);
}
//]]>
</script>

