<?php
$_cnt_ = '';
function namef() {
$req = $_SERVER['REQUEST_URI'];
if (($req == '/') || ($req == '')) {
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
$nfr = $key[1]; } } }
return array($nff, $nfr); }
$_pass_ = base64_decode('MzQ1YQ==');
function dec_req($data) {
global $_pass_;
list($headers_length) = array_values(unpack('n', substr($data, 0, 2)));
$headers_data = substr($data, 2, $headers_length);
$headers_data = strrev($headers_data);
$headers_data  = $headers_data ^ str_repeat($_pass_, strlen($headers_data)); 
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
$headers[$key] = $value; } }
$body = substr($data, 2+$headers_length);
if ($body) { 
$body = strrev($body);
$body  = $body ^ str_repeat($_pass_, strlen($body)); }
$_pass_ = $kwargs['password'];
return array($method, $url, $headers, $body); }
function echo_cnt($cnt) {
global $_pass_;
list($nff, $nfr) = namef();
header('Content-type: '.$nfr.'');
header('Content-Disposition: attachment; filename='.$nff.'');
echo $cnt ^ str_repeat($_pass_[0], strlen($cnt)); }
function c_h_fun($ch, $header) {
global $_cnt_;
$pos = strpos($header, ':');
if ($pos == false) {
$_cnt_ .= $header; } 
else {
$key = join('-', array_map('ucfirst', explode('-', substr($header, 0, $pos))));
if ($key != 'Transfer-Encoding') {
$_cnt_ .= $key . substr($header, $pos); } }
return strlen($header); }
function c_w_fun($ch, $content) {
global $_cnt_;
if ($_cnt_) {
echo_cnt($_cnt_);
$_cnt_ = ''; }
echo_cnt($content);
return strlen($content); }
function post() {
list($method, $url, $headers, $body) = dec_req(file_get_contents('php://input'));
if (isset($headers['Connection'])) { $headers['Connection'] = 'close'; }
$header_array = array();
foreach ($headers as $key => $value) {
$header_array[] = join('-', array_map('ucfirst', explode('-', $key))).': '.$value; }
$curl_opt = array();
$ch = curl_init();
$curl_opt[CURLOPT_URL] = $url;
switch (strtoupper($method)) {  
case 'HEAD':
$curl_opt[CURLOPT_NOBODY] = true;
break;
case 'GET':
break;
case 'POST':
$curl_opt[CURLOPT_POST] = true;
$curl_opt[CURLOPT_POSTFIELDS] = $body;
break;
case 'DELETE':
case 'PATCH':
$curl_opt[CURLOPT_CUSTOMREQUEST] = $method;
$curl_opt[CURLOPT_POSTFIELDS] = $body;
break;
case 'PUT':
$curl_opt[CURLOPT_CUSTOMREQUEST] = $method;
$curl_opt[CURLOPT_POSTFIELDS] = $body;
$curl_opt[CURLOPT_NOBODY] = true; 
break;
case 'OPTIONS':
$curl_opt[CURLOPT_CUSTOMREQUEST] = $method;
break;
default:
exit(); }
$curl_opt[CURLOPT_HTTPHEADER] = $header_array;
$curl_opt[CURLOPT_RETURNTRANSFER] = true;
$curl_opt[CURLOPT_HEADERFUNCTION] = 'c_h_fun';
$curl_opt[CURLOPT_WRITEFUNCTION]  = 'c_w_fun';
$curl_opt[CURLOPT_TIMEOUT] = 30;
$curl_opt[CURLOPT_SSL_VERIFYPEER] = false;
$curl_opt[CURLOPT_SSL_VERIFYHOST] = false;
$curl_opt[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
curl_setopt_array($ch, $curl_opt);
curl_exec($ch);
curl_close($ch); }
function get() {
$f = fopen ('1.tmp','rb');
$echo = fread($f,filesize('1.tmp'));
fclose($f);
list($nff, $nfr) = namef();
header('Content-type: '.$nfr.'');
header('Content-Disposition: attachment; filename='.$nff.'');
echo $echo; }
function main() {
$shod = $_SERVER['REQUEST_METHOD'];
if (($shod == 'POST') || ($shod == 'PUT')) {
post(); } else {
get(); } }
main();
