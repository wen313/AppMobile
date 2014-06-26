<?php
// 修改用户名和密码
$conn = @mysql_connect('localhost','baixintv','f6z6h3');
mysql_query("SET NAMES gbk");
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}
// 修改数据库名称
mysql_select_db('baixintv', $conn);

?>