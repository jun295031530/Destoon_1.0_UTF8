<?php
defined('IN_DESTOON') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="tt"><?php if($parentid) echo $AREA[$parentid]['areaname'];?>地区管理</div>
<form method="post" action="?file=<?php echo $file;?>&action=update&parentid=<?php echo $parentid;?>">
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="80">排序</th>
<th width="80">ID</th>
<th>地区名</th>
<th width="80">子地区</th>
<th width="120">操作</th>
</tr>
<?php foreach($DAREA as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input name="area[<?php echo $v['areaid'];?>][listorder]" type="text" size="2" value="<?php echo $v['listorder'];?>"/></td>
<td>&nbsp;<?php echo $v['areaid'];?></td>
<td><input name="area[<?php echo $v['areaid'];?>][areaname]" type="text" size="20" value="<?php echo $v['areaname'];?>"/></td>
<td>&nbsp;<a href="?file=<?php echo $file;?>&parentid=<?php echo $v['areaid'];?>"><?php echo $v['childs'];?></a></td>
<td>
<a href="?file=<?php echo $file;?>&parentid=<?php echo $v['areaid'];?>"><img src="<?php echo IMG_PATH;?>child.png" width="16" height="16" title="管理子地区，当前有<?php echo $v['childs'];?>个子地区" alt=""/></a>&nbsp;
<a href="?file=<?php echo $file;?>&action=add&parentid=<?php echo $v['areaid'];?>"><img src="<?php echo IMG_PATH;?>new.png" width="16" height="16" title="添加子地区" alt=""/></a>&nbsp;
<a href="?file=<?php echo $file;?>&action=delete&areaid=<?php echo $v['areaid'];?>&parentid=<?php echo $parentid;?>" onclick="return _delete();"><img src="<?php echo IMG_PATH;?>delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="submit" name="submit" value=" 更新地区 " class="btn" title="更新地区排序及名称"/>
<?php if($parentid) {?>
&nbsp;&nbsp;<input type="button" value=" 上级地区 " class="btn" onclick="window.location='?file=<?php echo $file;?>&parentid=<?php echo $AREA[$parentid]['parentid'];?>';"/>
<?php }?>
</div>
</form>
<br/>
<script type="text/javascript">Menuon(1);</script>
</body>
</html>