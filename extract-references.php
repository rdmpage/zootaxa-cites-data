<?php

require_once(dirname(__FILE__) . '/lib.php');

require_once 'vendor/autoload.php';
use Sunra\PhpSimple\HtmlDomParser;


$basedir = dirname(__FILE__) . '/articles';
$sourcedir = dirname(__FILE__) . '/articles';

$files1 = scandir($sourcedir);

// debugging
//$files2=array('2441.1.1.html');

$count = 1;

$files1 = scandir($sourcedir);

foreach ($files1 as $directory)
{
	//echo $directory . "\n";
	if (preg_match('/^\d+$/', $directory))
	{	
		//echo $directory . "\n";
		
		$files2 = scandir($sourcedir . '/' . $directory);

		foreach ($files2 as $filename)
		{
			echo $filename . "\n";
			if (preg_match('/\.html$/', $filename))
			{
			
				$html = file_get_contents($sourcedir . '/' . $directory . '/' . $filename);
			
				$dom = HtmlDomParser::str_get_html($html);
				
				//echo $html;
				
				$references = array();
				
				foreach ($dom->find('div[id=articleCitations] p') as $p)
				{
					$text = $p->plaintext;
					
					$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
					
					if (preg_match('/^\s+https?:\/\/(dx.)?doi/', $text))
					{
						$n = count($references);
						$references[$n-1] .= $text;
					}
					else
					{
						$references[] = $text;
					}
					
				}
				
				print_r($references);
				
			

			}
		}
	}
}

/*

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
*/
		
?>

