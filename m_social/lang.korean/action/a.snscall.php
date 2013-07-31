<?php
if(!defined('__KIMS__')) exit;

include_once $g['dir_module'].'var/var.php';

if ($d['social']['use_t'])
{
	if ($type == 't')
	{
		if ($_SESSION['t_token'])
		{
			echo '<script type="text/javascript">';
			echo 'var f1 = opener.document.getElementById(\'snsImg_t\');';
			echo 'var f2 = opener.document.getElementById(\'snsInp_t\');';
			echo 'f1.style.filter = "alpha(opacity=100)";';
			echo 'f1.style.opacity = "1";';
			echo 'f2.checked=true;';
			echo '</script>';
			getLink('','','이미 연결되어 있습니다.','close');
		}

		require_once($g['dir_module'].'oauth/twitteroauth/twitteroauth.php');
		$TWITCONN = new TwitterOAuth($d['social']['key_t'],$d['social']['secret_t']);
		$RCVTOKEN = $TWITCONN -> getRequestToken($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall&twitter=Y');

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
		require_once($g['path_module'].'social/oauth/twitteroauth/twitteroauth.php');
		$TWITCONN = new TwitterOAuth($d['social']['key_t'], $d['social']['secret_t'], $_SESSION['t_token'], $_SESSION['t_sekey']);
		$ACCESSTWIT = $TWITCONN -> getAccessToken($_REQUEST['oauth_verifier']);
		$_SESSION['t_token'] = $ACCESSTWIT['oauth_token'];
		$_SESSION['t_sekey'] = $ACCESSTWIT['oauth_token_secret'];
		$_SESSION['t_mbrid'] = $ACCESSTWIT['screen_name'];

		if ($my['uid'])
		{
			$snsarr = explode('|',$my['sns']);
			if ($snsarr[10] != $ACCESSTWIT['oauth_token'])
			{
				$sns = $ACCESSTWIT['screen_name'].'|'.$snsarr[1].'|'.$snsarr[2].'|'.$snsarr[3].'|'.$snsarr[4].'|'.$snsarr[5].'|'.$snsarr[6].'|'.$snsarr[7].'|'.$snsarr[8].'|'.$snsarr[9].'|'.$ACCESSTWIT['oauth_token'].'|'.$ACCESSTWIT['oauth_token_secret'].'|'.$snsarr[12].'|'.$snsarr[13].'|'.$snsarr[14].'|'.$snsarr[15].'|'.$snsarr[16].'|'.$snsarr[17].'|'.$snsarr[18].'|'.$snsarr[19].'|'.$snsarr[20].'|'.$snsarr[21].'|'.$snsarr[22].'|'.$snsarr[23].'|'.$snsarr[24].'|'.$snsarr[25].'|'.$snsarr[26].'|'.$snsarr[27].'|'.$snsarr[28].'|'.$snsarr[29].'|'.$snsarr[30].'|'.$snsarr[31].'|'.$snsarr[32].'|'.$snsarr[33].'|'.$snsarr[34].'|'.$snsarr[35].'|'.$snsarr[36].'|'.$snsarr[37].'|'.$snsarr[38].'|'.$snsarr[39].'|'.$snsarr[40].'|';
				getDbUpdate($table['s_mbrdata'],"sns='".$sns."'",'memberuid='.$my['uid']);
			}

			$isSNSDATA = getDbData($table[$m.'user'],'memberuid='.$my['uid'],'*');
			if (!$isSNSDATA['memberuid'])
			{
				getDbInsert($table[$m.'user'],"memberuid,provider,id_t,id_f,id_m,id_y,extra_t,extra_f,extra_m,extra_y","'$my[uid]','','".$ACCESSTWIT['screen_name']."','','','','','','',''");
			} else {
				if (!$isSNSDATA['id_t']){
					getDbUpdate($table[$m.'user'],"id_t='".$ACCESSTWIT['screen_name']."'",'memberuid='.$my['uid']);
				}
			}
		}

		echo '<script type="text/javascript">';
		echo 'var f1 = opener.document.getElementById(\'snsImg_t\');';
		echo 'var f2 = opener.document.getElementById(\'snsInp_t\');';
		echo 'f1.style.filter = "alpha(opacity=100)";';
		echo 'f1.style.opacity = "1";';
		echo 'f2.checked=true;';
		echo '</script>';
		getLink('','','트위터와 연결되었습니다.','close');
	}
}

if ($d['social']['use_f'])
{
	if ($type == 'f')
	{
		if ($_SESSION['f_token'])
		{
			echo '<script type="text/javascript">';
			echo 'var f1 = opener.document.getElementById(\'snsImg_f\');';
			echo 'var f2 = opener.document.getElementById(\'snsInp_f\');';
			echo 'f1.style.filter = "alpha(opacity=100)";';
			echo 'f1.style.opacity = "1";';
			echo 'f2.checked=true;';
			echo '</script>';
			getLink('','','이미 연결되어 있습니다.','close');
		}

		$callbackurl = urlencode($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall&facebook=Y');
		header('Location:http://www.facebook.com/connect/uiserver.php?app_id='.$d['social']['key_f'].'&next='.$callbackurl.'&cancel_url='.$callbackurl.'&perms=publish_stream,offline_access&return_session=1&session_version=3&fbconnect=1&canvas=0&legacy_return=1&method=permissions.request&display='.($g['mobile']&&$_SESSION['pcmode']!= 'Y'?'touch':'page'));
	}

	if ($facebook == 'Y')
	{

		if (!$_REQUEST['session']) getLink('','','','close');

		include_once $g['path_core'].'function/rss.func.php';
		$_SESSION['f_token'] = getJSONData(stripslashes($_REQUEST['session']),'access_token');
		$_SESSION['f_sekey'] = getJSONData(stripslashes($_REQUEST['session']),'secret');
		$_SESSION['f_signl'] = getJSONData(stripslashes($_REQUEST['session']),'sig');
		$_SESSION['f_mbrid'] = $_REQUEST['selected_profiles'];
		
		if ($my['uid'])
		{
			$snsarr = explode('|',$my['sns']);
			if ($snsarr[12] != $_SESSION['f_token'])
			{
				$sns = $snsarr[0].'|'.$_REQUEST['selected_profiles'].'|'.$snsarr[2].'|'.$snsarr[3].'|'.$snsarr[4].'|'.$snsarr[5].'|'.$snsarr[6].'|'.$snsarr[7].'|'.$snsarr[8].'|'.$snsarr[9].'|'.$snsarr[10].'|'.$snsarr[11].'|'.$_SESSION['f_token'].'|'.$snsarr[15].'|'.$snsarr[16].'|'.$snsarr[17].'|'.$snsarr[18].'|'.$snsarr[19].'|'.$snsarr[20].'|'.$snsarr[21].'|'.$snsarr[22].'|'.$snsarr[23].'|'.$snsarr[24].'|'.$snsarr[25].'|'.$snsarr[26].'|'.$snsarr[27].'|'.$snsarr[28].'|'.$snsarr[29].'|'.$snsarr[30].'|'.$snsarr[31].'|'.$snsarr[32].'|'.$snsarr[33].'|'.$snsarr[34].'|'.$snsarr[35].'|'.$snsarr[36].'|'.$snsarr[37].'|'.$snsarr[38].'|'.$snsarr[39].'|'.$snsarr[40].'|';
				getDbUpdate($table['s_mbrdata'],"sns='".$sns."'",'memberuid='.$my['uid']);
			}

			$isSNSDATA = getDbData($table[$m.'user'],'memberuid='.$my['uid'],'*');
			if (!$isSNSDATA['memberuid'])
			{
				getDbInsert($table[$m.'user'],"memberuid,provider,id_t,id_f,id_m,id_y,extra_t,extra_f,extra_m,extra_y","'$my[uid]','','','".$_REQUEST['selected_profiles']."','','','','','',''");
			} else {
				if (!$isSNSDATA['id_f']){
					getDbUpdate($table[$m.'user'],"id_f='".$_REQUEST['selected_profiles']."'",'memberuid='.$my['uid']);
				}
			}
		}

		echo '<script type="text/javascript">';
		echo 'var f1 = opener.document.getElementById(\'snsImg_f\');';
		echo 'var f2 = opener.document.getElementById(\'snsInp_f\');';
		echo 'f1.style.filter = "alpha(opacity=100)";';
		echo 'f1.style.opacity = "1";';
		echo 'f2.checked=true;';
		echo '</script>';
		getLink('','','페이스북과 연결되었습니다.','close');
	}
}

if ($d['social']['use_m'])
{
	if ($type == 'm')
	{
		if ($_SESSION['m_token'])
		{
			echo '<script type="text/javascript">';
			echo 'var f1 = opener.document.getElementById(\'snsImg_m\');';
			echo 'var f2 = opener.document.getElementById(\'snsInp_m\');';
			echo 'f1.style.filter = "alpha(opacity=100)";';
			echo 'f1.style.opacity = "1";';
			echo 'f2.checked=true;';
			echo '</script>';
			getLink('','','이미 연결되어 있습니다.','close');
		}

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

			if ($my['uid'])
			{
				$snsarr = explode('|',$my['sns']);
				if ($snsarr[15] != $token)
				{
					$sns = $snsarr[0].'|'.$snsarr[1].'|'.$user_id.'|'.$snsarr[3].'|'.$snsarr[4].'|'.$snsarr[5].'|'.$snsarr[6].'|'.$snsarr[7].'|'.$snsarr[8].'|'.$snsarr[9].'|'.$snsarr[10].'|'.$snsarr[11].'|'.$snsarr[12].'|'.$snsarr[13].'|'.$snsarr[14].'|'.$token.'|'.$user_key.'|'.$snsarr[17].'|'.$snsarr[18].'|'.$snsarr[19].'|'.$snsarr[20].'|'.$snsarr[21].'|'.$snsarr[22].'|'.$snsarr[23].'|'.$snsarr[24].'|'.$snsarr[25].'|'.$snsarr[26].'|'.$snsarr[27].'|'.$snsarr[28].'|'.$snsarr[29].'|'.$snsarr[30].'|'.$snsarr[31].'|'.$snsarr[32].'|'.$snsarr[33].'|'.$snsarr[34].'|'.$snsarr[35].'|'.$snsarr[36].'|'.$snsarr[37].'|'.$snsarr[38].'|'.$snsarr[39].'|'.$snsarr[40].'|';
					getDbUpdate($table['s_mbrdata'],"sns='".$sns."'",'memberuid='.$my['uid']);
				}
				$isSNSDATA = getDbData($table[$m.'user'],'memberuid='.$my['uid'],'*');
				if (!$isSNSDATA['memberuid'])
				{
					getDbInsert($table[$m.'user'],"memberuid,provider,id_t,id_f,id_m,id_y,extra_t,extra_f,extra_m,extra_y","'$my[uid]','','','','$user_id','','','','',''");
				} else {
					if (!$isSNSDATA['id_m']){
						getDbUpdate($table[$m.'user'],"id_m='".$user_id."'",'memberuid='.$my['uid']);
					}
				}
			}
		}
		echo '<script type="text/javascript">';
		echo 'var f1 = opener.document.getElementById(\'snsImg_m\');';
		echo 'var f2 = opener.document.getElementById(\'snsInp_m\');';
		echo 'f1.style.filter = "alpha(opacity=100)";';
		echo 'f1.style.opacity = "1";';
		echo 'f2.checked=true;';
		echo '</script>';
		getLink('','','미투데이와 연결되었습니다.','close');
	}
}

if ($d['social']['use_y'])
{
	if ($type == 'y')
	{
		if ($_SESSION['y_token'])
		{
			echo '<script type="text/javascript">';
			echo 'var f1 = opener.document.getElementById(\'snsImg_y\');';
			echo 'var f2 = opener.document.getElementById(\'snsInp_y\');';
			echo 'f1.style.filter = "alpha(opacity=100)";';
			echo 'f1.style.opacity = "1";';
			echo 'f2.checked=true;';
			echo '</script>';
			getLink('','','이미 연결되어 있습니다.','close');
		}
		require_once($g['dir_module'].'oauth/twitteroauth/yozm.php');
		$YOZMCONN = new YozmOAuth($d['social']['key_y'],$d['social']['secret_y']);
		$RCVTOKEN = $YOZMCONN -> getRequestToken($g['url_root'].'/?r='.$r.'&m='.$m.'&a=snscall&yozm=Y');

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


		if ($my['uid'])
		{
			$snsarr = explode('|',$my['sns']);
			if ($snsarr[17] != $ACCESSYOZM['oauth_token'])   //$YR->user->url_name
			{
				$sns = $snsarr[0].'|'.$snsarr[1].'|'.$snsarr[2].'|'.$YR->user->url_name.'|'.$snsarr[4].'|'.$snsarr[5].'|'.$snsarr[6].'|'.$snsarr[7].'|'.$snsarr[8].'|'.$snsarr[9].'|'.$snsarr[10].'|'.$snsarr[11].'|'.$snsarr[12].'|'.$snsarr[13].'|'.$snsarr[14].'|'.$snsarr[15].'|'.$snsarr[16].'|'.$ACCESSYOZM['oauth_token'].'|'.$ACCESSYOZM['oauth_token_secret'].'|'.$snsarr[19].'|'.$snsarr[20].'|'.$snsarr[21].'|'.$snsarr[22].'|'.$snsarr[23].'|'.$snsarr[24].'|'.$snsarr[25].'|'.$snsarr[26].'|'.$snsarr[27].'|'.$snsarr[28].'|'.$snsarr[29].'|'.$snsarr[30].'|'.$snsarr[31].'|'.$snsarr[32].'|'.$snsarr[33].'|'.$snsarr[34].'|'.$snsarr[35].'|'.$snsarr[36].'|'.$snsarr[37].'|'.$snsarr[38].'|'.$snsarr[39].'|'.$snsarr[40].'|';
				getDbUpdate($table['s_mbrdata'],"sns='".$sns."'",'memberuid='.$my['uid']);
			}
			$isSNSDATA = getDbData($table[$m.'user'],'memberuid='.$my['uid'],'*');			
			if (!$isSNSDATA['memberuid']) 
			{
				getDbInsert($table[$m.'user'],"memberuid,provider,id_t,id_f,id_m,id_y,extra_t,extra_f,extra_m,extra_y","'$my[uid]','','','','','".$_SESSION['y_mbrid']."','','','',''");
			} else {
				if (!$isSNSDATA['id_y']){
					getDbUpdate($table[$m.'user'],"id_y='".$_SESSION['y_mbrid']."'",'memberuid='.$my['uid']);
				}
			}
		}

		echo '<script type="text/javascript">';
		echo 'var f1 = opener.document.getElementById(\'snsImg_y\');';
		echo 'var f2 = opener.document.getElementById(\'snsInp_y\');';
		echo 'f1.style.filter = "alpha(opacity=100)";';
		echo 'f1.style.opacity = "1";';
		echo 'f2.checked=true;';
		echo '</script>';
		getLink('','','요즘과 연결되었습니다.','close');
	}
}
exit;
?>