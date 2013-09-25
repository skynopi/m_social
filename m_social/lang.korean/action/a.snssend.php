<?php
if(!defined('__KIMS__')) exit;

include_once $g['path_module'].'m_social/var/'.$s.'.var.php';
include_once $g['path_core'].'function/rss.func.php';

$snsSendResult = '';

if($upload){	
	$upArray = getArrayString($upload);
	foreach($upArray['data'] as $_pval)
	{
		$U = getUidData($table['s_upload'],$_pval);
		if (!$U['uid']) continue;
		if (strpos('_jpg,png',$U['ext']))
		{
			$snsPhoto = $g['path_file'].$U['folder'].'/'.$U['tmpname'];
			$snsPhoto2 = 'http'.($_SERVER['HTTPS']=='on'?'s':'').'://'.$_SERVER['HTTP_HOST'].'/files/'.$U['folder'].'/'.$U['tmpname'];
		}
	}
}


if($d['social']['use_b'])
{
	$orignUrl_se = $orignUrl;
	$ourldata = getUrlData("http://api.bit.ly/v3/shorten?login=".$d['social']['key_b']."&apikey=".$d['social']['secret_b']."&longUrl=".urlencode($orignUrl),10);
	$orignUrl = getJSONData(stripslashes($ourldata),'url');

	if(!$orignUrl){
		$ourldata = "http://to.ly/api.php?longurl=".urlencode($orignUrl_se);
		$orignUrl = get_shortURL($ourldata);
	}
	if(!$orignUrl) {
		$ourldata = "http://is.gd/api.php?longurl=".urlencode($orignUrl_se);
		$orignUrl = get_shortURL($ourldata);
	}
}

$mingid = getDbCnt($table['m_socialdata'],'min(gid)','');
$snsgid = $mingid ? $mingid-1 : 1000000000;
$QKEY = 'gid,provider,snsid,subject,name,nic,mbruid,id,targeturl,cync,d_regis';

if ($sns_t)
{
	$_mysnsdat=explode(',',$g['mysns'][0]);
	$twitter_content = $utubedata ? $twitter_content.' youtu.be/'.$utubedata : $twitter_content;

	require_once($g['path_module'].'m_social/oauth/http.php');
	require_once($g['path_module'].'m_social/oauth/oauth_client.php');
		
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
		if($upload){			
			$success_t = $client_t->CallAPI(
				"https://api.twitter.com/1.1/statuses/update_with_media.json",
				'POST', array(
					'status'=>$twitter_content.' [원문:'.$orignUrl.']',
					'media[]'=>$snsPhoto
				),array(
					'FailOnAccessError'=>true,
					'Files'=>array(
						'media[]'=>array(
						)
					)
				), $update_t);		
		}else{
			$success_t = $client_t->CallAPI(
				'https://api.twitter.com/1.1/statuses/update.json', 
				'POST', array('status'=>$twitter_content.' [원문:'.$orignUrl.']'), array('FailOnAccessError'=>true), $update_t);
		}

		if(!$success_t)	error_log(print_r($update_t->errors[0]->code, 1));

		$success_t = $client_t->Finalize($success_t);

		if($client_t->exit) exit;
	}

	
	if($success_t)
	{
		$QVAL = "'$snsgid','t','".$update_t->user->screen_name."','$subject','$name','$nic','$my[uid]','$my[id]','http://twitter.com/".$update_t->user->screen_name."/status/".$update_t->id."','$xcync','$date[totime]'";
		getDbInsert($table['m_socialdata'],$QKEY,$QVAL);
		$snsSendResult .= getDbCnt($table['m_socialdata'],'max(uid)','').',';
		$snsgid--;
	}	
}

if ($sns_f)
{
	$_mysnsdat=explode(',',$g['mysns'][1]);
	$f_youtube = $utubedata ? 'http://youtu.be/'.$utubedata : $orignUrl;

	require_once($g['path_module'].'m_social/oauth/http.php');
	require_once($g['path_module'].'m_social/oauth/oauth_client.php');
	
	$client_f = new oauth_client_class;
	$client_f->offline = true;
	$client_f->server = 'Facebook';

	$client_f->client_id = $d['social']['key_f'];
	$client_f->client_secret = $d['social']['secret_f'];

	$client_f->access_token = $_mysnsdat[2];

	if(($success_f = $client_f->Initialize()))
	{					
		if($upload){			
			$success_f = $client_f->CallAPI(
				'https://graph.facebook.com/me/photos', 
				'POST', array(
					'message' => $facebook_content.' [원문: '.$orignUrl.']',
					'url'=>$snsPhoto2
				),array('FailOnAccessError'=>true), $update_f);
			//$success_f2 = $client_f->Finalize($success_f2);
		}else{		
			$success_f = $client_f->CallAPI(
				'https://graph.facebook.com/me/feed', 
				'POST', array(
					'message' => $facebook_content.' [원문: '.$orignUrl.']', 'link' =>$f_youtube, 'name' => $subject, 'picture' => $sns_img[0], 'description' => $subject.' ...by '.$nic
				),array('FailOnAccessError'=>true), $update_f);
		}

		
		if(!$success_f)	error_log(print_r($update_f->errors[0]->code, 1));

		$success_f = $client_f->Finalize($success_f);

		if($client_f->exit) exit;
	}
	
	if($success_f)
	{
		$FBPARAM = explode('_',$update_f->id);
		$FBPAURL = 'http://facebook.com/permalink.php?story_fbid='.$FBPARAM[1].'&id='.$_mysnsdat[4];
		$QVAL = "'$snsgid','f','".$_mysnsdat[4]."','$subject','$name','$nic','$my[uid]','$my[id]','$FBPAURL','$xcync','$date[totime]'";
		getDbInsert($table['m_socialdata'],$QKEY,$QVAL);
		$snsSendResult .= getDbCnt($table['m_socialdata'],'max(uid)','').',';
		$snsgid--;
	}	
}

if ($sns_m)
{
	$_mysnsdat=explode(',',$g['mysns'][2]);
	$sendContent = urlencode($me2day_content).'+++["'.urlencode($_HS['title']).'":'.urlencode($orignUrl).'+]';
	$m_tag = '';
	if($tag) $m_tag = '&post[tags]='.urlencode(str_replace(","," ",$tag));
	$snsPhoto2 = urlencode($snsPhoto2);
	$meimg = '';
	if($upload) $meimg = '&icon_url='.$snsPhoto2.'&callback_url='.$snsPhoto2.'&content_type=photo';
	//if($upload) $meimg = '&attachment=@'.$snsPhoto2;
	
	$ME2RESULT = getUrlData("http://me2day.net/api/create_post/".$_mysnsdat[4].".json?uid=".$_mysnsdat[4]."&ukey=12345678".md5('12345678'.$_mysnsdat[3])."&akey=".$d['social']['key_m']."&post[body]=".$sendContent.$m_tag.$meimg,10);

	if($ME2RESULT)
	{
		$QVAL = "'$snsgid','m','".getJSONData($ME2RESULT,'id')."','$subject','$name','$nic','$my[uid]','$my[id]','".getJSONData($ME2RESULT,'permalink')."','$xcync','$date[totime]'";
		getDbInsert($table['m_socialdata'],$QKEY,$QVAL);
		$snsSendResult .= getDbCnt($table['m_socialdata'],'max(uid)','').',';
		$snsgid--;
	}
}

if ($sns_r)
{
	if($upload){	
		$_mysnsdat=explode(',',$g['mysns'][4]);

		require_once($g['path_module'].'m_social/oauth/phpFlickr.php');

		$f = new phpFlickr($d['social']['key_r'], $d['social']['secret_r']);

		$f->setToken($_mysnsdat[2]);
		//$f->people_getInfo($_mysnsdat[3]);

		$success_flickr = $f->sync_upload($snsPhoto, $title = $subject, $description = $twitter_content." 원문:".$orignUrl, $tags = $tag);
		

		if($success_flickr)
		{
			$success_link = 'http://www.flickr.com/photos/'.$_mysnsdat[3].'/'.$success_flickr;
			$QVAL = "'$snsgid','r','".$_mysnsdat[3]."','$subject','$name','$nic','$my[uid]','$my[id]','".$success_link."','$xcync','$date[totime]'";
			getDbInsert($table['m_socialdata'],$QKEY,$QVAL);
			$snsSendResult .= getDbCnt($table['m_socialdata'],'max(uid)','').',';
			$snsgid--;
		}
	}
}
?>