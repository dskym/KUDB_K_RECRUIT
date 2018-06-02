<? include ("../header.php"); ?>

<script type="text/javascript">
	$(document).ready(function() {
	    
		$("form#apply_form").bind("submit", function () {
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

session_start();

if($_SESSION['user_type'] == 'applicant' && $_SERVER['REQUEST_METHOD'] == 'POST') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$notice_num = $_GET['num'];
	$num = $_SESSION['user_num'];

	$title = $_POST['title'];
	$content = $_POST['content'];
	
	//지원서 등록
	$stmt = $conn->prepare("INSERT INTO application(application_title, application_content, applicant_num) VALUES(?, ?, ?)");
	$stmt->bind_param("ssi", $title, $content, $num);
	$stmt->execute();
	
	$stmt->reset();

	//지원서 번호 검색
	$stmt = $conn->prepare("SELECT application_num FROM application where application_title=?");
	$stmt->bind_param("s", $title);
	$stmt->execute();
    $stmt->bind_result($application_num);
	$stmt->fetch();

	$stmt->reset();

	//지원서 - 채용공고 연결i
	$stmt = $conn->prepare("INSERT INTO apply(notice_num, application_num) VALUES(?, ?)");
	$stmt->bind_param("ii", $notice_num, $application_num);
	$ret = $stmt->execute();
	
	if(!$ret)
	    msg('Query Error : '. mysqli_error($stmt->error));
	else {
	    s_msg ('지원서 등록 성공');
		    
	    echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
	}

	$stmt->close();
	$conn->close();
	
	echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
}
?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">지원서 작성</h2>

	<form name="apply_form" id="apply_form" class="form-horizontal" action="" method="post">
		<div class="form-group">
		    <label>지원서 제목</label>
			<div>
				<input type="text" class="form-control" name="title" id="title" placeholder="제목을 입력해주세요." autofocus />
			</div>
		</div>
		<div class="form-group">
		    <label>지원서 내용</label>
			<div>
				<textarea class="form-control" name="content" id="content" placeholder="내용을 입력해주세요."></textarea>
			</div>
		</div>
		<div class="form-group">
			<input class="btn btn-outline-primary" type="submit" value="완료" />
			<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
		</div>
	</form>
</div>

<? include ("../footer.php"); ?>