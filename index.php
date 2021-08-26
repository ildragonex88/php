<?php
$__content__ = '';
function namef() {
$req = $_SERVER['REQUEST_URI'];
if ($req == '//') {
exit;
}
if ($req == '/') {
$nff = 'zip.zip';
$nfr = 'application/zip'; }
else {
$nff = str_replace('/', '', $req);
$nfr = substr($req, 1); 
$nfr = explode('.', $nfr);
$nfr = $nfr[1];
$tmp = file('mime.tmp');
foreach ($tmp as $key) {
$key = explode('||', $key); 
if ($key[0] == $nfr) {
$nfr = $key[1];
break; }
}
}
return array($nff, $nfr);
}
$__password__ = base64_decode('MzQ1YQ==');
function message_html($title, $banner) {
$error = "<title>${title}</title><body>${banner}</body>";
return $error;
}
function decode_request($data) {
global $__password__;
list($headers_length) = array_values(unpack('n', substr($data, 0, 2)));
$headers_data = substr($data, 2, $headers_length);
$headers_data  = $headers_data ^ str_repeat($__password__, strlen($headers_data)); 
$headers_data = gzinflate($headers_data);
$lines = explode("\r\n", $headers_data); 
$request_line_items = explode(" ", array_shift($lines)); 
$method = $request_line_items[0];
$url = $request_line_items[1];
$headers = array();
$kwargs  = array();
$kwargs_prefix = 'X-URLFETCH-';
foreach ($lines as $line) {
if (!$line)
continue;
$pair = explode(':', $line, 2);
$key  = $pair[0];
$value = trim($pair[1]);
if (stripos($key, $kwargs_prefix) === 0) {
$kwargs[strtolower(substr($key, strlen($kwargs_prefix)))] = $value;
} else if ($key) {
$key = join('-', array_map('ucfirst', explode('-', $key)));
$headers[$key] = $value;
}
}
$body = substr($data, 2+$headers_length);
if ($body) { 
$body  = $body ^ str_repeat($__password__, strlen($body));
$body = gzinflate($body);
}
$__password__ = $kwargs['password'];
return array($method, $url, $headers, $body);
}
function echo_content($content) {
global $__password__;
list($nameff, $namefr) = namef();
header('Content-type: '.$namefr.'');
header('Content-Disposition: attachment; filename='.$nameff.'');
echo $content ^ str_repeat($__password__[0], strlen($content));
}
function header_function($header) {
global $__content__;
$pos = strpos($header, ':');
if ($pos == false) {
$__content__ .= $header;
} 
else {
$key = join('-', array_map('ucfirst', explode('-', substr($header, 0, $pos))));
if ($key != 'Transfer-Encoding') {
$__content__ .= $key . substr($header, $pos);
}
}
}
function write_function($content) {
global $__content__;
if ($__content__) {
echo_content($__content__);
$__content__ = '';
}
echo_content($content);
}
function post() {
list($method, $url, $headers, $body) = decode_request(file_get_contents('php://input'));

$headerin = array();
switch (strtoupper($method)) {  
case 'GET':
break;
case 'HEAD':
case 'OPTIONS':
case 'TRACE':
$headerin['method'] = $method;
break;
case 'POST':
case 'PATCH':
case 'PUT':
case 'DELETE':
$headerin['method'] = $method;
if ($body) {
$arrayyn = is_array($body) ? '1' : '0';
if ($arrayyn == 1) {
$body = http_build_query($body);
}
$headerin['content'] = $body;
}
break;
default:
echo_content("HTTP/1.0 502\r\n\r\n" . message_html('502 Urlfetch Error', 'Method error ' . $method));
exit(-1);
}
$headerin['protocol_version'] = 1.1;
$headerin['request_fulluri'] = 1;
$headerin['follow_location'] = false;
$headerin['header'] = array_map(function ($h, $v) {return "$h: $v";}, array_keys($headers), $headers);
$headerin['ignore_errors'] = 1;
$stcocr = array('http' => $headerin);
$context = stream_context_create($stcocr);
$strea = file_get_contents($url, false, $context);
foreach ($http_response_header as $header) {
$pos = strpos($header, ':');
if ($pos == false) {
header_function($header);
header_function("\r\n");
}
else {
$key = join('-', array_map('ucfirst', explode('-', substr($header, 0, $pos))));
if ($key != 'Transfer-Encoding') {
header_function($key . substr($header, $pos));
header_function("\r\n");
}
}
}
header_function("\r\n"); 
write_function($strea);
 
}
function get() {
$f = fopen ('1.tmp','rb');
$echo = fread($f,filesize('1.tmp'));
fclose($f);
list($nameff, $namefr) = namef();
header('Content-type: '.$namefr.'');
header('Content-Disposition: attachment; filename='.$nameff.'');
echo $echo;
}
function main() {
$shod = $_SERVER['REQUEST_METHOD'];
if ($shod == 'POST') {
post(); } else {
get(); } }
main();
