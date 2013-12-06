<div id="socialaccount">

<div class="tops">
<a href="<?php echo $g['sociallink']?>&amp;page=<?php echo $page?>&amp;type=<?php echo $type?>&amp;vtype=sns"<?php if($vtype=='sns'):?> class="b"<?php endif?>>소셜 계정</a>ㆍ
<a href="<?php echo $g['sociallink']?>&amp;page=<?php echo $page?>&amp;type=<?php echo $type?>&amp;vtype=site"<?php if($vtype=='site'):?> class="b"<?php endif?>>사이트 계정</a>ㆍ
<a href="<?php echo $g['sociallink']?>&amp;page=<?php echo $page?>&amp;type=<?php echo $type?>&amp;vtype=plus"<?php if($vtype=='plus'):?> class="b"<?php endif?>>계정 통합</a>ㆍ
<a href="<?php echo $g['sociallink']?>&amp;page=<?php echo $page?>&amp;type=<?php echo $type?>&amp;vtype=id"<?php if($vtype=='id'):?> class="b"<?php endif?>>아이디 변경</a>ㆍ
<a href="<?php echo $g['sociallink']?>&amp;page=<?php echo $page?>&amp;type=<?php echo $type?>&amp;vtype=pw"<?php if($vtype=='pw'):?> class="b"<?php endif?>>비밀번호 변경</a>
</div>


<?php include $g['dir_module_mode'].'/'.$vtype.'.php'?>


</div>



