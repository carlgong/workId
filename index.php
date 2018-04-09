
<html lang="zh">
<title>电子工牌制作</title>
<style>
    body {
        width: 35em;
        margin: 0 auto;
        font-family: Tahoma, Verdana, Arial, sans-serif;
    }
</style>
<body>
<h1>Welcome to workId!</h1>

<form action="handle.php" name="form" method="post" enctype="multipart/form-data">
 <table>
 <tr> <td>请选择图片<input type="file" name="file" /></td></tr>
 <tr><td> 员工名称<input type="text" name="ename"/></td></tr>
 <tr><td> 员工工号<input type="text" name="eId"/></td></tr>
 <tr><td> <input type="submit" name="submit" value="上传" /></tr>

 </table>

<?php
header("Content-Type: text/html;charset=utf-8");
 $filename = $_GET['filename'];

 $contect =  substr($_SERVER['REQUEST_URI'],0,strripos($_SERVER['REQUEST_URI'],"/"));
 $str = $contect."/out/".$filename;
if(file_exists("./out/".$filename)){
echo " <p> 生成图像:</p>";
  echo "<img width=300 src=".$str."  alt='上海鲜花港 - 郁金香' />";
  echo "<p></p>";
 //$url='http://'.$_SERVER['HTTP_HOST'].$contect."/download.php?filename=".$str;
  echo "<a href='download.php?filename=".$filename."'>下载图片</a>";
}
?>
</form>
</body>
</html>