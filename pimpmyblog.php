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

$blog_rss_url = "http://rajshekhar.net/blog/feeds/index.rss2";
$blog_xml = fetch_url($blog_rss_url);
$last_blog = fetch_last_post($blog_xml);
print_r($last_blog);
?>