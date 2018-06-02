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

session_start();

if($_SESSION['user_type'] == 'manager' && $_SERVER['REQUEST_METHOD'] == 'POST') {
	$conn = dbconnect($host, $dbid, $dbpass, $dbname);

	$num = $_SESSION['user_num'];
	$title = $_POST['title'];
	$content = $_POST['content'];
	
	//채용공고 등록
	$stmt = $conn->prepare("INSERT INTO notice(notice_title, notice_content, manager_num) VALUES(?, ?, ?)");
	$stmt->bind_param("ssi", $title, $content, $num);
	$ret = $stmt->execute();
	
	if(!$ret)
	    msg('Query Error : '. mysqli_error($stmt->error));
	else {
	    s_msg ('채용 공고 등록 성공');
		    
	    echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
	}

	$stmt->close();
	$conn->close();
	
	echo "<meta http-equiv='refresh' content='0;url=../index.php' />";
}
?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">채용공고 등록</h2>

	<form name="notice_form" id="notice_form" action="" method="post" class="form-horizontal">
		<div class="form-group">
		    <label>회사</label>
		    <label><?=$_SESSION['company_name']?></label>
		</div>

		<div class="form-group">
		    <label>담당자 명</label>
		    <label><?=$_SESSION['user_name']?></label>
		</div>

		<div class="form-group">
		    <label>채용공고 제목</label>
			<div>
				<input type="text" class="form-control" name="title" id="title" placeholder="제목을 입력해주세요." autofocus/>
			</div>
		</div>
		
		<div class="form-group">
		    <label>채용공고 내용</label>
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