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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
require_once 'PHPExcel/Writer/Excel2007.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';

/** PHPExcel_Worksheet_BaseDrawing */
require_once 'PHPExcel/Worksheet/BaseDrawing.php';

/** PHPExcel_Worksheet_ChartDrawing_PlotArea */
require_once 'PHPExcel/Worksheet/ChartDrawing/PlotArea.php';

/** PHPExcel_Worksheet_ChartDrawing_Legend */
require_once 'PHPExcel/Worksheet/ChartDrawing/Legend.php';

/** PHPExcel_Shared_XMLWriter */
require_once 'PHPExcel/Shared/XMLWriter.php';


/**
 * PHPExcel_Writer_Excel2007_Charts
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Charts extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write chart to XML format
	 *
	 * @param 	PHPExcel_Worksheet_ChartDrawing				$chartDrawing
	 * @return 	string 								XML Output
	 * @throws 	Exception
	 */
	public function writeChart(PHPExcel_Worksheet_ChartDrawing $chartDrawing = null)
	{
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}
			
		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');
  
		// c:chartSpace
		$objWriter->startElement('c:chartSpace');
		$objWriter->writeAttribute('xmlns:c', 'http://schemas.openxmlformats.org/drawingml/2006/chart');
		$objWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
		$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
			
			// c:lang
			$objWriter->startElement('c:lang');
			$objWriter->writeAttribute('val', 'en-US');
			$objWriter->endElement();
			
			// c:chart
			$objWriter->startElement('c:chart');
			
				// c:plotArea
				$this->_writePlotArea($objWriter, $chartDrawing);
				
				// c:legend
				$this->_writeLegend($objWriter, $chartDrawing);
				
				// c:plotVisOnly
				$objWriter->startElement('c:plotVisOnly');
				$objWriter->writeAttribute('val', '1');
				$objWriter->endElement();
			
			$objWriter->endElement();
			
			// c:printSettings
			$this->_writePrintSettings($objWriter);
				
		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}
	
	/**
	 * Write plotArea to XML format
	 *
	 * @param 	PHPExcel_Shared_XMLWriter			$objWriter 			XML Writer
	 * @param 	PHPExcel_Worksheet_ChartDrawing		$chartDrawing
	 * @return 	string 								XML Output
	 * @throws 	Exception
	 */
	public function _writePlotArea(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet_ChartDrawing $chartDrawing = null)
	{			
		// c:plotArea
		$objWriter->startElement('c:plotArea');

			// c:layout
			$objWriter->writeElement('c:layout');
			
			// .....
			
			// c:catAx and c:valAx
			$this->_writeAxis($objWriter, $chartDrawing);
				
		$objWriter->endElement();
	}
	
	/**
	 * Write catAx and valAx to XML format
	 *
	 * @param 	PHPExcel_Shared_XMLWriter			$objWriter 			XML Writer
	 * @param 	PHPExcel_Worksheet_ChartDrawing		$chartDrawing
	 * @return 	string 								XML Output
	 * @throws 	Exception
	 */
	public function _writeAxis(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet_ChartDrawing $chartDrawing = null)
	{			
		//////////////////////
		// TODO !!!
		//////////////////////
		
		// c:catAx
		$objWriter->startElement('c:catAx');

			// c:layout
			$objWriter->writeElement('c:layout');
			
			// .....
				
		$objWriter->endElement();
	}
	
	/**
	 * Write legend to XML format
	 *
	 * @param 	PHPExcel_Shared_XMLWriter			$objWriter 			XML Writer
	 * @param 	PHPExcel_Worksheet_ChartDrawing		$chartDrawing
	 * @return 	string 								XML Output
	 * @throws 	Exception
	 */
	public function _writeLegend(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_Worksheet_ChartDrawing $chartDrawing = null)
	{			
		// c:legend
		$objWriter->startElement('c:legend');

			// c:legendPos
			$objWriter->startElement('c:legendPos');
			$objWriter->writeAttribute('val', $chartDrawing->getLegend()->getPosition());
			$objWriter->endElement();
				
			// c:layout
			$objWriter->writeElement('c:layout');
					
			// c:overlay
			$objWriter->startElement('c:overlay');
			$objWriter->writeAttribute('val', $chartDrawing->getLegend()->getOverlay() ? 'true' : 'false');
			$objWriter->endElement();
				
		$objWriter->endElement();
	}
	
	/**
	 * Write print settings
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 			XML Writer
	 * @throws 	Exception
	 */
	public function _writePrintSettings(PHPExcel_Shared_XMLWriter $objWriter = null)
	{
		// c:printSettings
		$objWriter->startElement('c:printSettings');
		
			// c:headerFooter
			$objWriter->writeElement('c:headerFooter');
			
			// c:pageMargins
			$objWriter->startElement('c:pageMargins');
			$objWriter->writeAttribute('b', '0.75');
			$objWriter->writeAttribute('l', '0.7');
			$objWriter->writeAttribute('r', '0.7');
			$objWriter->writeAttribute('t', '0.75');
			$objWriter->writeAttribute('header', '0.3');
			$objWriter->writeAttribute('footer', '0.3');
			$objWriter->endElement();
			
			// c:pageSetup
			$objWriter->writeElement('c:pageSetup');
		
		$objWriter->endElement();
	}
}
