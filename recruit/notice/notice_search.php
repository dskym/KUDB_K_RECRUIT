<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

session_start();

$conn = dbconnect($host, $dbid, $dbpass, $dbname);

?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">채용공고</h2>
	
	<table class="table table-bordered table-hover">
		<thead class="thead-light">
			<tr>
				<th>번호</th>
				<th>제목</th>
				<th>작성자</tH>
			</tr>
		</thead>
		<tbody>

<?php
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$title = "%{$_POST['title']}%";

		$stmt = $conn->prepare("SELECT notice_num, notice_title, manager_name FROM notice NATURAL JOIN manager WHERE notice_title LIKE ?");
	    $stmt->bind_param("s", $title);
		$stmt->execute();
	    $stmt->bind_result($notice_num, $notice_title, $manager_name);
	    
	}
	else if($_SERVER['REQUEST_METHOD'] == 'GET'){
		$stmt = $conn->prepare("SELECT notice_num, notice_title, manager_name FROM notice NATURAL JOIN manager");
		$stmt->execute();
	    $stmt->bind_result($notice_num, $notice_title, $manager_name);
	}
	
	while($row = $stmt->fetch()) {
?>
			<tr>
				<td><?=$notice_num?></td>
				<td><a href="./notice/notice_detail.php?num=<?=$notice_num?>"><?=$notice_title?></a></td>
				<td><?=$manager_name?></td>
			</tr>
<?php

	}
	
    $stmt->reset();
?>
		</tbody>
	</table>
	
	<form name="search_form" class="form-horizontal mb-3" action="" method="POST">
		<div class="input-group">
			<input type="text" class="form-control" name="title" placeholder="채용공고 제목" autofocus />
			<input class="btn btn-outline-primary ml-3" type="submit" value="검색" />
		</div>
	</form>

	<?php
		if($_SESSION['user_type'] == 'manager') {
	?>
	
			<div class="form-group" align="right">
				<a class="btn btn-outline-primary" href="./notice/notice_insert.php" role="button">등록</a>
			</div>
	
	<?php
		}
	?>
	
</div>
