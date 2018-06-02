<script type="text/javascript">
	$(document).ready(function() {
	    
		$("form#login_form").bind("submit", function () {
			if ($("input#id").val().trim() == "") {
				alert("아이디를 입력해 주세요.");
			
				$("input#id").focus();
					return false;
			}

			if ($("input#pw").val().trim() == "") {
				alert("비밀번호를 입력해 주세요.");
			
				$("input#pw").focus();
					return false;
			}
		});
	});
</script>

<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	include "config.php";    //데이터베이스 연결 설정파일
	include "util.php";      //유틸 함수
	
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$id = $_POST['id'];
	$pw = $_POST['pw'];
	$pw = hash("sha256", $pw);
	$type = $_POST['type'];
	
	if($type == 'applicant') {
		$stmt = $conn->prepare("SELECT applicant_num, applicant_name FROM applicant WHERE applicant_id=? and applicant_pw=?");
		$stmt->bind_param("ss", $id, $pw);
		$stmt->execute();
	    $stmt->bind_result($applicant_num, $applicant_name);
		$ret = $stmt->fetch();
		
		if(!$ret) {
		    msg('아이디 또는 비밀번호를 확인해주세요.');
		}
		else {
			session_start();
			
			$_SESSION['user_num'] = $applicant_num;
			$_SESSION['user_name'] = $applicant_name;
			$_SESSION['user_type'] = $type;
		}
	}
	else if($type == 'manager') {
		$stmt = $conn->prepare("SELECT manager_num, manager_name, company.company_num, company_name FROM manager INNER JOIN company ON manager.company_num=company.company_num WHERE manager_id=? and manager_pw=?");
		$stmt->bind_param("ss", $id, $pw);
		$stmt->execute();
	    $stmt->bind_result($manager_num, $manager_name, $company_num, $company_name);
		$ret = $stmt->fetch();
		
		if(!$ret) {
		    msg('아이디 또는 비밀번호를 확인해주세요.');
		}
		else {
			session_start();
			
			$_SESSION['user_num'] = $manager_num;
			$_SESSION['user_name'] = $manager_name;
			$_SESSION['company_num'] = $company_num;
			$_SESSION['company_name'] = $company_name;
			$_SESSION['user_type'] = $type;
		}
	}

	$stmt->close();
	$conn->close();
	
	echo "<meta http-equiv='refresh' content='0;url=index.php' />";
}
else {
?>

<div class="container border border-info p-3 mb-3 w-50">
	<form name="login_form" id="login_form" class="form-horizontal" action="./login.php" method="post">
		<div class="form-group">
			<input type="radio" name="type" value="applicant" checked /> 지원자
			<input type="radio" name="type" value="manager" /> 인사담당자
		</div>
		<div class="form-group">
		    <label>Username</label>
			<div>
				<input type="text" class="form-control" name="id" id="id" placeholder="Username" autofocus />
			</div>
		</div>
		<div class="form-group">
		    <label>Password</label>
			<div>
				<input type="password" class="form-control" name="pw" id="pw" placeholder="Password" />
			</div>
		</div>
		<div class="form-group" align="center">
			<a class="btn btn-outline-primary" href="./register.php" role="button">회원가입</a>
			<input class="btn btn-outline-primary" type="submit" value="로그인" />
		</div>
	</form>
</div>

<?php
}
?>