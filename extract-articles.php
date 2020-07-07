<?php

require_once(dirname(__FILE__) . '/lib.php');

require_once 'vendor/autoload.php';
use Sunra\PhpSimple\HtmlDomParser;


$basedir = dirname(__FILE__) . '/articles';
$sourcedir = dirname(__FILE__) . '/contents';

$files = scandir($sourcedir);

// debugging
$files=array('2441.1.html');

$count = 1;


foreach ($files as $filename)
{
	// echo "filename=$filename\n";
	
	if (preg_match('/\.html$/', $filename))
	{	
		$html = file_get_contents($sourcedir . '/' . $filename);
		
		$dom = HtmlDomParser::str_get_html($html);
		
		foreach ($dom->find('div[class=tocTitle] a') as $a)
		{
			echo $a->href . "\n";
			
			$url = $a->href;
			
			$filename = str_replace('https://www.mapress.com/j/zt/article/view/zootaxa.', '', $url);
			$filename .= '.html';
			
			if (preg_match('/^(?<volume>\d+)\./', $filename, $m))
			{
				$volume = $m['volume'];
				
				// folder for volume
				
				$dir = $basedir . '/' . $volume ;
				if (!file_exists($dir))
				{
					$oldumask = umask(0); 
					mkdir($dir, 0777);
					umask($oldumask);
				}
	
				$filename = $dir . '/' . $filename;
				
				$article = get($url);
			
				file_put_contents($filename, $article);
			
				// Give server a break every 10 items
				if (($count++ % 10) == 0)
				{
					$rand = rand(1000000, 3000000);
					echo "\n ...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
					usleep($rand);
				}
				
				
			}
			else
			{
				echo "Problem\n";
				exit();
			}
	
			
		}
		
	}

}

		
?>

