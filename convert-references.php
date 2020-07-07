<?php

require_once(dirname(__FILE__) . '/lib.php');


$basedir = dirname(__FILE__) . '/articles';
$sourcedir = dirname(__FILE__) . '/articles';

$files1 = scandir($sourcedir);

// debugging
//$files2=array('2441.1.1.html');

$count = 1;

$files1 = scandir($sourcedir);

$keys = array('author', 'title', 'container-title', 'type', 'volume', 'issue', 'issued', 'page', 'publisher', 'publisher-place', 'editor', 'edition', 'genre', 'note', 'director', 'source', 'URL', 'DOI', 'PMID', 'PMCID');

echo join("\t", $keys) . "\n";


foreach ($files1 as $directory)
{
	//echo $directory . "\n";
	if (preg_match('/^\d+$/', $directory))
	{	
		//echo $directory . "\n";
		
		$files2 = scandir($sourcedir . '/' . $directory);

		foreach ($files2 as $filename)
		{
			//echo $filename . "\n";
			if (preg_match('/\.json$/', $filename))
			{
				$base_filename = str_replace('.json', '', $filename);
			
				$json = file_get_contents($sourcedir . '/' . $directory . '/' . $filename);
				
				$references = json_decode($json);
				
				//print_r($references);
				
				if ($references)
				{
					
					
					foreach ($references as $reference)
					{
						//print_r($reference);	
						
						// learn
						foreach ($reference as $k => $v)				
						{
							if (!in_array($k, $keys))
							{
								echo "$k no found\n";
								exit();
							}
						}					
					
						$values = array();
					
						foreach ($keys as $k)
						{
							if (isset($reference->{$k}))
							{
								if (is_array($reference->{$k}))
								{
									$key_value = array();
									foreach ($reference->{$k} as $v)
									{
										$parts = array();
										if (isset($v->given))
										{
											$parts[] = $v->given;
										}
										if (isset($v->family))
										{
											$parts[] = $v->family;
										}
										$key_value[] = join(' ', $parts);
									}
									$values[] = join(';', $key_value);
								}
								else
								{
									$values[] = $reference->{$k};
								}
							}
							else
							{
								$values[] = "";
							}
						}
						
						
						echo join("\t", $values) . "\n";
					}
				
				
				}

	
				
			

			}
		}
	}
}


		
?>

