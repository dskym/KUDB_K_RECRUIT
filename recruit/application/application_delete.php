<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	session_start();
	
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$application_num = $_GET['application_num'];
	$user_num = $_SESSION['user_num'];
	$user_type = $_SESSION['user_type'];
	
	$stmt = $conn->prepare("SELECT applicant_num FROM application WHERE application_num=?");
	$stmt->bind_param("i", $application_num);
	$stmt->execute();
    $stmt->bind_result($user_num);
	$stmt->fetch();

	$stmt->reset();

	if($user_type == 'applicant' && $user_num == $applicant_num) {
		$stmt = $conn->prepare("DELETE FROM application WHERE application_num=?");
		$stmt->bind_param("i", $application_num);
		$ret = $stmt->execute();
		
		if(!$ret)
		    msg('Query Error : '. mysqli_error($stmt->error));
		else
		    s_msg ('지원서 삭제 성공');
	}
	else {
	   msg('권한이 없습니다.');
	}
}

echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
?>
