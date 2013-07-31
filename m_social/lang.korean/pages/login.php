<?php 
if ($my['uid']) getLink('reload','parent.','','');
include_once $g['path_module'].'social/var/var.php';
?>
<div id="socialaccount">

<div class="tops">
SNS를 이용해서 로그인하실 수 있습니다.
</div>

<div class="snsaccount">

	<?php $i=0;foreach($g['snskor'] as $_key=>$_val):?>
	<?php if(!$d['social']['use_'.$_key])continue?>
	<?php $_snsuse=explode(',',$g['mysns'][$i])?>
	<div class="snsx">
		<img src="<?php echo $g['img_module_skin']?>/sns_<?php echo $_key?>.gif" id="sns_ico_<?php echo $_key?>" alt="" title="<?php echo $_val?>" />
		<span><?php echo $_val?></span>
		<?php if($g['mysns'][$i]):?>
		<img src="<?php echo $g['img_module_skin']?>/btn_disconnect.gif" alt="" class="hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','delete');" />
		<?php else:?>
		<img src="<?php echo $g['img_module_skin']?>/btn_connect.gif" alt="" class="hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','connect');" />
		<?php endif?>
	</div>
	<?php $i++;endforeach?>
	<div class="clear"></div>

</div>


</div>


