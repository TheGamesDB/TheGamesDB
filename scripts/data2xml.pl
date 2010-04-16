#!/usr/bin/perl
#use open ':utf8';

#############################################################
## Exit if lockfile exists
#############################################################
if (-e "data2xml.lock")  {
	print "Already running an instance of this program\n";
	exit;
}
else  {
	system("touch data2xml.lock");
}


#############################################################
## Modules and settings
#############################################################
use DBI;
use XML::Simple;
my $basedir = '/home/www/';
my $encoding = '<?xml version="1.0" encoding="UTF-8" ?>';
my $mirrorfiles = "";
my $time = time();

## Calculate datestamp
my $date_year = (localtime())[5]+1900;
my $date_month = (localtime())[4]+1;
my $date_day = (localtime())[3];
if ($date_month < 10)  {
	$date_month = "0" . $date_month;
}
if ($date_day < 10)  {
	$date_day = "0" . $date_day;
}
my $datestamp = join("-", $date_year, $date_month, $date_day);


#############################################################
# Connect to the database
#############################################################
my $dsn	= 'DBI:mysql:thetvdb:localhost';
my $db_user_name = '';
my $db_password = '';
my ($id, $password);
my $dbh = DBI->connect($dsn, $db_user_name, $db_password);
my $sth = $dbh->prepare(qq{ SET NAMES utf8 }); 
$sth->execute();



#############################################################
# If "all" is passed on the command line, process them all
#############################################################
if ($ARGV[0] eq "all")  {
	my $sth = $dbh->prepare(qq{ DELETE FROM seriesupdates }); 
	$sth->execute();
	my $sth = $dbh->prepare(qq{ INSERT INTO seriesupdates (seriesid) SELECT id FROM tvseries ORDER BY lastupdated DESC }); 
	$sth->execute();
}

#############################################################
## Print API userkey to file
#############################################################
open(FILE, ">" . $basedir . "api/.htaccess");
print FILE "RewriteEngine On\n";
my $sth = $dbh->prepare(qq{ SELECT apikey FROM apiusers }); 
$sth->execute();
while (my ($apikey) = $sth->fetchrow_array())  {
	print FILE "RewriteRule \^$apikey\(\.\*\\.xml\)\$ \/data\$1 [L]\n";
	print FILE "RewriteRule \^$apikey\(\.\*\\.zip\)\$ \/data\$1 [L]\n";
	print FILE "RewriteRule \^$apikey\(\.\*\)\$ \/data\$1\/ [L]\n";
}
close(FILE);
chmod(777,"$basedir/api/.htaccess");


#############################################################
## Read in the last run date then update w/ current date
#############################################################
open(FILE, $basedir . "scripts/data2xml.dat");
my $lastrun = join("", <FILE>);
close(FILE);
chomp($lastrun);
open(FILE, ">" . $basedir . "scripts/data2xml.dat");
print FILE time();
close(FILE);



#############################################################
## Print languages to file
#############################################################
unlink($basedir . "data/languages.xml");
my $sth = $dbh->prepare(qq{ SELECT id, name, abbreviation FROM languages WHERE enabled=1 ORDER BY id }); 
$sth->execute();
my $data = $sth->fetchall_hashref("id");
my $xml = XMLout($data, NoAttr => 1, XMLDecl => $encoding, RootName=>'Languages');
$xml =~ s/<(\/)?\d+>/<$1Language>/g;
string2file($xml, "languages.xml");


#############################################################
## Print mirrors to file
#############################################################
unlink($basedir . "data/mirrors.xml");
my $sth = $dbh->prepare(qq{ SELECT id, mirrorpath, typemask FROM mirrors ORDER BY RAND() }); 
$sth->execute();
my $data = $sth->fetchall_hashref("id");
my $xml = XMLout($data, NoAttr => 1, XMLDecl => $encoding, RootName=>'Mirrors');
$xml =~ s/<(\/)?\d+>/<$1Mirror>/g;
string2file($xml, "mirrors.xml");


#############################################################
## Get series updates
#############################################################
my $updates_day, $updates_week, $updates_month, $updates_all;
my $sth = $dbh->prepare(qq{ SELECT id, lastupdated AS time FROM tvseries }); 
$sth->execute();
while ($data = $sth->fetchrow_hashref())  {
	if ($data->{"time"} >= $time - 86400)  {
		$updates_day .= "<Series><id>" . $data->{"id"} . "</id><time>" . $data->{"time"} . "</time></Series>\n";
	}
	if ($data->{"time"} >= $time - 604800)  {
		$updates_week .= "<Series><id>" . $data->{"id"} . "</id><time>" . $data->{"time"} . "</time></Series>\n";
	}
	if ($data->{"time"} >= $time - 2592000)  {
		$updates_month .= "<Series><id>" . $data->{"id"} . "</id><time>" . $data->{"time"} . "</time></Series>\n";
	}
	$updates_all .= "<Series><id>" . $data->{"id"} . "</id><time>" . $data->{"time"} . "</time></Series>\n";
}


#############################################################
## Get episode updates
#############################################################
my $sth = $dbh->prepare(qq{ SELECT id, seriesid, lastupdated AS time FROM tvepisodes WHERE lastupdated > $time - 259200 }); 
$sth->execute();
while ($data = $sth->fetchrow_hashref())  {
	if ($data->{"time"} >= $time - 86400)  {
		$updates_day .= "<Episode><id>" . $data->{"id"} . "</id><Series>" . $data->{"seriesid"} . "</Series><time>" . $data->{"time"} . "</time></Episode>\n";
	}
	if ($data->{"time"} >= $time - 604800)  {
		$updates_week .= "<Episode><id>" . $data->{"id"} . "</id><Series>" . $data->{"seriesid"} . "</Series><time>" . $data->{"time"} . "</time></Episode>\n";
	}
	if ($data->{"time"} >= $time - 2592000)  {
		$updates_month .= "<Episode><id>" . $data->{"id"} . "</id><Series>" . $data->{"seriesid"} . "</Series><time>" . $data->{"time"} . "</time></Episode>\n";
	}
}

#############################################################
## Get banner updates
#############################################################
my $sth = $dbh->prepare(qq{ SELECT *, (SELECT abbreviation FROM languages WHERE id=banners.languageid LIMIT 1) AS abbreviation FROM banners }); 
$sth->execute();
while ($data = $sth->fetchrow_hashref())  {
	my %bannerdata;
	my $xml;
	if ($data->{"keytype"} eq "series")  {
		$bannerdata->{"type"} = "series";
		$bannerdata->{"format"} = $data->{"subkey"};
		$bannerdata->{"Series"} = $data->{"keyvalue"};
		$bannerdata->{"language"} = $data->{"abbreviation"};
		$bannerdata->{"path"} = $data->{"filename"};
		$xml = XMLout($bannerdata, NoAttr => 1, XMLDecl => "", RootName=>'Banner');
	}
	if ($data->{"keytype"} eq "season")  {
		$bannerdata->{"type"} = "season";
		$bannerdata->{"format"} = "standard";
		$bannerdata->{"Series"} = $data->{"keyvalue"};
		$bannerdata->{"language"} = $data->{"abbreviation"};
		$bannerdata->{"path"} = $data->{"filename"};
		$bannerdata->{"SeasonNum"} = $data->{"subkey"};
		$xml = XMLout($bannerdata, NoAttr => 1, XMLDecl => "", RootName=>'Banner');
	}
	if ($data->{"keytype"} eq "seasonwide")  {
		$bannerdata->{"type"} = "season";
		$bannerdata->{"format"} = "wide";
		$bannerdata->{"Series"} = $data->{"keyvalue"};
		$bannerdata->{"language"} = $data->{"abbreviation"};
		$bannerdata->{"path"} = $data->{"filename"};
		$bannerdata->{"SeasonNum"} = $data->{"subkey"};
		$xml = XMLout($bannerdata, NoAttr => 1, XMLDecl => "", RootName=>'Banner');
	}

	if ($data->{"dateadded"} >= $time - 86400)  {
		$updates_day .= $xml;
	}
	if ($data->{"dateadded"} >= $time - 604800)  {
		$updates_week .= $xml;
	}
	if ($data->{"dateadded"} >= $time - 2592000)  {
		$updates_month .= $xml;
	}
	$updates_all .= $xml;
}


## Output day
unlink($basedir . "data/updates/updates_day.xml");
string2file("$encoding\n<Data time=\"$time\">$updates_day</Data>", "updates/updates_day.xml");
system("zip -j " . $basedir . "data/updates/updates_day.zip " . $basedir . "data/updates/updates_day.xml");

## Output week
unlink($basedir . "data/updates/updates_week.xml");
string2file("$encoding\n<Data time=\"$time\">$updates_week</Data>", "updates/updates_week.xml");
system("zip -j " . $basedir . "data/updates/updates_week.zip " . $basedir . "data/updates/updates_week.xml");

## Output month
unlink($basedir . "data/updates/updates_month.xml");
string2file("$encoding\n<Data time=\"$time\">$updates_month</Data>", "updates/updates_month.xml");
system("zip -j " . $basedir . "data/updates/updates_month.zip " . $basedir . "data/updates/updates_month.xml");

## Output all
unlink($basedir . "data/updates/updates_all.xml");
string2file("$encoding\n<Data time=\"$time\">$updates_all</Data>", "updates/updates_all.xml");
system("zip -j " . $basedir . "data/updates/updates_all.zip " . $basedir . "data/updates/updates_all.xml");
system("chmod -R 777 $basedir/data/updates/*");


#############################################################
#############################################################
## Get the series that have been updated since the last run
#############################################################
my $sth = $dbh->prepare(qq{ SELECT id,seriesid FROM seriesupdates }); 
$sth->execute();
while (my ($id, $seriesid) = $sth->fetchrow_array())  {

	## Process this series
	## Let's remove the current directory
	if (-d $basedir . "data/series/" . $seriesid)  {
		system("rm -rf " . $basedir . "data/series/" . $seriesid);
	}
	banners2xml($seriesid);
	series2xml($seriesid);

	## Remove this series from the updates table
	my $sth = $dbh->prepare(qq{ DELETE FROM seriesupdates WHERE seriesid = $seriesid }); 
	$sth->execute();
}



#############################################################
## Remove lock file
#############################################################
system("rm data2xml.lock");


## Functions follow


#############################################################
## Function to dump a series record to XML
#############################################################
sub series2xml {
	## First, get the parameters
	my $id = shift;
	my %languages;
	my $htaccess = "DirectoryIndex en.xml\nRewriteEngine on\n";
	print "Processing $id\n";


	## Let's get our base series record
	my $sth = $dbh->prepare(qq{ SELECT *, Rating AS ContentRating, (SELECT ROUND(AVG(rating),1) FROM ratings WHERE itemtype='series' AND itemid=$id) AS Rating FROM tvseries WHERE id=$id LIMIT 1 }); 
	$sth->execute();
	my $data = $sth->fetchrow_hashref();

	## If we didnt find the base series record, we skip it
	if (!$data->{id})  {
		return;
	}


	## Let's get our top banner
	my $sth = $dbh->prepare(qq{ SELECT filename FROM banners WHERE keytype='series' AND keyvalue=$id ORDER BY (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) DESC,RAND() LIMIT 1 });
	$sth->execute();
	my $banner = $sth->fetchrow_hashref();
	$data->{"banner"} = $banner->{filename};

	## Now our top fan art
	my $sth = $dbh->prepare(qq{ SELECT filename FROM banners WHERE keytype='fanart' AND keyvalue=$id ORDER BY (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) DESC,RAND() LIMIT 1 });
	$sth->execute();
	my $banner = $sth->fetchrow_hashref();
	$data->{"fanart"} = $banner->{filename};


	## Remove fields
	delete $data->{"autoimport"};
	delete $data->{"bannerrequest"};
	delete $data->{"disabled"};
	delete $data->{"flagged"};
	delete $data->{"forceupdate"};
	delete $data->{"hits"};
	delete $data->{"locked"};
	delete $data->{"lockedby"};
	delete $data->{"mirrorupdate"};
	delete $data->{"requestcomment"};
	delete $data->{"updateID"};


	## Now let's loop through our languages
	my $sth = $dbh->prepare(qq{ SELECT * FROM languages WHERE enabled=1 ORDER BY id }); 
	$sth->execute();
	while (my $language = $sth->fetchrow_hashref())  {

		## Store this langauge record
		my $recordtranslated = 0;
		$languages{$language->{abbreviation}} = $language->{id};
		if ($language->{id} == 7)  {
			$recordtranslated = 1;
		}


		## Remove fields
		delete $data->{"SeriesName"};
		delete $data->{"Overview"};


		## Add fields
		$data->{"Language"} = $language->{"abbreviation"};


		## Get our series name for this language
		my $sth2 = $dbh->prepare(qq{ SELECT translation,languageid FROM translation_seriesname WHERE seriesid=$id AND (languageid = $language->{"id"} OR languageid = 7) ORDER BY languageid DESC LIMIT 1 });
		$sth2->execute();
		my ($translation, $languageid) = $sth2->fetchrow_array();
		$data->{"SeriesName"} = $translation;
		if ($languageid != 7)  {
			$recordtranslated = 1;
		}


		## Get our series overview for this language
		my $sth2 = $dbh->prepare(qq{ SELECT translation,languageid FROM translation_seriesoverview WHERE seriesid=$id AND (languageid = $language->{"id"} OR languageid = 7) ORDER BY languageid DESC LIMIT 1 });
		$sth2->execute();
		my ($translation, $languageid) = $sth2->fetchrow_array();
		$data->{"Overview"} = $translation;
		if ($languageid != 7)  {
			$recordtranslated = 1;
		}


		## Create our output file for this language
		if ($recordtranslated == 1)  {
			my %newdata = ();
			$newdata->{"Series"} = $data;
			my $xml = XMLout($newdata, NoAttr => 1, XMLDecl => $encoding, RootName=>'Data');
			string2file($xml, "series/$id/$language->{abbreviation}.xml");
			delete $newdata->{"Series"};
		}
		else  {
			$htaccess .= "RewriteRule ^$language->{abbreviation}.xml\$ en.xml [L]\n";
		}


		## Let's create our root items on our main series XML
		string2file("$encoding\n<Data>", "series/$id/all/$language->{abbreviation}.xml");
		my $seriesxml = XMLout($data, NoAttr => 1, XMLDecl => "", RootName=>'Series');
		string2file($seriesxml, "series/$id/all/$language->{abbreviation}.xml");
	}


	## Now let's get the seasons for each sort order
	## Sortorder = default
	my $sth = $dbh->prepare(qq{ SELECT (SELECT season FROM tvseasons WHERE id=tvepisodes.seasonid) AS season, id, EpisodeNumber FROM tvepisodes WHERE seriesid=$id ORDER BY season,EpisodeNumber });
	$sth->execute();
	while (my ($d_season, $d_id, $d_episode) = $sth->fetchrow_array())  {
		$d_id = int($d_id);
		$d_season = int($d_season);
		$d_episode = int($d_episode);
		$htaccess .= "RewriteRule ^default/$d_season/$d_episode/(.*)\$ /data/episodes/$d_id/\$1 [L]\n";
		$htaccess .= "RewriteRule ^default/$d_season/$d_episode\$ /data/episodes/$d_id/en.xml [L]\n";
		episode2xml($d_id, $id, $d_season);
	}


	## Sortorder = dvd
	my $sth = $dbh->prepare(qq{ SELECT dvd_season, id, dvd_episodenumber FROM tvepisodes WHERE seriesid=$id AND dvd_season IS NOT NULL AND dvd_episodenumber IS NOT NULL ORDER BY dvd_season,dvd_episodenumber });
	$sth->execute();
	while (my ($d_season, $d_id, $d_episode) = $sth->fetchrow_array())  {
		$d_id = int($d_id);
		$d_season = int($d_season);
		$d_episode = int($d_episode);
		$htaccess .= "RewriteRule ^dvd/$d_season/$d_episode/(.*)\$ /data/episodes/$d_id/\$1 [L]\n";
		$htaccess .= "RewriteRule ^dvd/$d_season/$d_episode\$ /data/episodes/$d_id/en.xml [L]\n";
	}


	## Sortorder = absolute
	my $sth = $dbh->prepare(qq{ SELECT id, absolute_number FROM tvepisodes WHERE seriesid=$id AND absolute_number IS NOT NULL ORDER BY absolute_number });
	$sth->execute();
	while (my ($d_id, $d_episode) = $sth->fetchrow_array())  {
		$d_id = int($d_id);
		$d_episode = int($d_episode);
		$htaccess .= "RewriteRule ^absolute/$d_episode/(.*)\$ /data/episodes/$d_id/\$1 [L]\n";
		$htaccess .= "RewriteRule ^absolute/$d_episode\$ /data/episodes/$d_id/en.xml [L]\n";
	}


	## Write our .htaccess file to disk
	string2file($htaccess, "series/$id/.htaccess");


	## Write end of root items for each language
	foreach my $abbreviation (keys %languages)  {
		string2file("</Data>", "series/$id/all/$abbreviation.xml");
		system("zip -j " . $basedir . "data/series/$id/all/$abbreviation.zip " . $basedir . "data/series/$id/all/$abbreviation.xml " . $basedir . "data/series/$id/banners.xml");
	}
	system("chmod -R 777 $basedir/data/series/$id");
}


#############################################################
## Function to dump an episode record to XML
#############################################################
sub episode2xml {
	## First, get the parameters
	my $id = shift;
	my $seriesid = shift;
	my $season = shift;
	my $htaccess = "DirectoryIndex en.xml\nRewriteEngine on\n";

	## Generate the full directory name
	my $fulldir = "episodes/" . substr($id, 0, 4) . "/" . $id;

	## Let's remove the current directory
	if (-d $basedir . "data/" . $fulldir)  {
		system("rm -rf " . $basedir . "data/" . $fulldir);
	}


	## Let's get our base episode record
	my $sth = $dbh->prepare(qq{ SELECT *, (SELECT ROUND(AVG(rating),1) FROM ratings WHERE itemtype='episode' AND itemid=$id) AS Rating FROM tvepisodes WHERE id=$id LIMIT 1 }); 
	$sth->execute();
	my $data = $sth->fetchrow_hashref();


	## Remove fields
	delete $data->{"ShowURL"};
	delete $data->{"flagged"};
	delete $data->{"lastupdatedby"};
	delete $data->{"locked"};
	delete $data->{"lockedby"};
	delete $data->{"mirrorupdate"};
	delete $data->{"thumb_author"};


	## Now let's loop through our languages
	my $sth = $dbh->prepare(qq{ SELECT * FROM languages WHERE enabled=1 ORDER BY id }); 
	$sth->execute();
	while (my $language = $sth->fetchrow_hashref())  {

		my $recordtranslated = 0;
		$languages{$language->{abbreviation}} = $language->{id};
		if ($language->{id} == 7)  {
			$recordtranslated = 1;
		}
		

		## Remove fields
		delete $data->{"EpisodeName"};
		delete $data->{"Overview"};


		## Add fields
		$data->{"Language"} = $language->{"abbreviation"};
		$data->{"SeasonNumber"} = $season;
		if ($data->{"DVD_season"})  {
			$data->{"Combined_season"} = $data->{"DVD_season"};
		}
		else  {
			$data->{"Combined_season"} = $data->{"SeasonNumber"};
		}
		if ($data->{"DVD_episodenumber"})  {
			$data->{"Combined_episodenumber"} = $data->{"DVD_episodenumber"};
		}
		else  {
			$data->{"Combined_episodenumber"} = $data->{"EpisodeNumber"};
		}

		## Delete some fields
		if ($data->{"SeasonNumber"} != 0)  {
			delete $data->{"airsafter_season"};
			delete $data->{"airsbefore_episode"};
			delete $data->{"airsbefore_season"};
		}


		## Get our episode name for this language
		my $sth2 = $dbh->prepare(qq{ SELECT translation,languageid FROM translation_episodename WHERE episodeid=$id AND (languageid = $language->{"id"} OR languageid = 7) ORDER BY languageid DESC LIMIT 1 });
		$sth2->execute();
		my ($translation, $languageid) = $sth2->fetchrow_array();
		$data->{"EpisodeName"} = $translation;
		if ($languageid != 7)  {
			$recordtranslated = 1;
		}


		## Get our episode overview for this language
		my $sth2 = $dbh->prepare(qq{ SELECT translation,languageid FROM translation_episodeoverview WHERE episodeid=$id AND (languageid = $language->{"id"} OR languageid = 7) ORDER BY languageid DESC LIMIT 1 });
		$sth2->execute();
		my ($translation, $languageid) = $sth2->fetchrow_array();
		$data->{"Overview"} = $translation;
		if ($languageid != 7)  {
			$recordtranslated = 1;
		}


		## Create our output file for this language
		if ($recordtranslated == 1)  {
			my %newdata = ();
			$newdata->{"Episode"} = $data;
			my $xml = XMLout($newdata, NoAttr => 1, XMLDecl => $encoding, RootName=>'Data');
			string2file($xml, "$fulldir/$language->{abbreviation}.xml");
			delete $newdata->{"Episode"};
		}
		else  {
			$htaccess .= "RewriteRule ^$language->{abbreviation}.xml\$ en.xml [L]\n";
		}


		## Store the xml for the series record (will append)
		my $seriesxml = XMLout($data, NoAttr => 1, XMLDecl => "", RootName=>'Episode');
		string2file($seriesxml, "series/$seriesid/all/$language->{abbreviation}.xml");
	}


	## Write our .htaccess file to disk
	string2file($htaccess, "$fulldir/.htaccess");
}


#############################################################
## Function to write a series' banners to file
#############################################################
sub banners2xml {
	## First, get the parameters
	my ($id) = shift;
	my $bannerxml = "$encoding\n<Banners>\n";


	## Get all of our banner data
	my $sth = $dbh->prepare(qq{ SELECT *, (SELECT abbreviation FROM languages WHERE id=banners.languageid LIMIT 1) AS Language, (SELECT AVG(rating) FROM ratings WHERE itemtype='banner' AND itemid=banners.id) AS Rating FROM banners WHERE keyvalue=$id  ORDER BY keytype,Rating DESC,RAND() });
	$sth->execute();
	while (my $banner = $sth->fetchrow_hashref())  {
		my $outdata;
		$outdata->{"id"} = $banner->{id};
		$outdata->{"BannerPath"} = $banner->{filename};
		$outdata->{"Language"} = $banner->{Language};

		if ($banner->{keytype} eq 'series')  {
			$outdata->{"BannerType"} = "series";
			$outdata->{"BannerType2"} = $banner->{subkey};
		}
		elsif ($banner->{keytype} eq 'season' || $banner->{keytype} eq 'seasonwide')  {
			$outdata->{"BannerType"} = "season";
			$outdata->{"BannerType2"} = $banner->{keytype};
			$outdata->{"Season"} = $banner->{subkey};
		}
		elsif ($banner->{keytype} eq 'fanart')  {
			$outdata->{"BannerType"} = "fanart";
			$outdata->{"BannerType2"} = $banner->{resolution};
			$outdata->{"Colors"} = $banner->{artistcolors};
			$outdata->{"VignettePath"} = $banner->{filename};
			$outdata->{"VignettePath"} =~ s/original/vignette/i;
			$outdata->{"ThumbnailPath"} = $banner->{filename};
			$outdata->{"ThumbnailPath"} =~ s/fanart/_cache\/fanart/i;
		}
		else  {
			next;
		}
		$bannerxml .= XMLout($outdata, NoAttr => 1, XMLDecl => "", RootName=>'Banner');
	}
	$bannerxml .= "</Banners>";


	## Output to file
	string2file($bannerxml, "series/$id/banners.xml");
}


#############################################################
## Function to write a string to file
#############################################################
sub string2file {
	## First, get the parameters
	my ($string, $filename) = @_;

	## Now store this file in $mirrorfiles
	$mirrorfiles .= "data/$filename\n";

	## Append our data dir
	$filename = $basedir . "data/" . $filename;

	## Now separate the directory portion and create it if necessary
	$filename	=~ /\A(.*)\//;
	mkdir_recursive($1);

	## Now create the file
	open(OUT, ">>$filename");
	print OUT $string;
	close(OUT);

	## Now change that file's permissions
	chmod(0777, $filename);
}


#############################################################
## Function to recursively create a directory
#############################################################
sub mkdir_recursive {
	## First, get the parameters
	my ($dir) = @_;

	## End case... no slashes left
	unless ($dir =~ /\//)  {
		return;
	}

	## Now we get the previous directory and call this function
	$dir =~ /\A(.*)\//;
	mkdir_recursive($1);

	## Once back, we create the current directory
	mkdir($dir);
}
