<?php
//获取要下载的文件名
$filename = $_GET['filename'];
//设置头信息
header('Content-Disposition:attachment;filename=' . basename($filename));
$localfile="./out/".$filename;
header('Content-Length:' . filesize($localfile));
//读取文件并写入到输出缓冲
readfile($localfile);