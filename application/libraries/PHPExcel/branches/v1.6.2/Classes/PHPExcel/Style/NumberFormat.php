<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2008 PHPExcel
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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';


/**
 * PHPExcel_Style_NumberFormat
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_NumberFormat implements PHPExcel_IComparable
{
	/* Pre-defined formats */
	const FORMAT_GENERAL					= 'General';
	
	const FORMAT_NUMBER						= '0';
	const FORMAT_NUMBER_00					= '0.00';
	const FORMAT_NUMBER_COMMA_SEPARATED1	= '#,##0.00';
	const FORMAT_NUMBER_COMMA_SEPARATED2	= '#,##0.00_-';
	
	const FORMAT_PERCENTAGE					= '0%';
	const FORMAT_PERCENTAGE_00				= '0.00%';
	
	const FORMAT_DATE_YYYYMMDD				= 'yyyy-mm-dd';
	const FORMAT_DATE_DDMMYYYY				= 'dd/mm/yyyy';
	const FORMAT_DATE_DMYSLASH				= 'd/m/Y';
	const FORMAT_DATE_DMYMINUS				= 'd-M-Y';
	const FORMAT_DATE_DMMINUS				= 'd-M';
	const FORMAT_DATE_MYMINUS				= 'M-Y';
	const FORMAT_DATE_DATETIME				= 'd/m/Y H:i';
	const FORMAT_DATE_TIME1					= 'h:i a';
	const FORMAT_DATE_TIME2					= 'h:i:s a';
	const FORMAT_DATE_TIME3					= 'H:i';
	const FORMAT_DATE_TIME4					= 'H:i:s';
	const FORMAT_DATE_TIME5					= 'i:s';
	const FORMAT_DATE_TIME6					= 'H:i:s';
	const FORMAT_DATE_TIME7					= 'i:s.S';
	const FORMAT_DATE_TIME8					= 'h:mm:ss;@';
	const FORMAT_DATE_YYYYMMDDSLASH			= 'yyyy/mm/dd;@';
	
	const FORMAT_CURRENCY_USD_SIMPLE		= '"$"#,##0.00_-';
	const FORMAT_CURRENCY_USD				= '$#,##0_-';
	const FORMAT_CURRENCY_EUR_SIMPLE		= '[$EUR ]#,##0.00_-';
	
	/**
	 * Format Code
	 *
	 * @var string
	 */
	private $_formatCode;

	/**
	 * Parent Style
	 *
	 * @var PHPExcel_Style
	 */
	 
	private $_parent;
	
	/**
	 * Parent Borders
	 *
	 * @var _parentPropertyName string
	 */
	private $_parentPropertyName;
		
	/**
     * Create a new PHPExcel_Style_NumberFormat
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_formatCode			= PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
    }

	/**
	 * Property Prepare bind
	 *
	 * Configures this object for late binding as a property of a parent object
	 *	 
	 * @param $parent
	 * @param $parentPropertyName
	 */
	public function propertyPrepareBind($parent, $parentPropertyName)
	{
		// Initialize parent PHPExcel_Style for late binding. This relationship purposely ends immediately when this object
		// is bound to the PHPExcel_Style object pointed to so as to prevent circular references.
		$this->_parent 				= $parent;
		$this->_parentPropertyName	= $parentPropertyName;
	}
    
    /**
     * Property Get Bound
     *
     * Returns the PHPExcel_Style_NumberFormat that is actual bound to PHPExcel_Style
	 *
	 * @return PHPExcel_Style_NumberFormat
     */
	private function propertyGetBound() {
		if(!isset($this->_parent))
			return $this;																// I am bound

		if($this->_parent->propertyIsBound($this->_parentPropertyName))
			return $this->_parent->getNumberFormat();									// Another one is bound

		return $this;																	// No one is bound yet
	}
	
    /**
     * Property Begin Bind
     *
     * If no PHPExcel_Style_NumberFormat has been bound to PHPExcel_Style then bind this one. Return the actual bound one.
	 *
	 * @return PHPExcel_Style_NumberFormat
     */
	private function propertyBeginBind() {
		if(!isset($this->_parent))
			return $this;																// I am already bound

		if($this->_parent->propertyIsBound($this->_parentPropertyName))
			return $this->_parent->getNumberFormat();									// Another one is already bound
			
		$this->_parent->propertyCompleteBind($this, $this->_parentPropertyName);		// Bind myself
		$this->_parent = null;
		
		return $this;
	}
    
    /**
     * Apply styles from array
     * 
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->applyFromArray(
     * 		array(
     * 			'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
     * 		)
     * );
     * </code>
     * 
     * @param	array	$pStyles	Array containing style information
     * @throws	Exception
     */
    public function applyFromArray($pStyles = null) {
        if (is_array($pStyles)) {
        	if (array_key_exists('code', $pStyles)) {
    			$this->setFormatCode($pStyles['code']);
    		}
    	} else {
    		throw new Exception("Invalid style array passed.");
    	}
    }
    
    /**
     * Get Format Code
     *
     * @return string
     */
    public function getFormatCode() {
    	return $this->propertyGetBound()->_formatCode;
    }
    
    /**
     * Set Format Code
     *
     * @param string $pValue
     */
    public function setFormatCode($pValue = PHPExcel_Style_NumberFormat::FORMAT_GENERAL) {
        if ($pValue == '') {
    		$pValue = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
    	}
    	$this->propertyBeginBind()->_formatCode = $pValue;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
		$property = $this->propertyGetBound();
    	return md5(
    		  $property->_formatCode
    		. __CLASS__
    	);
    }
        
	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * Convert a value in a pre-defined format to a PHP string
	 *
	 * @param mixed 	$value		Value to format
	 * @param string 	$format		Format code
	 * @return string	Formatted string
	 */
	public static function toFormattedString($value = '', $format = '') {
		if (preg_match ("/^([0-9.,-]+)$/", $value)) {
			switch ($format) {
				case self::FORMAT_NUMBER:
					return sprintf('%1.0f', $value);
				case self::FORMAT_NUMBER_00:
					return sprintf('%1.2f', $value);
					
				case self::FORMAT_NUMBER_COMMA_SEPARATED1:
				case self::FORMAT_NUMBER_COMMA_SEPARATED2:
					return number_fromat($value, 2, ',', '.');
					
				case self::FORMAT_PERCENTAGE:
					return round( (100 * $value), 0) . '%';
				case self::FORMAT_PERCENTAGE_00:
					return round( (100 * $value), 2) . '%';
					
				case self::FORMAT_DATE_YYYYMMDD:
					return date('Y-m-d', (1 * $value));
				case self::FORMAT_DATE_DDMMYYYY:
					return date('d/m/Y', (1 * $value));
				case 'yyyy/mm/dd;@':
					return date('Y/m/d', (1 * $value));
					
				case self::FORMAT_CURRENCY_USD_SIMPLE:
					return '$' . number_format($value, 2);
				case self::FORMAT_CURRENCY_USD:
					return '$' . number_format($value);
 				case self::FORMAT_CURRENCY_EUR_SIMPLE:
 					return 'EUR ' . sprintf('%1.2f', $value);
			}
		}
		
		return $value;
	}
}
