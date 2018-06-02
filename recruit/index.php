<? include ("header.php"); ?>

<?php
include "config.php";    //데이터베이스 연결 설정파일
include "util.php";      //유틸 함수

session_start();

if(isset($_SESSION['user_name'])) {
?>

<div class="container border border-info p-3 mb-3">
	<div class="form-group">
	    <label><?=$_SESSION['user_name']?>(<?=$_SESSION['user_type']?>)님 환영합니다.</label>
	</div>

	<div class="form-group">
		<a class="btn btn-outline-primary" href="./user/user_info.php" role="button">회원정보</a>
		<a class="btn btn-outline-primary" href="./logout.php" role="button">로그아웃</a>
		<a class="btn btn-outline-primary" href="./user/user_delete.php" role="button">회원탈퇴</a>
	</div>
</div>

<? include('./notice/notice_search.php'); ?>

<? include('./application/application_search.php'); ?>

<?php
}
else
	include ("login.php");
?>

<? include ("footer.php"); ?>