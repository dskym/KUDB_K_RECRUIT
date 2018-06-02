<? include ("../header.php"); ?>

<?php
include "../config.php";    //데이터베이스 연결 설정파일
include "../util.php";      //유틸 함수

session_start();

if(isset($_SESSION['user_type'])) {
?>

<div class="container border border-info p-3 mb-3">
	<h2 style="text-align:center" class="mb-3">회원 정보</h2>
	<table class="table table-bordered">
		<tr>
			<th>이름</th>
			<td><?=$_SESSION['user_name']?></td>
		</tr>
		<tr>
			<th>회원 유형</th>
			<td>
				<?php
					$user_type = $_SESSION['user_type'];
					
					if($user_type == 'applicant')
						echo "지원자";
					else if($user_type == 'manager')
						echo "인사담당자";
				?>
			</td>
		</tr>
		<?php
			if($_SESSION['user_type'] == 'manager') {
		?>
				<tr>
					<th>소속 회사</td>
					<td><?=$_SESSION['company_name']?></td>
				</tr>
		<?php
			}
		?>
	</table>
	<div class="form-group">
		<a class="btn btn-outline-primary" href="./user_update.php" role="button">수정</a>
		<a class="btn btn-outline-primary" href="javascript:history.back()" role="button">뒤로</a>
	</div>
</div>

<?php
}
else {
    msg('잘못된 접근입니다.');
}
?>

<? include ("../footer.php"); ?>