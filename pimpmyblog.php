#!/usr/bin/php -dopen_basedir=
<?php

function fetch_url($url)
{
  $ch = curl_init ($url) ;
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
  $res = curl_exec ($ch) ;
  curl_close ($ch) ;
  return $res;
}


function fetch_last_post($blog_xml)
{
  $h_xml = simplexml_load_string($blog_xml);
  $result['title'] = (string)$h_xml->channel->item[0]->title;
  $result['link'] = (string)$h_xml->channel->item[0]->link;
  $result['pubDate'] = (string)$h_xml->channel->item[0]->pubDate;
  return $result;
}

function create_mail_sig($template,$title,$link) 
{
  $tmpl = file_get_contents($template);
  $magic_url = "/\!url\!/";
  $magic_title = "/\!title\!/";
  
  
  $tmpl = preg_replace($magic_url,$link,$tmpl);
  $tmpl = preg_replace($magic_title,$title,$tmpl);
  //preg_replace($tmpl,"/$magic_title/",$title);
  return $tmpl;
}

$opts = getopt('u:t:');
if (!( key_exists('u',$opts) && key_exists('t',$opts))) {
  die("required args not passed\n");
}

$blog_rss_url = $opts['u'];
$f_template = $opts['t'];

$blog_xml = fetch_url($blog_rss_url);
$last_blog = fetch_last_post($blog_xml);

$mail_sig_txt = create_mail_sig($f_template,$last_blog['title'],$last_blog['link']);
print $mail_sig_txt;

?>