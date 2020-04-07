<?php

$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = 'XHN/YhS2kXs2BjBI32pCxJAvuTo3LLBhbAEmkcqPnwRqn2xu7XVUZuEonHfOO7mA7xe72k7v+nXmQtuSDDl6tmtVZsUFAIh55BquWQjJuCU6dxixD58HHwAdxw3Xgd1P1LYrQcbOEhK7yevEHpuzuAdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array

$dev_name = 'นายจุฑาธวัช ศตะกูรมะ';
$response = file_get_contents('https://covid19.th-stat.com/api/open/today');
$data_covid = json_decode($response);
if ( sizeof($request_array['events']) > 0 )
{

 foreach ($request_array['events'] as $event)
 {
  $reply_message = '';
  $reply_token = $event['replyToken'];

  if ( $event['type'] == 'message' ) 
  {
   
   if( $event['message']['type'] == 'text' )
   {
		$text = $event['message']['text'];
		$p = $data_covid->Confirmed;
	   	$p_new = $data_covid->NewConfirmed;
	   	$d = $data_covid->Deaths;
	   	$d_new = $data_covid->NewDeaths;
	   	$n = $data_covid->Recovered;
	   	$h = $data_covid->Hospitalized;
	   
		if(($text == "อยากทราบยอด COVID-19 ครับ")||($text == "ยอด")){
			$reply_message = 'รายงานสถานการณ์ ยอดผู้ติดเชื้อไวรัสโคโรนา 2019 (COVID-19) ในประเทศไทย
			ผู้ป่วยสะสม '.$p.' ราย (เพิ่มขึ้น '.$p_new.' ราย)
			ผู้เสียชีวิต '.$d.' ราย (เพิ่มขึ้น '.$d_new.' ราย)
			รักษาตัวที่โรงพยาบาล '.$h.' ราย
			รักษาหาย จำนวน '.$n.' ราย 
			ผู้รายงานข้อมูล: '.$dev_name;
		}
		else if(($text== "ข้อมูลส่วนตัวของผู้พัฒนาระบบ")||($text== "ข้อมูล")){
			$reply_message = 'ชื่อ'.$dev_name.' รหัสนิสิต 59160164 อายุ 22 ปี';
		}
		else
		{
			$reply_message = 'ระบบได้รับข้อความ ('.$text.') ของคุณแล้ว';
    		}
   
   }
   else
    $reply_message = 'ระบบได้รับ '.ucfirst($event['message']['type']).' ของคุณแล้ว';
  
  }
  else
   $reply_message = 'ระบบได้รับ Event '.ucfirst($event['type']).' ของคุณแล้ว';
 
  if( strlen($reply_message) > 0 )
  {
   //$reply_message = iconv("tis-620","utf-8",$reply_message);
   $data = [
    'replyToken' => $reply_token,
    'messages' => [['type' => 'text', 'text' => $reply_message]]
   ];
   $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

   $send_result = send_reply_message($API_URL, $POST_HEADER, $post_body);
   echo "Result: ".$send_result."\r\n";
  }
 }
}

echo "OK";

function send_reply_message($url, $post_header, $post_body)
{
 $ch = curl_init($url);
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 $result = curl_exec($ch);
 curl_close($ch);

 return $result;
}

?>
