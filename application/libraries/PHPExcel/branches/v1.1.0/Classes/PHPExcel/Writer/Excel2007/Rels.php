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
 * PHPExcel_Writer_Excel2007_Rels
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Rels extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write relationships to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeRelationships($pPHPExcel = null)
	{
		if ($pPHPExcel instanceof PHPExcel) {					
			// Create XML writer
			$objWriter = new xmlWriter();
			$objWriter->openMemory();
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');
			
			// Relationships
			$objWriter->startElement('Relationships');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

				// Relationship docProps/app.xml
				$this->_writeRelationship(
					$objWriter,
					3,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties',
					'docProps/app.xml'
				);

				// Relationship docProps/core.xml
				$this->_writeRelationship(
					$objWriter,
					2,
					'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties',
					'docProps/core.xml'
				);

				// Relationship xl/workbook.xml
				$this->_writeRelationship(
					$objWriter,
					1, 
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument',
					'xl/workbook.xml'
				);
				
			$objWriter->endElement();

			// Return
			return $objWriter->outputMemory(true);
		} else {
			throw new Exception("Invalid PHPExcel object passed.");
		}
	}
	
	/**
	 * Write workbook relationships to XML format
	 *
	 * @param 	PHPExcel	$pPHPExcel
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeWorkbookRelationships($pPHPExcel = null)
	{
		if ($pPHPExcel instanceof PHPExcel) {					
			// Create XML writer
			$objWriter = new xmlWriter();
			$objWriter->openMemory();
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');
			
			// Relationships
			$objWriter->startElement('Relationships');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

				// Relationship styles.xml
				$this->_writeRelationship(
					$objWriter,
					1,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles',
					'styles.xml'
				);

				// Relationship theme/theme1.xml
				$this->_writeRelationship(
					$objWriter,
					2,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme',
					'theme/theme1.xml'
				);

				// Relationship sharedStrings.xml
				$this->_writeRelationship(
					$objWriter,
					3,
					'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings',
					'sharedStrings.xml'
				);

				// Relationships with sheets			
				for ($i = 0; $i < $pPHPExcel->getSheetCount(); $i++) {
					$this->_writeRelationship(
						$objWriter,
						($i + 1 + 3),
						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet',
						'worksheets/sheet' . ($i + 1) . '.xml'
					);
				}
				
			$objWriter->endElement();

			// Return
			return $objWriter->outputMemory(true);
		} else {
			throw new Exception("Invalid PHPExcel object passed.");
		}
	}
	
	/**
	 * Write worksheet relationships to XML format
	 *
	 * @param 	PHPExcel_Worksheet	$pWorksheet
	 * @param 	int						$pWorksheetId
	 * @return 	string 					XML Output
	 * @throws 	Exception
	 */
	public function writeWorksheetRelationships($pWorksheet = null, $pWorksheetId = 1)
	{
		if ($pWorksheet instanceof PHPExcel_Worksheet) {					
			// Create XML writer
			$objWriter = new xmlWriter();
			$objWriter->openMemory();
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');
			
			// Relationships
			$objWriter->startElement('Relationships');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

				// Write drawing relationships?
				if ($pWorksheet->getDrawingCollection()->count() > 0) {
					$this->_writeRelationship(
						$objWriter,
						1,
						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing',
						'../drawings/drawing' . $pWorksheetId . '.xml'
					);
				}
				
			$objWriter->endElement();

			// Return
			return $objWriter->outputMemory(true);
		} else {
			throw new Exception("Invalid PHPExcel_Worksheet object passed.");
		}
	}
	
	/**
	 * Write drawing relationships to XML format
	 *
	 * @param 	PHPExcel_Worksheet			$pWorksheet
	 * @return 	string 							XML Output
	 * @throws 	Exception
	 */
	public function writeDrawingRelationships($pWorksheet = null)
	{
		if ($pWorksheet instanceof PHPExcel_Worksheet) {							
			// Create XML writer
			$objWriter = new xmlWriter();
			$objWriter->openMemory();
			
			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');
			
			// Relationships
			$objWriter->startElement('Relationships');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

				// Loop trough images and write relationships
				$i = 1;
				$iterator = $pWorksheet->getDrawingCollection()->getIterator();		
				while ($iterator->valid()) {
					// Write relationship
					$this->_writeRelationship(
						$objWriter,
						$i,
						'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
						'../media/' . $iterator->current()->getFilename()
					);
					
   					$iterator->next();
   					$i++;
				}
				
			$objWriter->endElement();

			// Return
			return $objWriter->outputMemory(true);
		} else {
			throw new Exception("Invalid PHPExcel_Worksheet object passed.");
		}
	}
	
	/**
	 * Write Override content type
	 *
	 * @param 	xmlWriter 	$objWriter 		XML Writer
	 * @param 	int			$pId			Relationship ID. rId will be prepended!
	 * @param 	string		$pType			Relationship type
	 * @param 	string 		$pTarget		Relationship target
	 * @throws 	Exception
	 */
	private function _writeRelationship($objWriter = null, $pId = 1, $pType = '', $pTarget = '')
	{
		if ($objWriter instanceof xmlWriter && $pType != '' && $pTarget != '') {
			// Write relationship
			$objWriter->startElement('Relationship');
			$objWriter->writeAttribute('Id', 		'rId' . $pId);
			$objWriter->writeAttribute('Type', 		$pType);
			$objWriter->writeAttribute('Target',	$pTarget);
			$objWriter->endElement();
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}
}
