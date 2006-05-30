<?php

# XML wrapper class
class xml
{
	# Function to convert XML to an array
	function xml2array ($xmlfile, $cacheXml = false, $documentToDataOrientatedXml = true, $xmlIsFile = true)
	{
		# If there is not a cached file, pre-process the XML
		if (!$cacheXml || ($cacheXml && !file_exists ($cacheXml))) {
			
			# Get the XML
			$xml = ($xmlIsFile ? file_get_contents ($xmlfile) : $xmlfile);
			
			# Remove the DOCTYPE
			$xml = ereg_replace ('<!DOCTYPE ([^>]+)>', '', $xml);
			
			# Convert entities
			$entities = array (
				#!# Convert useful section in http://xml.ascc.net/resource/entities/ISO/ISOlat1.pen to an array
				'&ucirc;' => '&#219;',
				'&ograve;' => '&#210;',
				'&agrave;' => '&#192;',
				'&ouml;' => '&#214;',
				'&eacute;' => '&#201;',
				'&pound;' => '&#163;',
				'&ugrave;' => '&#217;',
				'&egrave;' => '&#200;',
				'&acirc;' => '&#194;',
				'&ecirc;' => '&#202;',
				'&auml;' => '&#196;',
			);
			$xml = str_ireplace (array_keys ($entities), array_values ($entities), $xml);
			
			# Convert from document-orientated to data-orientated XML, if required
			if ($documentToDataOrientatedXml) {
				$xml = self::documentToDataOrientatedXml ($xml);
			}
			
			# Cache the contents if required
			if ($cacheXml) {
				file_put_contents ($cacheXml, $xml);
			}
			
		# Otherwise get the XML file
		} else {
			$xml = file_get_contents ($cacheXml ? $cacheXml : $xmlfile);
		}
		
		# Convert the XML to an object
		$xmlobject = simplexml_load_string ($xml);
		
		# Convert the object to an array
		$xml = self::simplexml2array ($xmlobject);
		
		# Return the XML
		return $xml;
	}
	
	
	# Function to convert from document-orientated to data-orientated XML
	function documentToDataOrientatedXml ($xml)
	{
		# Perform a search & replace on the offending strings
		#!# Note: this fails if xyz is one/two characters only: <CONTAINER>xyz<SUB-CONTAINER>Data</SUB-CONTAINER>
		#!# This is also catching simple top-level cases like e.g. <NUMBER-OF-ITEMS>1</NUMBER-OF-ITEMS> for some reason
		$search = "<([-a-zA-Z0-9]+)>([^<]{1})([^/]{1})([^<]+)" . "<([-a-zA-Z0-9]+)>";
				// e.g. <CLASSIFIED-NAME>Labrador Inuit\n<SYSTEM>
			   // "<([-a-zA-Z0-9]+)>([^<]{1})([^<]+)" . "<([-a-zA-Z0-9]+)>([^<]*)</\\4>" . "</\\1>"	// Note backreferences in search string	// e.g. <CLASSIFIED-NAME>Labrador Inuit\n<SYSTEM>Cultural affiliation - former</SYSTEM></CLASSIFIED-NAME>
		$replacement = "<\\1><\\1>\\2\\3\\4</\\1>" . "<\\5>";
					// "<\\1><\\1>\\2\\3</\\1>" . "<\\4>\\5</\\4>" . "</\\1>",
		$xml = preg_replace ('|' . $search . '|ims', $replacement, $xml);	// preg_replace is much faster than ereg_replace and supports backreferences in the search string
		
		# Return the XML
		return $xml;
	}
	
	
	# From http://uk2.php.net/manual/en/ref.simplexml.php
	function simplexml2array($xml)
	{
	   if (get_class($xml) == 'SimpleXMLElement') {
	       $attributes = $xml->attributes();
	       foreach($attributes as $k=>$v) {
	           if ($v) $a[$k] = (string) $v;
	       }
	       $x = $xml;
	       $xml = get_object_vars($xml);
	   }
	   
	   if (is_array($xml)) {
	       if (count($xml) == 0) return (string) $x; // for CDATA
	       foreach($xml as $key=>$value) {
	           $r[$key] = self::simplexml2array($value);
	       }
	       // if (isset($a)) $r['@'] = $a;    // Attributes
	       return $r;
	   }
	   
	   return (string) $xml;
	}
	
	
	# Function to chunk files into pieces into a database
	function databaseChunking ($file, $authenticationFile, $database, $table, $xpath, $recordIdPath, $documentToDataOrientatedXml = true, $timeLimit = 300)
	{
		# Set a larger time limit than the default
		set_time_limit ($timeLimit);
		
		# Obtain the file
		$xml = file_get_contents ($file);
		
		# Remove the DOCTYPE
		// $xml = ereg_replace ('<!DOCTYPE ([^>]+)>', '', $xml);
		
		# Replace . characters in tag names to work around bug http://bugs.mysql.com/20795
		#!# Remove this when fixed in MySQL
		// $xml = ereg_replace ('<([^\.>]*)\.([^>]*)>', '<\1-\2>', $xml);
		$xml = str_replace ('PART.SUMMARY>', 'PART-SUMMARY>', $xml);
		
		# Convert entities
		$entities = array (
			#!# Convert useful section in http://xml.ascc.net/resource/entities/ISO/ISOlat1.pen to an array
			'&ucirc;' => '&#219;',
			'&ograve;' => '&#210;',
			'&agrave;' => '&#192;',
			'&ouml;' => '&#214;',
			'&eacute;' => '&#201;',
			'&pound;' => '&#163;',
			'&ugrave;' => '&#217;',
			'&egrave;' => '&#200;',
			'&acirc;' => '&#194;',
			'&ecirc;' => '&#202;',
			'&auml;' => '&#196;',
		);
		$xml = str_ireplace (array_keys ($entities), array_values ($entities), $xml);
		
		# Convert from document-orientated to data-orientated XML, if required
		if ($documentToDataOrientatedXml) {
			require_once ('xml.php');
			$xml = self::documentToDataOrientatedXml ($xml);
		}
		
		# Cache the contents if required
		// file_put_contents ('./cache.xml', $xml);
		
		# Start an array of data
		$dataset = array ();
		
		# Chunk the XML
		$xml = new SimpleXMLElement ($xml);
		$records = $xml->xpath ($xpath);
		foreach ($records as $record) {
			
			# Assign the record number
			$id = (string) $record->$recordIdPath;
			
			# Get the record itself as XML
			$data = $record->asXML();
			
			# Add the data to the array of records
			$dataset[$id] = array ('id' => $id, 'data' => $data);
		}
		
		# Get the authentication credentials
		#!# This is failing
		if (!is_readable ($authenticationFile)) {
			echo "<p>The authentication file could not be read or does not exist.</p>";
			return false;
		}
		include ($authenticationFile);
		
		# Connect to the database
		require_once ('database.php');
		if (!$databaseConnection = new database ($credentials['hostname'], $credentials['username'], $credentials['password'])) {
			echo "<p>There was a problem connecting to the database.</p>";
			return false;
		}
		
		# Insert the data
		foreach ($dataset as $key => $data) {
			if (!$databaseConnection->insert ($database, $table, $data, 'data=VALUES(data)')) {
				echo "<p>There was a problem inserting the data into the database.</p>";
				return false;
			}
		}
		
		# Return success
		return true;
	}
}

?>