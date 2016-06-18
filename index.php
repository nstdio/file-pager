<?php
require_once __DIR__ . '/vendor/autoload.php';

use nstdio\FilePager;

$fileName = "FILENAME";

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 30;

$filePager = new FilePager($fileName, $pageSize);
$filePager->append("\n");
$filePager->append("===== {page} =====\n");
$filePager->append("\n");

echo "<pre>";
echo $filePager->getRange($page, $page + 20);
