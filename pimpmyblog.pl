#!/usr/bin/perl -w

use strict;
use warnings;

use LWP;
use Getopt::Long;
use XML::LibXML;
#use Data::Dumper;

sub print_usage {
  my $usage = <<USAGE;
$0 : -f "url of the blog" [-t "template file"] [-o "output file"]
if no -o is specified, it prints on STDOUT
USAGE
  
print $usage;

}

sub fetch_feed {
  my $url = shift;
  
  my $ua = LWP::UserAgent->new;
  
  # Create a request
  my $req = HTTP::Request->new( GET => $url );
  my $res = $ua->request($req);
  if ( $res->is_success ) {
    return $res->content;
  }
  else {
    $@ = "error status: ". $res->status_line."; server message: ". $res->message;
    return undef;
  }
  
}


sub parse_feed {
  # to id a feed do - 'name(/*)' 
  my $xml_str = shift;
  my $parser = XML::LibXML->new();
  my $doc = $parser->parse_string($xml_str);
  my $feed_type = $doc->findvalue("name(/*)");
  my ($query_title,$query_link) ;
  # we just need the first item and first link
  if ($feed_type eq "feed") {
    ($query_title,$query_link) = ("/feed/entry[1]/title","/feed/entry[1]/id")
  }
  elsif  ($feed_type eq "rss") {
    ($query_title,$query_link) = ("/rss/channel/item[1]/title","/rss/channel/item[1]/link")
  }
  else {
    $@ = "unknown feed type";
    return undef;
  }
  my %res;
  $res{"title"} = $doc->findvalue($query_title);
  $res{"link"} = $doc->findvalue($query_link);
  unless (defined($res{"link"}) and defined ($res{"title"})) {
    $@ = "error in parsing feed";
    return undef;
  }
  return \%res;
}

my ($feed_url,$tpl_file,$out_file) = 
   (undef, "/tmp/sig.tpl","-");

GetOptions( 
	   "f|feed=s"		=> \$feed_url,
	   "t|template=s"	=> \$tpl_file,
	   "o|output=s"	=> \$out_file ) 
  or die ("error processing options" . $@);

unless (defined($feed_url)) {print_usage ; exit 0;}

my $feed_xml = fetch_feed ($feed_url) 
  or die ("failed fetching $feed_url with error : " .$@);

my $ref_feed_item = parse_feed($feed_xml) 
  or die ("failed parsing feed from url $feed_url with error : ".$@);

open TPL,$tpl_file
  or die ("error opening template file $tpl_file with error : " .$!);
my $sig_str;

while (<TPL>) {
  s/(!title!)/$ref_feed_item->{title}/;
  s/(!url!)/$ref_feed_item->{link}/;
  $sig_str.= $_;  
}
close TPL;

if ($out_file eq "-") {
  print $sig_str;
}
else {
  open OUT,">",$out_file
    or die ("error opening signature file $out_file with error : " .$!);
  print OUT $sig_str;
  close OUT;    
}








