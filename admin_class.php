<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);		
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function login2(){
		
		extract($_POST);		
		$qry = $this->db->query("SELECT * FROM complainants where email = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = '$type' ";
		$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", address = '$address' ";
		$data .= ", contact = '$contact' ";
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * from complainants where email ='$email' ".(!empty($id) ? " and id != '$id' " : ''))->num_rows;
		if($chk > 0){
			return 3;
			exit;
		}
		if(empty($id))
			$save = $this->db->query("INSERT INTO complainants set $data");
		else
			$save = $this->db->query("UPDATE complainants set $data where id=$id ");
		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
				$qry = $this->db->query("SELECT * FROM complainants where id = $id ");
				if($qry->num_rows > 0){
					foreach ($qry->fetch_array() as $key => $value) {
						if($key != 'password' && !is_numeric($key))
							$_SESSION['login_'.$key] = $value;
					}
						return 1;
				}else{
					return 3;
				}
		}
	}
	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['system'][$key] = $value;
		}

			return 1;
				}
	}
	function save_supplier(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM suppliers where name ='$name' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO suppliers set $data");
		}else{
			$save = $this->db->query("UPDATE suppliers set $data where id = $id");
		}

		if($save)
			return 1;
	}
	function delete_supplier(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM suppliers where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_product(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','item_code')) && !is_numeric($k)){
				if($k == 'price'){
					$v= str_replace(',', '', $v);
				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM items where item_code ='$item_code' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($item_code)){
			$i = 0;
			while($i == 0){
				$item_code  = mt_rand(1,999999999999);
				$item_code = sprintf("%'012d", $item_code);
				$chk = $this->db->query("SELECT * FROM items where item_code ='$item_code' ");
				if($chk->num_rows <= 0){
					$i = 1;
				}
			}
			$data .= ", item_code = '$item_code' ";
		}
		if(empty($id)){
			// echo "INSERT INTO items set $data";
			$save = $this->db->query("INSERT INTO items set $data");
		}else{
			$save = $this->db->query("UPDATE items set $data where id = $id");
		}

		if($save)
			return 1;
	}
	function delete_product(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM items where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_receiving(){
		extract($_POST);
		$data = " supplier_id = $supplier_id ";
		$data .= ", total_cost = '$total_amount' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO receiving set $data");
			$id = $this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE receiving set $data where id != '$id' ");
		}
		if($save){
			$ids = $inv_id;
			$ids = array_filter($ids);
			$ids = implode(",",$inv_id);
			if(!empty($ids) > 0)
			$this->db->query("DELETE FROM stocks where id not in ($ids) ");
			foreach($inv_id as $k=>$v){
				$data  = " 	item_id = {$item_id[$k]}";
				$data .= ", type = 1 ";
				$data .= ", qty = '{$qty[$k]}' ";
				$data .= ", price = '{$cost[$k]}' ";
				if(empty($v)){
					$inv[] = $this->db->query("INSERT INTO stocks set $data");
					$inv_id[$k] = $this->db->insert_id;
				}else{
					$inv[] = $this->db->query("UPDATE stocks set $data where id = $v");
					$inv_id[$k] = $v;
				}
			}
			$this->db->query("UPDATE receiving set inventory_ids = '".implode(',',$inv_id)."' where id=$id ");
			return 1;
		}
	}
	function delete_receiving(){
		extract($_POST);
		$ids = $this->db->query("SELECT * FROM receiving where id=$id")->fetch_array()['inventory_ids'];
		if(!empty($ids) > 0)
			$this->db->query("DELETE FROM stocks where id in ($ids) ");
		$delete = $this->db->query("DELETE FROM receiving where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_order(){
		extract($_POST);
		$data = " user_id = {$_SESSION['login_id']} ";
		$data .= ", total_amount = '$total_amount' ";
		$data .= ", amount_tendered = '$total_tendered' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO sales set $data");
			$id = $this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE sales set $data where id = '$id' ");
		}
		if($save){
			$ids = $inv_id;
			$ids = array_filter($ids);
			$ids = implode(",",$inv_id);
			if(!empty($ids) > 0){
			$qry = $this->db->query("SELECT * FROM sales where id= '$id' ")->fetch_array();
			$this->db->query("DELETE FROM stocks where id not in ($ids) and id in ({$qry['inventory_ids']})");
			}
			foreach($inv_id as $k=>$v){
				$data  = " 	item_id = {$item_id[$k]}";
				$data .= ", type = 2 ";
				$data .= ", qty = '{$qty[$k]}' ";
				$data .= ", price = '{$price[$k]}' ";
				if(empty($v)){
					$inv[] = $this->db->query("INSERT INTO stocks set $data");
					$inv_id[$k] = $this->db->insert_id;
				}else{
					$inv[] = $this->db->query("UPDATE stocks set $data where id = $v");
					$inv_id[$k] = $v;
				}
			}
			$this->db->query("UPDATE sales set inventory_ids = '".implode(',',$inv_id)."' where id=$id ");
			return $id;
		}
	}
	function delete_order(){
		extract($_POST);
		$ids = $this->db->query("SELECT * FROM sales where id=$id")->fetch_array()['inventory_ids'];
		if(!empty($ids) > 0)
			$this->db->query("DELETE FROM stocks where id in ($ids) ");
		$delete = $this->db->query("DELETE FROM sales where id = ".$id);
		if($delete){
			return 1;
		}
	}
}