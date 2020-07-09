<?php

// Convert extracted references to TSV file

require_once(dirname(__FILE__) . '/lib.php');

require_once 'vendor/autoload.php';
use Sunra\PhpSimple\HtmlDomParser;

$sourcedir = dirname(__FILE__) . '/articles';
$sourcedir = '/Volumes/Samsung_T5/zootaxa-cites-data/articles';



$files1 = scandir($sourcedir);

// debugging
//$files1 = array('2441');


$keys = array('guid', 'author', 'title', 'container-title', 'type', 'volume', 'issue', 'issued', 'page', 'publisher', 'publisher-place', 'editor', 'edition', 'genre', 'note', 'director', 'producer', 'collection-title', 'citation-number', 'translator', 'source', 'URL', 'DOI', 'PMID', 'PMCID', 'isbn', 'medium');

// Keys we will export
$keys = array(
    'id',
	'guid', 
	'guid-date',
	'author', 
	'title', 
	'container-title', 
	'type', 
	'volume', 
	'issue', 
	'page', 
	'issued', 
	'publisher', 
	'publisher-place', 
	'editor', 
	'URL', 
	'DOI', 
	'PMID', 
	'PMCID', 
	'isbn',
	'note',
	'unstructured'
	);

// Header row for TSV file
echo join("\t", $keys) . "\n";

$count = 1;

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
				// Get CSL file and extract individual references
			
				$base_filename = str_replace('.json', '', $filename);
			
				// Get DOI of citing article from HTML
				$guid = $base_filename;
				
				// date of publication of citing article
				$guid_date = "";
				
				$html_filename = $base_filename . '.html';				
				$html = file_get_contents($sourcedir . '/' . $directory . '/' . $html_filename);
				$dom = HtmlDomParser::str_get_html($html);				
				$metas = $dom->find('meta');

				foreach ($metas as $meta)
				{
					switch ($meta->name)
					{
						case 'DC.Identifier.DOI':
							$guid = $meta->content;
							break;

						case 'DC.Date.issued':
							$guid_date = $meta->content;
							break;
							
						default:
							break;
					}
				}
				
				// Get text of references
				$text_filename = $base_filename . '.txt';
				$text = file_get_contents($sourcedir . '/' . $directory . '/' . $text_filename);

				// Get CSL JSON and convert to tab delimited row
				$json = file_get_contents($sourcedir . '/' . $directory . '/' . $filename);
				
				$references = json_decode($json);
				
				//print_r($references);
				
				if ($references)
				{
					// We also want to store unstructured text so that we can reparse if needed
					$text = preg_replace("/\x{0A}(\x{0A})+/", "\n", $text);
					$text = preg_replace("/^\x{0A}/", "", $text);
					$text = preg_replace("/\x{0A}$/", "", $text);
					
					$text = preg_replace("/\x{0D}$/", "", $text);
					
					// Text may have blacnk lines, which anystyle will skip, so make sure
					// we have same number of lines so we can match up strings and refs
					// echo "\n----\n$text\n----\n";
					$rows = explode("\n", $text);
					$num_rows = count($rows);
					$num_refs = count($references);
					
					// echo "Rows: $num_rows $num_refs\n";
					
					
										
					foreach ($references as $reference)
					{
						//print_r($reference);	
						
						// learn new terms (only use in early stages as we get familiar
						// with anystyle )
						foreach ($reference as $k => $v)				
						{
							if (!in_array($k, $keys))
							{
								//echo "$k not found\n";
								//exit();
							}
						}					
					
						$values = array();
						
						$reference->id = $count;
						
						// So we know what article cites these papers						
						$reference->guid = $guid;
						
						// So we know what date citing paper was published
						$reference->{'guid-date'} = $guid_date;
						
						if ($num_rows == $num_refs)
						{
							// No ambiguity in what reference matches what string, 
							// so unparsed string so we can check later
							$reference->unstructured = $rows[$count];
						}
					
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
						
						$count++;
					}
								
				}

			}
		}
	}
}


		
?>

