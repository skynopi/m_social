<?php
if(!defined('__KIMS__')) exit;

if (!$my['uid'])
{
	getLink('','','정상적인 접근이 아닙니다.','');
}

include_once $g['path_module'].'member/var/var.join.php';

//소셜계정 - ON/OFF
if ($act == 'sns')
{
	include_once $g['path_core'].'function/thumb.func.php';

	if ($stype == 't')
	{
		if ($cync == 1)
		{
			$_mysnsdat=explode(',',$g['mysns'][0]);
			include_once $g['path_module'].'social/var/var.php';
			require_once $g['path_module'].'social/oauth/twitteroauth/twitteroauth.php';
			$TC = new TwitterOAuth($d['social']['key_t'], $d['social']['secret_t'],$_mysnsdat[2],$_mysnsdat[3]);
			$TR = $TC->post('account/update_profile',array('name'=>$name,'description'=>$description));
		}
		else {
			include_once $g['path_core'].'function/rss.func.php';
			$photost = '';
			$picdata = getUrlData($photo,10);
			if ($picdata)
			{
				$fp = fopen($g['path_var'].'simbol/'.$my['id'].'.jpg','w');
				fwrite($fp,$picdata);
				fclose($fp);
				ResizeWidthHeight($g['path_var'].'simbol/'.$my['id'].'.jpg',$g['path_var'].'simbol/'.$my['id'].'.jpg',50,50);
				@chmod($g['path_var'].'simbol/'.$my['id'].'.jpg');
				$photost = ",photo='".$my['id'].".jpg'";
			}
			$picdata = getUrlData($photo_big,10);
			if ($picdata)
			{
				$fp = fopen($g['path_var'].'simbol/180.'.$my['id'].'.jpg','w');
				fwrite($fp,$picdata);
				fclose($fp);
				ResizeWidth($g['path_var'].'simbol/180.'.$my['id'].'.jpg',$g['path_var'].'simbol/180.'.$my['id'].'.jpg',180);
				@chmod($g['path_var'].'simbol/180.'.$my['id'].'.jpg');
				$photost = ",photo='".$my['id'].".jpg'";
			}

			$_QVAL = "name='$name'".$photost;
			getDbUpdate($table['s_mbrdata'],$_QVAL,'memberuid='.$my['uid']);

		}
	}

	if ($stype == 'f')
	{
		if ($cync == 1)
		{
			/*
			include_once $g['path_module'].'social/var/var.php';
			require_once $g['path_module'].'social/oauth/facebook/src/facebook.php';
			$FC = new Facebook(array('appId'=>$d['social']['key_f'],'secret'=>$d['social']['secret_f'],'cookie'=>true));
			$FUID = $FC->getUser();
			
			$FR1 = $FC->api(array('method'=>'fql.query','query'=>"update profile set name='".$name."' where id=".$FUID));
			$FC->api('/me/feed','POST',array('name'=>$name));
			*/
		}
		else {
			include_once $g['path_core'].'function/rss.func.php';
			$photost = '';
			$picdata = getUrlData($photo,10);
			if ($picdata)
			{
				$fp = fopen($g['path_var'].'simbol/'.$my['id'].'.jpg','w');
				fwrite($fp,$picdata);
				fclose($fp);
				ResizeWidthHeight($g['path_var'].'simbol/'.$my['id'].'.jpg',$g['path_var'].'simbol/'.$my['id'].'.jpg',50,50);
				@chmod($g['path_var'].'simbol/'.$my['id'].'.jpg');
				$photost = ",photo='".$my['id'].".jpg'";
			}
			$picdata = getUrlData($photo_big,10);
			if ($picdata)
			{
				$fp = fopen($g['path_var'].'simbol/180.'.$my['id'].'.jpg','w');
				fwrite($fp,$picdata);
				fclose($fp);
				ResizeWidth($g['path_var'].'simbol/180.'.$my['id'].'.jpg',$g['path_var'].'simbol/180.'.$my['id'].'.jpg',180);
				@chmod($g['path_var'].'simbol/180.'.$my['id'].'.jpg');
				$photost = ",photo='".$my['id'].".jpg'";
			}

			$_QVAL = "name='$name'".$photost;
			getDbUpdate($table['s_mbrdata'],$_QVAL,'memberuid='.$my['uid']);

		}
	}
	if ($stype == 'm')
	{
		if ($cync == 1)
		{

		}
		else {
			include_once $g['path_core'].'function/rss.func.php';
			$photost = '';
			$picdata = getUrlData($photo,10);
			if ($picdata)
			{
				$fp = fopen($g['path_var'].'simbol/'.$my['id'].'.jpg','w');
				fwrite($fp,$picdata);
				fclose($fp);
				ResizeWidthHeight($g['path_var'].'simbol/'.$my['id'].'.jpg',$g['path_var'].'simbol/'.$my['id'].'.jpg',50,50);
				@chmod($g['path_var'].'simbol/'.$my['id'].'.jpg');
				$photost = ",photo='".$my['id'].".jpg'";
			}
			$_QVAL = "name='$name'".$photost;
			getDbUpdate($table['s_mbrdata'],$_QVAL,'memberuid='.$my['uid']);
		}
	}

	if ($stype == 'y')
	{
		if ($cync == 1)
		{
			/*
			$_mysnsdat=explode(',',$g['mysns'][3]);
			include_once $g['path_module'].'social/var/var.php';
			require_once $g['path_module'].'social/oauth/twitteroauth/yozm.php';
			$YC = new YozmOAuth($d['social']['key_y'], $d['social']['secret_y'],$_mysnsdat[2],$_mysnsdat[3]);
			$YR = $YC->post('account/update_profile',array('name'=>$name,'description'=>$description));
			*/
		}
		else {
			include_once $g['path_core'].'function/rss.func.php';
			$photost = '';
			$picdata = getUrlData($photo,10);
			if ($picdata)
			{
				$fp = fopen($g['path_var'].'simbol/'.$my['id'].'.jpg','w');
				fwrite($fp,$picdata);
				fclose($fp);
				ResizeWidthHeight($g['path_var'].'simbol/'.$my['id'].'.jpg',$g['path_var'].'simbol/'.$my['id'].'.jpg',50,50);
				@chmod($g['path_var'].'simbol/'.$my['id'].'.jpg');
				$photost = ",photo='".$my['id'].".jpg'";
			}
			$picdata = getUrlData($photo_big,10);
			if ($picdata)
			{
				$fp = fopen($g['path_var'].'simbol/180.'.$my['id'].'.jpg','w');
				fwrite($fp,$picdata);
				fclose($fp);
				ResizeWidth($g['path_var'].'simbol/180.'.$my['id'].'.jpg',$g['path_var'].'simbol/180.'.$my['id'].'.jpg',180);
				@chmod($g['path_var'].'simbol/180.'.$my['id'].'.jpg');
				$photost = ",photo='".$my['id'].".jpg'";
			}

			$_QVAL = "name='$name'".$photost;
			getDbUpdate($table['s_mbrdata'],$_QVAL,'memberuid='.$my['uid']);

		}
	}

	getLink('reload','parent.',"적용되었습니다. \\n반영될때까지 잠시만 기다려 주세요.",'');
}
//소셜계정등록
if ($act == 'snslogin')
{
	if(!$email||!$pw1||!$pw2||!$nic) exit;
	getDbUpdate($table['s_mbrid'],"pw='".md5($pw1)."'",'uid='.$my['uid']);
	getDbUpdate($table['s_mbrdata'],"email='".$email."',nic='".trim($nic)."',mailing='".$remail."'",'memberuid='.$my['uid']);

	$_SESSION['mbr_pw']  = md5($pw1);

	getLink('reload','parent.','감사합니다. 사이트 회원으로 등록되셨습니다.','');
}
//회원기초정보수정
if ($act == 'info')
{
	if (!$my['admin'])
	{
		if(strstr(','.$d['member']['join_cutnic'].',',','.$nic.',') || getDbRows($table['s_mbrdata'],"memberuid<>".$my['uid']." and nic='".$nic."'"))
		{
			getLink('','','이미 존재하는 사용자명입니다.','');
		}
	}

	$birth1		= $birth_1;
	$birth2		= $birth_2.$birth_3;
	$birthtype	= $birthtype ? $birthtype : 0;

	$_QVAL = "name='$name',nic='$nic',sex='$sex',birth1='$birth1',birth2='$birth2',birthtype='$birthtype'";
	getDbUpdate($table['s_mbrdata'],$_QVAL,'memberuid='.$my['uid']);

	getLink('reload','parent.','변경되었습니다.','');
}
//계정통합
if ($act == 'plus')
{
	if ($plus)
	{
		if(!$_SESSION['plussns']) getLink('','','정상적인 접근이 아닙니다.','');
		if($hplus)
		{
			$_SESSION['plussns'] = '';
			getLink('reload','parent.','취소되었습니다.','');
		}
		$M1 = getDbData($table['s_mbrdata'],'memberuid='.$_SESSION['plussns'],'*');
		$BYSNS = getDbData($table['s_mbrsns'],'memberuid='.$_SESSION['plussns'],'*');
		$MYSNS = getDbData($table['s_mbrsns'],'memberuid='.$my['uid'],'*');
		$_sns = explode('|',$M1['sns']);
	}
	else {
		if(!$my['email']) getLink($g['s'].'/?r='.$r.'&m='.$m.'&page=account&vtype=site','parent.','계정을 통합하려면 먼저 사이트 계정에 등록하셔야 합니다.','');
		if(!strpos($email,'@')||!$pw) getLink('','','정상적인 접근이 아닙니다.','');
		if($email==$my['email']) getLink('','','입력한 이메일은 현재계정의 이메일주소입니다.','');
		$M1 = getDbData($table['s_mbrdata'],"email='".$email."'",'*');
		if(!$M1['memberuid']) getLink('','','존재하지 않는 이메일입니다.','');
		$MX	= getUidData($table['s_mbrid'],$M1['memberuid']);
		if($MX['pw']!=md5($pw)) getLink('','','비밀번호가 일치하지 않습니다.','');

		$BYSNS = getDbData($table['s_mbrsns'],'memberuid='.$M1['memberuid'],'*');
		$MYSNS = getDbData($table['s_mbrsns'],'memberuid='.$my['uid'],'*');
		$_sns = explode('|',$M1['sns']);
	}

	if ($MYSNS['memberuid'])
	{
		if ($BYSNS['memberuid'])
		{
			$updateSns = '';
			$updateDat = '';
			if (!$MYSNS['st'] && $BYSNS['st']) { $updateSns .= ",st='".$BYSNS['st']."'"; $updateDat .= $_sns[0].'|';} else { $updateDat .= $g['mysns'][0].'|'; }
			if (!$MYSNS['sf'] && $BYSNS['sf']) { $updateSns .= ",sf='".$BYSNS['sf']."'"; $updateDat .= $_sns[1].'|';} else { $updateDat .= $g['mysns'][1].'|'; }
			if (!$MYSNS['sm'] && $BYSNS['sm']) { $updateSns .= ",sm='".$BYSNS['sm']."'"; $updateDat .= $_sns[2].'|';} else { $updateDat .= $g['mysns'][2].'|'; }
			if (!$MYSNS['sy'] && $BYSNS['sy']) { $updateSns .= ",sy='".$BYSNS['sy']."'"; $updateDat .= $_sns[3].'|';} else { $updateDat .= $g['mysns'][3].'|'; }

			if ($updateSns)
			{
				getDbUpdate($table['s_mbrdata'],"sns='".$updateDat."'",'memberuid='.$my['uid']);
				getDbUpdate($table['s_mbrsns'],substr($updateSns,1,strlen($updateSns)),'memberuid='.$my['uid']);
			}
		}
	}
	else {
		if ($BYSNS['memberuid'])
		{
			$QKEY = 'memberuid';
			$QVAL = "'".$my['uid']."'";
			$updateDat = '';
			if (!$BYSNS['st']) { $QKEY .= ',st'; $QVAL .= ",'".$BYSNS['st']."'"; $updateDat .= $_sns[0].'|'; } else { $updateDat .= '|'; }
			if (!$BYSNS['sf']) { $QKEY .= ',sf'; $QVAL .= ",'".$BYSNS['sf']."'"; $updateDat .= $_sns[1].'|'; } else { $updateDat .= '|'; }
			if (!$BYSNS['sm']) { $QKEY .= ',sm'; $QVAL .= ",'".$BYSNS['sm']."'"; $updateDat .= $_sns[2].'|'; } else { $updateDat .= '|'; }
			if (!$BYSNS['sy']) { $QKEY .= ',sy'; $QVAL .= ",'".$BYSNS['sy']."'"; $updateDat .= $_sns[3].'|'; } else { $updateDat .= '|'; }

			if ($QKEY != 'memberuid')
			{
				getDbUpdate($table['s_mbrdata'],"sns='".$updateDat."'",'memberuid='.$my['uid']);
				getDbInsert($table['s_mbrsns'],$QKEY,$QVAL);
			}
		}
	}

	getDbDelete($table['s_mbrid'],'uid='.$M1['memberuid']);
	getDbDelete($table['s_mbrdata'],'memberuid='.$M1['memberuid']);
	getDbDelete($table['s_mbrcomp'],'memberuid='.$M1['memberuid']);
	getDbDelete($table['s_paper'],'my_mbruid='.$M1['memberuid']);
	getDbDelete($table['s_point'],'my_mbruid='.$M1['memberuid']);
	getDbDelete($table['s_scrap'],'mbruid='.$M1['memberuid']);
	getDbDelete($table['s_simbol'],'mbruid='.$M1['memberuid']);
	getDbDelete($table['s_friend'],'my_mbruid='.$M1['memberuid'].' or by_mbruid='.$M1['memberuid']);
	getDbDelete($table['s_mbrsns'],'memberuid='.$M1['memberuid']);
	getDbUpdate($table['s_mbrlevel'],'num=num-1','uid='.$M1['level']);
	getDbUpdate($table['s_mbrgroup'],'num=num-1','uid='.$M1['sosok']);

	$_SESSION['plussns'] = '';

	//엠블로그 개설
	$b_members	= '';   
	$d_last		= '';
	$memberuid = $my['memberuid'];
	$b_id = $my['id'];
	$b_name = $my['nic'];
	$d_regis = $my['d_regis'];

	if(!getDbRows($table['mbloglist'],"mbruid=".$memberuid)) {
		$Ugid = getDbCnt($table['mbloglist'],'max(gid)','') + 1;
		$bQKEY = "gid,blogtype,mbruid,members,id,name,d_regis,d_last,num_w,num_c,num_o,num_m";
		$bQVAL = "'$Ugid','1','$memberuid','$members','$b_id','$b_name','$d_regis','$d_last','0','0','0','0'";
		getDbInsert($table['mbloglist'],$bQKEY,$bQVAL);

		$fdset = array('layout','theme_pc','theme_mobile','iframe','snsconnect','vtype','recnum','vopen','editor','rlength');
		$fdset2 = array('','_pc/m_blog','','Y','social/inc/sns_joint01.php','review','20','','default','200');

		$gfile= $g['path_module'].'mblog/var/var.'.$memberuid.'.php';
		$fp = fopen($gfile,'w');
		fwrite($fp, "<?php\n");
		$bi = 0;
		foreach ($fdset as $val)
		{
			fwrite($fp, "\$d['blog']['".$val."'] = \"".$fdset2[$bi]."\";\n");
			$bi++;
		}
		fwrite($fp, "?>");
		fclose($fp);
		@chmod($gfile,0707);
	}
	//엠블로그 개설 끝




	getLink('reload','parent.',$my['email']?$my['email'].' 계정으로 통합되었습니다.':'계정이 통합되었습니다.','');
}
//아이디변경
if ($act == 'id')
{
	if(!$id || $id==$my['id']) exit;
	$isId = getDbRows($table['s_mbrid'],"id='".$id."' and id<>'".$my['id']."'");
	if($isId) getLink('','','존재하는 아이디입니다.','');

	getDbUpdate($table['s_mbrid'],"id='".$id."'",'uid='.$my['uid']);

	getLink('reload','parent.','아이디가 변경되었습니다.','');
}
//비번변경
if ($act == 'pw')
{
	if (!$pw || !$pw1 || !$pw2)
	{
		getLink('','','정상적인 접근이 아닙니다.','');
	}

	if (md5($pw) != $my['pw'] && $my['tmpcode'] != md5($pw))
	{
		getLink('','','현재 비밀번호가 일치하지 않습니다.','');
	}

	if ($pw == $pw1)
	{
		getLink('','','현재 비밀번호와 변경할 비밀번호가 같습니다.','');	
	}

	getDbUpdate($table['s_mbrid'],"pw='".md5($pw1)."'",'uid='.$my['uid']);
	getDbUpdate($table['s_mbrdata'],"last_pw='".$date['today']."',tmpcode=''",'memberuid='.$my['uid']);

	$_SESSION['mbr_pw']  = md5($pw1);

	getLink('reload','parent.','변경되었습니다.','');
}
?>