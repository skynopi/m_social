<?php
if(!defined('__KIMS__')) exit;

include_once $g['dir_module'].'var/'.$s.'.var.php';
include_once $g['path_module'].'member/var/var.join.php';

if ($d['social']['use_t'])
{
	if ($type == 't')
	{
		require_once($g['dir_module'].'oauth/twitteroauth/twitteroauth.php');
		$TWITCONN = new TwitterOAuth($d['social']['key_t'],$d['social']['secret_t']);
		$RCVTOKEN = $TWITCONN -> getRequestToken($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&twitter=Y');
		$_SESSION['t_token'] = $RCVTOKEN['oauth_token'];
		$_SESSION['t_sekey'] = $RCVTOKEN['oauth_token_secret'];

		switch ($TWITCONN -> http_code)
		{
			case 200:
			$TWITURL = $TWITCONN -> getAuthorizeURL($RCVTOKEN['oauth_token']);
			break;
			default:
			getLink('','','죄송합니다. 트위터 서버가 응답하지 않습니다.','close');
			break;
		}
		header('Location:'.$TWITURL);
		exit;
	}
	if ($twitter == 'Y')
	{
		if ($denied)
		{
			getLink('','','트위터 연결을 취소하셨습니다.','close');
		}
		require_once($g['path_module'].'social/oauth/twitteroauth/twitteroauth.php');
		$TWITCONN = new TwitterOAuth($d['social']['key_t'], $d['social']['secret_t'], $_SESSION['t_token'], $_SESSION['t_sekey']);
		$ACCESSTWIT = $TWITCONN -> getAccessToken($_REQUEST['oauth_verifier']);
		$_SESSION['t_token'] = $ACCESSTWIT['oauth_token'];
		$_SESSION['t_sekey'] = $ACCESSTWIT['oauth_token_secret'];
		$_SESSION['t_mbrid'] = $ACCESSTWIT['screen_name'];
		if (!$_SESSION['t_mbrid'])
		{
			getLink('','','죄송합니다. 세션에 문제가 있습니다. 다시 시도해 주세요.','close');
		}		
		$_rs2 = 'on,http://twitter.com/'.$_SESSION['t_mbrid'].','.$_SESSION['t_token'].','.$_SESSION['t_sekey'].','.$_SESSION['t_mbrid'].','.$_REQUEST['oauth_verifier'].',';
		$_rs1 = '';
		$_set = array('t','f','m','y','','');
		$_cnt = count($_set);
		for($i = 0; $i < $_cnt; $i++)
		{
			$_rs1 .= ($i==0?$_rs2:$g['mysns'][$i]).'|';
		}
			
		if ($my['uid'])
		{
			$_ISSNS = getDbData($table['s_mbrsns'],"st='".$_SESSION['t_mbrid']."'",'*');
			if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
			{
				$_SESSION['t_token'] = '';
				$_SESSION['t_sekey'] = '';
				$_SESSION['t_mbrid'] = '';
				$_SESSION['plussns'] = $_ISSNS['memberuid'];
				getLink('','',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
			}
			getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
			if (!$g['mysns'][0])
			{
				if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
				{
					getDbUpdate($table['s_mbrsns'],"st='".$_SESSION['t_mbrid']."'",'memberuid='.$my['uid']);
				}
				else {
					getDbInsert($table['s_mbrsns'],'memberuid,st',"'".$my['uid']."','".$_SESSION['t_mbrid']."'");
				}
			}
		}
		else {
			$_ISSNS = getDbData($table['s_mbrsns'],"st='".$_SESSION['t_mbrid']."'",'*');
			if($_ISSNS['memberuid'])
			{
				$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
				getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
				getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
				$_SESSION['mbr_uid'] = $M['uid'];
				$_SESSION['mbr_pw']  = $M['pw'];
			}
			else {
				include_once $g['path_core'].'function/rss.func.php';
				include_once $g['path_core'].'function/thumb.func.php';

				$TR = $TWITCONN->get('account/verify_credentials');
				$id = 'm'.sprintf('%-012s',str_replace('.','',$g['time_start']));
				getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
				$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');

				$picdata = getUrlData($TR->profile_image_url,10);
				if ($picdata)
				{
					$pic = $g['path_var'].'simbol/'.$id.'.jpg';
					$fp = fopen($pic,'w');
					fwrite($fp,$picdata);
					fclose($fp);
					ResizeWidthHeight($pic,$pic,50,50);
					@chmod($pic);
					$photo = $id.'.jpg';
				}
				$picdata = getUrlData(str_replace('_normal','_reasonably_small',$TR->profile_image_url),10);
				if ($picdata)
				{
					$pic = $g['path_var'].'simbol/180.'.$id.'.jpg';
					$fp = fopen($pic,'w');
					fwrite($fp,$picdata);
					fclose($fp);
					ResizeWidth($pic,$pic,180);
					@chmod($pic);
					$photo = $id.'.jpg';
				}
				$_QKEY = "memberuid,site,auth,sosok,level,comp,admin,adm_view,";
				$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
				$_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield";
				$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
				$_QVAL.= "'','".$TR->name."','".$TR->name."','','$photo','".str_replace('http://','',$TR->url)."','0','0','0','0','','','',";
				$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
				getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
				getDbUpdate($table['s_mbrlevel'],'num=num+1','uid=1');
				getDbUpdate($table['s_mbrgroup'],'num=num+1','uid=1');
				getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
				if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
				getDbInsert($table['s_mbrsns'],'memberuid,st',"'".$memberuid."','".$_SESSION['t_mbrid']."'");

				$_SESSION['mbr_uid'] = $memberuid;
				$_SESSION['mbr_pw']  = '';
			}
		}
		getLink('reload','opener.','트위터와 연결되었습니다.','close');
	}
}

if ($d['social']['use_f'])
{
	if ($type == 'f')
	{ 
		$f_start = $_SESSION['fb_'.$d['social']['key_f'].'_state'];
		$callbackurl = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&facebook=Y');
		header('Location:http://www.facebook.com/dialog/oauth?client_id='.$d['social']['key_f'].'&redirect_uri='.$callbackurl.'&scope=publish_stream,offline_access,user_about_me,email,photo_upload,user_birthday,user_location,user_work_history,user_hometown,user_groups,user_subscriptions,manage_pages,read_friendlists&display='.($g['mobile']&&$_SESSION['pcmode']!= 'Y'?'touch':'page').'&state='.$f_start);
		exit;
	}
	
	
	
	if ($facebook == 'Y')
	{
		$f_mbrid = 'fb_'.$d['social']['key_f'].'_user_id';
		$f_token = 'fb_'.$d['social']['key_f'].'_access_token'; 
		$_SESSION[$f_token] = '';
		$_SESSION[$f_mbrid] = '';

		require_once $g['path_module'].'social/oauth/facebook/src/facebook.php';
		$FC = new Facebook(array('appId'=>$d['social']['key_f'],'secret'=>$d['social']['secret_f'],));
		$FUID = $FC->getUser(); 

		//$loginUrl = $FC->getLoginUrl(array('scope' => 'publish_stream,offline_access,user_about_me,email,photo_upload,user_birthday,user_location,user_work_history,user_hometown,user_groups,user_subscriptions,manage_pages,read_friendlists','redirect_uri' => $callbackurl));

									
		if ($FUID) {
			try {			
			} catch (FacebookApiException $e) {
				$FUID = null;
				getLink('','','페이스북 연결을 취소하셨습니다.','close');
			}
		}
					
		if ($FUID)
		{
			if (!$_SESSION[$f_mbrid])
			{
				getLink('','','죄송합니다. 세션에 문제가 있습니다. 다시 시도해 주세요.','close');
			}

			//$user_profile = $FC->api('/me'); print_r($user_profile);
			$userInfo = $FC->api("/$FUID"); 
				

			
			$FR = $FC->api(array('method'=>'fql.query','query'=>'SELECT name,url,pic_square,pic_big from profile where id='.$FUID));
			$_rs1 = '';
			$_rs2 = 'on,'.$FR[0]['url'].','.$_SESSION[$f_token].',"",'.$_SESSION[$f_mbrid].',"",';
			$_set = array('t','f','m','y','','');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==1?$_rs2:$g['mysns'][$i]).'|';
			}	
			
			if ($my['uid'])
			{
				$_ISSNS = getDbData($table['s_mbrsns'],"sf='".$_SESSION[$f_mbrid]."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION[$f_token] = '';
					$_SESSION[$f_mbrid] = '';
					$f_start = '';

					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','top.opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][1])
				{
					if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table['s_mbrsns'],"sf='".$_SESSION[$f_mbrid]."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table['s_mbrsns'],'memberuid,sf',"'".$my['uid']."','".$_SESSION[$f_mbrid]."'");
					}
				}
			}
			else {	
				$_ISSNS = getDbData($table['s_mbrsns'],"sf='".$_SESSION[$f_mbrid]."'",'*');
				if($_ISSNS['memberuid'])
				{ 
					$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
					getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
					getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
					$_SESSION['mbr_uid'] = $M['uid'];
					$_SESSION['mbr_pw']  = $M['pw'];
				}
				else {
					include_once $g['path_core'].'function/rss.func.php';
					include_once $g['path_core'].'function/thumb.func.php';

					//$FR2 = $FC->api(array('method'=>'fql.query','query'=>'SELECT sex,birthday_date from user where uid='.$FUID));

					try{
						$pages = $FC->api(array('method'=>'fql.query','query'=>'SELECT page_id,name,page_url,categories,fan_count,can_post,is_published FROM page WHERE page_id IN ( SELECT page_id FROM page_admin WHERE uid = "'.$FUID.'" )' ));
					} catch (FacebookApiException $e) {
						$pages = null;
					}

					try{
						$groups = $FC->api(array('method'=>'fql.query','query'=>'SELECT gid, name, creator,email,privacy FROM group WHERE gid IN ( SELECT gid FROM group_member WHERE uid = "'.$FUID.'" )' ));
					} catch (FacebookApiException $e) {
						$groups = null;
					}

					try{
						$friends = $FC->api(array('method'=>'fql.query','query'=>'SELECT uid, name, sex FROM user WHERE uid IN ( SELECT uid2 FROM friend WHERE uid1 = "'.$FUID.'" )' ));
					} catch (FacebookApiException $e) {
						$friends = null;
					}

					$id = 'm'.sprintf('%-012s',str_replace('.','',$g['time_start']));
					getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
					$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');

					$picdata = getUrlData($FR[0]['pic_square'],10);
					if ($picdata)
					{
						$pic = $g['path_var'].'simbol/'.$id.'.jpg';
						$fp = fopen($pic,'w');
						fwrite($fp,$picdata);
						fclose($fp);
						ResizeWidthHeight($pic,$pic,50,50);
						@chmod($pic);
						$photo = $id.'.jpg';
					}
					$picdata = getUrlData($FR[0]['pic_big'],10);
					if ($picdata)
					{
						$pic = $g['path_var'].'simbol/180.'.$id.'.jpg';
						$fp = fopen($pic,'w');
						fwrite($fp,$picdata);
						fclose($fp);
						ResizeWidth($pic,$pic,180);
						@chmod($pic);
						$photo = $id.'.jpg';
					}

					$birth = explode('/',$userInfo['birthday']);

					$_QKEY = "memberuid,site,auth,sosok,level,comp,admin,adm_view,";
					$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
					$_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield";
					$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
					$_QVAL.= "'','".$FR[0]['name']."','".$FR[0]['name']."','','$photo','','".($userInfo['gender']=='male'?1:2)."','".$birth[2]."','".$birth[0].$birth[1]."','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid=1');
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid=1');
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table['s_mbrsns'],'memberuid,sf',"'".$memberuid."','".$_SESSION[$f_mbrid]."'");
					
					$_fuser = getDbData($table['s_mbrfuser'],"sf='".$_SESSION[$f_mbrid]."'",'pid,memberuids');
					if(!$_fuser['sf']) {
						getDbInsert($table['s_mbrfuser'],'sf,f_name,f_link,f_username,f_gender,f_email,f_birthday1,f_birthday2,f_bio',"'".$_SESSION[$f_mbrid]."','".$userInfo['name']."','".$userInfo['link']."','".$userInfo['username']."','".($userInfo['gender']=='male'?1:2)."','".$userInfo['email']."','".$birth[2]."','".$birth[0].$birth[1]."','".$userInfo['bio']."'");
					}
					
					$fpagecount = count($pages);
					for($i=0; $i<$fpagecount; $i++){
						$_page = getDbData($table['s_mbrfpage'],"pid='".$pages[$i]['page_id']."'",'pid,memberuids');
						if(!$_page['pid']) {
							getDbInsert($table['s_mbrfpage'],'pid,name,page_url,fan_count,can_post,is_published,fmid,memberuids',"'".$pages[$i]['page_id']."','".$pages[$i]['name']."','".$pages[$i]['page_url']."','".$pages[$i]['fan_count']."','".$pages[$i]['can_post']."','".$pages[$i]['is_published']."','".$_SESSION[$f_mbrid]."','".$_SESSION[$f_mbrid]."'");
						}else{
							getDbUpdate($table['s_mbrfpage'],'fmid="'.$_SESSION[$f_mbrid].'",memberuids="'.$_page['memberuids'].','.$_SESSION[$f_mbrid].'"',"pid='".$_page['pid']."'");
						}
					}

					$fgroupcount = count($groups);
					for($i=0; $i<$fgroupcount; $i++){
						$_group = getDbData($table['s_mbrfgroup'],"gid='".$groups[$i]['gid']."'",'gid,memberuids');
						if(!$_group['gid']) {
							getDbInsert($table['s_mbrfgroup'],'gid,name,creator,email,privacy,fmid,memberuids',"'".$groups[$i]['gid']."','".$groups[$i]['name']."','".$groups[$i]['creator']."','".$groups[$i]['email']."','".$groups[$i]['privacy']."','".$_SESSION[$f_mbrid]."','".$_SESSION[$f_mbrid]."'");
						}else{
							//getDbUpdate($table['s_mbrfgroup'],'fmid="'.$_SESSION[$f_mbrid].'",privacy="'.$groups[$i]['privacy'].'",memberuids="'.$_group['memberuids'].','.$_SESSION[$f_mbrid].'"',"gid='".$_group['gid']."'");
							getDbUpdate($table['s_mbrfgroup'],'privacy="'.$groups[$i]['privacy'].'",memberuids="'.$_group['memberuids'].','.$_SESSION[$f_mbrid].'"',"gid='".$_group['gid']."'");
						}
					}

					$ffriendscount = count($friends);
					for($i=0; $i<$ffriendscount; $i++){
						$_friend = getDbData($table['s_mbrffriend'],"uid='".$friends[$i]['uid']."'",'uid,memberuids');
						if(!$_friend['uid']) {
							getDbInsert($table['s_mbrffriend'],'uid,name,sex,fmid,memberuids',"'".$friends[$i]['uid']."','".$friends[$i]['name']."','".($friends[$i]['sex']=='male'?1:2)."','".$_SESSION[$f_mbrid]."','".$_SESSION[$f_mbrid]."'");
						}else{
							getDbUpdate($table['s_mbrffriend'],'memberuids="'.$_friend['memberuids'].','.$_SESSION[$f_mbrid].'"',"uid='".$_friend['uid']."'");
						}
					}

					$_SESSION['mbr_uid'] = $memberuid;
					$_SESSION['mbr_pw']  = '';
				}
			}

			getLink('reload','top.opener.','페이스북과 연결되었습니다.','close');
		}
		else {
			echo '<iframe src="'.$FC->getLoginUrl().'" width="0" height="0" frameborder="0"></iframe>';
			exit;
		}
	}
}

if ($d['social']['use_m'])
{
	if ($type == 'm')
	{
		include_once $g['path_core'].'function/rss.func.php';
		$callback = getUrlData('http://me2day.net/api/get_auth_url.json?akey='.$d['social']['key_m'],10);
		header('Location:http://'.($g['mobile']&&$_SESSION['pcmode']!= 'Y'?'m.':'').'me2day.net/account/login?hide_login_option=true&redirect_url='.urlencode(str_replace('start_auth','auth',getJSONData($callback,'url'))));
		exit;
	}
	if (strstr($_SERVER['HTTP_REFERER'],'me2day'))
	{	
		if ($result == 'true')
		{
			$_SESSION['m_token'] = $token;
			$_SESSION['m_mbrid'] = $user_id;
			$_SESSION['m_mbrky'] = $user_key;
			if (!$_SESSION['m_mbrid'])
			{
				getLink('','','죄송합니다. 세션에 문제가 있습니다. 다시 시도해 주세요.','close');
			}
			$_rs1 = '';
			$_rs2 = 'on,http://me2day.net/'.$_SESSION['m_mbrid'].','.$_SESSION['m_token'].','.$_SESSION['m_mbrky'].','.$_SESSION['m_mbrid'].',';
			$_set = array('t','f','m','y','','');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==2?$_rs2:$g['mysns'][$i]).'|';
			}
			if ($my['uid'])
			{
				$_ISSNS = getDbData($table['s_mbrsns'],"sm='".$_SESSION['m_mbrid']."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['m_token'] = '';
					$_SESSION['m_mbrky'] = '';
					$_SESSION['m_mbrid'] = '';
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][2])
				{
					if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table['s_mbrsns'],"sm='".$_SESSION['m_mbrid']."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table['s_mbrsns'],'memberuid,sm',"'".$my['uid']."','".$_SESSION['m_mbrid']."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table['s_mbrsns'],"sm='".$_SESSION['m_mbrid']."'",'*');
				if($_ISSNS['memberuid'])
				{
					$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
					getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
					getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
					$_SESSION['mbr_uid'] = $M['uid'];
					$_SESSION['mbr_pw']  = $M['pw'];
				}
				else {
					include_once $g['path_core'].'function/rss.func.php';
					include_once $g['path_core'].'function/thumb.func.php';

					$MR = getUrlData("http://me2day.net/api/get_person/".$_SESSION['m_mbrid'].".xml?akey=".$d['social']['key_m'],10);
					$id = 'm'.sprintf('%-012s',str_replace('.','',$g['time_start']));
					getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
					$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');

					$picdata = getUrlData(getRssTagValue($MR,'face'),10);
					if ($picdata)
					{
						$pic = $g['path_var'].'simbol/'.$id.'.jpg';
						$fp = fopen($pic,'w');
						fwrite($fp,$picdata);
						fclose($fp);
						ResizeWidthHeight($pic,$pic,50,50);
						@chmod($pic);
						$photo = $id.'.jpg';
					}

					$_QKEY = "memberuid,site,auth,sosok,level,comp,admin,adm_view,";
					$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
					$_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield";
					$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
					$_QVAL.= "'','".getRssTagValue($MR,'nickname')."','".getRssTagValue($MR,'nickname')."','','$photo','".str_replace('http://','',getRssTagValue($MR,'homepage'))."','0','0','0','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid=1');
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid=1');
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table['s_mbrsns'],'memberuid,sm',"'".$memberuid."','".$_SESSION['m_mbrid']."'");

					$_SESSION['mbr_uid'] = $memberuid;
					$_SESSION['mbr_pw']  = '';
				}
			}
		}
		else {
			getLink('','','미투데이와 연결을 취소하셨습니다.','close');
		}

		getLink('reload','opener.','미투데이와 연결되었습니다.','close');
	}
}

if ($d['social']['use_y'])
{
	if ($type == 'y')
	{
		require_once($g['dir_module'].'oauth/twitteroauth/yozm.php');
		$YOZMCONN = new YozmOAuth($d['social']['key_y'],$d['social']['secret_y']);
		$RCVTOKEN = $YOZMCONN -> getRequestToken($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&yozm=Y');

		$_SESSION['y_token'] = $RCVTOKEN['oauth_token'];
		$_SESSION['y_sekey'] = $RCVTOKEN['oauth_token_secret'];

		switch ($YOZMCONN -> http_code)
		{
			case 200:
			$YOZMURL = $YOZMCONN -> getAuthorizeURL($RCVTOKEN['oauth_token']);
			break;
			default:
			getLink('','','죄송합니다. 요즘서비스 서버가 응답하지 않습니다.','close');
			break;
		}

		header('Location:'.$YOZMURL);
		exit;
	}
	if ($yozm == 'Y')
	{
		require_once($g['path_module'].'social/oauth/twitteroauth/yozm.php');
		$YOZMCONN = new YozmOAuth($d['social']['key_y'], $d['social']['secret_y'], $_SESSION['y_token'], $_SESSION['y_sekey']);
		$ACCESSYOZM = $YOZMCONN -> getAccessToken($_REQUEST['oauth_verifier']);
		$YR = $YOZMCONN -> get('user/show', array());

		$_SESSION['y_token'] = $ACCESSYOZM['oauth_token'];
		$_SESSION['y_sekey'] = $ACCESSYOZM['oauth_token_secret'];
		$_SESSION['y_mbrid'] = $YR->user->url_name;
		if (!$YR->user->url_name)
		{
			getLink('','','요즘 연결을 취소하셨습니다.','close');
		}
		if (!$_SESSION['y_mbrid'])
		{
			getLink('','','죄송합니다. 세션에 문제가 있습니다. 다시 시도해 주세요.','close');
		}
		$_rs1 = '';
		$_rs2 = 'on,http://yozm.daum.net/'.$_SESSION['y_mbrid'].','.$_SESSION['y_token'].','.$_SESSION['y_sekey'].','.$_SESSION['y_mbrid'].',';
		$_set = array('t','f','m','y','','');
		$_cnt = count($_set);
		for($i = 0; $i < $_cnt; $i++)
		{
			$_rs1 .= ($i==3?$_rs2:$g['mysns'][$i]).'|';
		}
		if ($my['uid'])
		{
			$_ISSNS = getDbData($table['s_mbrsns'],"sy='".$_SESSION['y_mbrid']."'",'*');
			if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
			{
				$_SESSION['y_token'] = '';
				$_SESSION['y_sekey'] = '';
				$_SESSION['y_mbrid'] = '';
				$_SESSION['plussns'] = $_ISSNS['memberuid'];
				getLink('','opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
			}
			getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
			if (!$g['mysns'][3])
			{
				if(getDbRows($table['s_mbrsns'],'memberuid='.$my['uid']))
				{
					getDbUpdate($table['s_mbrsns'],"sy='".$_SESSION['y_mbrid']."'",'memberuid='.$my['uid']);
				}
				else {
					getDbInsert($table['s_mbrsns'],'memberuid,sy',"'".$my['uid']."','".$_SESSION['y_mbrid']."'");
				}
			}
		}
		else {
			$_ISSNS = getDbData($table['s_mbrsns'],"sy='".$_SESSION['y_mbrid']."'",'*');
			if($_ISSNS['memberuid'])
			{
				$M	= getUidData($table['s_mbrid'],$_ISSNS['memberuid']);
				getDbUpdate($table['s_mbrdata'],"num_login=num_login+1,now_log=1,last_log='".$date['totime']."'",'memberuid='.$M['uid']);
				getDbUpdate($table['s_numinfo'],'login=login+1',"date='".$date['today']."' and site=".$s);
				$_SESSION['mbr_uid'] = $M['uid'];
				$_SESSION['mbr_pw']  = $M['pw'];
			}
			else {
				include_once $g['path_core'].'function/rss.func.php';
				include_once $g['path_core'].'function/thumb.func.php';

				$id = 'm'.sprintf('%-012s',str_replace('.','',$g['time_start']));
				getDbInsert($table['s_mbrid'],'site,id,pw',"'$s','$id',''");
				$memberuid  = getDbCnt($table['s_mbrid'],'max(uid)','');

				$picdata = getUrlData($YR->user->profile_img_url,10);
				if ($picdata)
				{
					$pic = $g['path_var'].'simbol/'.$id.'.jpg';
					$fp = fopen($pic,'w');
					fwrite($fp,$picdata);
					fclose($fp);
					ResizeWidthHeight($pic,$pic,50,50);
					@chmod($pic);
					$photo = $id.'.jpg';
				}
				$picdata = getUrlData($YR->user->profile_big_img_url,10);
				if ($picdata)
				{
					$pic = $g['path_var'].'simbol/180.'.$id.'.jpg';
					$fp = fopen($pic,'w');
					fwrite($fp,$picdata);
					fclose($fp);
					ResizeWidth($pic,$pic,180);
					@chmod($pic);
					$photo = $id.'.jpg';
				}
				$_QKEY = "memberuid,site,auth,sosok,level,comp,admin,adm_view,";
				$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
				$_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield";
				$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
				$_QVAL.= "'','".$YR->user->nickname."','".$YR->user->nickname."','','$photo','','".($YR->user->sex=='남'?1:2)."','".$YR->user->birthday_year."','".sprintf('%02d',$YR->user->birthday_month).sprintf('%02d',$YR->user->birthday_day)."','0','','','',";
				$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
				getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
				getDbUpdate($table['s_mbrlevel'],'num=num+1','uid=1');
				getDbUpdate($table['s_mbrgroup'],'num=num+1','uid=1');
				getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
				if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
				getDbInsert($table['s_mbrsns'],'memberuid,sy',"'".$memberuid."','".$_SESSION['y_mbrid']."'");

				$_SESSION['mbr_uid'] = $memberuid;
				$_SESSION['mbr_pw']  = '';
			}
		}

		getLink('reload','opener.','요즘과 연결되었습니다.','close');
	}
}
exit;
?>