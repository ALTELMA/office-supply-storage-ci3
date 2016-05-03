<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2007 PHPExcel, Maarten Balliauw
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/gpl.txt	GPL
 */


/** PHPExcel_Writer_Excel2007 */
require_once 'PHPExcel/Writer/Excel2007.php';

/** PHPExcel_Writer_Excel2007_WriterPart */
require_once 'PHPExcel/Writer/Excel2007/WriterPart.php';


/**
 * PHPExcel_Writer_Excel2007_Workbook
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Workbook extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write workbook to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeWorkbook($pPHPExcel = null)
	{
		if ($pPHPExcel instanceof PHPExcel) {					
			// Create XML writer
			$objWriter = new xmlWriter();
			$objWriter->openMemory();
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');

			// workbook
			$objWriter->startElement('workbook');
			$objWriter->writeAttribute('xml:space', 'preserve');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
  
				// fileVersion
				$this->_writeFileVersion($objWriter);
							
				// workbookPr
				$this->_writeWorkbookPr($objWriter);

				// sheets
				$objWriter->startElement('sheets');
				
				for ($i = 0; $i < $pPHPExcel->getSheetCount(); $i++) {
					// sheet
					$this->_writeSheet(
						$objWriter,
						$pPHPExcel->getSheet($i)->getTitle(),
						($i + 1),
						($i + 1 + 3)
					);
				}
				
				$objWriter->endElement();
				
				// calcPr
				$this->_writeCalcPr($objWriter);
  
			$objWriter->endElement();

			// Return
			return $objWriter->outputMemory(true);
		} else {
			throw new Exception("Invalid PHPExcel object passed.");
		}
	}
				
	/**
	 * Write file version
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeFileVersion($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			$objWriter->startElement('fileVersion');
			$objWriter->writeAttribute('appName', 'xl');
			$objWriter->writeAttribute('lastEdited', '4');
			$objWriter->writeAttribute('lowestEdited', '4');
			$objWriter->writeAttribute('rupBuild', '4505');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Write WorkbookPr
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeWorkbookPr($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			$objWriter->startElement('workbookPr');
			$objWriter->writeAttribute('codeName', 'ThisWorkbook');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
	
	/**
	 * Write calcPr
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @throws 	Exception
	 */
	private function _writeCalcPr($objWriter = null)
	{
		if ($objWriter instanceof xmlWriter) {
			$objWriter->startElement('calcPr');
			$objWriter->writeAttribute('calcId', '122211');
			$objWriter->writeAttribute('calcMode', 'auto');
			$objWriter->writeAttribute('fullCalcOnLoad', 'true');
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}	
					
	/**
	 * Write sheet
	 *
	 * @param 	xmlWriter $objWriter 		XML Writer
	 * @param 	string 	$pSheetname 		Sheet name
	 * @param 	int		$pSheetId	 		Sheet id
	 * @param 	int		$pRelId				Relationship ID
	 * @throws 	Exception
	 */
	private function _writeSheet($objWriter = null, $pSheetname = '', $pSheetId = 1, $pRelId = 1)
	{
		if ($objWriter instanceof xmlWriter && $pSheetname != '') {
			// Write sheet
			$objWriter->startElement('sheet');
			$objWriter->writeAttribute('name', 		$pSheetname);
			$objWriter->writeAttribute('sheetId', 	$pSheetId);
			$objWriter->writeAttribute('r:id', 		'rId' . $pRelId);
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
}
