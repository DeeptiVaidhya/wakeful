<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['smtp_host']    = 'smtp.gmail.com';
$config['smtp_user'] = 'qa1.ideavate@gmail.com';// your email id
$config['smtp_pass'] = 'ideavate@123';// your pass goes here

$config['protocol']     = "smtp";
$config['smtp_port']    = '587';
$config['smtp_timeout'] = '30';

$config['mailtype'] = 'html';
$config['charset']  = 'utf-8';
$config['newline']  = "\r\n";
$config['wordwrap'] = TRUE;
$config['smtp_crypto'] = 'tls';
$config['_bit_depths'] = array('7bit', '8bit', 'base64');
$config['_encoding'] = 'base64';
