<?php
require_once __DIR__ . '/vendor/autoload.php';

use nstdio\FilePaginator;

$fileName = "C:\\Users\\Asatryan\\Desktop\\qconsole.log";

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 30;

$filePaginator = new FilePaginator("C:\\Users\\Asatryan\\Desktop\\qconsole.log", $pageSize);
$filePaginator->append("\n");
$filePaginator->append("===== {page} =====\n");
$filePaginator->append("\n");

echo "<pre>";
echo $filePaginator->getRange($page, $page + 20);
