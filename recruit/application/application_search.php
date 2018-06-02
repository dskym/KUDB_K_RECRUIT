<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

session_start();

if(isset($_SESSION['user_type'])) {
	$user_type = $_SESSION['user_type'];
	
	//지원자 뷰
	if($user_type == 'applicant') {
?>
	
		<div class="container border border-info p-3 mb-3">
			<h2 style="text-align:center" class="mb-3">나의 지원 내역</h2>

			<table class="table table-bordered table-hover">
				<thead class="thead-light">
					<tr>
						<th>번호</th>
						<th>회사명</th>
						<th>채용공고 제목</th>
						<th>지원서 제목</th>
						<th>결과</td></th>
					</tr>
				</thead>
				<tbody>
		<?php
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$title = "%{$_POST['title']}%";
		
				$stmt = $conn->prepare("SELECT notice_title, company_name, application_num, application_title, apply_result FROM company NATURAL JOIN (SELECT notice_title, company_num, application_num, application_title, apply_result FROM manager NATURAL JOIN (SELECT notice_title, manager_num, application_num, application_title, apply_result FROM notice NATURAL JOIN (SELECT application_num, application_title, apply_result, notice_num FROM apply NATURAL JOIN (SELECT application_num, application_title FROM application WHERE application_title LIKE ?) as application) as apply) as notice) as manager");
			    $stmt->bind_param("s", $title);
				$stmt->execute();
			    $stmt->bind_result($notice_title, $company_name, $application_num, $application_title, $apply_result);
			    
			}
			else if($_SERVER['REQUEST_METHOD'] == 'GET'){
				$num = $_SESSION['user_num'];
			
				$stmt = $conn->prepare("SELECT notice_title, company_name, application_num, application_title, apply_result FROM company NATURAL JOIN (SELECT application_num, application_title, apply_result, notice_title, company_num from manager NATURAL JOIN (SELECT application_num, application_title, apply_result, notice_title, manager_num FROM notice NATURAL JOIN (SELECT application_num, application_title, notice_num, apply_result FROM apply NATURAL JOIN (SELECT application_num, application_title FROM application WHERE applicant_num=?) as application) as notice) as manager) as company");
			    $stmt->bind_param("i", $num);
				$stmt->execute();
			    $stmt->bind_result($notice_title, $company_name, $application_num, $application_title, $apply_result);
			}

			while($row = $stmt->fetch()) {
		?>
				<tr>
					<td><?=$application_num?></td>
					<td><?=$company_name?></td>
					<td><?=$notice_title?></td>
					<?php
						if($apply_result == 'Process') {
					?>
						<td>
							<a href="./application/application_detail.php?application_num=<?=$application_num?>"><?=$application_title?></a>
						</td>
					<?php
						}
						else {
					?>
					<td><?=$application_title?></td>
					<?php
						}
					?>
					<td><?=$apply_result?></td>
				</tr>
		<?php
			}
		?>
				</tbody>
			</table>
			
			<form name="search_form" class="form-horizontal mb-3" action="" method="POST">
				<div class="input-group">
					<input type="text" class="form-control" name="title" placeholder="지원서 제목" autofocus />
					<input class="btn btn-outline-primary ml-3" type="submit" value="검색" />
				</div>
			</form>
		</div>
	
<?php
	}
	//인사담당자 뷰
	else if($user_type == 'manager') {
?>
		<div class="container border border-info p-3 mb-3">
			<h2 style="text-align:center" class="mb-3">회사 지원 내역</h2>
			<table class="table table-bordered table-hover">
				<thead class="thead-light">
					<tr>
						<th>번호</td>
						<th>채용공고 제목</td>
						<th>지원서 제목</td>
						<th>지원자 명</td>
						<th>결과</td>
					</tr>
				</thead>
				<tbody>
		<?php
			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$title = "%{$_POST['title']}%";
		
				$stmt = $conn->prepare("SELECT notice_num, notice_title, application_num, application_title, applicant_name, apply_result FROM notice NATURAL JOIN (SELECT application_num, application_title, applicant_name, apply_result, notice_num FROM apply NATURAL JOIN (SELECT application_num, application_title, applicant_name FROM applicant NATURAL JOIN (SELECT application_num, application_title, applicant_num FROM application WHERE application_title LIKE ?) as application) as applicant) as apply");
			    $stmt->bind_param("s", $title);
				$stmt->execute();
			    $stmt->bind_result($notice_num, $notice_title, $application_num, $application_title, $applicant_name, $apply_result);
			    
			}
			else if($_SERVER['REQUEST_METHOD'] == 'GET'){
				$num = $_SESSION['user_num'];
			
				$stmt = $conn->prepare("SELECT notice_num, notice_title, application_num, application_title, applicant_name, apply_result from applicant NATURAL JOIN (SELECT notice_num, notice_title, application_num, application_title, applicant_num, apply_result FROM application NATURAL JOIN (SELECT notice_num, notice_title, apply_result, application_num FROM apply NATURAL JOIN (SELECT notice_num, notice_title FROM notice WHERE manager_num=?) as notice) as apply) as application");
			    $stmt->bind_param("i", $num);
				$stmt->execute();
			    $stmt->bind_result($notice_num, $notice_title, $application_num, $application_title, $applicant_name, $apply_result);
			}

			while($row = $stmt->fetch()) {
		?>
				<tr>
					<td><?=$notice_num?></td>
					<td><?=$notice_title?></td>
					<td>
						<a href="./application/application_detail.php?application_num=<?=$application_num?>&notice_num=<?=$notice_num?>"><?=$application_title?></a>
					</td>
					<td><?=$applicant_name?></td>
					<td><?=$apply_result?></td>
				</tr>
		<?php
			}
		?>
				</tbody>
			</table>
			
			<form name="search_form" class="form-horizontal mb-3" action="" method="POST">
				<div class="input-group">
					<input type="text" class="form-control" name="title" placeholder="지원서 제목" autofocus />
					<input class="btn btn-outline-primary ml-3" type="submit" value="검색" />
				</div>
			</form>
		</div>
<?php
	}
}
?>