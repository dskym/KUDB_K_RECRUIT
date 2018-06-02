<? include ("../header.php"); ?>

<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET') {

	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$application_num = $_GET['application_num'];

	$stmt = $conn->prepare("SELECT application_title, application_content, applicant_name, apply_result FROM applicant NATURAL JOIN apply NATURAL JOIN (SELECT application_num, applicant_num, application_title, application_content FROM application WHERE application_num=?) as application");
	
	$stmt->bind_param("i", $application_num);
	$stmt->execute();
    $stmt->bind_result($application_title, $application_content, $applicant_name, $apply_result);
	$stmt->fetch();
	
?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">지원서 정보</h2>

	<form name="information_form" action="" class="form-horizontal" method="post">
		<table class="table table-bordered">
			<tr>
				<th>번호</th>
				<td><?=$application_num?></td>
			</tr>
			<tr>
				<th>제목</th>
				<td><?=$application_title?></td>
			</tr>
			<tr>
				<th>내용</th>
				<td><?=$application_content?></td>
			</tr>
			<tr>
				<th>지원자</th>
				<td><?=$applicant_name?></td>
			</tr>
		
		<?php
			$user_type = $_SESSION['user_type'];
			
			if($user_type == 'applicant') {
		?>
		</table>
		<div class="form-group">
			<a class="btn btn-outline-primary" href="./application_update.php?application_num=<?=$application_num?>" role="button">수정</a>
			<a class="btn btn-outline-primary" href="./application_delete.php?application_num=<?=$application_num?>" role="button">삭제</a>
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
		<?php
				
			}
			else if($user_type == 'manager') {
				if($apply_result == 'Process') {
		?>
			<tr>
				<th>결과</th>
				<td>
					<select name="apply_result" class="form-control">
						<option value="Pass">Pass</option>
						<option value="Process" selected>Process</option>
						<option value="Fail">Fail</option>
					</select>
				</td>
			</tr>
		</table>
		<div class="form-group">
			<input class="btn btn-outline-primary" type="submit" value="수정" />
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
		<?php
				}
				else {
		?>
			<tr>
				<th>결과</td>
				<td><?=$apply_result?></td>
			</tr>
		</table>
		<div class="form-group">
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
		<?php
				}
			}
		?>
	</form>
</div>
<?php
}
else if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);
	
	if($_SESSION['user_type'] == 'manager') {
		$application_num = $_GET['application_num'];
		$notice_num = $_GET['notice_num'];
		$apply_result = $_POST['apply_result'];
		
		$stmt = $conn->prepare("SELECT manager_num FROM notice WHERE notice_num=(SELECT notice_num FROM apply WHERE application_num=?)");
		$stmt->bind_param("i", $manager_num);
		$stmt->execute();
	    $stmt->bind_result($user_num);
		$stmt->fetch();
	
		$stmt->reset();

		if($user_num == $manager_num) {
			$stmt = $conn->prepare("UPDATE apply SET apply_result=? WHERE application_num=? and notice_num=?");
			$stmt->bind_param("sii", $apply_result, $application_num, $notice_num);
			$ret = $stmt->execute();
		
			if(!$ret)
			    msg('Query Error : '. mysqli_error($stmt->error));
			else
			    s_msg ('결과 수정 성공');
			    
			echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
		}
		else {
		   msg('권한이 없습니다.');
		}
	}
}
?>

<? include ("../footer.php"); ?>
