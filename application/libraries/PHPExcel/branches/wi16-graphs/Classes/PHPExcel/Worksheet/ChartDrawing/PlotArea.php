<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2009 PHPExcel
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
 * @package    PHPExcel_Worksheet_ChartDrawing
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';

/** PHPExcel_Worksheet_ChartDrawing_Chart */
require_once 'PHPExcel/Worksheet/ChartDrawing/Chart.php';

/** PHPExcel_Worksheet_ChartDrawing_BarChart */
require_once 'PHPExcel/Worksheet/ChartDrawing/BarChart.php';


/**
 * PHPExcel_Worksheet_ChartDrawing_PlotArea
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet_ChartDrawing
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_ChartDrawing_PlotArea implements PHPExcel_IComparable
{
	/**
	 * Chart
	 * 
	 * @var PHPExcel_Worksheet_ChartDrawing_Chart
	 */
	private $_chart = null;
	
	/**
     * Create a new PHPExcel_Worksheet_ChartDrawing_PlotArea
     */
    public function __construct()
    {
    	$self->_chart = null;
    }
    
    /**
     * Get chart
     * 
     * @return PHPExcel_Worksheet_ChartDrawing_Chart
     */
    public function getChart() {
    	return $this->_chart;
    }
    
    /**
     * Set chart
     * 
     * @param PHPExcel_Worksheet_ChartDrawing_Chart $value
     */
    public function setChart(PHPExcel_Worksheet_ChartDrawing_Chart $value) {
    	$this->_chart = $value;
    }
    
    /**
     * Create chart
     * 
     * @param string $chartType Chart type to create
     * @return PHPExcel_Worksheet_ChartDrawing_Chart
     */
    public function createChart($chartType = 'BarChart') {
    	$chartClass = "PHPExcel_Worksheet_ChartDrawing_$chartType";
    	$this->_chart = new $chartClass();
    	return $this->_chart;
    }
	
	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_chart->getHashCode()
    		. __CLASS__
    	);
    }
    
    /**
     * Hash index
     *
     * @var string
     */
    private $_hashIndex;
    
	/**
	 * Get hash index
	 * 
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @return string	Hash index
	 */
	public function getHashIndex() {
		return $this->_hashIndex;
	}
	
	/**
	 * Set hash index
	 * 
	 * Note that this index may vary during script execution! Only reliable moment is
	 * while doing a write of a workbook and when changes are not allowed.
	 *
	 * @param string	$value	Hash index
	 */
	public function setHashIndex($value) {
		$this->_hashIndex = $value;
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
}
