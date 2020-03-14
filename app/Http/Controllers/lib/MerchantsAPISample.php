 <?php
// Note : When creating a signature the URL should not have a trailing slash "/". But add the slash when sending the 
// request. 
$url = 'https://vendors.pay-leo.com/api/v2/test/deposit';
$msisdn = "";
$amount = ""; 
$transactionId =  rand(100000, 9999999);
$narration = "";

$consumerSecret = "";
$merchantCode = "";
$consumerKey = "";

$data = $url."&".$msisdn."&".$amount."&".$merchantCode."&".$transactionId."&".$narration;
$signature = hash_hmac("sha256", $data, $consumerSecret);

$PostData = array(
	"msisdn"=> $msisdn,
	"amount"=> $amount,
	"merchantCode"=> $merchantCode,
	"transactionId"=> $transactionId,
	"consumerKey"=> $consumerKey,
	"auth_signature"=> $signature,
	"narration"=> $narration
); 

$postDatas = json_encode($PostData);
$resp = postReq($postDatas);
var_dump($resp); 	

function postReq($data){
 $url = "https://vendors.pay-leo.com/api/v2/test/deposit/";
 $ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	$content=curl_exec($ch);
	curl_close($ch);
	return $content;
}

?>
