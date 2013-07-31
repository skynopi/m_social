<?php include_once $g['path_module'].'social/var/var.php'?>
<div class="snsaccount">
	
	<form name="procForm" action="<?php echo $g['s']?>/" method="post" onsubmit="return saveCheck(this);">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="a" value="member_config" />
	<input type="hidden" name="act" value="<?php echo $vtype?>" />
	<input type="hidden" name="stype" value="<?php echo $stype?>" />
	<input type="hidden" name="cync" value="" />
	<input type="hidden" name="check_id" value="1" />


	<?php $i=0;foreach($g['snskor'] as $_key=>$_val):?>
	<?php if(!$d['social']['use_'.$_key])continue?>
	<?php $_snsuse=explode(',',$g['mysns'][$i])?>
	<div class="snsx">
		<a href="<?php echo $g['sociallink']?>&amp;page=<?php echo $page?>&amp;type=<?php echo $type?>&amp;vtype=<?php echo $vtype?>&amp;stype=<?php echo $_key?>"><img src="<?php echo $g['img_module_skin']?>/sns_<?php echo $_key?>.gif" id="sns_ico_<?php echo $_key?>"<?php if($_snsuse[0]==''):?> class="filter gray"<?php endif?> alt="" title="<?php echo $_val?>" /></a>
		<span><?php echo $_val?></span>

		<?php if($g['mysns'][$i]):?>
			<?php if($_snsuse[0]!='on'):?>
			<img src="<?php echo $g['img_module_skin']?>/btn_off.gif" id="sns_btn_<?php echo $_key?>" alt="OFF" title="ON으로 변경하시겠습니까?" class="bicon hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','connect');" />
			<?php else:?>
			<img src="<?php echo $g['img_module_skin']?>/btn_on.gif" id="sns_btn_<?php echo $_key?>" alt="ON" title="OFF로 변경하시겠습니까?" class="bicon hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','disconnect');" />
			<?php endif?>
			<br />
			<img src="<?php echo $g['img_module_skin']?>/btn_disconnect.gif" alt="" class="hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','delete');" />
		<?php else:?>
			<img src="<?php echo $g['img_module_skin']?>/btn_connect.gif" alt="" class="hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','connect');" />
		<?php endif?>

	</div>
	<?php $i++;endforeach?>
	<div class="clear"></div>

	<?php if($stype):?>

	<?php if($stype == 't'):?>
	<?php $_mysnsdat=explode(',',$g['mysns'][0])?>
	<?php if($_mysnsdat[1]):?>
	<?php require_once $g['path_module'].'social/oauth/twitteroauth/twitteroauth.php'?>
	<?php $TC = new TwitterOAuth($d['social']['key_t'], $d['social']['secret_t'],$_mysnsdat[2],$_mysnsdat[3])?>
	<?php $TR = $TC->get('account/verify_credentials')?>

	<table class="configtbl tline">
	<tr>
	<td class="td1">주소</td>
	<td class="td2">
		<a href="http://www.twitter.com/<?php echo $TR->screen_name?>" target="_blank" class="u b">http://www.twitter.com/<?php echo $TR->screen_name?></a>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">프로필 이미지</td>
	<td class="td2">
		<img src="<?php echo $TR->profile_image_url?>" width="50" height="50" alt="" />
		<input type="hidden" name="photo" value="<?php echo $TR->profile_image_url?>" />
		<input type="hidden" name="photo_big" value="<?php echo str_replace('_normal','_reasonably_small',$TR->profile_image_url)?>" />
		<div>동기화 실행시 작은 이미지, 큰 이미지 모두 갱신됩니다.</div>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">수치정보</td>
	<td class="td2">
		트윗 <?php echo $TR->statuses_count?> , 
		팔로잉 <?php echo $TR->friends_count?> , 
		팔로워 <?php echo $TR->followers_count?> , 
		리스트 <?php echo $TR->listed_count?>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">이름</td>
	<td class="td2">
		<input type="text" name="name" value="<?php echo $TR->name?>" />
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">자기소개</td>
	<td class="td2">
		<textarea cols="50" rows="4" name="description"><?php echo $TR->description?></textarea>
	</td>
	<td class="td3"></td>
	</tr>
	</table>
<!--
	<div class="submit">
	<input type="button" value="변경내용을 트위터에 적용" class="btnblue" onclick="snsCync(1);" />
	<input type="button" value="킴스큐 계정과 동기화" class="btnblue" onclick="snsCync(2);" />
	</div>
-->
	<?php else:?>
	<div class="none">트위터 연결정보가 없습니다.</div>
	<?php endif?>
	<?php endif?>


	<?php if($stype == 'f'):?>
	<?php $_mysnsdat=explode(',',$g['mysns'][1])?>
	<?php if($_mysnsdat[1]):?>
	<?php
	require_once $g['path_module'].'social/oauth/facebook/src/facebook.php';
	$FC = new Facebook(array('appId'=>$d['social']['key_f'],'secret'=>$d['social']['secret_f'],'cookie'=>true));
	$FUID = $FC->getUser();
	if (!$FUID) getLink($FC->getLoginUrl(),'','','');

	$FR1 = $FC->api(array('method'=>'fql.query','query'=>'SELECT id,can_post,name,url,pic,pic_square,pic_small,pic_big,pic_crop,type,username from profile where id='.$FUID));
	$FR2 = $FC->api(array('method'=>'fql.query','query'=>'SELECT username,first_name,middle_name,last_name,affiliations,profile_update_time,timezone,religion,birthday,birthday_date,sex,hometown_location,meeting_sex,meeting_for,relationship_status,significant_other_id,political,current_location,activities,interests,is_app_user,music,tv,movies,books,quotes,about_me,hs_info,education_history,work_history,notes_count,wall_count,status,has_added_app,online_presence,proxied_email,profile_url,email_hashes,pic_small_with_logo,pic_big_with_logo,pic_square_with_logo,pic_with_logo,allowed_restrictions,verified,profile_blurb,family,website,is_blocked,contact_email,email,third_party_id,name_format,video_upload_limits,games,is_minor,work,education,sports,favorite_athletes,favorite_teams,inspirational_people,languages,likes_count,friend_count,mutual_friend_count from user where uid='.$FUID));
	?>

	<table class="configtbl tline">
	<tr>
	<td class="td1">주소</td>
	<td class="td2">
		<a href="<?php echo $FR1[0]['url']?>" target="_blank" class="u b"><?php echo $FR1[0]['url']?></a>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">프로필 이미지</td>
	<td class="td2">
		<img src="<?php echo $FR1[0]['pic_square']?>" width="50" height="50" alt="" />
		<input type="hidden" name="photo" value="<?php echo $FR1[0]['pic_square']?>" />
		<input type="hidden" name="photo_big" value="<?php echo $FR1[0]['pic_big']?>" />
		<div>동기화 실행시 작은 이미지,큰 이미지 모두 갱신됩니다.</div>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">수치정보</td>
	<td class="td2">
		담벼락 <?php echo $FR2[0]['wall_count']?> , 
		팔로잉 <?php echo $FR2[0]['friend_count']?> , 
		팔로워 <?php echo $FR2[0]['mutual_friend_count']?> , 
		좋아요 <?php echo number_format($FR2[0]['likes_count'])?>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">이름</td>
	<td class="td2">
		<input type="text" name="name" value="<?php echo $FR1[0]['name']?>" />
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">자기소개</td>
	<td class="td2">
		<textarea cols="50" rows="4" name="description"><?php echo $FR2[0]['about_me']?></textarea>
	</td>
	<td class="td3"></td>
	</tr>
	</table>
<!--
	<div class="submit">
	<input type="button" value="킴스큐 계정과 동기화" class="btnblue" onclick="snsCync(2);" />
	</div>
-->

	<?php else:?>
	<div class="none">페이스북 연결정보가 없습니다.</div>
	<?php endif?>
	<?php endif?>


	<?php if($stype == 'm'):?>
	<?php $_mysnsdat=explode(',',$g['mysns'][2])?>
	<?php if($_mysnsdat[1]):?>
	<?php include_once $g['path_core'].'function/rss.func.php'?>
	<?php $MR = getUrlData("http://me2day.net/api/get_person/".$_mysnsdat[4].".xml?akey=".$d['social']['key_m'],10)?>

	<table class="configtbl tline">
	<tr>
	<td class="td1">주소</td>
	<td class="td2">
		<a href="<?php echo getRssTagValue($MR,'me2dayHome')?>" target="_blank" class="u b"><?php echo getRssTagValue($MR,'me2dayHome')?></a>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">프로필 이미지</td>
	<td class="td2">
		<img src="<?php echo getRssTagValue($MR,'face')?>" width="50" height="50" alt="" />
		<input type="hidden" name="photo" value="<?php echo getRssTagValue($MR,'face')?>" />
		<input type="hidden" name="photo_big" value="" />
		<div>동기화 실행시 작은 이미지만 갱신됩니다.</div>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">수치정보</td>
	<td class="td2">
		포스트 <?php echo getRssTagValue($MR,'totalPosts')?> , 
		친구 <?php echo getRssTagValue($MR,'friendsCount')?> , 
		친구신청 <?php echo getRssTagValue($MR,'pinMeCount')?>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">이름</td>
	<td class="td2">
		<input type="text" name="name" value="<?php echo getRssTagValue($MR,'nickname')?>" />
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">자기소개</td>
	<td class="td2">
		<textarea cols="50" rows="4" name="description"><?php echo getRssTagValue($MR,'description')?></textarea>
	</td>
	<td class="td3"></td>
	</tr>
	</table>
<!--
	<div class="submit">
	<input type="button" value="킴스큐 계정과 동기화" class="btnblue" onclick="snsCync(2);" />
	</div>
-->


	<?php else:?>
	<div class="none">미투데이 연결정보가 없습니다.</div>
	<?php endif?>
	<?php endif?>


	<?php if($stype == 'y'):?>
	<?php $_mysnsdat=explode(',',$g['mysns'][3])?>
	<?php if($_mysnsdat[1]):?>
	<?php require_once $g['path_module'].'social/oauth/twitteroauth/yozm.php'?>
	<?php $YC = new YozmOAuth($d['social']['key_y'], $d['social']['secret_y'],$_mysnsdat[2],$_mysnsdat[3])?>
	<?php $YR = $YC->get('user/show', array())?>

	<table class="configtbl tline">
	<tr>
	<td class="td1">주소</td>
	<td class="td2">
		<a href="http://yozm.daum.net/<?php echo $YR->user->url_name?>" target="_blank" class="u b">http://yozm.daum.net/<?php echo $YR->user->url_name?></a>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">프로필 이미지</td>
	<td class="td2">
		<img src="<?php echo $YR->user->profile_img_url?>" width="50" height="50" alt="" />
		<input type="hidden" name="photo" value="<?php echo $YR->user->profile_img_url?>" />
		<input type="hidden" name="photo_big" value="<?php echo $YR->user->profile_big_img_url?>" />
		<div>동기화 실행시 작은 이미지,큰 이미지 모두 갱신됩니다.</div>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">수치정보</td>
	<td class="td2">
		포스트 <?php echo $YR->user->msg_cnt?> , 
		친구 <?php echo $YR->user->following_cnt?> , 
		인기 <?php echo $YR->user->follower_cnt?>
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">이름</td>
	<td class="td2">
		<input type="text" name="name" value="<?php echo $YR->user->user_identity->real_name?$YR->user->user_identity->real_name:$YR->user->nickname?>" />
	</td>
	<td class="td3"></td>
	</tr>
	<tr>
	<td class="td1">자기소개</td>
	<td class="td2">
		<textarea cols="50" rows="4" name="description"><?php echo $YR->user->user_info->introduce?></textarea>
	</td>
	<td class="td3"></td>
	</tr>
	</table>

<!--
	<div class="submit">
	<input type="button" value="킴스큐 계정과 동기화" class="btnblue" onclick="snsCync(2);" />
	</div>
-->

	<?php else:?>
	<div class="none">다음요즘 연결정보가 없습니다.</div>
	<?php endif?>
	<?php endif?>


	<?php else:?>
	<div class="none">연결 후 SNS 아이콘을 클릭하시면 요약정보를 확인하실 수 있습니다.</div>
	<?php endif?>

	</form>

</div>


<script type="text/javascript">
//<![CDATA[
function snsCync(s)
{
	if (confirm('정말로 실행하시겠습니까?     '))
	{
		var f = document.procForm;
		getIframeForAction(f);
		f.cync.value = s;
		f.submit();
	}
}
function saveCheck(f)
{
	getIframeForAction(f);
	return false;
}
//]]>
</script>
