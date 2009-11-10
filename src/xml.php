<?php

# XML wrapper class
# Version 1.2.2
class xml
{
	# Function to convert XML to an array
	#!# Consider making the last two items default to false
	function xml2array ($xmlfile, $cacheXml = false, $documentToDataOrientatedXml = true, $xmlIsFile = true, $getAttributes = false, $entityConversions = false, $utf8Decode = false)
	{
		# If there is not a cached file, pre-process the XML
		if (!$cacheXml || ($cacheXml && !file_exists ($cacheXml))) {
			
			# Get the XML
			if (!$xml = ($xmlIsFile ? file_get_contents ($xmlfile) : $xmlfile)) {return false;}
			
			# Remove the DOCTYPE
			$xml = ereg_replace ('<!DOCTYPE ([^]]+)]>', '', $xml);
			$xml = ereg_replace ('<!DOCTYPE ([^>]+)>', '', $xml);
			
			# Do entity conversions if necessary
			if ($entityConversions) {
				
				# Get the entities
				$entities = self::getEntityConversions ();
				
				# Convert entities
				$xml = str_replace (array_keys ($entities), array_values ($entities), $xml);
			}
			
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
		
		# End if a problem
		if (!$xml) {return false;}
		
		# Convert the XML to an object
		if (!$xmlobject = simplexml_load_string ($xml, NULL, LIBXML_NOENT)) {return false;}
		
		# Convert the object to an array
		if (!$xml = self::simplexml2array ($xmlobject, $getAttributes, $utf8Decode)) {return false;}
		
		# Return the XML
		return $xml;
	}
	
	
	# Function to get entity conversion
	function getEntityConversions ($url = 'http://www.w3.org/TR/html4/sgml/entities.html')
	{
		/* This commented out code results in the array below
		# Get the file contents
		$document = file_get_contents ($url);
		
		# Perform matches
		preg_match_all ('/ENTITY\s([a-zA-Z]+)(\s+)CDATA(\s+)"&amp;([^"]+)"/', $document, $matches);
		
		# Surround the entities with & and ;]
		foreach ($matches[1] as $index => $value) {
			$matches[1][$index] = "&{$value};";
		}
		foreach ($matches[4] as $index => $value) {
			$matches[4][$index] = "&{$value}";
		}
		
		# Arrange as key to value
		$entities = array_combine ($matches[1], $matches[4]);
		*/
		
		$entities = array (
			'&nbsp;' => '&#160;',
			'&iexcl;' => '&#161;',
			'&cent;' => '&#162;',
			'&pound;' => '&#163;',
			'&curren;' => '&#164;',
			'&yen;' => '&#165;',
			'&brvbar;' => '&#166;',
			'&sect;' => '&#167;',
			'&uml;' => '&#168;',
			'&copy;' => '&#169;',
			'&ordf;' => '&#170;',
			'&laquo;' => '&#171;',
			'&not;' => '&#172;',
			'&shy;' => '&#173;',
			'&reg;' => '&#174;',
			'&macr;' => '&#175;',
			'&deg;' => '&#176;',
			'&plusmn;' => '&#177;',
			'&acute;' => '&#180;',
			'&micro;' => '&#181;',
			'&para;' => '&#182;',
			'&middot;' => '&#183;',
			'&cedil;' => '&#184;',
			'&ordm;' => '&#186;',
			'&raquo;' => '&#187;',
			'&iquest;' => '&#191;',
			'&Agrave;' => '&#192;',
			'&Aacute;' => '&#193;',
			'&Acirc;' => '&#194;',
			'&Atilde;' => '&#195;',
			'&Auml;' => '&#196;',
			'&Aring;' => '&#197;',
			'&AElig;' => '&#198;',
			'&Ccedil;' => '&#199;',
			'&Egrave;' => '&#200;',
			'&Eacute;' => '&#201;',
			'&Ecirc;' => '&#202;',
			'&Euml;' => '&#203;',
			'&Igrave;' => '&#204;',
			'&Iacute;' => '&#205;',
			'&Icirc;' => '&#206;',
			'&Iuml;' => '&#207;',
			'&ETH;' => '&#208;',
			'&Ntilde;' => '&#209;',
			'&Ograve;' => '&#210;',
			'&Oacute;' => '&#211;',
			'&Ocirc;' => '&#212;',
			'&Otilde;' => '&#213;',
			'&Ouml;' => '&#214;',
			'&times;' => '&#215;',
			'&Oslash;' => '&#216;',
			'&Ugrave;' => '&#217;',
			'&Uacute;' => '&#218;',
			'&Ucirc;' => '&#219;',
			'&Uuml;' => '&#220;',
			'&Yacute;' => '&#221;',
			'&THORN;' => '&#222;',
			'&szlig;' => '&#223;',
			'&agrave;' => '&#224;',
			'&aacute;' => '&#225;',
			'&acirc;' => '&#226;',
			'&atilde;' => '&#227;',
			'&auml;' => '&#228;',
			'&aring;' => '&#229;',
			'&aelig;' => '&#230;',
			'&ccedil;' => '&#231;',
			'&egrave;' => '&#232;',
			'&eacute;' => '&#233;',
			'&ecirc;' => '&#234;',
			'&euml;' => '&#235;',
			'&igrave;' => '&#236;',
			'&iacute;' => '&#237;',
			'&icirc;' => '&#238;',
			'&iuml;' => '&#239;',
			'&eth;' => '&#240;',
			'&ntilde;' => '&#241;',
			'&ograve;' => '&#242;',
			'&oacute;' => '&#243;',
			'&ocirc;' => '&#244;',
			'&otilde;' => '&#245;',
			'&ouml;' => '&#246;',
			'&divide;' => '&#247;',
			'&oslash;' => '&#248;',
			'&ugrave;' => '&#249;',
			'&uacute;' => '&#250;',
			'&ucirc;' => '&#251;',
			'&uuml;' => '&#252;',
			'&yacute;' => '&#253;',
			'&thorn;' => '&#254;',
			'&yuml;' => '&#255;',
			'&fnof;' => '&#402;',
			'&Alpha;' => '&#913;',
			'&Beta;' => '&#914;',
			'&Gamma;' => '&#915;',
			'&Delta;' => '&#916;',
			'&Epsilon;' => '&#917;',
			'&Zeta;' => '&#918;',
			'&Eta;' => '&#919;',
			'&Theta;' => '&#920;',
			'&Iota;' => '&#921;',
			'&Kappa;' => '&#922;',
			'&Lambda;' => '&#923;',
			'&Mu;' => '&#924;',
			'&Nu;' => '&#925;',
			'&Xi;' => '&#926;',
			'&Omicron;' => '&#927;',
			'&Pi;' => '&#928;',
			'&Rho;' => '&#929;',
			'&Sigma;' => '&#931;',
			'&Tau;' => '&#932;',
			'&Upsilon;' => '&#933;',
			'&Phi;' => '&#934;',
			'&Chi;' => '&#935;',
			'&Psi;' => '&#936;',
			'&Omega;' => '&#937;',
			'&alpha;' => '&#945;',
			'&beta;' => '&#946;',
			'&gamma;' => '&#947;',
			'&delta;' => '&#948;',
			'&epsilon;' => '&#949;',
			'&zeta;' => '&#950;',
			'&eta;' => '&#951;',
			'&theta;' => '&#952;',
			'&iota;' => '&#953;',
			'&kappa;' => '&#954;',
			'&lambda;' => '&#955;',
			'&mu;' => '&#956;',
			'&nu;' => '&#957;',
			'&xi;' => '&#958;',
			'&omicron;' => '&#959;',
			'&pi;' => '&#960;',
			'&rho;' => '&#961;',
			'&sigmaf;' => '&#962;',
			'&sigma;' => '&#963;',
			'&tau;' => '&#964;',
			'&upsilon;' => '&#965;',
			'&phi;' => '&#966;',
			'&chi;' => '&#967;',
			'&psi;' => '&#968;',
			'&omega;' => '&#969;',
			'&thetasym;' => '&#977;',
			'&upsih;' => '&#978;',
			'&piv;' => '&#982;',
			'&bull;' => '&#8226;',
			'&hellip;' => '&#8230;',
			'&prime;' => '&#8242;',
			'&Prime;' => '&#8243;',
			'&oline;' => '&#8254;',
			'&frasl;' => '&#8260;',
			'&weierp;' => '&#8472;',
			'&image;' => '&#8465;',
			'&real;' => '&#8476;',
			'&trade;' => '&#8482;',
			'&alefsym;' => '&#8501;',
			'&larr;' => '&#8592;',
			'&uarr;' => '&#8593;',
			'&rarr;' => '&#8594;',
			'&darr;' => '&#8595;',
			'&harr;' => '&#8596;',
			'&crarr;' => '&#8629;',
			'&lArr;' => '&#8656;',
			'&uArr;' => '&#8657;',
			'&rArr;' => '&#8658;',
			'&dArr;' => '&#8659;',
			'&hArr;' => '&#8660;',
			'&forall;' => '&#8704;',
			'&part;' => '&#8706;',
			'&exist;' => '&#8707;',
			'&empty;' => '&#8709;',
			'&nabla;' => '&#8711;',
			'&isin;' => '&#8712;',
			'&notin;' => '&#8713;',
			'&ni;' => '&#8715;',
			'&prod;' => '&#8719;',
			'&sum;' => '&#8721;',
			'&minus;' => '&#8722;',
			'&lowast;' => '&#8727;',
			'&radic;' => '&#8730;',
			'&prop;' => '&#8733;',
			'&infin;' => '&#8734;',
			'&ang;' => '&#8736;',
			'&and;' => '&#8743;',
			'&or;' => '&#8744;',
			'&cap;' => '&#8745;',
			'&cup;' => '&#8746;',
			'&int;' => '&#8747;',
			'&sim;' => '&#8764;',
			'&cong;' => '&#8773;',
			'&asymp;' => '&#8776;',
			'&ne;' => '&#8800;',
			'&equiv;' => '&#8801;',
			'&le;' => '&#8804;',
			'&ge;' => '&#8805;',
			'&sub;' => '&#8834;',
			'&sup;' => '&#8835;',
			'&nsub;' => '&#8836;',
			'&sube;' => '&#8838;',
			'&supe;' => '&#8839;',
			'&oplus;' => '&#8853;',
			'&otimes;' => '&#8855;',
			'&perp;' => '&#8869;',
			'&sdot;' => '&#8901;',
			'&lceil;' => '&#8968;',
			'&rceil;' => '&#8969;',
			'&lfloor;' => '&#8970;',
			'&rfloor;' => '&#8971;',
			'&lang;' => '&#9001;',
			'&rang;' => '&#9002;',
			'&loz;' => '&#9674;',
			'&spades;' => '&#9824;',
			'&clubs;' => '&#9827;',
			'&hearts;' => '&#9829;',
			'&diams;' => '&#9830;',
			'&quot;' => '&#34;',
			'&amp;' => '&#38;',
			'&lt;' => '&#60;',
			'&gt;' => '&#62;',
			'&OElig;' => '&#338;',
			'&oelig;' => '&#339;',
			'&Scaron;' => '&#352;',
			'&scaron;' => '&#353;',
			'&Yuml;' => '&#376;',
			'&circ;' => '&#710;',
			'&tilde;' => '&#732;',
			'&ensp;' => '&#8194;',
			'&emsp;' => '&#8195;',
			'&thinsp;' => '&#8201;',
			'&zwnj;' => '&#8204;',
			'&zwj;' => '&#8205;',
			'&lrm;' => '&#8206;',
			'&rlm;' => '&#8207;',
			'&ndash;' => '&#8211;',
			'&mdash;' => '&#8212;',
			'&lsquo;' => '&#8216;',
			'&rsquo;' => '&#8217;',
			'&sbquo;' => '&#8218;',
			'&ldquo;' => '&#8220;',
			'&rdquo;' => '&#8221;',
			'&bdquo;' => '&#8222;',
			'&dagger;' => '&#8224;',
			'&Dagger;' => '&#8225;',
			'&permil;' => '&#8240;',
			'&lsaquo;' => '&#8249;',
			'&rsaquo;' => '&#8250;',
			'&euro;' => '&#8364;',
		);
		
		# Return the matches
		return $entities;
	}
	
	
	# Function to convert from document-orientated to data-orientated XML
	function documentToDataOrientatedXml ($xml)
	{
		# Perform a search & replace on the offending strings
		#!# Note: this fails if xyz is one/two characters only: <CONTAINER>xyz<SUB-CONTAINER>Data</SUB-CONTAINER>
		
		/*  Here is a worked-through example of this bug:
		
		<Administration><Progress><Keyword>R</Keyword><Type>
		
		$search = "<([-a-zA-Z0-9]+)>([^<]{1})([^/]{1})([^<]+)" . "<([-a-zA-Z0-9]+)>";
		$replacement = "<\\1><\\1>\\2\\3\\4</\\1>" . "<\\5>";
				
		<([-a-zA-Z0-9]+)>	([^<]{1})	([^/]{1})	([^<]+)		<([-a-zA-Z0-9]+)>
		
		<Keyword>			R			<			/Keyword>	<Type>
		
		<Keyword><Keyword>R</Keyword></Keyword><Type>
		
		*/
		
		#!# This is also catching simple top-level cases like e.g. <NUMBER-OF-ITEMS>1</NUMBER-OF-ITEMS> for some reason
		#!# Can't get the spaces aspect working
		$search = "<([-a-zA-Z0-9]+)>" . /* "\s*" . */ "([^<])([^/])([^<]+)" . /* "\s*" . */ "<([-a-zA-Z0-9]+)(/?)>";
		$replacement = "<\\1><\\1>\\2\\3\\4</\\1><\\5\\6>";
		#!# This is currently extremely memory-intensive and prone to failure with large files
		$xml = preg_replace ('|' . $search . '|ims', $replacement, $xml);	// preg_replace is much faster than ereg_replace and supports backreferences in the search string
		
		# Return the XML
		return $xml;
	}
	
	
	# From http://uk2.php.net/manual/en/ref.simplexml.php
	function simplexml2array (/* Object */ $xml, $getAttributes = false, $utf8decode = false)
	{
	   if (is_a ($xml, 'SimpleXMLElement')) {
	       $attributes = $xml->attributes();
	       foreach ($attributes as $k => $v) {
	           if ($v) {
//			   	$v = str_replace ("\xe2\x80\xa6", '&#8230;', $v);
//			   	if ($utf8decode) {$v = utf8_decode ($v);}
				$string = (string) $v;
				$a[$k] = $string;
			 }
	       }
	       $x = $xml;
	       $xml = get_object_vars ($xml);
	   }
	   
	   if (is_array ($xml)) {
	       if (count ($xml) == 0) {
//		   	   if ($utf8decode) {$x = utf8_decode ($x);}
		       $return = (string) $x; // for CDATA
			   return $return;
		   }
	       foreach ($xml as $key => $value) {
	           $r[$key] = self::simplexml2array ($value, $getAttributes, $utf8decode);
	       }
	       if ($getAttributes) {
				if (isset ($a)) {
					$r['@'] = $a;    // Attributes
				}
		   }
	       return $r;
	   }
	   
	   if ($utf8decode) {$xml = utf8_decode ($xml);}
	   return (string) $xml;
	}
	
	
	# Function to chunk files into pieces into a database
	function databaseChunking ($file, $authenticationFile, $database, $table, $xpathRecordsRoot, $recordIdPath, $otherPaths = array (), $multiplesDelimiter = '|', $entityConversions = true, $documentToDataOrientatedXml = true, $timeLimit = 300)
	{
		# Set a larger time limit than the default
		set_time_limit ($timeLimit);
		
		# Obtain the file
		if (!is_readable ($file)) {
			echo "\n<p class=\"warning\">The file {$file} could not be read or does not exist.</p>";
			return false;
		}
		$xml = file_get_contents ($file);
		
		# Do entity conversions if required
		if ($entityConversions) {
			
			# Get the entities
			$entities = self::getEntityConversions ();
			
			# Convert entities
			$xml = str_replace (array_keys ($entities), array_values ($entities), $xml);
		}
		
		# Convert from document-orientated to data-orientated XML, if required
		if ($documentToDataOrientatedXml) {
			$xml = self::documentToDataOrientatedXml ($xml);
		}
		
		# Cache the contents if required
		// file_put_contents ('./cache.xml', $xml);
		
		# Start an array of data
		$dataset = array ();
		
		# Chunk the XML
		$xml = new SimpleXMLElement ($xml);
		$records = $xml->xpath ($xpathRecordsRoot);
		foreach ($records as $record) {
			
			# Assign the record number
			$id = NULL;
			$xpathResults = $record->xpath ($recordIdPath);
			foreach ($xpathResults as $xpathResult) {
				$id = trim ((string) $xpathResult);
				break;
			}
			
			# Skip if no ID
			if (!$id) {continue;}
			
			# Get the record itself as XML
			$data = $record->asXML();
			
/*
	if ($id == 'N: 257') {
		echo "<pre>" . htmlspecialchars ($data) . '</pre>';
		return false;
	}
*/
			
			# Add the data to the array of records
			$dataset[$id] = array ('id' => $id, 'data' => $data);
			
			# Add other records
			if ($otherPaths) {
				foreach ($otherPaths as $name => $path) {
					
					# Skip if absent
					if (empty ($path)) {continue;}
					
					# Switch to special handling if required, effectively using a callback method
					# e.g. Material,Description/Material>foo::bar means
					# $name = Material
					# $path to get the array for is Description/Material, but pass that into foo::bar() statically first
					$useCallback = false;
					if (ereg ('^([^>]+)>([^:]+)::(.+)$', $path, $matches)) {
						list ($ignore, $path, $class, $method) = $matches;
						if (method_exists ($class, $method)) {
							$useCallback = true;
						}
					}
					
					# Process this node
					if ($xpathResults = $record->xpath ($path)) {
						
						# Use the callback if required
						if ($useCallback) {
							$result = call_user_func (array ($class, $method), $xpathResults);
						} else {
							
							# Otherwise process natively
							$xpathResultComponents = array ();
							foreach ($xpathResults as $xpathResult) {
								$item = trim ((string) $xpathResult);
								if (!empty ($item)) {$xpathResultComponents[] = $item;}
							}
							$result = implode ("{$multiplesDelimiter}{$multiplesDelimiter}", $xpathResultComponents);
							
							# Surround with the delimiter if there is more than one component
							if (count ($xpathResultComponents) > 1) {$result = $multiplesDelimiter . $result . $multiplesDelimiter;}
						}
						
						# Assign the result
						$dataset[$id][$name] = $result;
					}
				}
			}
		}
		
		# Get the authentication credentials
		#!# This is failing
		if (!is_readable ($authenticationFile)) {
			echo "\n<p class=\"warning\">The authentication file could not be read or does not exist.</p>";
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
			if (!$databaseConnection->insert ($database, $table, $data, true)) {
				echo "<p>There was a problem inserting the data into the database. MySQL said:</p>";
				application::dumpData ($databaseConnection->error ());
				return false;
			}
		}
		
		# Return success
		return count ($dataset);
	}
}



// From: http://uk2.php.net/book.dom
/**
 * basic class for converting an array to xml.
 * @author Matt Wiseman (trollboy at shoggoth.net)
 * License unknown - contact the author
 *
 */
class array2xml {
    
    public $data;
    public $dom_tree;
    
    /**
     * basic constructor
     *
     * @param array $array
     */
    public  function __construct($array){
        if(!is_array($array)){
            throw new Exception('array2xml requires an array', 1);
            unset($this);
        }
        if(!count($array)){
            throw new Exception('array is empty', 2);
            unset($this);
        }
        
        $this->data = new DOMDocument('1.0');
        
        $this->dom_tree = $this->data->createElement('result');
        $this->data->appendChild($this->dom_tree);
        $this->recurse_node($array, $this->dom_tree);
    }
    
    /**
     * recurse a nested array and return dom back
     *
     * @param array $data
     * @param dom element $obj
     */
    private function recurse_node($data, $obj){
        $i = 0;
        foreach($data as $key=>$value){
            if(is_array($value)){
                //recurse if neccisary
                $sub_obj[$i] = $this->data->createElement($key);
                $obj->appendChild($sub_obj[$i]);
                $this->recurse_node($value, $sub_obj[$i]);
            } elseif(is_object($value)) {
                //no object support so just say what it is
                $sub_obj[$i] = $this->data->createElement($key, 'Object: "' . $key . '" type: "'  . get_class($value) . '"');
                $obj->appendChild($sub_obj[$i]);
            } else {
                //straight up data, no weirdness
                $sub_obj[$i] = $this->data->createElement($key, $value);
                $obj->appendChild($sub_obj[$i]);
            }
            $i++;
        }
    }
    
    /**
     * get the finished xml
     *
     * @return string
     */
    public function saveXML(){
        return $this->data->saveXML();
    }
}

?>
