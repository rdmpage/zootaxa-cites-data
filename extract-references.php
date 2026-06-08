<?php

// Process individual article pages and extract list of references

require_once(dirname(__FILE__) . '/lib.php');

if (1)
{
	require_once (dirname(__FILE__) . '/HtmlDomParser.php');
}
else
{
	require_once (dirname(__FILE__) . '/vendor/autoload.php');
}

use Sunra\PhpSimple\HtmlDomParser;


$basedir = dirname(__FILE__) . '/articles';
$sourcedir = dirname(__FILE__) . '/articles';

$files1 = scandir($sourcedir);

//$files1 = array('1026');

//$files1 = array('3904');

//$files1 = array('270');
//$files1 = array('4162');
//$files1 = array('1342');

// debugging

$count = 1;


foreach ($files1 as $directory)
{
	//echo $directory . "\n";
	if (preg_match('/^\d+$/', $directory))
	{	
		//echo $directory . "\n";
		
		$files2 = scandir($sourcedir . '/' . $directory);
		
		//$files2 = ['1342.1.1.html'];

		foreach ($files2 as $filename)
		{
			echo $filename . "\n";
			if (preg_match('/\.html$/', $filename))
			{
				$base_filename = str_replace('.html', '', $filename);
			
				$html = file_get_contents($sourcedir . '/' . $directory . '/' . $filename);
				
				$pid = '';
			
				$dom = HtmlDomParser::str_get_html($html);
				
				foreach ($dom->find('meta') as $meta)
				{		
					switch ($meta->name)
					{				
						case 'citation_doi':
							$pid = $meta->content;
							break;
							
						default:
							break;
					}
				}
				
				if ($pid == '')
				{
					// make up DOI based on file name
					$pid = '10.11646/zootaxa.' . $base_filename;
				}
				
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
						// https://stackoverflow.com/a/18992691/9684
						$text = preg_replace('/\R/u', ' ', $text);					
						$references[] = $text;
					}
					
				}
				
				print_r($references);
				
				$text = join("\n",$references);					
				$textfilename = $sourcedir . '/' . $directory . '/' . $base_filename . '.txt';
				file_put_contents($textfilename, $text);		
				
				$jsonfilename = $sourcedir . '/' . $directory . '/' . $base_filename . '.json';		
				
				if (0)
				{					
					$command = 'anystyle -f csl parse ' . $textfilename . ' > ' . $jsonfilename;
				
					echo $command . "\n";
								
					system($command);
				}
				else
				{					
					// use my parser
					$url = 'http://localhost/citation-parsing/api.php';
					
					$json = post($url, $text);
					
					$obj = json_decode($json);
					
					foreach ($obj as $index => &$csl)
					{
						// add identifier
						$csl->id = $pid . '#row=' . $index;
						
						// add reference
						$citing_work = new stdclass;
						$citing_work->DOI = $pid;
						
						$csl->{'is-referenced-by'} = [$citing_work];						
					}
										
					file_put_contents($jsonfilename, json_encode($obj, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
				}
			}
		}
	}
}


?>

