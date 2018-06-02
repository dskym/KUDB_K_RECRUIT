<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

session_start();

if(isset($_SESSION)) {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$user_num = $_SESSION['user_num'];
	$user_type = $_SESSION['user_type'];

	if($user_type == 'applicant')
		$stmt = $conn->prepare("DELETE FROM applicant WHERE applicant_num=?");
	else if($user_type == 'manager')
		$stmt = $conn->prepare("DELETE FROM manager WHERE manager_num=?");
		
	$stmt->bind_param("i", $user_num);
	$ret = $stmt->execute();
		
	if(!$ret)
	    msg('Query Error : '. mysqli_error($stmt->error));
	else
	    s_msg ('회원 정보 삭제 완료');

	$stmt->close();
	$conn->close();
	
	session_destroy();
}

echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
?>