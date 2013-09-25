<?php 
if ($my['uid']) getLink('reload','parent.','','');
include_once $g['path_module'].'m_social/var/'.$s.'.var.php';
?>
<div id="socialaccount">

<div class="snsaccount">

	<?php $i=0;foreach($g['snskor'] as $_key=>$_val):?>
	<?php if(!$d['social']['use_'.$_key])continue?>
	<?php $_snsuse=explode(',',$g['mysns'][$i])?>
	<div class="snsx">	
		
		<?php if($g['mysns'][$i]):?>		
		<img src="<?php echo $g['img_module_skin']?>/sns_<?php echo $_key?>.gif" id="sns_ico_<?php echo $_key?>" alt="" title="<?php echo $_val?> 해제" class="hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','delete');" />
		<?php else:?>		
		<img src="<?php echo $g['img_module_skin']?>/sns_<?php echo $_key?>.gif" id="sns_ico_<?php echo $_key?>" alt="" title="<?php echo $_val?> 연결" class="hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','connect');" />
		<img src="<?php echo $g['img_module_skin']?>/btn_connect.gif" alt="" class="hand" onclick="snsCheck('<?php echo $_key?>','<?php echo $_snsuse[0]?>','connect');" />
		<?php endif?>
	</div>
	<?php $i++;endforeach?>
	<div class="clear"></div>

</div>


</div>


