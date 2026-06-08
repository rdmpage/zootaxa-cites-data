<?php

// Visit all references and do something with them

$sourcedir = dirname(__FILE__) . '/articles';

$files1 = scandir($sourcedir);

// debugging
$files1 = array('1409');

foreach ($files1 as $directory)
{
	if (preg_match('/^\d+$/', $directory))
	{	
		echo $directory . "\n";		
		
		$files2 = scandir($sourcedir . '/' . $directory);
		
		//$files2 = array('1409.1.1.json');

		foreach ($files2 as $filename)
		{
			if (preg_match('/\.json$/', $filename))
			{
				echo $filename . "\n";
				
				$full_filename = $sourcedir . '/' . $directory . '/' . $filename;
				
				$json = file_get_contents($full_filename);
				
				echo $json . "\n";
			}
		}
	}
}

?>
