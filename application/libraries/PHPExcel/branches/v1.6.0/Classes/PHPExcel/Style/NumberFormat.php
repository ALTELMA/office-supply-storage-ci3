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
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
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
	const FORMAT_DATE_YYYYMMDDSLASH			= 'yyyy/mm/dd;@';
	
	const FORMAT_CURRENCY_USD_SIMPLE		= '"$"#,##0.00_-';
	const FORMAT_CURRENCY_EUR_SIMPLE		= '[$€]#,##0.00_-';
	
	/**
	 * Format Code
	 *
	 * @var string
	 */
	private $_formatCode;

    /**
     * Create a new PHPExcel_Style_NumberFormat
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_formatCode			= PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
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
    	return $this->_formatCode;
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
    	$this->_formatCode = $pValue;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_formatCode
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
					
				case self::FORMAT_PERCENTAGE:
					return round( (100 * $value), 0) ; '%';
				case self::FORMAT_PERCENTAGE_00:
					return round( (100 * $value), 2) ; '%';
					
				case self::FORMAT_DATE_YYYYMMDD:
					return date('Y-m-d', (1 * $value));
				case self::FORMAT_DATE_DDMMYYYY:
					return date('d/m/Y', (1 * $value));
				case 'yyyy/mm/dd;@':
					return date('Y/m/d', (1 * $value));
					
				case self::FORMAT_CURRENCY_USD_SIMPLE:
					return '$' . sprintf('%1.2f', $value);
				case self::FORMAT_CURRENCY_EUR_SIMPLE:
					return 'EUR ' . sprintf('%1.2f', $value);
			}
		}
		
		return $value;
	}
}
