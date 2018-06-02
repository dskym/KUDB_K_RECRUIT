<? include ("header.php"); ?>

<script type="text/javascript">
	$(document).ready(function() {
	    
		$("form#register_form").bind("submit", function () {
			if ($("input#name").val().trim() == "") {
				alert("이름을 입력해 주세요.");
			
				$("input#name").focus();
					return false;
			}

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
		
		var checkedValue = $('input:radio[name="type"]:checked').val();
		
		if(checkedValue == 'applicant')
			$("#company").hide();
		else if(checkedValue == 'manager')
			$("#company").show();
		
	    $('input:radio[name="type"]').change(function() {
	        if (this.value == 'applicant') {
				$("#company").hide();
	        }
	        else if (this.value == 'manager') {
				$("#company").show();
	        }
	    });
	});
</script>

<?php
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";      //유틸 함수

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	if($_POST['type'] == 'applicant') {
		//전체적인 null check 필요
		
		$name = $_POST['name'];
		//id 중복검사 필요
		$id = $_POST['id'];
		$pw = $_POST['pw'];
		$pw = hash("sha256", $pw);

		$stmt = $conn->prepare("INSERT INTO applicant(applicant_name, applicant_id, applicant_pw) VALUES(?, ?, ?)");
		$stmt->bind_param("sss", $name, $id, $pw);
		$ret = $stmt->execute();
		
		if(!$ret)
		    msg('Query Error : '. mysqli_error($stmt->error));
		else
		    s_msg ('회원가입 성공');
	}
	else if($_POST['type'] == 'manager') {
		$name = $_POST['name'];
		$company = $_POST['company'];
		//id 중복검사 필요
		$id = $_POST['id'];
		$pw = $_POST['pw'];
		$pw = hash("sha256", $pw);
		
		$stmt = $conn->prepare("SELECT company_num FROM company where company_name=?");
		$stmt->bind_param("s", $company);
		$stmt->execute();
	    $stmt->bind_result($company_num);
		$stmt->fetch();
		
		$stmt->reset();

		$stmt = $conn->prepare("INSERT INTO manager(manager_name, manager_id, manager_pw, company_num) VALUES(?, ?, ?, ?)");
		$stmt->bind_param("sssi", $name, $id, $pw, $company_num);
		$ret = $stmt->execute();
		
		if(!$ret)
		    msg('Query Error : '. mysqli_error($stmt->error));
		else
		    s_msg ('회원가입 성공');
	}
	
	$stmt->close();
	$conn->close();

	echo "<meta http-equiv='refresh' content='0;url=index.php' />";
}
?>

<div class="container border border-info p-3 w-50">
	<form name="register_form" class="form-horizontal" id="register_form" action="" method="post">
		<div class="form-group">
			<input type="radio" name="type" value="applicant" checked /> 지원자
			<input type="radio" name="type" value="manager" /> 인사담당자
		</div>
		<div class="form-group">
		    <label>이름</label>
			<div>
				<input type="text" class="form-control" name="name" id="name" placeholder="이름" autofocus />
			</div>
		</div>
		<div class="form-group" id="company">
		    <label>회사</label>
			<select name="company" class="form-control">
			<?php
				$conn = dbconnect($host, $dbid, $dbpass, $dbname);
				
				$stmt = $conn->prepare("SELECT company_name FROM company");
				$stmt->execute();
			    $stmt->bind_result($company_name);
			    
				while($row = $stmt->fetch()) {
			?>
			<option value=<?=$company_name?>><?=$company_name?></option>
			<?php
				}
				
				$stmt->close();
				$conn->close();
			?>
			</select>
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
			<input class="btn btn-outline-primary" type="submit" value="완료" />
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
	</form>
</div>

<? include ("footer.php"); ?>
