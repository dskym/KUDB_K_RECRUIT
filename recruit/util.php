<?php
function dbconnect($host, $id, $pass, $db)  //데이터베이스 연결
{
    $conn = new mysqli($host, $id, $pass, $db);

    if ($conn->connect_error) {
        die('Not connected : ' . $conn->connect_error);
    }

    return $conn;
}

function msg($msg) // 경고 메시지 출력 후 이전 페이지로 이동
{
    echo "
        <script>
             window.alert('$msg');
             history.go(-1);
        </script>";
    exit;
}

function s_msg($msg) //일반 메시지 출력
{
    echo "
        <script>
            window.alert('$msg');
        </script>";
}

?>
