<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Calculation_Functions
 *
 * @category   PHPExcel
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Functions {
	/**
	 * List of error codes
	 *
	 * @var array
	 */
	private static $_errorCodes = array("#NULL!", "#DIV/0!", "#VALUE!", "#REF!", "#NAME?", "#NUM!", "#N/A");
	
	/**
	 * DUMMY
	 *
	 * @return  string	#N/A!
	 */
	public static function DUMMY() {
		return '#N/A';
	}
	
	/**
	 * NA
	 *
	 * @return  string	#N/A!
	 */
	public static function NA() {
		return '#N/A';
	}
	
	/**
	 * LOGICAL_AND
	 *
	 * @return  boolean
	 */
	public static function LOGICAL_AND() {
		// Return value
		$returnValue = null;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			if (is_null($returnValue)) {
				$returnValue = $arg;
			} else {
				// Is it a boolean value?
				if (is_bool($arg)) {
					$returnValue = $returnValue && $arg;
				}
			}
		}
		
		// Return
		return $returnValue;
	}
	
	/**
	 * LOGICAL_OR
	 *
	 * @return  boolean
	 */
	public static function LOGICAL_OR() {
		// Return value
		$returnValue = null;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			if (is_null($returnValue)) {
				$returnValue = $arg;
			} else {
				// Is it a boolean value?
				if (is_bool($arg)) {
					$returnValue = $returnValue || $arg;
				}
			}
		}
		
		// Return
		return $returnValue;
	}
	
	/**
	 * SUM
	 *
	 * @return  int
	 */
	public static function SUM() {
		// Return value
		$returnValue = null;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if (is_numeric($arg)) {
				if (is_null($returnValue)) {
					$returnValue = $arg;
				} else {
					$returnValue += $arg;
				}
			}
		}
		
		// Return
		return $returnValue;
	}
	
	/**
	 * PRODUCT
	 *
	 * @return  int
	 */
	public static function PRODUCT() {
		// Return value
		$returnValue = null;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
				if (is_null($returnValue)) {
					$returnValue = $arg;
				} else {
					$returnValue *= $arg;
				}
		}
		
		// Return
		return $returnValue;
	}
	
	/**
	 * QUOTIENT
	 *
	 * @return  int
	 */
	public static function QUOTIENT() {
		// Return value
		$returnValue = null;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
				if (is_null($returnValue)) {
					$returnValue = $arg;
				} else {
					$returnValue /= $arg;
				}
		}
		
		// Return
		return intval($returnValue);
	}
	
	/**
	 * MIN
	 *
	 * @return  int
	 */
	public static function MIN() {
		// Return value
		$returnValue = 0;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$returnValue = $aArgs[0];
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if (is_numeric($arg)) {
				if ($arg < $returnValue) {
					$returnValue = $arg;
				}
			}
		}
		
		// Return
		return $returnValue;
	}
	
	/**
	 * MAX
	 *
	 * @return  int
	 */
	public static function MAX() {
		// Return value
		$returnValue = 0;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$returnValue = $aArgs[0];
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if (is_numeric($arg)) {
				if ($arg > $returnValue) {
					$returnValue = $arg;
				}
			}
		}
		
		// Return
		return $returnValue;
	}
	
	/**
	 * COUNT
	 *
	 * @return  int
	 */
	public static function COUNT() {
		// Return value
		$returnValue = 0;
		
		// Loop trough arguments
		$aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
		$returnValue = $aArgs[0];
		foreach ($aArgs as $arg) {
			// Is it a numeric value?
			if (is_numeric($arg)) {
				$returnValue++;
			}
		}
		
		// Return
		return $returnValue;
	}
	
	/**
	 * RAND
	 *
	 * @param 	int		$min	Minimal value
	 * @param 	int		$max	Maximal value
	 * @return  int		Random number
	 */
	public static function RAND($min = 0, $max = 0) {
		$min 		= self::flattenSingleValue($min);
		$max 		= self::flattenSingleValue($max);
		
		if ($min == 0 && $max == 0) {
			return (rand(0,10000000)) / 10000000;
		} else {
			return rand($min, $max);
		}
	}
	
	/**
	 * MOD
	 *
	 * @param 	int		$a		Dividend
	 * @param 	int		$b		Divisor
	 * @return  int		Remainder
	 */
	public static function MOD($a = 1, $b = 1) {
		$a 		= self::flattenSingleValue($a);
		$b 		= self::flattenSingleValue($b);
		
		return $a % $b;
	}
	
	/**
	 * LEFT
	 *
	 * @param 	string	$value	Value
	 * @param 	int		$chars	Number of characters
	 * @return  string
	 */
	public static function LEFT($value = '', $chars = null) {
		$value 		= self::flattenSingleValue($value);
		$chars 		= self::flattenSingleValue($chars);
		
		return substr($value, 0, $chars);
	}
	
	/**
	 * RIGHT
	 *
	 * @param 	string	$value	Value
	 * @param 	int		$chars	Number of characters
	 * @return  string
	 */
	public static function RIGHT($value = '', $chars = null) {
		$value 		= self::flattenSingleValue($value);
		$chars 		= self::flattenSingleValue($chars);
		
		return substr($value, strlen($value) - $chars);
	}
	
	/**
	 * MID
	 *
	 * @param 	string	$value	Value
	 * @param 	int		$start	Start character
	 * @param 	int		$chars	Number of characters
	 * @return  string
	 */
	public static function MID($value = '', $start = 1, $chars = null) {
		$value 		= self::flattenSingleValue($value);
		$start 		= self::flattenSingleValue($start);
		$chars 		= self::flattenSingleValue($chars);
		
		return substr($value, $start - 1, $chars);
	}
	
	/**
	 * IS_BLANK
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_BLANK($value = '') {
		$value 		= self::flattenSingleValue($value);
		
		return ($value == '');
	}
	
	/**
	 * IS_ERR
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_ERR($value = '') {
		$value 		= self::flattenSingleValue($value);
		
		return self::IS_ERROR($value) && (!self::IS_NA($value));
	}
	
	/**
	 * IS_ERROR
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_ERROR($value = '') {
		$value 		= self::flattenSingleValue($value);
		
		return in_array($value, self::_errorCodes); 
	}
	
	/**
	 * IS_NA
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_NA($value = '') {
		$value 		= self::flattenSingleValue($value);
		
		return ($value == '#N/A');
	}
	
	/**
	 * IS_EVEN
	 *
	 * @param 	mixed	$value	Value to check
	 * @return  boolean
	 */
	public static function IS_EVEN($value = 0) {
		$value 		= self::flattenSingleValue($value);
		
		while (intval($value) != $value) {
			$value *= 10;
		}
		return ($value % 2 == 0);
	}
	
	/**
	 * IS_NUMBER
	 *
	 * @param 	mixed	$value		Value to check
	 * @return  boolean
	 */
	public static function IS_NUMBER($value = 0) {
		$value 		= self::flattenSingleValue($value);
		
		return is_numeric($value);
	}
	
	/**
	 * STATEMENT_IF
	 *
	 * @param 	mixed	$value		Value to check
	 * @param 	mixed	$truepart	Value when true
	 * @param 	mixed	$falsepart	Value when false
	 * @return  mixed
	 */
	public static function STATEMENT_IF($value = true, $truepart = '', $falsepart = '') {
		$value 		= self::flattenSingleValue($value);
		$truepart 	= self::flattenSingleValue($truepart);
		$falsepart 	= self::flattenSingleValue($falsepart);

		return ($value ? $truepart : $falsepart);
	}
	
	/**
	 * STATEMENT_IFERROR
	 *
	 * @param 	mixed	$value		Value to check , is also value when no error
	 * @param 	mixed	$errorpart	Value when error
	 * @return  mixed
	 */
	public static function STATEMENT_IFERROR($value = '', $errorpart = '') {
		return self::STATEMENT_IF(self::IS_ERROR($value), $errorpart, $value);
	}	
	
	/**
	 * ACCRINT
	 *
	 * Computes the accrued interest for a security that pays periodic interest.
	 *
	 * @param 	int		$issue
	 * @param 	int		$firstInterest
	 * @param 	int		$settlement
	 * @param 	int		$rate
	 * @param 	int		$par
	 * @param 	int		$frequency
	 * @param 	int		$basis
	 * @return  int		The accrued interest for a security that pays periodic interest.
	 */
	/*
	public static function ACCRINT($issue = 0, $firstInterest = 0, $settlement = 0, $rate = 0, $par = 1000, $frequency = 1, $basis = 0) {
		$issue 			= self::flattenSingleValue($issue);
		$firstInterest 	= self::flattenSingleValue($firstInterest);
		$settlement 	= self::flattenSingleValue($settlement);
		$rate 			= self::flattenSingleValue($rate);
		$par 			= self::flattenSingleValue($par);
		$frequency 		= self::flattenSingleValue($frequency);
		$basis 			= self::flattenSingleValue($basis);
		
		// Perform checks
		if ($issue >= $settlement || $rate <= 0 || $par <= 0 || !($frequency == 1 || $frequency == 2 || $frequency == 4) || $basis < 0 || $basis > 4) return '#NUM!';
		
		// Calculate value
		return $par * ($rate / $frequency) * 
	}
	*/
	
	/**
	 * Flatten multidemensional array
	 *
	 * @param 	array	$array	Array to be flattened
	 * @return  array	Flattened array
	 */
	public static function flattenArray($array) {
		$arrayValues = array();
	
		foreach ($array as $value) {
			if (is_scalar($value) || is_resource($value)) {
				$arrayValues[] = $value;
			} else if (is_array($value)) {
				$arrayValues = array_merge($arrayValues, PHPExcel_Calculation_Functions::flattenArray($value));
			}
		}
	
		return $arrayValues;
	}
	
	/**
	 * Convert an array with one element to a flat value
	 *
	 * @param 	mixed		$value		Array or flat value
	 * @return 	mixed
	 */
	public static function flattenSingleValue($value = '') {
		if (is_array($value)) {
			$value = array_pop($value);
		}
		return $value;
	}
}