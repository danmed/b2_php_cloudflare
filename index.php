<?PHP
$cfurl - ""; // Cloudflare URL with a trailing slash
$bucketname = ""; // Bucket Name with a traling slash
$download = $cfurl . $bucketname;
$account_id = ""; // Obtained from your B2 account page
$application_key = ""; // Obtained from your B2 account page
$credentials = base64_encode($account_id . ":" . $application_key);
$url = "https://api.backblazeb2.com/b2api/v2/b2_authorize_account";

$session = curl_init($url);

// Add headers
$headers = array();
$headers[] = "Accept: application/json";
$headers[] = "Authorization: Basic " . $credentials;
curl_setopt($session, CURLOPT_HTTPHEADER, $headers);  // Add headers

curl_setopt($session, CURLOPT_HTTPGET, true);  // HTTP GET
curl_setopt($session, CURLOPT_RETURNTRANSFER, true); // Receive server response
$server_output = curl_exec($session);
curl_close ($session);
$myArray = explode(',', $server_output);
$api_url = substr($myArray[16], 14);
$api_url = substr($api_url, 0, -1);
$auth_token = substr($myArray[17], 26);
$auth_token = substr($auth_token, 0, -1);

//$api_url = $server_output['apiUrl']; // From b2_authorize_account call
//$auth_token = $server_output['authorizationToken']; // From b2_authorize_account call
$bucket_id = "";  // The ID of the bucket

$session = curl_init($api_url .  "/b2api/v2/b2_list_file_names");

// Add post fields
$data = array("bucketId" => $bucket_id);
$post_fields = json_encode($data);
curl_setopt($session, CURLOPT_POSTFIELDS, $post_fields);

// Add headers
$headers = array();
$headers[] = "Authorization: " . $auth_token;
curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

curl_setopt($session, CURLOPT_POST, true); // HTTP POST
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  // Receive server response
$server_output = curl_exec($session); // Let's do this!
curl_close ($session); // Clean up
//echo ($server_output); // Tell me about the rabbits, George!

$myArray2 = explode('fileName', $server_output);
foreach($myArray2 as $value) {
 $value2 = substr($value, 3);
 $parsed = get_string_between($value2, '"','"');
 print "<a href='" . $download . $parsed . "'>" . $parsed . "</a>";
 print "<BR>";
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
?>
