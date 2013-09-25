<?php
if(!defined('__KIMS__')) exit;

include_once $g['dir_module'].'var/'.$s.'.var.php';
include_once $g['path_module'].'member/var/var.join.php';

if ($d['social']['use_t'])
{   
	if ($type == 't')
	{
		require_once($g['dir_module'].'oauth/http.php');
		require_once($g['dir_module'].'oauth/oauth_client.php');
		
		$client = new oauth_client_class;
		$client->debug = 1;
		$client->debug_http = 1;
		$client->server = 'Twitter';
		$client->redirect_uri = $g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&type=t';

		$client->client_id = $d['social']['key_t'];
		$client->client_secret = $d['social']['secret_t'];

		if(strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
			getLink('','',"m_social모듈 환경설정에서 Consumer Key, Consumer Secret키 등록 해 주십시오.",'close');
		
		if(($success = $client->Initialize()))
		{
			if(($success = $client->Process()))
			{
				if(strlen($client->access_token))
				{
					$success = $client->CallAPI(
						'https://api.twitter.com/1.1/account/verify_credentials.json', 
						'GET', array(), array('FailOnAccessError'=>true), $user);
				}
			}
			$success = $client->Finalize($success);
		}
		if($client->exit)	exit;
		
		if($success)
		{
			$client->ResetAccessToken();			
			$_rs2 = 'on,http://twitter.com/'.$user->screen_name.','.$client->access_token.','.$client->access_token_secret.','.$user->screen_name.',,';
			$_rs1 = '';
			$_set = array('t','f','m','y','r','g','instagram','tumblr','linkedin','ms','yahoo','xing','surveymonkey','stocktwits','rightsignature','fitbit','eventful','dropbox','disqus','box','bitbucket','github');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==0?$_rs2:$g['mysns'][$i]).'|';
			}

			if ($my['uid'])
			{
				$_ISSNS = getDbData($table[$m.'mbrsns'],"st='".$user->screen_name."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][0])
				{
					if(getDbRows($table[$m.'mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table[$m.'mbrsns'],"st='".$user->screen_name."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table[$m.'mbrsns'],'memberuid,st',"'".$my['uid']."','".$user->screen_name."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table[$m.'mbrsns'],"st='".$user->screen_name."'",'*');
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

					$picdata = getUrlData($user->profile_image_url,10);
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
					$picdata = getUrlData(str_replace('_normal','_reasonably_small',$user->profile_image_url),10);
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
					$_QVAL = "'$memberuid','$s','1','".$d['member']['join_group']."','".$d['member']['join_level']."','0','0','',";
					$_QVAL.= "'','".$user->name."','".$user->name."','','$photo','".'twitter.com/'.$user->screen_name."','0','0','0','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$d['member']['join_level']);
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$d['member']['join_group']);
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table[$m.'mbrsns'],'memberuid,st',"'".$memberuid."','".$user->screen_name."'");

					$_SESSION['mbr_uid'] = $memberuid;
					$_SESSION['mbr_pw']  = '';
				}
			}
			getLink('reload','opener.','트위터와 연결되었습니다.','close');

		}
		else
		{
			$client->ResetAccessToken();
			 getLink('','',HtmlSpecialChars($client->error),'close');
		}

	}
}

if ($d['social']['use_f'])
{
	if ($type == 'f')
	{ 
		require_once($g['dir_module'].'oauth/http.php');
		require_once($g['dir_module'].'oauth/oauth_client.php');

		$client = new oauth_client_class;
		$client->server = 'Facebook';
		$client->redirect_uri = $g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&type=f';

		$client->client_id = $d['social']['key_f'];
		$client->client_secret = $d['social']['secret_f'];

		if(strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
			getLink('','',"m_social모듈 환경설정에서 Consumer Key, Consumer Secret키 등록 해 주십시오.",'close');

		$client->scope = 'publish_stream,offline_access,user_about_me,email,photo_upload,user_birthday,user_location,user_work_history,user_hometown,user_groups,user_subscriptions,manage_pages,read_friendlists';
		if(($success = $client->Initialize()))
		{
			if(($success = $client->Process()))
			{
				if(strlen($client->access_token))
				{
					$success = $client->CallAPI(
						'https://graph.facebook.com/me', 
						'GET', array(), array('FailOnAccessError'=>true), $user);
				}
			}
			$success = $client->Finalize($success);
		}
		if($client->exit)
			exit;
		if($success)
		{
			$success2 = $client->CallAPI('https://api.facebook.com/method/', 'POST', array('method'=>'fql.query','query'=>'SELECT pic_square,pic_big from profile where id='.$user->id,'format'=>'json'), array('FailOnAccessError'=>true,'application/json'), $user2);

			$_rs1 = '';
			$_rs2 = 'on,'.$user2[0]->url.','.$client->access_token.',,'.$user->id.',,';
			$_set = array('t','f','m','y','r','g','instagram','tumblr','linkedin','ms','yahoo','xing','surveymonkey','stocktwits','rightsignature','fitbit','eventful','dropbox','disqus','box','bitbucket','github');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==1?$_rs2:$g['mysns'][$i]).'|';
			}	
			
			if ($my['uid'])
			{
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sf='".$user->id."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','top.opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][1])
				{
					if(getDbRows($table[$m.'mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table[$m.'mbrsns'],"sf='".$user->id."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table[$m.'mbrsns'],'memberuid,sf',"'".$my['uid']."','".$user->id."'");
					}
				}
			}
			else {	
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sf='".$user->id."'",'*');
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

					$picdata = getUrlData($user2[0]->pic_square,10);
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
					$picdata = getUrlData($user2[0]->pic_big,10);
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

					$birth = explode('/',$user->birthday);

					$_QKEY = "memberuid,site,auth,sosok,level,comp,admin,adm_view,";
					$_QKEY.= "email,name,nic,grade,photo,home,sex,birth1,birth2,birthtype,tel1,tel2,zip,";
					$_QKEY.= "addr0,addr1,addr2,job,marr1,marr2,sms,mailing,smail,point,usepoint,money,cash,num_login,pw_q,pw_a,now_log,last_log,last_pw,is_paper,d_regis,tmpcode,sns,addfield";
					$_QVAL = "'$memberuid','$s','1','1','1','0','0','',";
					$_QVAL.= "'".$user->email."','".$user->name."','".$user->name."','','$photo','".$user->link."','".($user->gender=='male'?1:2)."','".$birth[2]."','".$birth[0].$birth[1]."','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$d['member']['join_level']);
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$d['member']['join_group']);
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table[$m.'mbrsns'],'memberuid,sf',"'".$memberuid."','".$user->id."'");
					
					$_SESSION['mbr_uid'] = $memberuid;
					$_SESSION['mbr_pw']  = '';
				}
			}

			getLink('reload','top.opener.','페이스북과 연결되었습니다.','close');
		}
		else {
			$client->ResetAccessToken();
			 getLink('','',HtmlSpecialChars($client->error),'close');   //exit;
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
			$_set = array('t','f','m','y','r','g','instagram','tumblr','linkedin','ms','yahoo','xing','surveymonkey','stocktwits','rightsignature','fitbit','eventful','dropbox','disqus','box','bitbucket','github');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==2?$_rs2:$g['mysns'][$i]).'|';
			}
			if ($my['uid'])
			{
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sm='".$_SESSION['m_mbrid']."'",'*');
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
					if(getDbRows($table[$m.'mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table[$m.'mbrsns'],"sm='".$_SESSION['m_mbrid']."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table[$m.'mbrsns'],'memberuid,sm',"'".$my['uid']."','".$_SESSION['m_mbrid']."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sm='".$_SESSION['m_mbrid']."'",'*');
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
					$_QVAL = "'$memberuid','$s','1','".$d['member']['join_group']."','".$d['member']['join_level']."','0','0','',";
					$_QVAL.= "'','".getRssTagValue($MR,'nickname')."','".getRssTagValue($MR,'nickname')."','','$photo','".str_replace('http://','',getRssTagValue($MR,'homepage'))."','0','0','0','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$d['member']['join_level']);
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$d['member']['join_group']);
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table[$m.'mbrsns'],'memberuid,sm',"'".$memberuid."','".$_SESSION['m_mbrid']."'");

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
		require_once($g['dir_module'].'oauth/http.php');
		require_once($g['dir_module'].'oauth/oauth_client.php');

		$client = new oauth_client_class;
		$client->debug = true;
		$client->debug_http = false;
		$client->server = 'yozm';
		$client->redirect_uri = $g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&type=y';

		$client->client_id = $d['social']['key_y'];
		$client->client_secret = $d['social']['secret_y'];

		if(strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
			getLink('','',"m_social모듈 환경설정에서 Consumer Key, Consumer Secret키 등록 해 주십시오.",'close');

		if(($success = $client->Initialize()))
		{
			if(($success = $client->Process()))
			{
				if(strlen($client->access_token))
				{   
					$success = $client->CallAPI(
						'https://apis.daum.net/profile/show.json', 
						'GET', array('format'=>'json'), array('FailOnAccessError'=>true), $user);
				}
			}
			$success = $client->Finalize($success);
		}
		if($client->exit)
			exit;
		if($success)
		{
			$user2 = json_decode($user);
			$_rs1 = '';
			$_rs2 = 'on,http://www.daum.net/,'.$client->access_token.','.$client->access_token_secret.','.$user2->user->id.',';
			$_set = array('t','f','m','y','r','g','instagram','tumblr','linkedin','ms','yahoo','xing','surveymonkey','stocktwits','rightsignature','fitbit','eventful','dropbox','disqus','box','bitbucket','github');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==3?$_rs2:$g['mysns'][$i]).'|';
			}
			if ($my['uid'])
			{
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sy='".$user2->user->id."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','opener.',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][3])
				{
					if(getDbRows($table[$m.'mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table[$m.'mbrsns'],"sy='".$user2->user->id."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table[$m.'mbrsns'],'memberuid,sy',"'".$my['uid']."','".$user2->user->id."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sy='".$user2->user->id."'",'*');
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

					$picdata = getUrlData($user2->user->profile_image_url,10);
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
					$picdata = getUrlData($user2->user->profile_big_image_url,10);
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
					$_QVAL = "'$memberuid','$s','1','".$d['member']['join_group']."','".$d['member']['join_level']."','0','0','',";
					$_QVAL.= "'','".$user2->user->nickname."','".$user2->user->nickname."','','$photo','','1','','','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$d['member']['join_level']);
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$d['member']['join_group']);
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table[$m.'mbrsns'],'memberuid,sy',"'".$memberuid."','".$user2->user->id."'");

					$_SESSION['mbr_uid'] = $memberuid;
					$_SESSION['mbr_pw']  = '';
				}
			}

			getLink('reload','opener.','요즘과 연결되었습니다.','close');
		}
		else {
			$client->ResetAccessToken();
			 // echo HtmlSpecialChars($client->error); 
			 getLink('','',HtmlSpecialChars($client->error),'close');   //exit;
		}
	}
}

if ($d['social']['use_r'])
{
	if ($type == 'r')
	{
		require_once($g['dir_module'].'oauth/phpFlickr.php');
		$permissions             = "delete"; // 'read', 'write' or 'delete'
		
		if(strlen($d['social']['key_r']) == 0 || strlen($d['social']['secret_r']) == 0)
			getLink('','',"m_social모듈 환경설정에서 Consumer Key, Consumer Secret키 등록 해 주십시오.",'close');

		$f = new phpFlickr($d['social']['key_r'], $d['social']['secret_r']);

		if (empty($_GET['frob'])) {
			$f->auth($permissions, false);
			$a1 = $f->test_login();
			$arr_user = $f->people_getInfo($a1['id']);
		} else {
			$a2 = $f->auth_getToken($_GET['frob']);
			$arr_user = $f->people_getInfo($a2['user']['nsid']);
		}
			
						
		if($arr_user['id'])
		{
			$_rs2 = 'on,http://www.flickr.com/'.$arr_user['id'].','.$_SESSION['phpFlickr_auth_token'].','.$arr_user['nsid'].',';
			$_rs1 = '';
			$_set = array('t','f','m','y','r','g','instagram','tumblr','linkedin','ms','yahoo','xing','surveymonkey','stocktwits','rightsignature','fitbit','eventful','dropbox','disqus','box','bitbucket','github');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==4?$_rs2:$g['mysns'][$i]).'|';
			}

			if ($my['uid'])
			{
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sr='".$arr_user['nsid']."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][4])
				{
					if(getDbRows($table[$m.'mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table[$m.'mbrsns'],"sr='".$arr_user['nsid']."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table[$m.'mbrsns'],'memberuid,sr',"'".$my['uid']."','".$arr_user['nsid']."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sr='".$arr_user['nsid']."'",'*');
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

					$picdata = getUrlData('http://static.flickr.com/'.$arr_user['iconserver'].'/buddyicons/'.$arr_user['nsid'].'.jpg',10);
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
					$picdata = getUrlData(str_replace('_normal','_reasonably_small','http://static.flickr.com/'.$arr_user['iconserver'].'/buddyicons/'.$arr_user['nsid'].'.jpg'),10);
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
					$_QVAL = "'$memberuid','$s','1','".$d['member']['join_group']."','".$d['member']['join_level']."','0','0','',";
					$_QVAL.= "'','".$arr_user['username']."','".$arr_user['username']."','','$photo','".'flickr.com/'.$arr_user['id']."','0','0','0','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$d['member']['join_level']);
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$d['member']['join_group']);
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table[$m.'mbrsns'],'memberuid,sr',"'".$memberuid."','".$arr_user['nsid']."'");

					$_SESSION['mbr_uid'] = $memberuid;
					$_SESSION['mbr_pw']  = '';
				}
			}
			getLink('reload','opener.','플리커와 연결되었습니다.','close');

		}
		else
		{
			 getLink('','',HtmlSpecialChars($client->error),'close');
		}

	}
}

if ($d['social']['use_g'])
{   
	if ($type == 'g')
	{
		require_once($g['dir_module'].'oauth/http.php');
		require_once($g['dir_module'].'oauth/oauth_client.php');
		
		$client = new oauth_client_class;
		$client->offline = true;
		$client->debug = false;
		$client->debug_http = true;
		$client->server = 'Google';
		$client->redirect_uri = $g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall_direct&type=g';

		$client->client_id = $d['social']['key_g'];
		$client->client_secret = $d['social']['secret_g'];

		if(strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
			getLink('','',"m_social모듈 환경설정에서 Consumer Key, Consumer Secret키 등록 해 주십시오.",'close');
		
		$client->scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/plus.me';

		if(($success = $client->Initialize()))
		{
			if(($success = $client->Process()))
			{
				if(strlen($client->authorization_error))
				{
					$client->error = $client->authorization_error;
					$success = false;
				}
				else if(strlen($client->access_token))
				{
					$success = $client->CallAPI(
						'https://www.googleapis.com/oauth2/v1/userinfo',
						'GET', array(), array('FailOnAccessError'=>true), $user);
				}
			}
			$success = $client->Finalize($success);
		}
		if($client->exit)	exit;
		
		if($success)
		{	
			$_rs2 = 'on,https://plus.google.com/'.$user->id.','.$client->access_token.','.$client->access_token_secret.','.$user->name.','.$client->refresh_token.','.$client->access_token_expiry;
			$_rs1 = '';
			$_set = array('t','f','m','y','r','g','instagram','tumblr','linkedin','ms','yahoo','xing','surveymonkey','stocktwits','rightsignature','fitbit','eventful','dropbox','disqus','box','bitbucket','github');
			$_cnt = count($_set);
			for($i = 0; $i < $_cnt; $i++)
			{
				$_rs1 .= ($i==5?$_rs2:$g['mysns'][$i]).'|';
			}

			if ($my['uid'])
			{
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sg='".$user->id."'",'*');
				if ($_ISSNS['memberuid']&&$_ISSNS['memberuid']!=$my['uid'])
				{
					$_SESSION['plussns'] = $_ISSNS['memberuid'];
					getLink('','',"이미 다른계정에 연결되어 있습니다. \\n회원님의 계정이라면 통합해 주세요.",'close');
				}
				getDbUpdate($table['s_mbrdata'],"sns='".$_rs1."'",'memberuid='.$my['uid']);
				if (!$g['mysns'][0])
				{
					if(getDbRows($table[$m.'mbrsns'],'memberuid='.$my['uid']))
					{
						getDbUpdate($table[$m.'mbrsns'],"sg='".$user->id."'",'memberuid='.$my['uid']);
					}
					else {
						getDbInsert($table[$m.'mbrsns'],'memberuid,sg',"'".$my['uid']."','".$user->id."'");
					}
				}
			}
			else {
				$_ISSNS = getDbData($table[$m.'mbrsns'],"sg='".$user->id."'",'*');
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
										
					$picdata = getUrlData($user->picture,10);
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
					$picdata = getUrlData(str_replace('_normal','_reasonably_small',$user->picture),10);
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
					$_QVAL = "'$memberuid','$s','1','".$d['member']['join_group']."','".$d['member']['join_level']."','0','0','',";
					$_QVAL.= "'".$user->email."','".$user->name."','".$user->name."','','$photo','".'plus.google.com/'.$user->id."','0','0','0','0','','','',";
					$_QVAL.= "'','','','','0','0','0','1','0','".$d['member']['join_point']."','0','0','0','1','','','1','".$date['totime']."','".$date['totime']."','0','".$date['totime']."','','$_rs1',''";
					getDbInsert($table['s_mbrdata'],$_QKEY,$_QVAL);
					getDbUpdate($table['s_mbrlevel'],'num=num+1','uid='.$d['member']['join_level']);
					getDbUpdate($table['s_mbrgroup'],'num=num+1','uid='.$d['member']['join_group']);
					getDbUpdate($table['s_numinfo'],'login=login+1,mbrjoin=mbrjoin+1',"date='".$date['today']."' and site=".$s);
					if($d['member']['join_point']) getDbInsert($table['s_point'],'my_mbruid,by_mbruid,price,content,d_regis',"'$memberuid','0','".$d['member']['join_point']."','".$d['member']['join_pointmsg']."','".$date['totime']."'");
					getDbInsert($table[$m.'mbrsns'],'memberuid,sg',"'".$memberuid."','".$user->id."'");

					$_SESSION['mbr_uid'] = $memberuid;
					$_SESSION['mbr_pw']  = '';
				}
			}
			getLink('reload','opener.','구글플러스와 연결되었습니다.','close');

		}
		else
		{
			 getLink('','',HtmlSpecialChars($client->error),'close');
		}

	}
}

exit;
?>