<?php
defined('IN_DESTOON') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?file=<?php echo $file;?>&moduleid=<?php echo $moduleid;?>&action=<?php echo $action;?>&itemid=<?php echo $itemid;?>" id="dform" onsubmit="return check();">
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt">修改<?php echo $MOD['name'];?></div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl">所属栏目 <span class="f_red">*</span></td>
<td><?php echo category_select('post[catid]', '选择栏目', $catid, $moduleid, 'id="catid" onchange="cat(this.value);"');?> <span id="dcatid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><?php echo $MOD['name'];?>标题 <span class="f_red">*</span></td>
<td><input name="post[title]" type="text" id="title" size="60" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '级别', $level);?> <?php echo dstyle('post[style]', $style);?> <br/><span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><?php echo $MOD['name'];?>日期 <span class="f_red">*</span></td>
<td><?php echo dcalendar('post[fromtime]', $fromtime);?> 至 <?php echo dcalendar('post[totime]', $totime);?> <span id="dtime" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">展出城市 <span class="f_red">*</span></td>
<td><input name="post[city]" type="text" id="city" size="10" value="<?php echo $city;?>"/> <span id="dcity" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">展出地址 <span class="f_red">*</span></td>
<td><input name="post[address]" type="text" id="address" size="60" value="<?php echo $address;?>"/> <span id="daddress" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">展馆名称 <span class="f_red">*</span></td>
<td><input name="post[hallname]" type="text" id="hallname" size="40" value="<?php echo $hallname;?>"/> <span id="dhallname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><?php echo $MOD['name'];?>说明 <span class="f_red">*</span></td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', 'Default', '95%', 350);?><br/>
<span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><?php echo $MOD['name'];?>备注</td>
<td><textarea name="post[remark]" style="width:90%;height:45px;"><?php echo $remark;?></textarea></td>
</tr>
<tr>
<td class="tl">标题图片</td>
<td><input name="post[thumb]" id="thumb" type="text" size="60" value="<?php echo $thumb;?>"/>&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, $('thumb').value);" class="jt">[上传]</span>&nbsp;&nbsp;<span onclick="_preview($('thumb').value);" class="jt">[预览]</span>&nbsp;&nbsp;<span onclick="$('thumb').value='';" class="jt">[删除]</span></td>
</tr>
<tr>
<td class="tl">主办单位 <span class="f_red">*</span></td>
<td><input name="post[sponsor]" id="sponsor" type="text" size="60" value="<?php echo $sponsor;?>"/> <span id="dsponsor" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">承办单位</td>
<td><input name="post[undertaker]" type="text" size="60" value="<?php echo $undertaker;?>" /></td>
</tr>
<tr>
<td class="tl">联系人 <span class="f_red">*</span></td>
<td><input name="post[name]" id="name" type="text" size="10" value="<?php echo $name;?>" /> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">联系电话 <span class="f_red">*</span></td>
<td><input name="post[telephone]" id="telephone" type="text" size="30" value="<?php echo $telephone;?>" /> <span id="dtelephone" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">联系手机</td>
<td><input name="post[mobile]" id="mobile" type="text" size="30" value="<?php echo $mobile;?>" /> <span id="dmobile" class="f_red"></span></td>
</tr>
<tr>
<td class="tl">联系地址</td>
<td><input name="post[addr]" type="text" size="60" value="<?php echo $addr;?>" /></td>
</tr>
<tr>
<td class="tl">联系传真</td>
<td><input name="post[fax]" type="text" size="30" value="<?php echo $fax;?>" /></td>
</tr>
<tr>
<td class="tl">联系Email</td>
<td><input name="post[email]" type="text" size="30" value="<?php echo $email;?>" /></td>
</tr>
<tr>
<td class="tl">联系MSN</td>
<td><input name="post[msn]" type="text" size="30" value="<?php echo $msn;?>" /></td>
</tr>
<tr>
<td class="tl">联系QQ</td>
<td><input name="post[qq]" type="text" size="30" value="<?php echo $qq;?>"/></td>
</tr>
<tr>
<td class="tl"><?php echo $MOD['name'];?>状态</td>
<td>
<input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> 通过
<input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> 待审
<input type="radio" name="post[status]" value="1" <?php if($status == 1) echo 'checked';?> onclick="if(this.checked) $('note').style.display='';"/> 拒绝
<input type="radio" name="post[status]" value="4" <?php if($status == 4) echo 'checked';?>/> 过期
<input type="radio" name="post[status]" value="0" <?php if($status == 0) echo 'checked';?>/> 删除
</td>
</tr>
<tr id="note" style="display:<?php echo $status==1 ? '' : 'none';?>">
<td class="tl">拒绝理由 <span class="f_red">*</span></td>
<td><input name="post[note]" type="text"  size="40" value="<?php echo $note;?>"/></td>
</tr>
<tr>
<td class="tl">添加时间</td>
<td><input type="text" size="22" name="post[addtime]" value="<?php echo $addtime;?>"/></td>
</tr>
<tr>
<td class="tl">内容模板</td>
<td><?php echo tpl_select('show', $module, 'post[template]', '默认模板', $template, 'id="template"');?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value=" 重 置 " class="btn"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function cat(catid) {
	if(catid) {
		var templates = new Array();
		<?php foreach($CATEGORY as $k=>$v) { ?>
		templates[<?php echo $k;?>] = '<?php echo $v['show_template'];?>';
		<?php } ?>
		select_op('template', templates[catid]);
	}
}
function check() {
	var l;
	var f;
	f = 'catid';
	if($(f).value == 0) {
		Dmsg('请选择所属栏目', f, 1);
		return false;
	}
	f = 'title';
	l = $(f).value.length;
	if(l < 2) {
		Dmsg('标题最少2字，当前已输入'+l+'字', f);
		return false;
	}
	if($('postfromtime').value.length != 10 || $('posttotime').value.length != 10) {
		Dmsg('请选择展会日期', 'time', 1);
		return false;
	}
	f = 'city';
	l = $(f).value.length;
	if(l < 2) {
		Dmsg('请填写展出城市', f);
		return false;
	}
	f = 'address';
	l = $(f).value.length;
	if(l < 6) {
		Dmsg('请填写详细的展出地址', f);
		return false;
	}
	f = 'hallname';
	l = $(f).value.length;
	if(l < 2) {
		Dmsg('请填写展馆名称', f);
		return false;
	}
	f = 'content';
	l = FCKLen();
	if(l < 5) {
		Dmsg('内容最少5字，当前已输入'+l+'字', f);
		return false;
	}
	f = 'sponsor';
	l = $(f).value.length;
	if(l < 2) {
		Dmsg('请填写主办单位', f);
		return false;
	}
	f = 'name';
	l = $(f).value.length;
	if(l < 2) {
		Dmsg('请填写联系人', f);
		return false;
	}
	f = 'telephone';
	l = $(f).value.length;
	if(l < 7) {
		Dmsg('请填写联系电话', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuon[$status];?>);</script>
</body>
</html>