<?php

// Check whether we managed to get references from most articles.
// If the modal siz eof .json files is 0 then we have a problem,
// so list the corresponding HTML fiels so we can refetch them.
// Also get list of enclosing directories so we have a list of
// directories to rerun.

require_once 'vendor/autoload.php';
use Sunra\PhpSimple\HtmlDomParser;


$basedir = dirname(__FILE__) . '/articles';
$sourcedir = dirname(__FILE__) . '/articles';

$basedir   = '/Volumes/Samsung_T5/zootaxa-cites-data/articles';
$sourcedir = '/Volumes/Samsung_T5/zootaxa-cites-data/articles';


$files1 = scandir($sourcedir);

//$files1 = array('3904');
//$files1 = array('3610');

// debugging

$count = 1;

$redo_fetch = array();
$redo_process = array();

foreach ($files1 as $directory)
{
	echo $directory . "\n";
	if (preg_match('/^\d+$/', $directory))
	{	
		//echo $directory . "\n";
		
		$files2 = scandir($sourcedir . '/' . $directory);
		
		$filesizes = array();
		
		
		$zero = array();

		foreach ($files2 as $filename)
		{
			
			if (preg_match('/\.txt$/', $filename))
			{
				echo $filename . "\n";
				$path = $sourcedir . '/' . $directory . '/' . $filename;
				
				$size = filesize($path);
			
				$filesizes[] = $size;
				
				if ($size == 0)
				{
					$name = preg_replace('/(\d+\.\d+)(\.\d+)+.txt/', '$1', $filename);
					$name .= '.html';
					$zero[] = '"' . $name . '"';
				}
			}
		}
		
		sort($filesizes);
		
		$half = floor(count($filesizes)/2);
		
		print_r($filesizes);
		
		if ($filesizes[$half] == 0)
		{
			$zero = array_unique($zero);
			$redo_fetch = array_merge($redo_fetch, $zero);
			$redo_process[] = $directory;
		}
		
		
		
		echo "modal size=" . $filesizes[$half] . "\n";
	}
}

print_r($redo_fetch);
print_r($redo_process);


echo '$files=array(' . "\n";
echo join(",\n", $redo_fetch);
echo "\n" . ');' . "\n";

echo '$files1=array(' . "\n";
echo join(",\n", $redo_process);
echo "\n" . ');' . "\n";