<?php 
	header("content-type:text/html;charset=utf-8");

	require("connect.php");
	require("antixss.php");
	
	$a=$_GET['a'];

	switch ($a) {
		case 'login':
			$username=trim($_POST['username']);
			$userpass=$_POST['userpass'];
			
			//sqli waf
			$black_list = "/by|substr|benchmark|char|union|xor|\^|&|flag|substring|delete|drop|alter|change|rename|execute|prepare|deallocate|greatest|*|regexp|%00|;|=| |like|\'|rlike|-|ascii|mid|select|into|where|\\\|limit|or|and|if|extractvalue|updatexml|concat|insert|join|having|sleep/im";
			//if(preg_match($black_list, $username)) die("<script>alert('非法字符');window.location.href='login.php';</script>");
			//if(preg_match($black_list, $userpass)) die("<script>alert('非法字符');window.location.href='login.php';</script>");
			
			$white_list = "/^[A-Za-z0-9_\-]+$/im";
			if(!preg_match($white_list, $username)) die("<script>alert('非法字符');window.location.href='login.php';</script>");
			if(!preg_match($white_list, $userpass)) die("<script>alert('非法字符');window.location.href='login.php';</script>");
			
			
			$userpass=md5($_POST['userpass']);

			$sql="select * from user where username = '$username'";
			if ($conn) {
			    $query = mysqli_query($conn,$sql);
			    $result = mysqli_fetch_array($query);
			}else{
				printf("Error: %s\n", mysqli_error($conn));
				exit();
			}			
			//重构了登录查询语句，将cookie换为session，提高了安全性
			if(empty($result)||$result['userpass']!==$userpass){
			    echo "<script>alert('用户名或密码错误，请重新登录');window.location.href='login.php';</script>";
			}else{
				session_start();
				$_SESSION['username'] = $username;
				$_SESSION['islogin'] = "Leon";
				mysqli_free_result($query);
				echo "<script>alert('登录成功');window.location.href='index.php';</script>";
			}
			mysqli_close($conn);

			break;

		case 'register':
			$myfile=$_FILES['imgpath'];
			//设置上传图片大小
			if($myfile['size']>2*1024*1024){
				echo "<script>alert('文件不能大于2M');window.location.href='register.php';</script>";
			}
			//设置上传图片类型
			$arr=array("image/jpg","image/jpeg","image/png","image/gif");
			if(!in_array($myfile['type'],$arr)){
				echo "<script>alert('文件类型有误');window.location.href='register.php';</script>";
			}


			do{
				$name=time().mt_rand(1000,9999).".jpg";
			}while(file_exists("./img/".$name));

			if(!is_dir("./img")){
				mkdir("./img/",0777,true);
			}
			if(move_uploaded_file($myfile['tmp_name'],"img/".$name)){
				$username=trim($_POST['username']);
				$userpass=$_POST['userpass'];
				//sql waf
				$white_list = "/^[A-Za-z0-9_\-]+$/im";
				if(!preg_match($white_list, $username)) die("<script>alert('非法字符');window.location.href='register.php';</script>");
				if(!preg_match($white_list, $userpass)) die("<script>alert('非法字符');window.location.href='register.php';</script>");
				$userpass=md5($userpass);
				
				$confirm_pass=md5($_POST['confirm_pass']);
				$imgpath=$name;
				$create_time=time();

				/*var_dump($imgpath);*/
				if($userpass!=$confirm_pass){
					echo "<script>alert('两次密码不一致');window.location.href='register.php';</script>";
				}else{
					$sql="select * from user where name=".$username;
					$result=mysqli_query($conn,$sql);

					if(@mysqli_num_rows($result)>0){
						echo "<script>alert('该用户已存在，请前往登录');window.location.href='login.php';</script>";
					}else{
						$sql="insert into user(username,userpass,create_time,imgpath) values('{$username}','{$userpass}','{$create_time}','{$imgpath}')";
						mysqli_query($conn,$sql);
						if(@mysqli_affected_rows($conn)>0){
							echo "<script>alert('注册成功，前往登录');window.location.href='login.php';</script>";
						}else{
							echo "<script>alert('注册失败，请重新注册');window.location.href='register.php';</script>";
						}
					}

				}
				
				

			}

			mysqli_close($conn);

			break;

		case 'quit':
		    session_start();
			session_destroy();
			header("location:index.php");

			break;

		case 'message':
		    session_start();
			$message_name=$_SESSION['username'];//留言用户名
			$sql_id="select id from user where username='{$message_name}'";
			$result=mysqli_query($conn,$sql_id);

			$ID=mysqli_fetch_assoc($result);
			foreach($ID as $id){}//获取留言用户id
			$message_content=$_POST['message_content'];//留言内容
			
			$message_content = antixss($message_content);//xss waf
			
			$create_time=date("Y-m-d H:i:s",time());//留言时间

			$sql="insert into message(message_content,create_time,id,message_name) values('{$message_content}','{$create_time}',{$id},'{$message_name}')";
			mysqli_query($conn,$sql);
			
			if(mysqli_affected_rows($conn)>0){
				echo "<script>alert('留言成功');window.location.href='index.php';</script>";
			}else{
				echo "<script>alert('留言失败，请重新留言');window.location.href='index.php';</script>";
			}

			mysqli_close($conn);
			
			break;

		case 'delete_m':
			$sql="delete from message where message_id=".$_GET['message_id'];
			mysqli_query($conn,$sql);
			if(mysqli_affected_rows($conn)>0){
				echo "<script>alert('删除成功');window.location.href='index.php';</script>";
			}else{
				echo "<script>alert('删除失败');window.location.href='index.php';</script>";
			}
			
			mysqli_close($conn);

			break;

		case 'reply':
			$reply_content=$_POST['reply_content'];
			
			$reply_content = antixss($reply_content);//xss waf

			$create_time=date("Y-m-d H:i:s",time());

			$message_id=$_GET['message_id'];
			session_start();
			$reply_name=$_SESSION['username'];

			$sql_mname="select message_name from message where message_id=".$message_id;
			$result_mname=mysqli_query($conn,$sql_mname);
			$message_Name=mysqli_fetch_assoc($result_mname);
			foreach($message_Name as $message_name){}		

			$sql_id="select id from user where username='{$reply_name}'";
			$result=mysqli_query($conn,$sql_id);
			$ID=mysqli_fetch_assoc($result);
			foreach($ID as $id){}//获取留言用户id
			
			
			$sql="insert into reply(reply_content,message_name,create_time,message_id,id,reply_name) values('{$reply_content}','{$message_name}','{$create_time}',{$message_id},{$id},'{$reply_name}')";
			mysqli_query($conn,$sql);

			if(mysqli_affected_rows($conn)>0){
				echo "<script>alert('回复成功');window.location.href='index.php';</script>";
			}else{
				echo "<script>alert('回复失败');window.location.href='reply.php?a=reply&message_id={$message_id}';</script>";
			}

			mysqli_close($conn);

			break;

		case 'delete_r':
			$sql="delete from reply where reply_id=".$_GET['reply_id'];
			mysqli_query($conn,$sql);
			if(mysqli_affected_rows($conn)>0){
				echo "<script>alert('删除成功');window.location.href='index.php';</script>";
			}else{
				echo "<script>alert('删除失败');window.location.href='index.php';</script>";
			}
			
			mysqli_close($conn);

			break;

		case 'updateuser':
		    session_start();
			$username_cookie=$_SESSION['username'];
			$username=trim($_POST['username']);
			$userpass=$_POST['userpass'];
			$reuserpass=$_POST['reuserpass'];
			//sql waf
			$white_list = "/^[A-Za-z0-9_\-]+$/im";
			if(!preg_match($white_list, $username)) die("<script>alert('非法字符');window.location.href='register.php';</script>");
			if(!preg_match($white_list, $userpass)) die("<script>alert('非法字符');window.location.href='register.php';</script>");
			
			$sql_user="select * from user where username = '{$username}'";
			if(isset($username)&&isset($userpass)){
			    $result=mysqli_query($conn,$sql_user);
			}else{
			    die("<script>alert('错误');window.location.href='editinfo.php';</script>");
			}
			$row=mysqli_fetch_assoc($result);

			if(empty($userpass)){
				echo "<script>alert('密码不能为空，请输入原始密码或重置密码');window.location.href='editinfo.php';</script>";
			}

			if($userpass!==$reuserpass){
				echo "<script>alert('两次密码不一致');window.location.href='editinfo.php';</script>";
			}

			if($username===$username_cookie){
				$userpass=md5($userpass);
				$sql="update user set username='{$username}',userpass='{$userpass}' where username='{$username_cookie}'";
				mysqli_query($conn,$sql);	
				if(mysqli_affected_rows($conn)>0){
					setcookie("username",null);
					setcookie("username",$username);
					echo "<script>alert('修改成功');window.location.href='index.php';</script>";
				}else{
					echo "<script>alert('密码未修改');window.location.href='index.php';</script>";
				}
			}else{
				if(@mysqli_num_rows($result)>0){
					echo "<script>alert('用户名已存在');</script>";
				}else{
					$userpass=md5($userpass);
					$sql="update user set username='{$username}',userpass='{$userpass}' where username='{$username_cookie}'";
					mysqli_query($conn,$sql);	
					if(mysqli_affected_rows($conn)>0){
						setcookie("username",null);
						setcookie("username",$username);
						echo "<script>alert('修改成功,请重新登录');window.location.href='action.php?a=quit';</script>";
					}else{
						echo "<script>alert('密码未修改');window.location.href='index.php';</script>";
					}
				}
			}

			
			

			break;
		case 'updateinfo':
		    session_start();
			$username_cookie=$_SESSION['username'];
			$myfile=$_FILES['imgpath'];
			//设置上传图片大小
			if($myfile['size']>2*1024*1024){
				echo "<script>alert('文件不能大于2M');window.location.href='register.php';</script>";
			}
			//设置上传图片类型
			$arr=array("image/jpg","image/jpeg","image/png","image/gif");
			if(!in_array($myfile['type'],$arr)){
				echo "<script>alert('文件类型有误');window.location.href='register.php';</script>";
			}


			do{
				$name=time().mt_rand(1000,9999).".jpg";
			}while(file_exists("./img/".$name));

			if(move_uploaded_file($myfile['tmp_name'],"img/".$name)){
				$imgpath=$name;
				$sql="update user set imgpath='{$imgpath}' where username='{$username_cookie}'";
				mysqli_query($conn,$sql);
				header("location:editinfo.php");

			}else{
				echo "<script>alert('头像上传失败');window.location.href='editinfo.php';</script>";
			}
			break;
	}
