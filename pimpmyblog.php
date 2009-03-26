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


function fetch_last_post_xpath($blog_xml)
{
  $h_xml = simplexml_load_string($blog_xml);
  $t = $h_xml->xpath('/rss/channel/item[1]/title');
  $result['title'] = (string)$t[0];
  //var_dump($result);
  return $result;
}

function fetch_last_post($blog_xml)
{
  $h_xml = simplexml_load_string($blog_xml);
  $result['title'] = (string)$h_xml->channel->item[0]->title;
  $result['title'] = (string)$h_xml->xpath('/rss/channel/item');
  $result['link'] = (string)$h_xml->channel->item[0]->link;
  $result['pubDate'] = (string)$h_xml->channel->item[0]->pubDate;
  $result = $h_xml->xpath('/rss/channel/item[1]/title');
  return $result;
}

$blog_rss_url = "http://joomladev.rajshekhar.net/index.php?format=feed&type=rss";
$blog_xml = fetch_url($blog_rss_url);
$last_blog_xpath = fetch_last_post_xpath($blog_xml);
//$last_blog = fetch_last_post($blog_xml);
?>