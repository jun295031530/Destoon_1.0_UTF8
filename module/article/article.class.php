<?php
defined('IN_DESTOON') or exit('Access Denied');
class article {
	var $moduleid;
	var $itemid;
	var $db;
	var $table;
	var $table_data;
	var $text_data;
	var $fields;
	var $errmsg = errmsg;

    function article($moduleid) {
		global $db, $table, $table_data, $MOD;
		$this->moduleid = $moduleid;
		$this->table = $table;
		$this->table_data = $table_data;
		$this->text_data = $MOD['text_data'];
		$this->db = &$db;
		$this->fields = array('catid','level','title','style','introduce','thumb','tag','keyword','author','copyfrom','fromurl','status','username','addtime','eidtor','edittime','template', 'islink', 'linkurl','note');
    }

	function pass($post) {
		if(!is_array($post)) return false;
		if(!$post['catid']) return $this->_('请选择栏目');
		if(!$post['title']) return $this->_('请填写标题');
		if(isset($post['islink'])) {
			if(!$post['linkurl']) return $this->_('请填写链接地址');
		} else {
			if(!$post['content']) return $this->_('请填写内容');
		}
		return true;
	}

	function set($post) {
		global $MOD, $DT_TIME, $CATEGORY, $_username, $_userid;
		$post['islink'] = isset($post['islink']) ? 1 : 0;
		$post['addtime'] = (isset($post['addtime']) && $post['addtime']) ? strtotime($post['addtime']) : $DT_TIME;
		$post['edittime'] = $DT_TIME;
		$post['content'] = stripslashes($post['content']);
		//clear link
		if($post['content'] && isset($post['clear_link'])) $post['content'] = clear_link($post['content']);
		//save remote
		if($post['content'] && isset($post['save_remotepic'])) $post['content'] = save_remote($post['content']);
		//Fix pagebraek
		if(strpos($post['content'], 'pagebreak') !== false) $post['content'] = str_ireplace(array('[pagebreak]</p>', '<p>[pagebreak]', '[pagebreak]</div>', '<div>[pagebreak]'), array('</p>[pagebreak]', '[pagebreak]<p>', '</div>[pagebreak]', '[pagebreak]<div>'), $post['content']);
		//get introduce
		if($post['content'] && !$post['introduce'] && isset($post['get_introduce']) && $post['introduce_length']) $post['introduce'] = addslashes(dsubstr(dtrim(strip_tags($post['content'])), $post['introduce_length']));
		//make keyword
		$post['keyword'] = addslashes($post['tag'].','.$post['title'].','.strip_tags(cat_pos($post['catid'], ',')));
		//clear uploads
		clear_upload($post['content'].$post['thumb']);
		if($this->itemid) {
			$post['editor'] = $_username;
			$new = $post['content'];
			if($post['thumb']) $new .= '<img src="'.$post['thumb'].'">';
			$r = $this->get_one();
			$old = $r['content'];
			if($r['thumb']) $old .= '<img src="'.$r['thumb'].'">';
			delete_diff($new, $old);
		} else {
			$post['username'] = $post['editor'] = $_username;
		}
		if(!defined('DT_ADMIN')) {
			$content = dsafe($post['content']);
			unset($post['content']);
			$post = dhtmlspecialchars($post);
			$post['content'] = $content;
		}
		$post['content'] = addslashes($post['content']);
		return $post;
	}

	function get_one() {
        return $this->db->get_one("SELECT * FROM {$this->table} a,{$this->table_data} c WHERE a.itemid=c.itemid and a.itemid='$this->itemid' limit 0,1");
	}

	function get_list($condition = 'status=3', $order = 'addtime DESC', $cache = '') {
		global $MOD, $pages, $page, $pagesize, $offset;
		$r = $this->db->get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE $condition", $cache);
		$pages = pages($r['num'], $page, $pagesize);		
		$lists = array();
		$result = $this->db->query("SELECT * FROM {$this->table} WHERE $condition ORDER BY $order LIMIT $offset,$pagesize", $cache);
		while($r = $this->db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['title'] = set_style($r['title'], $r['style']);
			if(!$r['islink']) $r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		$sqlk = $sqlv = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) { $sqlk .= ','.$k; $sqlv .= ",'$v'"; }
		}
        $sqlk = substr($sqlk, 1);
        $sqlv = substr($sqlv, 1);
		$this->db->query("INSERT INTO {$this->table} ($sqlk) VALUES ($sqlv)");
		$this->itemid = $this->db->insert_id();
		$this->db->query("INSERT INTO {$this->table_data} (itemid,content) VALUES ('$this->itemid', '$post[content]')");
		$this->update($this->itemid, $post, $post['content']);
		if($post['status'] == 3) $this->tohtml($this->itemid, $post['catid']);
		return $this->itemid;
	}

	function edit($post) {
		$this->delete($this->itemid, false);
		$post = $this->set($post);
		$sql = '';
		foreach($post as $k=>$v) {
			if(in_array($k, $this->fields)) $sql .= ",$k='$v'";
		}
        $sql = substr($sql, 1);
	    $this->db->query("UPDATE {$this->table} SET $sql WHERE itemid=$this->itemid");
	    $this->db->query("UPDATE {$this->table_data} SET content='$post[content]' WHERE itemid=$this->itemid");
		$this->update($this->itemid, $post, $post['content']);
		if($post['status'] == 3) $this->tohtml($this->itemid, $post['catid']);
		return true;
	}

	function tohtml($itemid = 0, $catid = 0) {
		global $module, $MOD;
		if($MOD['show_html'] && $itemid) tohtml('show', $module, "itemid=$itemid");
		if($MOD['list_html'] && $catid) tohtml('list', $module, "catid=$catid&fid=1&num=3");
		if($MOD['index_html']) tohtml('index', $module);
	}

	function update($itemid, $r = array(), $content = '') {
		$r or $r = $this->db->get_one("SELECT catid,addtime,islink FROM {$this->table} WHERE itemid=$itemid");
		if(!$r['islink']) {
			$linkurl = itemurl($itemid, $r['catid'], $r['addtime']);
			$this->db->query("UPDATE {$this->table} SET linkurl='$linkurl' WHERE itemid=$itemid");
		}
		if($this->text_data) {
			if(!$content) {
				$content = $this->db->get_one("SELECT content FROM {$this->table_data} WHERE itemid=$itemid");
				$content = $content['content'];
			}
			text_write($itemid, $this->moduleid, $content);
		} else {
			text_delete($itemid, $this->moduleid);
		}
	}

	function recycle($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->recycle($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=0 WHERE itemid=$itemid");
			$this->delete($itemid, false);
			return true;
		}		
	}

	function delete($itemid, $all = true) {
		global $CFG, $MOD;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v, $all);
			}
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			if($MOD['show_html'] && !$r['islink']) {
				$_file = DT_ROOT.'/'.$MOD['moduledir'].'/'.$r['linkurl'];
				if(is_file($_file)) unlink($_file);
				$i = 1;
				while($i) {
					$_file = DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($itemid, $r['catid'], $r['addtime'], $i);
					if(is_file($_file)) {
						unlink($_file);
						$i++;
					} else {
						break;
					}
				}
			}
			if($all) {
				$userid = get_user($r['username']);
				if($r['thumb']) delete_upload($r['thumb'], $userid);
				if($r['content']) delete_local($r['content'], $userid);
				$this->db->query("DELETE FROM {$this->table} WHERE itemid=$itemid");
				$this->db->query("DELETE FROM {$this->table_data} WHERE itemid=$itemid");
				if($this->text_data) text_delete($this->itemid, $this->moduleid);
			}
		}
	}

	function check($itemid) {
		global $_username, $DT_TIME;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->check($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=3,editor='$_username' WHERE itemid=$itemid");
			$this->tohtml($itemid);
			return true;
		}
	}

	function reject($itemid) {
		global $_username, $DT_TIME;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->reject($v); }
		} else {
			$this->db->query("UPDATE {$this->table} SET status=1,editor='$_username' WHERE itemid=$itemid");
			return true;
		}
	}

	function clear() {		
		$result = $this->db->query("SELECT itemid FROM {$this->table} WHERE status=0");
		while($r = $this->db->fetch_array($result)) {
			$this->delete($r['itemid']);
		}
	}
	
	function move($ids, $catid, $type = 0) {
		if($type) {
			$this->db->query("UPDATE {$this->table} SET catid='$catid' WHERE itemid IN ($ids) ");
		} else {
			$this->db->query("UPDATE {$this->table} SET catid='$catid' WHERE catid IN ($ids) ");
		}
	}

	function level($itemid, $level) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->level($v, $level); }
		} else {
			$this->db->query("UPDATE {$this->table} SET level=$level WHERE itemid=$itemid");
			return true;
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>