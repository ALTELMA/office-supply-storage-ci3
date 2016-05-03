<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel, Maarten Balliauw
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
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
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
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
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
     * Duplicate object
     *
     * Duplicates the current object, also duplicating referenced objects (deep cloning).
     * Standard PHP clone does not copy referenced objects!
     *
     * @return PHPExcel_Style_NumberFormat
     */
	public function duplicate() {
		return unserialize(serialize($this));
	}
}
