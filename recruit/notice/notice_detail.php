<? include ("../header.php"); ?>

<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	session_start();
	
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$num = $_GET['num'];

	$stmt = $conn->prepare("SELECT notice_title, notice_content, manager_name, company_name FROM manager NATURAL JOIN company NATURAL JOIN (SELECT notice_title, notice_content, manager_num, notice_num FROM notice WHERE notice_num=?) as notice");
	$stmt->bind_param("i", $num);
	$stmt->execute();
    $stmt->bind_result($notice_title, $notice_content, $manager_name, $company_name);
	$stmt->fetch();
	
?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">채용공고 정보</h2>
	
	<table class="table table-bordered">
		<tr>
			<th>번호</th>
			<td><?=$num?></td>
		</tr>
		<tr>
			<th>제목</th>
			<td><?=$notice_title?></td>
		</tr>
		<tr>
			<th>내용</th>
			<td><?=$notice_content?></td>
		</tr>
		<tr>
			<th>담당자</th>
			<td><?=$manager_name?></td>
		</tr>
		<tr>
			<th>회사</th>
			<td><?=$company_name?></td>
		</tr>
	</table>

	<?php
		$user_type = $_SESSION['user_type'];
		
		if($user_type == 'applicant') {
	?>
		<div class="form-group">
			<a class="btn btn-outline-primary" href="../application/application_insert.php?num=<?=$num?>" role="button">지원</a>
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
	<?php
			
		}
		else if($user_type == 'manager') {
	?>
		<div class="form-group">
			<a class="btn btn-outline-primary" href="./notice_update.php?num=<?=$num?>" role="button">수정</a>
			<a class="btn btn-outline-primary" href="./notice_delete.php?num=<?=$num?>" role="button">삭제</a>
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
	<?php
		}
	?>
	</table>
	
	<?php
			
		$stmt->close();
		$conn->close();
	}
	?>
</div>

<? include ("../footer.php"); ?>

