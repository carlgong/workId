<?php
/**
 * Created by PhpStorm.
 * User: 帅
 * Date: 2017/2/24
 * Time: 11:49
 */
header('content-type:text/html;charset=utf-8');
$baseImage = './images/mm.jpg';    //背景图片
$avatarImage = './images/avatar.jpg';    //用户头像图片

$file = $_FILES['file'];//得到传输的数据
//得到文件名称
$name = $file['name'];
$type = strtolower(substr($name,strrpos($name,'.')+1)); //得到文件类型，并且都转化成小写
$allow_type = array('jpg','jpeg','gif','png'); //定义允许上传的类型
//判断文件类型是否被允许上传
if(!in_array($type, $allow_type)){
  //如果不被允许，则直接停止程序运行
  return ;
}
//判断是否是通过HTTP POST上传的
if(!is_uploaded_file($file['tmp_name'])){
  //如果不是通过HTTP POST上传的
  return ;
}

$str = $_POST['ename'];
$num=$_POST['eId'];

$upload_path = "./images/"; //上传文件的存放路径
//开始移动文件到相应的文件夹
if(move_uploaded_file($file['tmp_name'],$upload_path.$file['name'])){
  echo "Successfully!";
 $avatarImage= $upload_path.$file['name'];
}else{
  echo "Failed!";
}

/**
 * 生成图片数据函数
 * @param String $fileName 图片文件url
 * @return array array[0] = 图片资源, array[1] = 图片mime类型, array[2] = 图片宽度, array[3] = 图片高度
 */
function getCreateImageInfo($fileName) {
    list($width, $height, $imageTypeInt) = getimagesize($fileName);    //获取原图片的宽，高，类型
    $imageTypeArray = array(1=>'gif', 2=>'jpeg', 3=>'png');    //根据返回常量值定义图像类型数组
    $imageFrom = 'imagecreatefrom'.$imageTypeArray[$imageTypeInt];    //拼装创建图像图像函数，根据类型不同创建不同函数
    $image = $imageFrom($fileName);    //创建图片
    if(!$image)
    {
        //生成错误图片信息
        $image  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($image, 255, 255, 255);
        $tc  = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, 150, 30, $bgc);
        imagestring($image, 1, 5, 5, 'Error loading ' . $fileName, $tc);
        $imageOut = 'image'.$imageTypeArray[$imageTypeInt];    //根据图片类型不同来拼接图片输出函数
        header('Content-Type: image/'.$imageTypeArray[$imageTypeInt]);    //根据图片类型不同来拼接头信息
        $imageOut($image);    //输出图片
        imagedestroy($image);    //销毁资源
        return false;
    }
    return array($image, $imageTypeArray[$imageTypeInt], $width, $height);
}
/**
 * 生成正方形小图片函数
 * @param String $fileName 图片文件url
 * @param Int $imageSize 文件大小
 * @return array array[0] = 图片资源, array[1] = width ,array[2]=height
 */
function createSmallImage($fileName, $imagewidth =254 ,$imageHeight=380) {
    list($image, , $width ,$height) = getCreateImageInfo($fileName);
    if ($width != $imagewidth && $height != $imageHeight) {    //判断图片是否为指定大小的正方形图片
        //重新设定图片大小
        $tmpImage = imagecreatetruecolor(254, 380);
        imagecopyresized($tmpImage, $image, 0, 0, 0, 0, $imagewidth,$imageHeight, $width, $height);
        return array($tmpImage, $imagewidth,$imageHeight);
    } else {
        return array($image, $imagewidth,$imageHeight);
    }
}
/**
 * 生成原型小图片函数
 * @param String $fileName 图片文件url
 * @param Array $rgb 基础图片定位周围的rgb色值
 * @return array array[0] = 图片资源, array[1] = 图片大小
 */
function createCircleImage($fileName, $rgb = array(246, 247, 229)) {
    list($image,$radius) = createSmallImage($fileName);

    $circleImage = imagecreatetruecolor($radius, $radius);

    imagesavealpha($circleImage, true);    //保存透明图像通道

    //拾取一个完全透明的颜色,最后一个参数127为全透明
    $backgroundColor = imagecolorallocatealpha($circleImage, $rgb[0], $rgb[1], $rgb[2], 127);

    imagefill($circleImage, 0, 0, $backgroundColor);

    $r   = $radius / 2; //圆半径
    //重新拾取圆形区域像素并绘制图片
    for ($x = 0; $x < $radius; $x++) {
        for ($y = 0; $y < $radius; $y++) {
            $rgbColor = imagecolorat($image, $x, $y);
            if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                imagesetpixel($circleImage, $x, $y, $rgbColor);
            }
        }
    }

    return array($circleImage,$radius);
}

/**
 * 生成合成后的图片函数
 * @param String $baseImage 基础背景图片文件url
 * @param String $avatarImage 头像图片文件url
 * @return void 输出图片资源
 */
function createMergeImage($baseImage,$avatarImage,$str,$num) {
    ob_clean();    //清除缓冲区内容，防止无法输出图像
    list($avatar,$radius,$radiusH) = createSmallImage($avatarImage);
    list($image,$imageType) = getCreateImageInfo($baseImage);
    imagecopymerge($image,$avatar,114,165,0,0,$radius,$radiusH,100);
    


$black = imagecolorallocate($image,0x00,0x00,0x00);

//imagestring($im,2,50,160,"gonghe",$black);
//$text = iconv("GB2312", "UTF-8", "回忆经典");
imagettftext($image,53,0,660,380,$black,"./simhei.ttf",$str); //字体设置部分linux和windows的路径可能不同
imagettftext($image,60,0,660,500,$black,"./simhei.ttf",$num); //字体设置部分linux和windows的路径可能不同

    $imageOut = 'image'.$imageType;    //根据图片类型不同来拼接图片输出函数
    $imagefile='./out/'.$num.".".$imageType;
    //header('content-type:text/html;charset=GBK');    //根据图片类型不同来拼接头信息
    header("Content-Disposition", "attachment; filename=" + fileName);
    header("Pragma", "No-cache");
	header("Cache-Control", "No-cache");
	header("Expires", 0);
   // $imageOut($image);    //输出图片
    $imageOut($image,$imagefile);
 $contect =  substr($_SERVER['REQUEST_URI'],0,strripos($_SERVER['REQUEST_URI'],"/"));
     //$url='http://'.$_SERVER['HTTP_HOST'].$contect."/download.php?filename=".'./out/'.$num.".".$imageType;
    // $url='http://'.$_SERVER['HTTP_HOST'].$contect."/index.php?filename='".'./out/'.$num.".".$imageType."'";
      $url='http://'.$_SERVER['HTTP_HOST'].$contect."/index.php?filename=".$num.".".$imageType;
     
     //&imageUrl='http://'.$_SERVER['HTTP_HOST'].$contect.'/out/'.$num.".".$imageType;
    Header("Location: $url"); 
    imagedestroy($image);    //销毁资源
}

//调用合成函数
createMergeImage($baseImage,$avatarImage,$str,$num);











