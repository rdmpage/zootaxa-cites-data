<?php

//----------------------------------------------------------------------------------------

	
$ris_key_map = array(
	
	'title'				=> 'TI', 
	'container-title'	=> 'JO', 
	'volume'			=> 'VL', 
	'issue'				=> 'IS', 
	'issued'			=> 'Y1', 
	'container-title'	=> 'JO', 
	
	'publisher'			=> 'PB',
	'publisher-place'	=> 'PP',
	
	'DOI'				=> 'DO', 
	'URL'				=> 'UR', 
	
	'unstructured'		=> 'N1',

);



$filename = 'cited.tsv';
$filename = 'test.tsv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		"\t" 
		);
		
	$go = is_array($row);
	
	if ($go)
	{
		$go = count($row) > 1;
	}
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
		}
		else
		{
			$obj = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
				}
			}
		
			// print_r($obj);	
			
			$ris = '';
			
			if (isset($obj->type))
			{
				switch ($obj->type)
				{
					case 'article-journal':
						$ris .= "TY  - JOUR\n";
						break;

					case 'chapter':
						$ris .= "TY  - CHAP\n";
						break;

					case 'book':
						$ris .= "TY  - BOOK\n";
						break;				
					
					default:
						$ris .= "TY  - GEN\n";
						break;				
				}
			}
			else
			{
				$ris .= "TY  - GEN\n";
			}
			
			foreach ($obj as $k => $v)
			{
				switch ($k)
				{
					case 'author':
						$parts = explode(';', $v);
						$ris .= 'AU  - ' . join("\nAU  - ", $parts) . "\n";
						break;
						
					case 'page':
						$parsed = false;
						
						if (!$parsed)
						{
							if (preg_match('/^[a-z]?\d+$/', $v))
							{
								$ris .= 'SP  - ' . $v . "\n";
								$parsed = true;
							}
						}

						if (!$parsed)
						{
							if (preg_match('/(?<spage>[a-z]?\d+)[-|–](?<epage>[a-z]?\d+)/u', $v, $m))
							{
								$ris .= 'SP  - ' . $m['spage'] . "\n";
								$ris .= 'EP  - ' . $m['epage'] . "\n";
								$parsed = true;
							}
						}

						if (!$parsed)
						{
							$ris .= 'SP  - ' . $v . "\n";
						}						
						
						break;

					default:
						if (isset($ris_key_map[$k]))
						{
							$ris .= $ris_key_map[$k] . '  - ' . $v . "\n";
						}
						break;
				}
			}
			
			$ris .= "ER  - \n";
			
			echo $ris . "\n";
		}
	}	
	$row_count++;
}
?>

