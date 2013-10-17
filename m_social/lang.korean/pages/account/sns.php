<?php include_once $g['path_module'].$m.'/var/'.$s.'.var.php'?>
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

	<?php 
	if($stype):

	if($stype == 't'):
		$_mysnsdat=explode(',',$g['mysns'][0]);

		if($_mysnsdat[1]):
			require_once($g['dir_module'].'oauth/http.php');
			require_once($g['dir_module'].'oauth/oauth_client.php');

			$client_t = new oauth_client_class;
			$client_t->offline = true;
			$client_t->debug = true;
			$client_t->debug_http = true;
			$client_t->server = 'Twitter';

			$client_t->client_id = $d['social']['key_t'];
			$client_t->client_secret = $d['social']['secret_t'];

			$client_t->access_token = $_mysnsdat[2];
			$client_t->access_token_secret = $_mysnsdat[3];

			if(($success_t = $client_t->Initialize()))
			{
				$success_t = $client_t->CallAPI(
					'https://api.twitter.com/1.1/account/verify_credentials.json', 
					'GET', array(), array('FailOnAccessError'=>true), $user_t);
				
				$success_t = $client_t->Finalize($success_t);
			}
			if($client_t->exit)	exit;
			
			if($success_t) :
		?>
				<table class="configtbl tline">
				<tr>
				<td class="td1">주소</td>
				<td class="td2">
					<a href="http://www.twitter.com/<?php echo $user_t->screen_name?>" target="_blank" class="u b">http://www.twitter.com/<?php echo $user_t->screen_name?></a>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">프로필 이미지</td>
				<td class="td2">
					<img src="<?php echo $user_t->profile_image_url?>" width="50" height="50" alt="" />
					<input type="hidden" name="photo" value="<?php echo $user_t->profile_image_url?>" />
					<input type="hidden" name="photo_big" value="<?php echo str_replace('_normal','_reasonably_small',$user_t->profile_image_url)?>" />
					<div>동기화 실행시 작은 이미지, 큰 이미지 모두 갱신됩니다.</div>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">수치정보</td>
				<td class="td2">
					트윗 <?php echo $user_t->statuses_count?> , 
					팔로잉 <?php echo $user_t->friends_count?> , 
					팔로워 <?php echo $user_t->followers_count?> , 
					리스트 <?php echo $user_t->listed_count?>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">이름</td>
				<td class="td2">
					<input type="text" name="name" value="<?php echo $user_t->name?>" />
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">자기소개</td>
				<td class="td2">
					<textarea cols="50" rows="4" name="description"><?php echo $user_t->description?></textarea>
				</td>
				<td class="td3"></td>
				</tr>
				</table>

			<?php else:?>
				<div class="none">트위터 연결정보가 없습니다.</div>
			<?php endif?>
		<?php endif?>
	<?php endif?>


	<?php 
	if($stype == 'f'):
		$_mysnsdat=explode(',',$g['mysns'][1]);

		if($_mysnsdat[1]):
			require_once($g['dir_module'].'oauth/http.php');
			require_once($g['dir_module'].'oauth/oauth_client.php');

			$client_f = new oauth_client_class;
			$client_f->server = 'Facebook';

			$client_f->client_id = $d['social']['key_f'];
			$client_f->client_secret = $d['social']['secret_f'];

			$client_f->access_token = $_mysnsdat[2];
			
			if(($success_f = $client_f->Initialize()))
			{
				$success_f = $client_f->CallAPI(
					'https://graph.facebook.com/me', 
					'GET', array(), array('FailOnAccessError'=>true), $user_f);
				
				$success_f = $client_f->Finalize($success_f);
			}
			if($client_f->exit)	exit;
			
			if($success_f) :
				$client_f->CallAPI('https://api.facebook.com/method/', 'POST', array('method'=>'fql.query','query'=>'SELECT pic_square,pic_big,wall_count,friend_count,mutual_friend_count,likes_count from user where uid='.$user_f->id,'format'=>'json'), array('FailOnAccessError'=>true,'application/json'), $user_f2);			
			?>

				<table class="configtbl tline">
				<tr>
				<td class="td1">주소</td>
				<td class="td2">
					<a href="<?php echo $user_f->link?>" target="_blank" class="u b"><?php echo $user_f->link?></a>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">프로필 이미지</td>
				<td class="td2">
					<img src="<?php echo $user_f2[0]->pic_square?>" width="50" height="50" alt="" />
					<input type="hidden" name="photo" value="<?php echo $user_f2[0]->pic_square?>" />
					<input type="hidden" name="photo_big" value="<?php echo $user_f2[0]->pic_big?>" />
					<div>동기화 실행시 작은 이미지,큰 이미지 모두 갱신됩니다.</div>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">수치정보</td>
				<td class="td2">
					담벼락 <?php echo $user_f2[0]->wall_count?> , 
					팔로잉 <?php echo $user_f2[0]->friend_count?> , 
					팔로워 <?php echo $user_f2[0]->mutual_friend_count?> , 
					좋아요 <?php echo number_format($user_f2[0]->likes_count)?>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">이름</td>
				<td class="td2">
					<input type="text" name="name" value="<?php echo $user_f->name?>" />
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">자기소개</td>
				<td class="td2">
					<textarea cols="50" rows="4" name="description"><?php echo $user_f->bio?></textarea>
				</td>
				<td class="td3"></td>
				</tr>
				</table>

			<?php else:?>
				<div class="none">페이스북 연결정보가 없습니다.</div>
			<?php endif?>
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

	<?php else:?>
	<div class="none">미투데이 연결정보가 없습니다.</div>
	<?php endif?>
	<?php endif?>

	
	<?php 
	if($stype == 'y'):
		$_mysnsdat=explode(',',$g['mysns'][3]);

		if($_mysnsdat[1]):
			require_once($g['dir_module'].'oauth/http.php');
			require_once($g['dir_module'].'oauth/oauth_client.php');

			$client_y = new oauth_client_class;
			$client_y->offline = true;
			$client_y->debug = false;
			$client_y->debug_http = true;
			$client_y->server = 'yozm';

			$client_y->client_id = $d['social']['key_y'];
			$client_y->client_secret = $d['social']['secret_y'];

			$client_y->access_token = $_mysnsdat[2];
			$client_y->access_token_secret = $_mysnsdat[3];
			
			if(($success_y = $client_y->Initialize()))
			{
				$success_y = $client_y->CallAPI(
					'https://apis.daum.net/blog/info/blog.do', 
					'GET', array('output'=>'json'), array('FailOnAccessError'=>true), $user_y);
				
				$success_y = $client_y->Finalize($success_y);
			}
			if($client_y->exit)	exit;
	
			if($success_y) :
				$user_y2 = json_decode($user_y); 
			?>

				<table class="configtbl tline">				
				<tr>
				<td class="td1">블로그 이미지</td>
				<td class="td2">
					<img src="<?php echo $user_y2->channel->profileImageUrl?>" width="50" height="50" alt="" />
					<input type="hidden" name="photo" value="<?php echo $user_y2->channel->profileImageUrl?>" />
					<input type="hidden" name="photo_big" value="<?php echo $user_y2->channel->profileImageUrl?>" />
					<div>동기화 실행시 작은 이미지,큰 이미지 모두 갱신됩니다.</div>
				</td>
				<td class="td3"></td>
				</tr>
				
				<tr>
				<td class="td1">블로그명</td>
				<td class="td2">
					<input type="text" name="name" value="<?php echo $user_y2->channel->name?>" />
				</td>
				<td class="td3"></td>
				</tr>
				</table>
				
			<?php else:?>
				<div class="none">다음 블로그 연결정보가 없습니다.</div>
			<?php endif?>
		<?php endif?>
	<?php endif?>


	<?php 
	if($stype == 'r'):
		$_mysnsdat=explode(',',$g['mysns'][4]);

		if($_mysnsdat[1]):
			require_once($g['dir_module'].'oauth/phpFlickr.php');

			$f = new phpFlickr($d['social']['key_r'], $d['social']['secret_r']);

			$f->setToken($_mysnsdat[2]);
			$arr_user = $f->people_getInfo($_mysnsdat[3]);
			
			if($arr_user['id']) :
				
			?>

				<table class="configtbl tline">
				<tr>
				<td class="td1">주소</td>
				<td class="td2">
					<a href="<?php echo $arr_user['photosurl']?>" target="_blank" class="u b"><?php echo $arr_user['photosurl']?></a>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">프로필 이미지</td>
				<td class="td2">
					<img src="<?php echo 'http://static.flickr.com/'.$arr_user['iconserver'].'/buddyicons/'.$arr_user['nsid'].'.jpg'?>" width="50" height="50" alt="" />
					<input type="hidden" name="photo" value="<?php echo 'http://static.flickr.com/'.$arr_user['iconserver'].'/buddyicons/'.$arr_user['nsid'].'.jpg'?>" />
					<input type="hidden" name="photo_big" value="<?php echo 'http://static.flickr.com/'.$arr_user['iconserver'].'/buddyicons/'.$arr_user['nsid'].'.jpg'?>" />
					<div>동기화 실행시 작은 이미지,큰 이미지 모두 갱신됩니다.</div>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">수치정보</td>
				<td class="td2">
					전체 이미지 <?php echo $arr_user['photos']['count']?> 장
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">이름</td>
				<td class="td2">
					<input type="text" name="name" value="<?php echo $arr_user['username']?>" />
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">자기소개</td>
				<td class="td2">
					<textarea cols="50" rows="4" name="description"><?php echo $arr_user['description']?></textarea>
				</td>
				<td class="td3"></td>
				</tr>
				</table>

			<?php else:?>
				<div class="none">플리커 연결정보가 없습니다.</div>
			<?php endif?>
		<?php endif?>
	<?php endif?>


	<?php 
	if($stype == 'g'):
		$_mysnsdat=explode(',',$g['mysns'][5]);

		if($_mysnsdat[1]):
			require_once($g['dir_module'].'oauth/http.php');
			require_once($g['dir_module'].'oauth/oauth_client.php');

			$client_g = new oauth_client_class;
			$client_g->offline = true;
			$client_g->debug = false;
			$client_g->debug_http = true;
			$client_g->server = 'Google';

			$client_g->client_id = $d['social']['key_g'];
			$client_g->client_secret = $d['social']['secret_g'];

			$client_g->access_token = $_mysnsdat[2];
			$client_g->refresh_token = $_mysnsdat[3];
			$client_g->refresh_token = $_mysnsdat[5];
			$client_g->access_token_expiry = $_mysnsdat[6];

			if(($success_g = $client_g->Initialize()))
			{
				$success_g = $client_g->CallAPI(
					'https://www.googleapis.com/oauth2/v1/userinfo',
					'GET', array(), array('FailOnAccessError'=>true), $user_g);
				
				$success_g = $client_g->Finalize($success_g);
			}
			if($client_g->exit)	exit;

			if($success_g) :				
			?>

				<table class="configtbl tline">
				<tr>
				<td class="td1">주소</td>
				<td class="td2">
					<a href="<?php echo $user_g->link?>" target="_blank" class="u b"><?php echo $user_g->link?></a>
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">프로필 이미지</td>
				<td class="td2">
					<img src="<?php echo $user_g->picture?>" width="50" height="50" alt="" />
					<input type="hidden" name="photo" value="<?php echo $user_g->picture?>" />
					<input type="hidden" name="photo_big" value="<?php echo $user_g->picture?>" />
					<div>동기화 실행시 작은 이미지,큰 이미지 모두 갱신됩니다.</div>
				</td>
				<td class="td3"></td>
				</tr>
				
				<tr>
				<td class="td1">이름</td>
				<td class="td2">
					<input type="text" name="name" value="<?php echo $user_g->name?>" />
				</td>
				<td class="td3"></td>
				</tr>
				<tr>
				<td class="td1">자기소개</td>
				<td class="td2">
					<textarea cols="50" rows="4" name="description"><?php //echo $user_g->bio?></textarea>
				</td>
				<td class="td3"></td>
				</tr>
				</table>
			
			<?php else:?>
				<div class="none">구글 플러스 연결정보가 없습니다.</div>
			<?php endif?>
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
