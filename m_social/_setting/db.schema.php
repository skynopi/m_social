<?php
if(!defined('__KIMS__')) exit;


//SNS유저데이터
$_tmp = db_query( "select count(*) from ".$table[$module.'user'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'user']." (
memberuid	INT				PRIMARY KEY		NOT NULL,
provider	CHAR(1)			DEFAULT ''		NOT NULL,
id_t		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_f		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_m		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_y		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_k		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_g		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_i		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_r		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_l		VARCHAR(50)		DEFAULT ''		NOT NULL,
id_s		VARCHAR(50)		DEFAULT ''		NOT NULL,
extra_t		TEXT			NOT NULL,
extra_f		TEXT			NOT NULL,
extra_m		TEXT			NOT NULL,
extra_y		TEXT			NOT NULL,
extra_k		TEXT			NOT NULL,
extra_g		TEXT			NOT NULL,
extra_i		TEXT			NOT NULL,
extra_r		TEXT			NOT NULL,
extra_l		TEXT			NOT NULL,
extra_s		TEXT			NOT NULL) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'user'],$DB_CONNECT); 
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