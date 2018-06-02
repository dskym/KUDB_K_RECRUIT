<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	session_start();
	
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$num = $_GET['num'];
	$user_num = $_SESSION['user_num'];
	$user_type = $_SESSION['user_type'];
	
	$stmt = $conn->prepare("SELECT manager_num FROM notice WHERE notice_num=?");
	$stmt->bind_param("i", $num);
	$stmt->execute();
    $stmt->bind_result($manager_num);
	$stmt->fetch();

	$stmt->reset();

	if($user_type == 'manager' && $user_num == $manager_num) {
		$stmt = $conn->prepare("DELETE FROM notice WHERE notice_num=?");
		$stmt->bind_param("i", $num);
		$ret = $stmt->execute();

		if(!$ret)
		    msg('Query Error : '. mysqli_error($stmt->error));
		else
		    s_msg ('채용공고 삭제 성공');
	}
	else {
	   msg('권한이 없습니다.');
	}
}

echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
?>