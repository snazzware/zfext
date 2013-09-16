<?php 

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
function array_merge_recursive_distinct ( array $array1, array $array2 )
{
	$merged = $array1;

	foreach ( $array2 as $key => &$value )
	{
		if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
		{
			$merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
		}
		else
		{
			$merged [$key] = $value;
		}
	}

	return $merged;
}

/**
 * Returns an array of all bounded identifiers within a string.
 * 
 * $boundaries must be an array. If there are at least two elements, the first will be used as the 
 * left identifier boundary, and the second as the right. If only one element is present, it is used as
 * both the left and the right boundary identifier.
 * 
 * Example input: "{hello}, {world}! welcome to this {function}"
 * Example output: array('hello','world','function');
 * 
 * @param string $string
 * @param array $boundaries
 * @return multitype:string unknown
 */
function getIdentifiers($string, $boundaries=array('{','}')) {
	$identifiers = array();

	$len = strlen($string);
	$tok = '';
	$bracketed = false;
	
	if (!is_array($boundaries) || count($boundaries)<0 || !is_string(reset($boundaries))) throw new Exception('boundaires must be an array and must contain at least one string');
	
	$leftBoundary = reset($boundaries);
	$rightBoundary = next($boundaries);
	if (!$rightBoundary || !is_string($rightBoundary)) $rightBoundary = $leftBoundary;
	
	for ($i=0;$i<$len;$i++) {
		if (!$bracketed) {
			if ($string[$i] == $leftBoundary) {
				$bracketed = true;
			}
		} else {
			if ($string[$i] == $rightBoundary) {
				$bracketed = false;
				if (!empty($tok)) {
					$identifiers[$tok] = $tok;
					$tok = '';
				}
			} else $tok .= $string[$i];
		}
	}
	if (!empty($tok)) $identifiers[$tok] = $tok;

	return $identifiers;
}

/**
 *
 * Finds all values inside curly brackets, and using them as keys vs the $values array, replacing them with the corresponding
 * values from the $values array.
 *
 * @param string $string
 * @param array $values
 */
function replaceIdentifiers($string,$values) {
	$identifiers = getIdentifiers($string);

	$result = $string;

	foreach ($identifiers as $identifier) {
		if (isset($values[$identifier])) {
			$result = str_replace('{'.$identifier.'}',$values[$identifier],$result);
		}
	}

	return $result;
}



?>