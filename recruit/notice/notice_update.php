<? include ("../header.php"); ?>

<script type="text/javascript">
	$(document).ready(function() {
	    
		$("form#notice_form").bind("submit", function () {
			if ($("input#title").val().trim() == "") {
				alert("제목을 입력해 주세요.");
			
				$("input#title").focus();
					return false;
			}

			if ($("input#content").val().trim() == "") {
				alert("내용을 입력해 주세요.");
			
				$("input#content").focus();
					return false;
			}
		});
	});
</script>

<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

if($_SERVER['REQUEST_METHOD'] == 'GET') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$num = $_GET['num'];

	$stmt = $conn->prepare("SELECT notice_title, notice_content, manager_name, company_name FROM manager NATURAL JOIN company NATURAL JOIN (SELECT notice_title, notice_content, manager_num, notice_num FROM notice WHERE notice_num=?) as notice");
	//$stmt = $conn->prepare("SELECT notice_title, notice_content, manager_name, company_name FROM notice NATURAL JOIN enroll NATURAL JOIN manager NATURAL JOIN company WHERE notice_num=?");
	$stmt->bind_param("i", $num);
	$stmt->execute();
    $stmt->bind_result($notice_title, $notice_content, $manager_name, $company_name);
	$stmt->fetch();
?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">채용공고 수정</h2>

	<form name="update_form" class="form-horizontal" action="" method="POST">
		<table class="table table-bordered">
			<tr>
				<th>번호</th>
				<td><?=$num?></td>
			</tr>
			<tr>
				<th>제목</th>
				<td>
					<input type="text" class="form-control" name="title" autofocus value="<?=$notice_title?>" />
				</td>
			</tr>
			<tr>
				<th>내용</th>
				<td>
					<textarea class="form-control" name="content"><?=$notice_content?></textarea>
				</td>
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
		
		<div class="form-group">
			<input class="btn btn-outline-primary" type="submit" value="완료" />
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
	</form>
</div>
<?php
}
else if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$notice_num = $_GET['num'];
	$notice_title = $_POST['title'];
	$notice_content = $_POST['content'];
	
	$user_num = $_SESSION['user_num'];
	$user_type = $_SESSION['user_type'];

	$stmt = $conn->prepare("SELECT manager_num FROM notice WHERE notice_num=?");
	$stmt->bind_param("i", $notice_num);
	$stmt->execute();
    $stmt->bind_result($manager_num);
	$stmt->fetch();
	
	$stmt->reset();

	if($user_type == 'manager' && $user_num == $manager_num) {
		$stmt = $conn->prepare("UPDATE notice SET notice_title=?, notice_content=? WHERE notice_num=?");
		$stmt->bind_param("ssi", $notice_title, $notice_content, $notice_num);
		$ret = $stmt->execute();
	
		if(!$ret)
		    msg('Query Error : '. mysqli_error($stmt->error));
		else {
		    s_msg ('채용 공고 정보 수정 성공');
		    
		    echo "<meta http-equiv='refresh' content='0;url=./notice_detail.php?num=$notice_num' />";
		}
	}
	else {
	   msg('권한이 없습니다.');
	}

}
?>

<? include ("../footer.php"); ?>
