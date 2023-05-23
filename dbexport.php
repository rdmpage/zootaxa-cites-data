<?php

// RIS dump for spoecific queries

$pdo 		= new PDO('sqlite:zootaxa.db');    // name of SQLite database (a file on disk)

//----------------------------------------------------------------------------------------
function do_query($pdo, $sql)
{
	$stmt = $pdo->query($sql);

	$data = array();

	while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

		$item = new stdclass;
		
		$keys = array_keys($row);
	
		foreach ($keys as $k)
		{
			if ($row[$k] != '')
			{
				$item->{$k} = $row[$k];
			}
		}
	
		$data[] = $item;
	}
	
	return $data;	
}

//----------------------------------------------------------------------------------------
	
$ris_key_map = array(
	'id'				=> 'ID',
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


$sql = 'SELECT * FROM export WHERE `container-title` LIKE "Marine Investigations%" ORDER BY CAST(issued AS INT)';



$data = do_query($pdo, $sql);

//print_r($data);

foreach ($data as $obj)
{

	// print_r($obj);

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

?>
