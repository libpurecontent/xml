<?php

# Class providing static functions to manipulate XML
class xml
{
	# Wrapper to get the XML data
	function xml2array ($xmlfile, $isString = false) {
		
		if ($isString) {
			$xml = $xmlfile;
		} else {
			
			# Check the file exists
			if (!file_exists ($xmlfile)) {
				echo "\n<p>The specified data file could not be found.</p>";
				return false;
			}
			
			# Get the contents
			$xml = file_get_contents ($xmlfile);
		}
		
		# Convert entities
		$entities = array (
			'&ucirc;' => '&#219;',
			'&ograve;' => '&#219;',
			'&agrave;' => '&#219;',
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
		
		# Get the XML as an object
		/*
		$xml = str_replace ('file:///C:\MODES\OBJECT\OBJECT50.DTD', '/data/modes/object/object50.DTD', $xml);
		$xmlobject = simplexml_load_string ($xml, NULL, LIBXML_DTDLOAD);
		*/
		// $xmlobject = simplexml_load_string ($xml);
		$dom = new domDocument;
		$dom->loadXML($xml);
		$xmlobject = simplexml_import_dom ($dom);
		
		
		# echo "<pre>"; var_dump ($xmlobject->asXml ()); echo "</pre>";
		
		/*
		foreach ($xmlobject->OBJECT->asXml as $item) {
			$foo = 'ADMIN-STATUS';
			
		}
		*/
		
		/*
		$foo = 'ADMIN-STATUS';
		echo "<pre>"; var_dump ($xmlobject->OBJECT->$foo->asXml ()); echo "</pre>";
		// echo "<pre>"; var_dump ($xmlobject->asXml ()); echo "</pre>";
		*/
		
/*
		
		$xmlobject = simplexml_load_file ($xmlfile, NULL, LIBXML_NOWARNING);
*/
		
		# Convert the object to a multi-dimensional array
		$xml = self::simplexml2array ($xmlobject);
		# echo "<pre>"; var_dump ($xml); echo "</pre>";
		
		/*
		if ($result = $xmlobject->xpath ('/OBJECT-SET/OBJECT/RECORD-NUMBER')) {
			while(list(, $node) = each($result)) {
	   			$recordNumber = $node->asXML();
				ereg ('<RECORD-NUMBER>([^<]*)</RECORD-NUMBER>', $recordNumber, $matches);
				$records[] = $matches[0];
			}
		}
		if ($result = $xmlobject->xpath ('/OBJECT-SET/OBJECT/ADMIN_STATUS')) {
			while(list(, $node) = each($result)) {
	   			$status = $node->asXML();
				ereg ('<STATUS>([^<]*)</STATUS>', $recordNumber, $matches);
				application::dumpData ($matches);
			}
		}
		*/
		# Return the XML
		return $xml;
	}
	
	
	# DOM-based xml2array
	function DOMxml2array ($xmlfile)
	{
		$xml = file_get_contents ($xmlfile);
		# Convert entities
		$entities = array (
			'&ucirc;' => '&#219;',
			'&ograve;' => '&#219;',
			'&agrave;' => '&#219;',
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
		$dom = new domDocument;
		$dom->loadXML($xml);
//		echo "<pre>"; var_dump ($dom); echo "</pre>";
		$xmlobject = simplexml_import_dom ($dom);
		
		# Convert the object to a multi-dimensional array
		$data = self::DOMxml2array ($xmlobject);
		
		# Convert the object to a multi-dimensional array
//		$data = array();
//		dom_to_simple_array ($dom, $data);
		
		# Return the XML
		return $data;
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
	
	
	
	
	/*
	# From http://uk.php.net/manual/en/function.dom-domdocument-load.php
	function processXsd ($file)
	{
		$XSDDOC = new DOMDocument();
		$XSDDOC->preserveWhiteSpace = false;
		$attributes = array();
		if ($XSDDOC->load ($file)) {
		   $xsdpath = new DOMXPath($XSDDOC);
		   $attributeNodes = $xsdpath->query('//xs:simpleType[@name="attributeType"]')->item(0);
		   foreach ($attributeNodes->childNodes as $attr) {
		       $attributes[ $attr->getAttribute('value') ] = $attr->getAttribute('name');
		   }
		   unset($xsdpath);
		}
		
		# Return the attributes
		return $attributes;
	}
	*/
}




	# From http://uk.php.net/manual/en/ref.domxml.php#47418
	function dom_to_simple_array($domnode, &$array) {
	  require_once ('domxml-php4-to-php5/domxml-php4-to-php5.php');
	  $array_ptr = &$array;
	  $domnode = $domnode->firstChild;
	  while (!is_null($domnode)) {
	   if (! (trim($domnode->nodeValue) == "") ) {
	     switch ($domnode->nodeType) {
	       case XML_TEXT_NODE: {
	         $array_ptr['cdata'] = $domnode->nodeValue;
	         break;
	       }
	       case XML_ELEMENT_NODE: {
	         $array_ptr = &$array[$domnode->nodeName][];
	         if ($domnode->hasAttributes() ) {
	           $attributes = $domnode->attributes ();
	           if (!is_array ($attributes)) {
	             break;
	           }
	           foreach ($attributes as $index => $domobj) {
	             $array_ptr[$index] = $array_ptr[$domobj->name] = $domobj->value;
	           }
	         }
	         break;
	       }
	     }
	     if ( $domnode->hasChildNodes() ) {
	       dom_to_simple_array($domnode, $array_ptr);
	     }
	   }
	   $domnode = $domnode->nextSibling;
	  }
	}

?>
