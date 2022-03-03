<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//configure cheetahmail


// #1. API
// API Credentials
// Username: aegon_api
// Password: E5bCE2qR
 
// AID: 2097186151
// Test Subscription list ID: 2097933662
// Members Subscription list ID: 2097925860
// Non-Members Subscription list ID: 2097925495
 
// -----------------
 
// #2. sftp
// sftp credentials
// URL: https://tt.cheetahmail.com
// Username: aegon_cm
// Password: E5bCE2qR
 
// Aegon can retrieve unsubscriptions from /fromcheetah
 
// File format: unsubs_YYYYMMDD.dat.gz
// aid,pid,reason,email,DATE_UNSUB,DATE_SUB,rcode
// 13079252,429465,b,sample@cheetahmail.com,20050116092435,20050106112641,PARTNER

// mailchimp
// marketing: dff617b903
// newsletter: 3b6703b579


$config['user_name'] 	= 'aegon_api';
$config['password'] 	= 'E5bCE2qR';
$config['affiliate_id'] = '2097186151';
$config['newsletter_member'] 		= '2098031596';
$config['newsletter_non_member'] 	= '2097925495';
$config['member_edm'] 	= '2097925860';
// $config['member'] 		= '2097933662';
// $config['non_member'] 	= '2097933662';
// $config['marketing'] 	= '2097933662';