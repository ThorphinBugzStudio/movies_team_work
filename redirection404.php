<?php
/*
	PHP script handling HTTP redirections. To be used as a custom HTTP 404 error page.

	Version 1.9, 2015-01-06, http://alexandre.alapetite.fr/doc-alex/redirection-404/

	------------------------------------------------------------------
	Written by Alexandre Alapetite, http://alexandre.alapetite.fr/cv/

	Open Source, Copyright 2007-2015
	Licence: Creative Commons "Attribution-ShareAlike 2.0 France", BY-SA (FR),
	http://creativecommons.org/licenses/by-sa/2.0/fr/
	- Attribution. You must give the original author credit
	- Share Alike. If you alter, transform, or build upon this work,
	  you may distribute the resulting work only under a license identical to this one
	  (Can be included in GPL/LGPL projects)
	- The French law is authoritative
	- Any of these conditions can be waived if you get permission from Alexandre Alapetite
	- Please send to Alexandre Alapetite the modifications you make,
	  in order to improve this file for the benefit of everybody

	If you want to distribute this code, please do it as a link to:
	http://alexandre.alapetite.fr/doc-alex/redirection-404/
*/

//--<Constants>--

//Number of levels from the root of the Web site, to this script, for automatic handling of Web sites which root is in a sub-folder of the Web server.
//Set to -1 to disable this functionality.
//For example, set to 1 if this script is in http://example.com/errors/ or http://example.net/~myWebSite/errors/
$distanceToRoot=1;

//Root of the folder containing the redirection (404.txt files)
$path404=(empty($_SERVER['SCRIPT_FILENAME']) ? '.' : dirname($_SERVER['SCRIPT_FILENAME'])).'/404/';

$customRedirect='/';	//To specify an optional custom HTML redirection after the error message
$customRedirectTimeOut=5;	//Delay in second before redirection to the optional above address

$defaultNewServer='';	//Change the server for redirections using relative addresses: for example 'http://example.net:80'

$allowASPmode=true;	//Allow old addresses to be given as a parameter (ASP.NET mode, or to receive 404 errors from an external server)

//--</Constants>--

//Get old address
$oldUrl='';
if (!empty($_SERVER['REQUEST_URI'])) $oldUrl=substr($_SERVER['REQUEST_URI'],0,1024);	//Apache, IIS6
elseif (!empty($_SERVER['QUERY_STRING'])) $oldUrl=substr($_SERVER['QUERY_STRING'],0,1024);	//IIS5
else $oldUrl='/_unknown_';	//Do not use $_SERVER['REDIRECT_URL'] (see Apache PHP module)
if (($sc=strpos($oldUrl,'404;'))!==false) $oldUrl=trim(substr($oldUrl,$sc+4));	//IIS
$oldUrlParsed=strpos($oldUrl,'://')===false ? @parse_url($oldUrl) : '';
if (empty($oldUrlParsed))
{
	$oldUrl='/_unknown_';
	$oldUrlParsed=parse_url($oldUrl);
}
if ($allowASPmode&&(!empty($oldUrlParsed['query'])))	//Special ASP.NET
{
	parse_str($oldUrlParsed['query'],$oldquery);
	if (!empty($oldquery['aspxerrorpath'])) $oldUrlParsed=parse_url($oldquery['aspxerrorpath']);
}
$oldPath=$oldUrlParsed['path'];
$siteRoot='';	//For the case when the Web site is not at the root of the Web server, such as http://example.net/~myWebSite/
if (($distanceToRoot>=0)&&(!empty($_SERVER['SCRIPT_NAME'])))
{
	$map404=$_SERVER['SCRIPT_NAME'];
	if (substr($map404,-1)!=='/') $map404=dirname($map404);
	$map404=trim($map404,'/\\');
	$dirs=explode('/',$map404);
	$nbSubLevels=count($dirs)-$distanceToRoot;
	for ($i=0;$i<$nbSubLevels;$i++) $siteRoot.='/'.$dirs[$i];
	if (!empty($siteRoot))
	{
		if (strcasecmp(substr($oldPath,0,strlen($siteRoot)),$siteRoot)===0) $oldPath=substr($oldPath,strlen($siteRoot));
		if ((!empty($customRedirect))&&($customRedirect[0]==='/')) $customRedirect=$siteRoot.$customRedirect;
	}
}

//Search the best 404.txt mapping file in the file-tree structure
$absolute='/';
$dirs=explode('/',$oldPath);	//We do not do urldecode(), so special characters (e.g. space) of local folders must be %-encoded: ./404/Hello%20World/404.txt
foreach ($dirs as $dir)
	if (strlen($dir)>0)
	{
		if (($dir[0]!=='.')&&is_dir($path404.$dir))
		{
			$path404.=$dir.'/';
			$absolute.=$dir.'/';
		}
		else break;
	}
$path404.='404.txt';

//Search in the 404.txt file for the first matching for $oldPath
$newPath='';
$httpStatus=302;
$found=false;
if (is_file($path404)&&($handle=@fopen($path404,'r')))
{
	while (!feof($handle))
	{
		$line=trim(fgets($handle,4096));
		if ((strlen($line)<3)||($line[0]=='#')) continue;	//comment or invalid
		$map=preg_split('"\s+"',$line,4);
		if (count($map)<2) continue;	//invalid
		$mapOld=$map[1];
		if ($mapOld[0]!='/') $mapOld=$absolute.$mapOld;
		if (@preg_match('"^'.$mapOld.'$"iD',$oldPath)&&
		 ((($status=$map[0])==='gone')||
		  ((count($map)>2)&&
		   (strlen($newPath=@preg_replace('"^'.$mapOld.'$"iD',$map[2],$oldPath))>0))))
		{
			switch ($status)
			{
				case 'permanent': $httpStatus=301; break;
				case 'found':
				case 'temp': $httpStatus=302; break;
				case 'seeother': $httpStatus=303; break;
				case 'temporary': $httpStatus=307; break;
				case 'gone': $httpStatus=410; break;
				default: continue;
			}
			$found=true;
			if ($httpStatus!==410)
			{
				if (!empty($siteRoot)) $newPath=$siteRoot.$newPath;
				if (!preg_match('"^(?:(?:[a-z]{3,6}:)|(?:\.\./))"i',$newPath))	//No URI Scheme, and no ../ in front
				{//When it is possible and not already the case, make the redirection an absolute URL
					if (empty($defaultNewServer)&&isset($_SERVER['HTTP_HOST']))
					{
						$defaultNewServer=(empty($_SERVER['HTTPS'])?'http':'https').'://'.$_SERVER['HTTP_HOST'];
						if (!empty($_SERVER['SERVER_PORT']))
						{
							if (empty($_SERVER['HTTPS']))
							{
								if ($_SERVER['SERVER_PORT']!='80') $defaultNewServer.=':'.$_SERVER['SERVER_PORT'];
							}
							elseif ($_SERVER['SERVER_PORT']!='443') $defaultNewServer.=':'.$_SERVER['SERVER_PORT'];
						}
						if (empty($newPath)||($newPath[0]!=='/'))	//relative address
							$newPath=rtrim(substr($oldPath,-1)==='/' ? $oldPath : dirname($oldPath),'/\\').'/'.$newPath;
					}
					$newPath=$defaultNewServer.$newPath;
				}
			}
			break;
		}
	}
	fclose($handle);
}

if ($found)	//Redirect if new address is found
{
	if ($httpStatus===410)
	{
		header('HTTP/1.1 410 Gone');
		header('Status: 410 Gone');
		echo '<!DOCTYPE html>'."\n",
		 '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">'."\n",
		 '<head>'."\n",
		 '<meta charset="UTF-8" />'."\n",
		 empty($customRedirect) ? '' : '<meta http-equiv="Refresh" content="'.$customRedirectTimeOut.'; url='.$customRedirect.'" />'."\n",
		 '<title>410 Gone</title>'."\n",
		 '<meta name="robots" content="noindex,follow" />'."\n",
		 '</head>'."\n",
		 '<body>'."\n",
		 '<h1>Gone</h1>'."\n",
		 '<p>The requested resource <kbd>'.$oldPath.'</kbd> is no longer available on this server and there is no forwarding address. ',
		 'Please remove all references to this resource.</p>'."\n",
		 '</body>'."\n",
		 '</html>'."\n";
	}
	else
	{
		if (isset($oldUrlParsed['query'])) $newPath.='?'.$oldUrlParsed['query'];
		$status=array(301=>'Moved Permanently',302=>'Found',303=>'See Other',307=>'Temporary Redirect');
		header('Location: '.$newPath);
		header('HTTP/1.1 '.$httpStatus.' '.$status[$httpStatus]);
		header('Status: '.$httpStatus.' '.$status[$httpStatus]);
		echo '<!DOCTYPE html>'."\n",
		 '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">'."\n",
		 '<head>'."\n",
		 '<meta charset="UTF-8" />'."\n",
		 '<meta http-equiv="Refresh" content="0; url='.$newPath.'" />'."\n",
		 '<title>'.$httpStatus.' '.$status[$httpStatus].'</title>'."\n",
		 '<meta name="robots" content="noindex,follow" />'."\n",
		 '</head>'."\n",
		 '<body>'."\n",
		 '<h1>'.$status[$httpStatus].'</h1>'."\n",
		 '<p>The document has moved <a href="'.$newPath.'">here</a>.</p>'."\n",
		 '</body>'."\n",
		 '</html>'."\n";
	}
}
else	//404 error message
{
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	echo '<!DOCTYPE html>'."\n",
	 '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">'."\n",
	 '<head>'."\n",
	 '<meta charset="UTF-8" />'."\n",
	 empty($customRedirect) ? '' : '<meta http-equiv="Refresh" content="'.$customRedirectTimeOut.'; url='.$customRedirect.'" />'."\n",
	 '<title>404 Not Found</title>'."\n",
	 '<meta name="robots" content="noindex,follow" />'."\n",
	 '</head>'."\n",
	 '<body>'."\n",
	 '<h1>Not Found</h1>'."\n",
	 '<p>The requested <abbr title="Uniform Resource Locator">URL</abbr> <kbd>'.$oldPath.'</kbd> was not found on this server.</p>'."\n",
	 '</body>'."\n",
	 '</html>'."\n";
}
