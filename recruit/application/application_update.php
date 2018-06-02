<? include ("../header.php"); ?>

<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$application_num = $_GET['application_num'];

	$stmt = $conn->prepare("SELECT application_title, application_content, applicant_name FROM applicant NATURAL JOIN (SELECT applicant_num, application_title, application_content FROM application WHERE application_num=?)");
	$stmt->bind_param("i", $application_num);
	$stmt->execute();
    $stmt->bind_result($application_title, $application_content, $applicant_name);
	$stmt->fetch();
	
?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">지원서 수정</h2>

	<form name="update_form" class="form-horizontal" action="" method="post">
		<table class="table table-bordered">
			<tr>
				<th>번호</th>
				<td><?=$application_num?></td>
			</tr>
			<tr>
				<th>제목</th>
				<td>
					<input type="text" class="form-control" name="title" autofocus value="<?=$application_title?>" />
				</td>
			</tr>
			<tr>
				<th>내용</th>
				<td>
					<textarea class="form-control" name="content" value="<?=$application_content?>"></textarea>
				</td>
			</tr>
			<tr>
				<th>지원자</th>
				<td><?=$applicant_name?></td>
			</tr>
		</table>

		<div class="form-group">
			<input class="btn btn-outline-primary" type="submit" value="완료" />
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
	</form>
</div>

<?php
	$stmt->close();
	$conn->close();
}
else if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$application_num = $_GET['application_num'];
	$application_title = $_POST['title'];
	$application_content = $_POST['content'];

	$user_num = $_SESSION['user_num'];
	$user_type = $_SESSION['user_type'];

	$stmt = $conn->prepare("SELECT applicant_num FROM application WHERE application_num=?");
	$stmt->bind_param("i", $application_num);
	$stmt->execute();
    $stmt->bind_result($applicant_num);
	$stmt->fetch();
	
	$stmt->reset();

	if($user_type == 'applicant' && $user_num == $applicant_num) {
		$stmt = $conn->prepare("UPDATE application SET application_title=?, application_content=? WHERE application_num=?");
		$stmt->bind_param("ssi", $application_title, $application_content, $application_num);
		$ret = $stmt->execute();
		
		if(!$ret)
		    msg('Query Error : '. mysqli_error($stmt->error));
		else {
		    s_msg ('지원서 정보 수정 성공');
		
			echo "<meta http-equiv='refresh' content='0;url=./application_detail.php?application_num=$application_num' />";
		}
	}
	else {
	   msg('권한이 없습니다.');
	}
}
?>

<? include ("../footer.php"); ?>