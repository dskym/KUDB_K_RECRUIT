<? include ("../header.php"); ?>

<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	if(isset($_SESSION['user_type'])) {
	?>
	<div class="container border border-info p-3 mb-3">
		<h2 style="text-align:center" class="mb-3">회원 정보 수정</h2>
		
	<form name="update_form" class="form-horizontal" action="" method="POST">
		<table class="table table-bordered">
			<tr>
				<th>이름</th>
				<td><?=$_SESSION['user_name']?></td>
			</tr>
			<tr>
				<th>비밀번호</th>
				<td>
					<input type="password" class="form-control" name="name" placeholder="Password" autofocus />
				</td>
			</tr>
			<tr>
				<th>회원 유형</th>
				<td>
					<?php
						$user_type = $_SESSION['user_type'];
						
						if($user_type == 'applicant')
							echo "지원자";
						else if($user_type == 'manager')
							echo "인사담당자";
					?>
				</td>
			</tr>
			<?php
				if($_SESSION['user_type'] == 'manager') {
			?>
					<tr>
						<th>소속 회사</th>
						<td><?=$_SESSION['company_name']?></td>
					</tr>
			<?php
				}
			?>
		</table>
		
		<div class="form-group">
			<input class="btn btn-outline-primary" type="submit" value="완료" />
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
	</form>
	<?php
	}
	else {
	    msg('잘못된 접근입니다.');
	}
}
else if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_SESSION['user_type'])) {
		$conn = dbconnect($host, $dbid, $dbpass, $dbname);
	
		$pw = $_POST['pw'];
		$pw = hash("sha256", $pw);
		$user_type = $_SESSION['user_type'];
		$user_num = $_SESSION['user_num'];

		if($user_type == 'applicant') {
			$stmt = $conn->prepare("UPDATE applicant SET applicant_pw=? WHERE applicant_num=?");
		}
		else if($user_type == 'manager') {
			$stmt = $conn->prepare("UPDATE manager SET manager_pw=? WHERE manager_num=?");
		}
		
		$stmt->bind_param("si", $pw, $user_num);
		$ret = $stmt->execute();
		
		if(!$ret)
		    msg('Query Error : '. mysqli_error($stmt->error));
		else {
		    s_msg ('회원 정보 수정 성공');

		    echo "<meta http-equiv='refresh' content='0;url=./user_info.php' />";
		}
	}
	else {
	    msg('잘못된 접근입니다.');
	}
}
?>

<? include ("../footer.php"); ?>