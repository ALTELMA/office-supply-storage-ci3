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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel_IComparable */
require_once 'PHPExcel/IComparable.php';

/** PHPExcel_Worksheet */
require_once 'PHPExcel/Worksheet.php';

/** PHPExcel_Worksheet_BaseDrawing */
require_once 'PHPExcel/Worksheet/BaseDrawing.php';

/** PHPExcel_Worksheet_ChartDrawing_PlotArea */
require_once 'PHPExcel/Worksheet/ChartDrawing/PlotArea.php';

/** PHPExcel_Worksheet_ChartDrawing_Legend */
require_once 'PHPExcel/Worksheet/ChartDrawing/Legend.php';


/**
 * PHPExcel_Worksheet_ChartDrawing
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_ChartDrawing extends PHPExcel_Worksheet_BaseDrawing implements PHPExcel_IComparable 
{	
	/**
	 * Plot area
	 * 
	 * @var PHPExcel_Worksheet_ChartDrawing_PlotArea
	 */
	private $_plotArea = null;
	
	/**
	 * Legend
	 * 
	 * @var PHPExcel_Worksheet_ChartDrawing_Legend
	 */
	private $_legend = null;
	
    /**
     * Create a new PHPExcel_Worksheet_ChartDrawing
     */
    public function __construct()
    {
    	// Initialize parent
    	parent::__construct();
    	
    	// Set defaults
    	$this->_width 	= 480;
    	$this->_height 	= 290;
    	
    	// Create plot area and legend
    	$this->_plotArea = new PHPExcel_Worksheet_ChartDrawing_PlotArea();
    	$this->_legend = new PHPExcel_Worksheet_ChartDrawing_Legend();
    }
    
    /**
     * Get plot area
     * 
     * @return PHPExcel_Worksheet_ChartDrawing_PlotArea
     */
    public function getPlotArea() {
    	return $this->_plotArea;
    }
    
    /**
     * Get legend
     * 
     * @return PHPExcel_Worksheet_ChartDrawing_Legend
     */
    public function getLegend() {
    	return $this->_legend;
    }

    /**
     * Get indexed filename (using image index)
     *
     * @return string
     */
    public function getIndexedFilename() {
    	return 'chart' . $this->getImageIndex() . '.xml';
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */	
	public function getHashCode() {
    	return md5(
    		  $this->_plotArea->getHashCode()
    		. $this->_legend->getHashCode()
    		. parent::getHashCode()
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
