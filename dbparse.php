<?php

// Parse unstructured citations in the database

require_once (dirname(__FILE__) . '/lib.php');

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


$sql = 'SELECT * FROM export WHERE issued="1922" ORDER BY `container-title`';


$journal = 'Journal of the Asiatic Society of Bengal';
$journal = 'Spixiana';
$sql = 'SELECT * FROM export WHERE unstructured LIKE "%' . $journal . '%"';

$sql = 'SELECT * FROM export WHERE guid="10.11646/zootaxa.270.1.1" ORDER BY `container-title`';


$data = do_query($pdo, $sql);

//print_r($data);

foreach ($data as $obj)
{
	$text = $obj->unstructured;
	
	$url = 'http://localhost/citation-parsing/api.php?text=' . urlencode($text);

	$json = get($url);

	//echo $json;

	$doc = json_decode($json);
	
	if (isset($doc[0]))
	{
		$csl = $doc[0];
		
		$citing_work = new stdclass;
		$citing_work->DOI = $obj->guid;
		$csl->{'is-referenced-by'} = [$citing_work];
				
		// print_r($csl);
		
		echo json_encode($csl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . "\n";
	}				
}

?>
