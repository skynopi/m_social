<?php
if(!defined('__KIMS__')) exit;

//SNS테이블
$_tmp = db_query( "select count(*) from ".$table[$module.'mbrsns'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'mbrsns']." (
memberuid	INT				PRIMARY KEY		NOT NULL,
st			VARCHAR(40)		DEFAULT ''		NOT NULL,
sf			VARCHAR(40)		DEFAULT ''		NOT NULL,
sm			VARCHAR(40)		DEFAULT ''		NOT NULL,
sy			VARCHAR(40)		DEFAULT ''		NOT NULL,
sr			VARCHAR(40)		DEFAULT ''		NOT NULL,
sg			VARCHAR(40)		DEFAULT ''		NOT NULL,
KEY st(st),
KEY sf(sf),
KEY sm(sm),
KEY sy(sy),
KEY sr(sr),
KEY sg(sg)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'mbrsns'],$DB_CONNECT); 
}


//SNS전송데이터
$_tmp = db_query( "select count(*) from ".$table[$module.'data'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'data']." (
uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
gid			INT				DEFAULT '0'		NOT NULL,
provider	CHAR(1)			DEFAULT ''		NOT NULL,
snsid		VARCHAR(50)		DEFAULT ''		NOT NULL,
subject		VARCHAR(250)	DEFAULT ''		NOT NULL,
name		VARCHAR(30)		DEFAULT ''		NOT NULL,
nic			VARCHAR(50)		DEFAULT ''		NOT NULL,
mbruid		INT				DEFAULT '0'		NOT NULL,
id			VARCHAR(16)		DEFAULT ''		NOT NULL,
targeturl	VARCHAR(250)	DEFAULT ''		NOT NULL,
cync		TEXT			NOT NULL,
d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
KEY gid(gid),
KEY provider(provider),
KEY snsid(snsid),
KEY mbruid(mbruid),
KEY id(id)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'data'],$DB_CONNECT); 
}
?>