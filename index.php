<?php
/**
 * Step 1: Require the Slim PHP 5 Framework
 *
 * If using the default file layout, the `Slim/` directory
 * will already be on your include path. If you move the `Slim/`
 * directory elsewhere, ensure that it is added to your include path
 * or update this file path as needed.
 */
require 'Slim/Slim.php';

/**
 * Step 2: Instantiate the Slim application
 *
 * Here we instantiate the Slim application with its default settings.
 * However, we could also pass a key-value array of settings.
 * Refer to the online documentation for available settings.
 */
$pageNum = 30;//每页显示的个数
$app = new Slim();
$response = $app->response();
$response->header('Content-Type', 'application/json,charset=utf-8');
/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function. If you are using PHP < 5.3, the
 * second argument should be any variable that returns `true` for
 * `is_callable()`. An example GET route for PHP < 5.3 is:
 *
 * $app = new Slim();
 * $app->get('/hello/:name', 'myFunction');
 * function myFunction($name) { echo "Hello, $name"; }
 *
 * The routes below work with PHP >= 5.3.
 */

//GET route

/***********************************************
   functionName: JSON()  get JSON Array
 *  @param $rs database connection
 *  @return $output JSON Array
 ***********************************************/
function JSON($rs) {
  $array = array();
  $i=0;
  while($row = mysql_fetch_object($rs))
  {
      $item = $row;
      foreach ($item as $key => $value) 
      {
        $changeImageSrc = str_replace("/d/file/p","http://www.zgvtc.cn.img.800cdn.com/d/file/p",$value);
          $item->$key = urlencode($changeImageSrc);  
      }
      $array[$i] = $item;
    $i++;
  }
    $json = json_encode($array);
  $output = iconv('gbk','utf-8',urldecode($json));
  return $output;
}

/***********************************************
 *  functionName: JSON2()  get single JSON
 *  @param $rs database connection
 *  @return $json single JSON
 ***********************************************/
function JSON2($rs)
{
    if ($row = mysql_fetch_object($rs))
  { 
    foreach ($row as $key => $value) 
    {
      $changeImageSrc = str_replace("/d/file/p","http://www.zgvtc.cn.img.800cdn.com/d/file/p",$value);
      $urlencodeValue = urlencode(iconv('gbk','utf-8',$changeImageSrc));
      //deal with white space
      $row->$key = str_replace("+","%20",$urlencodeValue);
    }
    
    $json = json_encode($row);
      return $json;
  } else
  {
      $error = array("PHP"=>"error");
      echo json_encode($error);
  }
}

/***********************************************
 *  functionName: JSON2()  get single JSON
 *  @param $rs database connection
 *  @return $json single JSON 
 *  只需要正文部分
 ***********************************************/
function JSON3($rs)
{
    if ($row = mysql_fetch_object($rs))
  { 
    foreach ($row as $key => $value) 
    {
      if($key=='classtext'){
        preg_match('/<div\s*id=\\\\\"dr-zhengwen\\\\\"([\s\S]*)<\/div>/iU',$value,$changeImageSrc);
        $urlencodeValue = urlencode(iconv('gbk','utf-8',$changeImageSrc[0]));
        //deal with white space
        $row->$key = str_replace("+","%20",$urlencodeValue);
      }else{
        $row->$key = urlencode(iconv('gbk','utf-8',$value));
      }
    }
    $json = json_encode($row);
      return $json;
  } else
  {
      $error = array("PHP"=>"error");
      echo json_encode($error);
  }
}

/***********************************************
 *  functionName: JSON2()  get single JSON
 *  @param $rs database connection
 *  @return $json single JSON 
 *  只需要正文部分
 ***********************************************/
function JSON4($rs)
{
  $array = array();
  $i=0;
  while($row = mysql_fetch_object($rs))
  {
      $item = $row;
      foreach ($item as $key => $value) 
      {
        if($key=='newstext'){
            preg_match('/src=\\\\\"([\s\S]*)\\\\\"/iU',$value,$changeImageSrc);
            $value = $changeImageSrc[1];
        }
        $changeImageSrc = str_replace("/d/file/p","http://www.zgvtc.cn.img.800cdn.com/d/file/p",$value);
        $item->$key = urlencode($changeImageSrc);  
      }
      $array[$i] = $item;
    $i++;
  }
    $json = json_encode($array);
  $output = iconv('gbk','utf-8',urldecode($json));
  return $output;
}

//"HomePage"
$app->get('/', 'HomePage');
function HomePage()
 {
     $array = array("PHP"=>"Hello World!");
   echo json_encode($array);
 }


//"CampusNews"
$app->get('/news/campus_news/:page','getCampusNews');

function getCampusNews($page)
{
    global $app;
    global $pageNum;
    include 'conn.php';
  $sql = "select id,classid,newspath,title from zgvtccn_ecms_news where classid = 9 order by newspath desc limit ".($page-1)*$pageNum.",".$pageNum;
  $rs = mysql_query($sql);
  echo JSON($rs);
}

//"Notifications"
$app->get('/news/notifications/:page','getNotifications');

function getNotifications($page)
{
    global $app;
    global $pageNum;
    include 'conn.php';
  $sql = "select id,classid,newspath,title from zgvtccn_ecms_news where classid = 10 order by newspath desc limit ".($page-1)*$pageNum.",".$pageNum;
  $rs = mysql_query($sql);
  echo JSON($rs);
}

//"NewsDetail"
$app->get('/news/news_detail/:id','getNewsDetailById');
function getNewsDetailById($id)
{
    global $app;
    include 'conn.php';
    $sql = "select * from zgvtccn_ecms_news_data_1 where id = '$id'";
    $rs = mysql_query($sql);
    echo JSON2($rs);
}


/******************************2.WalkIntoCampus**************************************/
//"CampusOverview"
$app->get('/walkintocampus/campus_overview/','getCampusBriefIntroduction');
function getCampusBriefIntroduction()
{
    global $app;
    include 'conn.php';
    $sql = "select * from zgvtccn_enewsclassadd where classid = 4 limit 1";
    $rs = mysql_query($sql);
    echo JSON3($rs);
}

//"CampusWorkPolicy"：学校工作思路
$app->get('/walkintocampus/campus_workpolicy/','getCampusWorkPolicy');
function getCampusWorkPolicy()
{
    global $app;
    include 'conn.php';
    $sql = "select * from zgvtccn_enewsclassadd where classid = 3 limit 1";
    $rs = mysql_query($sql);
    echo JSON3($rs);
}

//"CampusLandscape":校园风貌
$app->get('/walkintocampus/campus_landscape/','getCampusLandscape');
function getCampusLandscape()
{
    global $app;
    include 'conn.php';
    $sql = "select news.id,news.classid,news.newspath,news.title,news.titlepic,data1.newstext from zgvtccn_ecms_news news,zgvtccn_ecms_news_data_1 data1 where news.classid = 5 and news.id=data1.id order by newspath DESC";
    $rs = mysql_query($sql);
    echo JSON4($rs);
}

//"CampusOrganization"
$app->get('/walkintocampus/campus_organization/:page','getCampusOrganization');
function getCampusOrganization($page)
{
    global $app;
    global $pageNum;
    include 'conn.php';
    $sql = "select id,classid,newspath,title,titlepic from zgvtccn_ecms_news where classid = 97 order by newspath desc limit ".($page-1)*$pageNum.",".$pageNum;
    $rs = mysql_query($sql);
    echo JSON($rs); 
}

//"CampusHonours"
$app->get('/walkintocampus/campus_honours/:page','getCampusHonours');
function getCampusHonours($page)
{
    global $app;
    global $pageNum;
    include 'conn.php';
    $sql = "select id,classid,newspath,title,titlepic from zgvtccn_ecms_news where classid = 7 order by newspath desc limit ".($page-1)*$pageNum.",".$pageNum;
    $rs = mysql_query($sql);
    echo JSON($rs); 
}

/******************************3.DemonstrationSchool**************************************/
$app->get('/demonstrationschool/demonstration_item_list/:id/:page','getDemonstrationItemListById');
function getDemonstrationItemListById($id,$page)
{
    global $app;
    global $pageNum;
    include 'conn.php';
    $sql = "select id,classid,newspath,title,titlepic from zgvtccn_ecms_news where classid = '$id' order by newspath desc limit ".($page-1)*$pageNum.",".$pageNum;
    $rs = mysql_query($sql);
    echo JSON($rs); 
}

$app->get('/demonstrationschool/demonstration_item_detail/:id','getDemonstrationItemDetailById');
function getDemonstrationItemDetailById($id)
{
    global $app;
    include 'conn.php';
    $sql = "select * from zgvtccn_ecms_news_data_1 where id = '$id'";
    $rs = mysql_query($sql);
    echo JSON2($rs);
}

/******************************4.AdultShortTermTraining**************************************/
$app->get('/adultshorttermtraining/shorttrain_item_list/:id/:page','getShortTrainItemListById');
function getShortTrainItemListById($id,$page)
{
    global $app;
    global $pageNum;
    include 'conn.php';
    $sql = "select id,classid,newspath,title,titlepic from zgvtccn_ecms_news where classid = '$id' order by newspath desc limit ".($page-1)*$pageNum.",".$pageNum;
    $rs = mysql_query($sql);
    echo JSON($rs); 
}

$app->get('/adultshorttermtraining/shorttrain_item_detail/:id','getShortTrainItemDetailById');
function getShortTrainItemDetailById($id)
{
    global $app;
    include 'conn.php';
    $sql = "select * from zgvtccn_ecms_news_data_1 where id = '$id'";
    $rs = mysql_query($sql);
    echo JSON2($rs);
}

/******************************5.AdmissionEmployment**************************************/
$app->get('/admissionemployment/admission_item_list/:id/:page','getAdmissionEmploymentItemListById');
function getAdmissionEmploymentItemListById($id,$page)
{
    global $app;
    global $pageNum;
    include 'conn.php';
    $sql = "select id,classid,newspath,title,titlepic from zgvtccn_ecms_news where classid = '$id' order by newspath desc limit ".($page-1)*$pageNum.",".$pageNum;
    $rs = mysql_query($sql);
    echo JSON($rs); 
}

$app->get('/admissionemployment/admission_item_detail/:id','getAdmissionEmploymentItemDetailById');
function getAdmissionEmploymentItemDetailById($id)
{
    global $app;
    include 'conn.php';
    $sql = "select * from zgvtccn_ecms_news_data_1 where id = '$id'";
    $rs = mysql_query($sql);
    echo JSON2($rs);
}
$app->run();
