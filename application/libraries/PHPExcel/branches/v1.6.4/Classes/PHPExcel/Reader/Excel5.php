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
 * You should have received a copy of tshhe GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

// Original file header of ParseXL (used as the base for this class):
// --------------------------------------------------------------------------------
// Adapted from Excel_Spreadsheet_Reader developed by users bizon153,
// trex005, and mmp11 (SourceForge.net)
// http://sourceforge.net/projects/phpexcelreader/
// Primary changes made by canyoncasa (dvc) for ParseXL 1.00 ...
//	 Modelled moreso after Perl Excel Parse/Write modules
//	 Added Parse_Excel_Spreadsheet object
//		 Reads a whole worksheet or tab as row,column array or as
//		 associated hash of indexed rows and named column fields
//	 Added variables for worksheet (tab) indexes and names
//	 Added an object call for loading individual woorksheets
//	 Changed default indexing defaults to 0 based arrays
//	 Fixed date/time and percent formats
//	 Includes patches found at SourceForge...
//		 unicode patch by nobody
//		 unpack("d") machine depedency patch by matchy
//		 boundsheet utf16 patch by bjaenichen
//	 Renamed functions for shorter names
//	 General code cleanup and rigor, including <80 column width
//	 Included a testcase Excel file and PHP example calls
//	 Code works for PHP 5.x

// Primary changes made by canyoncasa (dvc) for ParseXL 1.10 ...
// http://sourceforge.net/tracker/index.php?func=detail&aid=1466964&group_id=99160&atid=623334
//	 Decoding of formula conditions, results, and tokens.
//	 Support for user-defined named cells added as an array "namedcells"
//		 Patch code for user-defined named cells supports single cells only.
//		 NOTE: this patch only works for BIFF8 as BIFF5-7 use a different
//		 external sheet reference structure


/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_Reader_IReader */
require_once 'PHPExcel/Reader/IReader.php';

/** PHPExcel_Shared_OLERead */
require_once 'PHPExcel/Shared/OLERead.php';

/** PHPExcel_Cell */
require_once 'PHPExcel/Cell.php';

/** PHPExcel_Reader_IReadFilter */
require_once 'PHPExcel/Reader/IReadFilter.php';

/** PHPExcel_Reader_DefaultReadFilter */
require_once 'PHPExcel/Reader/DefaultReadFilter.php';


/**
 * PHPExcel_Reader_Excel5
 *
 * This class uses {@link http://sourceforge.net/projects/phpexcelreader/parseXL}
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2008 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel5 implements PHPExcel_Reader_IReader
{
	// ParseXL definitions
	const XLS_BIFF8						= 0x0600;
	const XLS_BIFF7						= 0x0500;
	const XLS_WorkbookGlobals			= 0x0005;
	const XLS_Worksheet					= 0x0010;

	// record identifiers
	const XLS_Type_FORMULA				= 0x0006;
	const XLS_Type_EOF					= 0x000a;
	const XLS_Type_PROTECT				= 0x0012;
	const XLS_Type_PASSWORD				= 0x0013;
	const XLS_Type_HEADER				= 0x0014;
	const XLS_Type_FOOTER				= 0x0015;
	const XLS_Type_EXTSHEET				= 0x0017;
	const XLS_Type_NAME					= 0x0018;
	const XLS_Type_VERTICALPAGEBREAKS	= 0x001a;
	const XLS_Type_HORIZONTALPAGEBREAKS	= 0x001b;
	const XLS_Type_NOTE					= 0x001c;
	const XLS_Type_NINETEENFOUR			= 0x0022;
	const XLS_Type_LEFTMARGIN			= 0x0026;
	const XLS_Type_RIGHTMARGIN			= 0x0027;
	const XLS_Type_TOPMARGIN			= 0x0028;
	const XLS_Type_BOTTOMMARGIN			= 0x0029;
	const XLS_Type_FILEPASS				= 0x002f;
	const XLS_Type_FONT					= 0x0031;
	const XLS_Type_CONTINUE				= 0x003c;
	const XLS_Type_CODEPAGE				= 0x0042;
	const XLS_Type_DEFCOLWIDTH 			= 0x0055;
	const XLS_Type_COLINFO				= 0x007d;
	const XLS_Type_IMDATA				= 0x007f;
	const XLS_Type_SHEETPR				= 0x0081;
	const XLS_Type_HCENTER				= 0x0083;
	const XLS_Type_VCENTER				= 0x0084;
	const XLS_Type_BOUNDSHEET			= 0x0085;
	const XLS_Type_PAGESETUP			= 0x00a1;
	const XLS_Type_MULRK				= 0x00bd;
	const XLS_Type_MULBLANK				= 0x00be;
	const XLS_Type_DBCELL				= 0x00d7;
	const XLS_Type_XF					= 0x00e0;
	const XLS_Type_MERGEDCELLS			= 0x00e5;
	const XLS_Type_SST					= 0x00fc;
	const XLS_Type_LABELSST				= 0x00fd;
	const XLS_Type_EXTSST				= 0x00ff;
	const XLS_Type_EXTERNALBOOK			= 0x01ae;
	const XLS_Type_TXO					= 0x01b6;
	const XLS_Type_HYPERLINK			= 0x01b8;
	const XLS_Type_DIMENSION			= 0x0200;
	const XLS_Type_BLANK				= 0x0201;
	const XLS_Type_NUMBER				= 0x0203;
	const XLS_Type_LABEL				= 0x0204;
	const XLS_Type_BOOLERR				= 0x0205;
	const XLS_Type_STRING				= 0x0207;
	const XLS_Type_ROW					= 0x0208;
	const XLS_Type_INDEX				= 0x020b;
	const XLS_Type_ARRAY				= 0x0221;
	const XLS_Type_DEFAULTROWHEIGHT 	= 0x0225;
	const XLS_Type_RK					= 0x027e;
	const XLS_Type_FORMAT				= 0x041e;
	const XLS_Type_BOF					= 0x0809;
	const XLS_Type_UNKNOWN				= 0xffff;

	/**
	 * Read data only?
	 *
	 * @var boolean
	 */
	private $_readDataOnly = false;

	/**
	 * Restict which sheets should be loaded?
	 *
	 * @var array
	 */
	private $_loadSheetsOnly = null;

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;

	/**
	 * OLE reader
	 *
	 * @var PHPExcel_Shared_OLERead
	 */
	private $_ole;

	/**
	 * Stream data that is read. Includes workbook globals substream as well as sheet substreams
	 *
	 * @var string
	 */
	private $_data;

	/**
	 * Current position in stream
	 *
	 * @var integer
	 */
	private $_pos;

	/**
	 * Workbook to be returned by the reader.
	 *
	 * @var PHPExcel
	 */
	private $_excel;

	/**
	 * Worksheet that is currently being built by the reader.
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_sheet;

	/**
	 * BIFF version
	 *
	 * @var int
	 */
	private $_version;

	/**
	 * Codepage set in the Excel file being read. Only important for BIFF5 (Excel 5.0 - Excel 95)
	 * For BIFF8 (Excel 97 - Excel 2003) this will always have the value 'UTF-16LE'
	 *
	 * @var string
	 */
	private $_codepage;

	/**
	 * Shared fonts
	 *
	 * @var array
	 */
	private $_fonts = array();

	/**
	 * Shared formats
	 *
	 * @var array
	 */
	private $_formats = array();

	/**
	 * Shared styles
	 *
	 * @var array
	 */
	private $_xf = array();

	/**
	 * Worksheets
	 *
	 * @var array
	 */
	private $_boundsheets = array();

	/**
	 * External books
	 *
	 * @var array
	 */
	private $_externalBooks = array();

	/**
	 * REF structures. Only applies to BIFF8.
	 *
	 * @var array
	 */
	private $_ref = array();

	/**
	 * Defined names
	 *
	 * @var array
	 */
	private $_definedname = array();

	/**
	 * Shared strings. Only applies to BIFF8.
	 *
	 * @var array
	 */
	private $_sst = array();


	// to be removed
	// dvc: added for external sheets references
	private $_extshref = array();

	// dvc: added list of names and their sheet associated indexes
	//private $_namedcells = array();

	/**
	 * Read data only?
	 *
	 * @return boolean
	 */
	public function getReadDataOnly()
	{
		return $this->_readDataOnly;
	}

	/**
	 * Set read data only
	 *
	 * @param boolean $pValue
	 */
	public function setReadDataOnly($pValue = false)
	{
		$this->_readDataOnly = $pValue;
	}

	/**
	 * Get which sheets to load
	 *
	 * @return mixed
	 */
	public function getLoadSheetsOnly()
	{
		return $this->_loadSheetsOnly;
	}

	/**
	 * Set which sheets to load
	 *
	 * @param mixed $value
	 */
	public function setLoadSheetsOnly($value = null)
	{
		$this->_loadSheetsOnly = is_array($value) ?
			$value : array($value);
	}

	/**
	 * Set all sheets to load
	 */
	public function setLoadAllSheets()
	{
		$this->_loadSheetsOnly = null;
	}

	/**
	 * Read filter
	 *
	 * @return PHPExcel_Reader_IReadFilter
	 */
	public function getReadFilter() {
		return $this->_readFilter;
	}

	/**
	 * Set read filter
	 *
	 * @param PHPExcel_Reader_IReadFilter $pValue
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
	}

	/**
	 * Create a new PHPExcel_Reader_Excel5 instance
	 */
	public function __construct() {
		$this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
	}

	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @throws 	Exception
	 */
	public function load($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Initialisations
		$this->_excel = new PHPExcel;
		$this->_excel->removeSheetByIndex(0);

		// Use ParseXL for the hard work.
		$this->_ole = new PHPExcel_Shared_OLERead();

		$this->_encoderFunction = function_exists('mb_convert_encoding') ?
			'mb_convert_encoding' : 'iconv';

		// get excel data
		$res = $this->_ole->read($pFilename);

		// oops, something goes wrong (Darko Miljanovic)
		if($res === false) { // check error code
			if($this->_ole->error == 1) { // bad file
				throw new Exception('The filename ' . $pFilename . ' is not readable');
			} elseif($this->_ole->error == 2) {
				throw new Exception('The filename ' . $pFilename . ' is not recognised as an Excel file');
			}
			// check other error codes here (eg bad fileformat, etc...)
		}

		$this->_data = $this->_ole->getWorkBook();
		$this->_pos = 0;

		// Parse workbook
		while (true) {
			$code = $this->_GetInt2d($this->_data, $this->_pos);

			switch ($code) {

			case self::XLS_Type_BOF:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				// offset: 0; size: 2; BIFF version
				$version = $this->_GetInt2d($this->_data, $pos + 4);
				$this->_version = $version;
				if (($version != self::XLS_BIFF8) && ($version != self::XLS_BIFF7)) {
					return false;
				}
				// offset: 2; size: 2; type of stream
				$substreamType = $this->_GetInt2d($this->_data, $pos + 6);
				if ($substreamType != self::XLS_WorkbookGlobals) {
					return false;
				}
				$this->_pos += 4 + $length;
				break;

			/**
			 * SHEETPROTECTION
			 *
			 * This record is part of the File Protection Block. It
			 * contains information about the read/write password of the
			 * file. All record contents following this record will be
			 * encrypted.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 */
			case self::XLS_Type_FILEPASS:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				$this->_pos += 4 + $length;
				throw new Exception('Cannot read encrypted file');
				break;

			/**
			 * CODEPAGE
			 *
			 * This record stores the text encoding used to write byte
			 * strings, stored as MS Windows code page identifier.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 */
			case self::XLS_Type_CODEPAGE:
				$this->_readCodepage();
				break;

			/**
			 * DATEMODE
			 *
			 * This record specifies the base date for displaying date
			 * values. All dates are stored as count of days past this
			 * base date. In BIFF2-BIFF4 this record is part of the
			 * Calculation Settings Block. In BIFF5-BIFF8 it is
			 * stored in the Workbook Globals Substream.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 */
			case self::XLS_Type_NINETEENFOUR:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				$this->_nineteenFour = (ord($this->_data[$pos + 4]) == 1);

				/*
				if (ord($this->_data[$pos + 4]) == 1) {
					PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_MAC_1904);
				} else {
					PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900);
				}
				*/
				$this->_pos += 4 + $length;
				break;

			case self::XLS_Type_FONT:
				$this->_readFont();
				break;

			/**
			 * FORMAT
			 *
			 * This record contains information about a number format.
			 * All FORMAT records occur together in a sequential list.
			 *
			 * In BIFF2-BIFF4 other records referencing a FORMAT record
			 * contain a zero-based index into this list. From BIFF5 on
			 * the FORMAT record contains the index itself that will be
			 * used by other records.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 */
			case self::XLS_Type_FORMAT:
				$this->_readFormat();
				break;

			/**
			 * XF - Extended Format
			 *
			 * This record contains formatting information for cells,
			 * rows, columns or styles.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 */
			case self::XLS_Type_XF:
				$this->_readXf();
				break;

			/**
			 * SHEET
			 *
			 * This record is  located in the  Workbook Globals
			 * Substream  and represents a sheet inside the workbook.
			 * One SHEET record is written for each sheet. It stores the
			 * sheet name and a stream offset to the BOF record of the
			 * respective Sheet Substream within the Workbook Stream.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 */
			case self::XLS_Type_BOUNDSHEET:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				// offset: 0; size: 4; absolute stream position of the BOF record of the sheet
				$rec_offset = $this->_GetInt4d($this->_data, $pos + 4);
				// offset: 4; size: 1; sheet state
				$rec_typeFlag = ord($this->_data[$pos + 8]);
				// offset: 5; size: 1; sheet type
				$rec_visibilityFlag = ord($this->_data[$pos + 9]);
				// offset: 6; size: var; sheet name
				if ($version == self::XLS_BIFF8) {
					$string = $this->_readUnicodeStringShort(substr($recordData, 6));
					$rec_name = $string['value'];
				} elseif ($version == self::XLS_BIFF7) {
					$string = $this->_readByteStringShort(substr($recordData, 6));
					$rec_name = $string['value'];
				}
				$this->_boundsheets[] = array(
					'name' => $rec_name,
					'offset' => $rec_offset
				);
				$this->_pos += 4 + $length;
				break;

			case self::XLS_Type_EXTERNALBOOK:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);

				$offset = 0;
				// there are 4 types of records
				if (strlen($recordData) > 4) {
					// external reference
					// offset: 0; size: 2; number of sheet names ($nm)
					$nm = $this->_GetInt2d($recordData, 0);
					$offset += 2;

					// offset: 2; size: var; encoded URL without sheet name (Unicode string, 16-bit length)
					$encodedUrlString = $this->_readUnicodeStringLong(substr($recordData, 2));
					$offset += $encodedUrlString['size'];

					// offset: var; size: var; list of $nm sheet names (Unicode strings, 16-bit length)
					$externalSheetNames = array();
					for ($i = 0; $i < $nm; ++$i) {
						$externalSheetNameString = $this->_readUnicodeStringLong(substr($recordData, $offset));
						$externalSheetNames[] = $externalSheetNameString['value'];
						$offset += $externalSheetNameString['size'];
					}

					// store the record data
					$this->_externalBooks[] = array(
						'type' => 'external',
						'encodedUrl' => $encodedUrlString['value'],
						'externalSheetNames' => $externalSheetNames,
					);

				} elseif (substr($recordData, 2, 2) == pack('CC', 0x01, 0x04)) {
					// internal reference
					// offset: 0; size: 2; number of sheet in this document
					// offset: 2; size: 2; 0x01 0x04
					$this->_externalBooks[] = array(
						'type' => 'internal',
					);
				} elseif (substr($recordData, 0, 4) == pack('VCC', 0x0001, 0x01, 0x3A)) {
					// add-in function
					// offset: 0; size: 2; 0x0001
					$this->_externalBooks[] = array(
						'type' => 'addInFunction',
					);
				} elseif (substr($recordData, 0, 2) == pack('V', 0x0000)) {
					// DDE links, OLE links
					// offset: 0; size: 2; 0x0000
					// offset: 2; size: var; encoded source document name
					$this->_externalBooks[] = array(
						'type' => 'DDEorOLE',
					);
				}

				$this->_pos += 4 + $length;
				break;

			case self::XLS_Type_EXTSHEET:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				// external sheet references provided for named cells
				if ($version == self::XLS_BIFF8) {
					$xpos = $pos + 4;
					$xcnt = $this->_GetInt2d($this->_data, $xpos);
					for ($x = 0; $x < $xcnt; ++$x) {
						$this->_extshref[$x] = $this->_GetInt2d($this->_data, $xpos + 4 + 6 * $x);
					}
				}

				// this if statement is going to replace the above one later
				if ($version == self::XLS_BIFF8) {
					// offset: 0; size: 2; number of following ref structures
					$nm = $this->_GetInt2d($recordData, 0);
					for ($i = 0; $i < $nm; ++$i) {
						$this->_ref[] = array(
							// offset: 2 + 6 * $i; index to EXTERNALBOOK record
							'externalBookIndex' => $this->_GetInt2d($recordData, 2 + 6 * $i),
							// offset: 4 + 6 * $i; index to first sheet in EXTERNALBOOK record
							'firstSheetIndex' => $this->_GetInt2d($recordData, 4 + 6 * $i),
							// offset: 6 + 6 * $i; index to last sheet in EXTERNALBOOK record
							'lastSheetIndex' => $this->_GetInt2d($recordData, 6 + 6 * $i),
						);
					}
				}
				$this->_pos += 4 + $length;
				break;

			/**
			 * DEFINEDNAME
			 *
			 * This record is part of a Link Table. It contains the name
			 * and the token array of an internal defined name. Token
			 * arrays of defined names contain tokens with aberrant
			 * token classes.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 */
			case self::XLS_Type_NAME:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				if ($this->_version == self::XLS_BIFF8) {
					// retrieves named cells
					$npos = $pos + 4;
					// offset: 0; size: 2; option flags
					$opts = $this->_GetInt2d($this->_data, $npos);
						// bit: 5; mask: 0x0020; 0 = user-defined name, 1 = built-in-name
						$isBuiltInName = (0x0020 & $opts) >> 5;
					// offset: 2; size: 1; keyboard shortcut
					// offset: 3; size: 1; length of the name (character count)
					$nlen = ord($this->_data[$npos + 3]);
					// offset: 4; size: 2; size of the formula data
					$flen = $this->_GetInt2d($this->_data, $npos + 4);

					// offset: 14; size: var; Name (Unicode string without length field)
					//$nstr = substr($this->_data, $npos + 15, $nlen);
					$string = $this->_readUnicodeString(substr($recordData, 14), $nlen);

					// offset: var; size: $flen; formula data
					$offset = 14 + $string['size'];
					$formulaStructure = pack('v', $flen) . substr($recordData, $offset, $flen);

					/*
					$fpos = $npos + 14 + 1 + $nlen;
					$ftoken = ord($this->_data[$fpos]);
					if ($ftoken == 0x3A && $opts == 0 && $flen == 7) {
						// then we have BIFF8

						// index to REF entry in EXTERNSHEET record
						$xref = $this->_GetInt2d($this->_data, $fpos + 1);

						// encoded cell address
						$frow = $this->_GetInt2d($this->_data, $fpos + 3);
						$fcol = ord($this->_data[$fpos + 5]);
						if (array_key_exists($xref,$this->_extshref)) {
							$fsheet = $this->_extshref[$xref];
							var_dump($fsheet);
						} else {
							$fsheet = '';
						}
						$this->_namedcells[$nstr] = array(
							'sheet' => $fsheet,
							'row' => $frow,
							'column' => $fcol
						);
					}
					*/

					try {
						$formula = $this->_getFormulaFromStructure($formulaStructure);
					} catch (Exception $e) {
						$formula = '';
					}

					$this->_definedname[] = array(
						'isBuiltInName' => $isBuiltInName,
						'name' => $string['value'],
						'formula' => $formula,
					);
				}

				$this->_pos += 4 + $length;
				break;

			/**
			 * SST - Shared String Table
			 *
			 * This record contains a list of all strings used anywhere
			 * in the workbook. Each string occurs only once. The
			 * workbook uses indexes into the list to reference the
			 * strings.
			 *
			 * --	"OpenOffice.org's Documentation of the Microsoft
			 * 		Excel File Format"
			 **/
			case self::XLS_Type_SST:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				$spos = $pos + 4;
				// last position in record data
				$limitpos = $spos + $length;
				// offset: 0; size: 4; total number of strings in the workbook
				// offset: 4; size: 4; number of following strings ($nm)
				$nm = $this->_GetInt4d($this->_data, $spos + 4);
				$spos += 8;
				// loop through the Unicode strings (16-bit length)
				for ($i = 0; $i < $nm; ++$i) {
					if ($spos == $limitpos) {
						// then we have reached end of SST record data
						$opcode = $this->_GetInt2d($this->_data, $spos);
						$conlength = $this->_GetInt2d($this->_data, $spos + 2);
						if ($opcode != self::XLS_Type_CONTINUE) {
							throw new Exception('Excel file is corrupt. Didn\'t find CONTINUE record while reading shared strings.');
						}
						$spos += 4;
						$limitpos = $spos + $conlength;
						$this->_pos += 4 + $conlength;
					}
					// Read in the number of characters in the Unicode string
					$numChars = $this->_GetInt2d($this->_data, $spos);
					$spos += 2;
					// option flags
					$optionFlags = ord($this->_data[$spos]);
					++$spos;
					// bit: 0; mask: 0x01; 0 = compressed; 1 = uncompressed
					$asciiEncoding = (($optionFlags & 0x01) == 0) ;
					// bit: 2; mask: 0x02; 0 = ordinary; 1 = Asian phonetic
					$extendedString = (($optionFlags & 0x04) != 0); // Asian phonetic
					// bit: 3; mask: 0x03; 0 = ordinary; 1 = Rich-Text
					$richString = (($optionFlags & 0x08) != 0);
					if ($richString) { // Read in the crun
						// number of Rich-Text formatting runs
						$formattingRuns = $this->_GetInt2d($this->_data, $spos);
						$spos += 2;
					}
					if ($extendedString) {
						// size of Asian phonetic setting
						$extendedRunLength = $this->_GetInt4d($this->_data, $spos);
						$spos += 4;
					}
					// read in the characters
					$len = ($asciiEncoding) ? $numChars : $numChars * 2;
					if ($spos + $len < $limitpos) {
						$retstr = substr($this->_data, $spos, $len);
						$spos += $len;
					} else {
						// found countinue record
						$retstr = substr($this->_data, $spos, $limitpos - $spos);
						$bytesRead = $limitpos - $spos;
						// remaining characters in Unicode string
						$charsLeft = $numChars - (($asciiEncoding) ? $bytesRead : ($bytesRead / 2));
						$spos = $limitpos;
						// keep reading the characters
						while ($charsLeft > 0) {
							// record data
							$opcode = $this->_GetInt2d($this->_data, $spos);

							// length of continue record data
							$conlength = $this->_GetInt2d($this->_data, $spos + 2);
							if ($opcode != self::XLS_Type_CONTINUE) {
								throw new Exception('Excel file is corrupt. Didn\'t find CONTINUE record while reading shared strings.');
							}
							$spos += 4;
							$limitpos = $spos + $conlength;

							// option flags are repeated when Unicode string is split by a continue record
							// OpenOffice.org documentation 5.21
							$option = ord($this->_data[$spos]);
							++$spos;

							if ($asciiEncoding && ($option == 0)) {
								// 1st fragment compressed
								// this fragment compressed
								$len = min($charsLeft, $limitpos - $spos);
								$retstr .= substr($this->_data, $spos, $len);
								$charsLeft -= $len;
								$asciiEncoding = true;

							} elseif (!$asciiEncoding && ($option != 0)) {
								// 1st fragment uncompressed
								// this fragment uncompressed
								$len = min($charsLeft * 2, $limitpos - $spos);
								$retstr .= substr($this->_data, $spos, $len);
								$charsLeft -= $len/2;
								$asciiEncoding = false;

							} elseif (!$asciiEncoding && ($option == 0)) {
								// 1st fragment uncompressed
								// this fragment compressed
								$len = min($charsLeft, $limitpos - $spos);
								for ($j = 0; $j < $len; ++$j) {
									$retstr .= $this->_data[$spos + $j].chr(0);
								}
								$charsLeft -= $len;
								$asciiEncoding = false;
							} else {
								// 1st fragment compressed
								// this fragment uncompressed
								$newstr = '';
								for ($j = 0; $j < strlen($retstr); ++$j) {
									$newstr .= $retstr[$j].chr(0);
								}
								$retstr = $newstr;
								$len = min($charsLeft * 2, $limitpos - $spos);
								$retstr .= substr($this->_data, $spos, $len);
								$charsLeft -= $len/2;
								$asciiEncoding = false;
							}
							$spos += $len;
							$this->_pos += 4 + $conlength;
						}
					}
					$retstr = $this->_encodeUTF16($retstr, $asciiEncoding);

					$fmtRuns = array();
					if ($richString) {
						// list of formatting runs
						for ($j = 0; $j < $formattingRuns; ++$j) {
							// first formatted character; zero-based
							$charPos = $this->_GetInt2d($this->_data, $spos + $j * 4);
							// index to font record
							$fontIndex = $this->_GetInt2d($this->_data, $spos + 2 + $j * 4);
							$fmtRuns[] = array(
								'charPos' => $charPos,
								'fontIndex' => $fontIndex,
							);
						}
						$spos += 4 * $formattingRuns;
					}
					if ($extendedString) {
						// For Asian phonetic settings, we skip the extended string data
						$spos += $extendedRunLength;
					}
					$this->_sst[] = array(
						'value' => $retstr,
						'fmtRuns' => $fmtRuns,
					);
				}
				$this->_pos += 4 + $length;
				break;

			case self::XLS_Type_EOF:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				$this->_pos += 4 + $length;
				break 2;

			default:
				$pos = $this->_pos;
				$length = $this->_GetInt2d($this->_data, $pos + 2);
				$recordData = substr($this->_data, $pos + 4, $length);
				$this->_pos += 4 + $length;
				break;

			}

		}

		// Parse the individual sheets
		foreach ($this->_boundsheets as $key => $val) {

			// check if sheet should be skipped
			if (isset($this->_loadSheetsOnly) && !in_array($val['name'], $this->_loadSheetsOnly)) {
				continue;
			}

			// add sheet to PHPExcel object
			$this->_sheet = $this->_excel->createSheet();
			$this->_sheet->setTitle($val['name']);

			$this->_sn = $key;
			$spos = $val['offset'];
			$this->_pos = $val['offset'];

			while (true) {
				$code = $this->_GetInt2d($this->_data, $this->_pos);

				switch ($code) {

				case self::XLS_Type_BOF:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					// do not use this version information for anything
					// it is unreliable (OpenOffice doc, 5.8), use only version information from the global stream
					$substreamType = ord($this->_data[$spos + 2]) | ord($this->_data[$spos + 3]) << 8;
					if ($substreamType != self::XLS_Worksheet) {
						return -2;
					}
					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_DEFAULTROWHEIGHT:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					// offset: 0; size: 2; option flags
					// offset: 2; size: 2; default height for unused rows, (twips 1/20 point)
					$height = $this->_GetInt2d($recordData, 2);
					$this->_sheet->getDefaultRowDimension()->setRowHeight($height / 20);

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_SHEETPR:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					// offset: 0; size: 2
					// bit: 6; mask: 0x0040; 0 = outline buttons above outline group
					$isSummaryBelow = (0x0040 & $this->_GetInt2d($recordData, 0)) >> 6;
					$this->_sheet->setShowSummaryBelow($isSummaryBelow);
					// bit: 7; mask: 0x0080; 0 = outline buttons left of outline group
					$isSummaryRight = (0x0080 & $this->_GetInt2d($recordData, 0)) >> 7;
					$this->_sheet->setShowSummaryRight($isSummaryRight);

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_HORIZONTALPAGEBREAKS:
					$this->_readHorizontalPageBreaks();
					break;

				case self::XLS_Type_VERTICALPAGEBREAKS:
					$this->_readVerticalPageBreaks();
					break;

				case self::XLS_Type_HEADER:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: var
						// realized that $recordData can be empty even when record exists
						if ($recordData) {
							$string = $this->_readUnicodeStringLong($recordData);
							$this->_sheet->getHeaderFooter()->setOddHeader($string['value']);
							$this->_sheet->getHeaderFooter()->setEvenHeader($string['value']);
						}
					}

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_FOOTER:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: var
						// realized that $recordData can be empty even when record exists
						if ($recordData) {
							$string = $this->_readUnicodeStringLong($recordData);
							$this->_sheet->getHeaderFooter()->setOddFooter($string['value']);
							$this->_sheet->getHeaderFooter()->setEvenFooter($string['value']);
						}
					}

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_HCENTER:
					$this->_readHcenter();
					break;

				case self::XLS_Type_VCENTER:
					$this->_readVcenter();
					break;

				case self::XLS_Type_LEFTMARGIN:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 8
						$this->_sheet->getPageMargins()->setLeft($this->_extractNumber($recordData));
					}

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_RIGHTMARGIN:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 8
						$this->_sheet->getPageMargins()->setRight($this->_extractNumber($recordData));
					}

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_TOPMARGIN:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 8
						$this->_sheet->getPageMargins()->setTop($this->_extractNumber($recordData));
					}

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_BOTTOMMARGIN:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 8
						$this->_sheet->getPageMargins()->setBottom($this->_extractNumber($recordData));
					}

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_PAGESETUP:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 2; paper size
						$paperSize = $this->_GetInt2d($recordData, 0);
						// offset: 2; size: 2; scaling factor
						$scale = $this->_GetInt2d($recordData, 2);
						// offset: 10; size: 2; option flags
							// bit: 1; mask: 0x0002; 0=landscape, 1=portrait
							$isPortrait = (0x0002 & $this->_GetInt2d($recordData, 10)) >> 1;
							// bit: 2; mask: 0x0004; 1= paper size, scaling factor, paper orient. not init
							// when this bit is set, do not use flags for those properties
							$isNotInit = (0x0004 & $this->_GetInt2d($recordData, 10)) >> 2;

						if (!$isNotInit) {
							$this->_sheet->getPageSetup()->setPaperSize($paperSize);
							switch ($isPortrait) {
							case 0: $this->_sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE); break;
							case 1: $this->_sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT); break;
							}
							$this->_sheet->getPageSetup()->setScale($scale);
						}
					}

					$this->_pos += 4 + $length;
					break;

				/**
				 * PROTECT - Sheet protection (BIFF2 through BIFF8)
				 *   if this record is omitted, then it also means no sheet protection
				 */
				case self::XLS_Type_PROTECT:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 2;
						// bit 0, mask 0x01; sheet protection
						$isSheetProtected = (0x01 & $this->_GetInt2d($recordData, 0)) >> 0;
						switch ($isSheetProtected) {
							case 0: break;
							case 1: $this->_sheet->getProtection()->setSheet(true); break;
						}
					}

					$this->_pos += 4 + $length;
					break;

				/**
				 * PASSWORD - Sheet protection (hashed) password (BIFF2 through BIFF8)
				 */
				case self::XLS_Type_PASSWORD:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 2; 16-bit hash value of password
						$password = strtoupper(dechex($this->_GetInt2d($recordData, 0))); // the hashed password
						$this->_sheet->getProtection()->setPassword($password, true);
					}

					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_DEFCOLWIDTH:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					// offset: 0; size: 2; row index
					$width = $this->_GetInt2d($recordData, 0);
					$this->_sheet->getDefaultColumnDimension()->setWidth($width);

					$this->_pos += 4 + $length;
					break;

				/**
				 * COLINFO - Column information
				 */
				case self::XLS_Type_COLINFO:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 2; index to first column in range
						$fc = $this->_GetInt2d($recordData, 0); // first column index
						// offset: 2; size: 2; index to last column in range
						$lc = $this->_GetInt2d($recordData, 2); // first column index
						// offset: 4; size: 2; width of the column in 1/256 of the width of the zero character
						$width = $this->_GetInt2d($recordData, 4);

						// offset: 6; size: 2; index to XF record for default column formatting

						// offset: 8; size: 2; option flags
							// bit: 0; mask: 0x0001; 1= columns are hidden
							$isHidden = (0x0001 & $this->_GetInt2d($recordData, 8)) >> 0;
							// bit: 10-8; mask: 0x0700; outline level of the columns (0 = no outline)
							$level = (0x0700 & $this->_GetInt2d($recordData, 8)) >> 8;
							// bit: 12; mask: 0x1000; 1 = collapsed
							$isCollapsed = (0x1000 & $this->_GetInt2d($recordData, 8)) >> 12;

						// offset: 10; size: 2; not used

						for ($i = $fc; $i <= $lc; ++$i) {
							$this->_sheet->getColumnDimensionByColumn($i)->setWidth($width / 256);
							$this->_sheet->getColumnDimensionByColumn($i)->setVisible(!$isHidden);
							$this->_sheet->getColumnDimensionByColumn($i)->setOutlineLevel($level);
							$this->_sheet->getColumnDimensionByColumn($i)->setCollapsed($isCollapsed);
						}
					}

					$this->_pos += 4 + $length;
					break;

				/**
				 * DIMENSION
				 *
				 * This record contains the range address of the used area
				 * in the current sheet.
				 *
				 * --	"OpenOffice.org's Documentation of the Microsoft
				 * 		Excel File Format"
				 */
				case self::XLS_Type_DIMENSION:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					$this->_pos += 4 + $length;
					break;

				/**
				 * ROW
				 *
				 * This record contains the properties of a single row in a
				 * sheet. Rows and cells in a sheet are divided into blocks
				 * of 32 rows.
				 *
				 * --	"OpenOffice.org's Documentation of the Microsoft
				 * 		Excel File Format"
				 */
				case self::XLS_Type_ROW:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 2; index of this row
						$r = $this->_GetInt2d($recordData, 0);
						// offset: 2; size: 2; index to column of the first cell which is described by a cell record
						// offset: 4; size: 2; index to column of the last cell which is described by a cell record, increased by 1
						// offset: 6; size: 2;
							// bit: 14-0; mask: 0x7FF; height of the row, in twips = 1/20 of a point
							$height = (0x7FF & $this->_GetInt2d($recordData, 6)) >> 0;
							// bit: 15: mask: 0x8000; 0 = row has custom height; 1= row has default height
							$useDefaultHeight = (0x8000 & $this->_GetInt2d($recordData, 6)) >> 15;

							if (!$useDefaultHeight) {
								$this->_sheet->getRowDimension($r + 1)->setRowHeight($height / 20);
							}
						// offset: 8; size: 2; not used
						// offset: 10; size: 2; not used in BIFF5-BIFF8
						// offset: 12; size: 4; option flags and default row formatting
							// bit: 2-0: mask: 0x00000007; outline level of the row
							$level = (0x00000007 & $this->_GetInt4d($recordData, 12)) >> 0;
							$this->_sheet->getRowDimension($r + 1)->setOutlineLevel($level);
							// bit: 4; mask: 0x00000010; 1 = outline group start or ends here... and is collapsed
							$isCollapsed = (0x00000010 & $this->_GetInt4d($recordData, 12)) >> 4;
							$this->_sheet->getRowDimension($r + 1)->setCollapsed($isCollapsed);
							// bit: 5; mask: 0x00000020; 1 = row is hidden
							$isHidden = (0x00000020 & $this->_GetInt4d($recordData, 12)) >> 5;
							$this->_sheet->getRowDimension($r + 1)->setVisible(!$isHidden);
					}
					$this->_pos += 4 + $length;
					break;

				/**
				 * DBCELL
				 *
				 * This record is written once in a Row Block. It contains
				 * relative offsets to calculate the stream position of the
				 * first cell record for each row. The offset list in this
				 * record contains as many offsets as ROW records are
				 * present in the Row Block.
				 *
				 * --	"OpenOffice.org's Documentation of the Microsoft
				 * 		Excel File Format"
				 */
				case self::XLS_Type_DBCELL:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_RK:
					$this->_readRk();
					break;

				case self::XLS_Type_LABELSST:
					$this->_readLabelSst();
					break;

				case self::XLS_Type_MULRK:
					$this->_readMulRk();
					break;

				case self::XLS_Type_NUMBER:
					$this->_readNumber();
					break;

				case self::XLS_Type_FORMULA:
					$this->_readFormula();
					break;

				case self::XLS_Type_BOOLERR:
					$this->_readBoolErr();
					break;

				case self::XLS_Type_MULBLANK:
					$this->_readMulBlank();
					break;

				case self::XLS_Type_LABEL:
					$this->_readLabel();
					break;

				case self::XLS_Type_BLANK:
					$this->_readBlank();
					break;

				/**
				 * MERGEDCELLS
				 *
				 * This record contains the addresses of merged cell ranges
				 * in the current sheet.
				 *
				 * --	"OpenOffice.org's Documentation of the Microsoft
				 * 		Excel File Format"
				 */
				case self::XLS_Type_MERGEDCELLS:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if ($version == self::XLS_BIFF8 && !$this->_readDataOnly) {
						$cellRangeAddressList = $this->_readBIFF8CellRangeAddressList($recordData);
						foreach ($cellRangeAddressList['cellRangeAddresses'] as $cellRangeAddress) {
							$this->_sheet->mergeCells($cellRangeAddress);
						}
					}
					$this->_pos += 4 + $length;
					break;

				case self::XLS_Type_HYPERLINK:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					if (!$this->_readDataOnly) {
						// offset: 0; size: 8; cell range address of all cells containing this hyperlink
						$cellRange = $this->_readBIFF8CellRangeAddressFixed($recordData, 0, 8);
						// offset: 8, size: 16; GUID of StdLink
						// offset: 24, size: 4; unknown value
						// offset: 28, size: 4; option flags
							// bit: 0; mask: 0x00000001; 0 = no link or extant, 1 = file link or URL
							$isFileLinkOrUrl = (0x00000001 & $this->_GetInt2d($recordData, 28)) >> 0;
							// bit: 1; mask: 0x00000002; 0 = relative path, 1 = absolute path or URL
							$isAbsPathOrUrl = (0x00000001 & $this->_GetInt2d($recordData, 28)) >> 1;
							// bit: 2 (and 4); mask: 0x00000014; 0 = no description
							$hasDesc = (0x00000014 & $this->_GetInt2d($recordData, 28)) >> 2;
							// bit: 3; mask: 0x00000008; 0 = no text, 1 = has text
							$hasText = (0x00000008 & $this->_GetInt2d($recordData, 28)) >> 3;
							// bit: 7; mask: 0x00000080; 0 = no target frame, 1 = has target frame
							$hasFrame = (0x00000080 & $this->_GetInt2d($recordData, 28)) >> 7;
							// bit: 8; mask: 0x00000100; 0 = file link or URL, 1 = UNC path (inc. server name)
							$isUNC = (0x00000100 & $this->_GetInt2d($recordData, 28)) >> 8;

						$offset = 32;
						if ($hasDesc) {
							// offset: 32; size: var; character count of description text
							$dl = $this->_GetInt4d($recordData, 32);
							// offset: 36; size: var; character array of description text, no Unicode string header, always 16-bit characters, zero terminated
							$desc = $this->_encodeUTF16(substr($recordData, 36, 2 * ($dl - 1)), false);
							$offset += 4 + 2 * $dl;
						}
						if ($hasFrame) {
							$fl = $this->_GetInt4d($recordData, $offset);
							$offset += 4 + 2 * $fl;
						}

						// detect type of hyperlink (there are 4 types)
						$hyperlinkType = null;

						if ($isUNC) {
							$hyperlinkType = 'UNC';
						} else if (!$isFileLinkOrUrl) {
							$hyperlinkType = 'workbook';
						} else if (ord($recordData[$offset]) == 0x03) {
							$hyperlinkType = 'local';
						} else if (ord($recordData[$offset]) == 0xE0) {
							$hyperlinkType = 'URL';
						}

						switch ($hyperlinkType) {

						case 'URL':
							// offset: var; size: 16; GUID of URL Moniker
							$offset += 16;
							// offset: var; size: 4; size (in bytes) of character array of the URL including trailing zero word
							$us = $this->_GetInt4d($recordData, $offset);
							$offset += 4;
							// offset: var; size: $us; character array of the URL, no Unicode string header, always 16-bit characters, zero-terminated
							$url = $this->_encodeUTF16(substr($recordData, $offset, $us - 1), false);
							$url .= $hasText ? '#' : '';
							$offset += $us;
							break;
						case 'workbook':
							// section 5.58.5: Hyperlink to the Current Workbook
							// e.g. Sheet2!B1:C2, stored in text mark field
							$url = 'sheet://';
							break;
						case 'local':
							// section 5.58.2: Hyperlink containing a URL
							// e.g. http://example.org/index.php
							// todo: implement
						case 'UNC':
							// section 5.58.4: Hyperlink to a File with UNC (Universal Naming Convention) Path
							// todo: implement
						default:
							$this->_pos += 4 + $length;
							break 2;

						}

						if ($hasText) {
							// offset: var; size: 4; character count of text mark including trailing zero word
							$tl = $this->_GetInt4d($recordData, $offset);
							$offset += 4;
							// offset: var; size: var; character array of the text mark without the # sign, no Unicode header, always 16-bit characters, zero-terminated
							$text = $this->_encodeUTF16(substr($recordData, $offset, 2 * ($tl - 1)), false);
							$url .= $text;
						}

						// apply the hyperlink to all the relevant cells
						foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cellRange) as $coordinate) {
							$this->_sheet->getCell($coordinate)->getHyperLink()->setUrl($url);
						}
					}
					$this->_pos += 4 + $length;
					break;

				//case self::XLS_Type_IMDATA:
				//	$this->_readImData();
				//	break;

				case self::XLS_Type_EOF:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					$this->_pos += 4 + $length;
					break 2;

				default:
					$spos = $this->_pos;
					$length = $this->_GetInt2d($this->_data, $spos + 2);
					$recordData = substr($this->_data, $spos + 4, $length);
					$spos += 4;
					$this->_pos += 4 + $length;
					break;

				}

			}
		}

		// add the named ranges (defined names)
		foreach ($this->_definedname as $definedName) {
			if ($definedName['isBuiltInName']) {
				switch ($definedName['name']) {

				case pack('C', 0x06):
					// print area
					//	in general, formula looks like this: Foo!$C$7:$J$66,Bar!$A$1:$IV$2

					$ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?

					foreach ($ranges as $range) {
						// $range should look like this one of these
						//		Foo!$C$7:$J$66
						//		Bar!$A$1:$IV$2

						$explodes = explode('!', $range);

						if (count($explodes) == 2) {
							if ($docSheet = $this->_excel->getSheetByName($explodes[0])) {
								$extractedRange = $explodes[1];
								$extractedRange = str_replace('$', '', $extractedRange);
								$docSheet->getPageSetup()->setPrintArea($extractedRange);
							}
						}
					}
					break;

				case pack('C', 0x07):
					// print titles (repeating rows)
					// Assuming BIFF8, there are 3 cases
					// 1. repeating rows
					//		formula looks like this: Sheet!$A$1:$IV$2
					//		rows 1-2 repeat
					// 2. repeating columns
					//		formula looks like this: Sheet!$A$1:$B$65536
					//		columns A-B repeat
					// 3. both repeating rows and repeating columns
					//		formula looks like this: Sheet!$A$1:$B$65536,Sheet!$A$1:$IV$2

					$ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?

					foreach ($ranges as $range) {
						// $range should look like this one of these
						//		Sheet!$A$1:$B$65536
						//		Sheet!$A$1:$IV$2

						$explodes = explode('!', $range);

						if (count($explodes) == 2) {
							if ($docSheet = $this->_excel->getSheetByName($explodes[0])) {

								$extractedRange = $explodes[1];
								$extractedRange = str_replace('$', '', $extractedRange);

								$coordinateStrings = explode(':', $extractedRange);
								if (count($coordinateStrings) == 2) {
									list($firstColumn, $firstRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[0]);
									list($lastColumn, $lastRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[1]);

									if ($firstColumn == 'A' and $lastColumn == 'IV') {
										// then we have repeating rows
										$docSheet->getPageSetup()->setRowsToRepeatAtTop(array($firstRow, $lastRow));
									} elseif ($firstRow == 1 and $lastRow == 65536) {
										// then we have repeating columns
										$docSheet->getPageSetup()->setColumnsToRepeatAtLeft(array($firstColumn, $lastColumn));
									}
								}
							}
						}
					}
					break;

				}
			} else {
				// Extract range
				$explodes = explode('!', $definedName['formula']);

				if (count($explodes) == 2) {
					if ($docSheet = $this->_excel->getSheetByName($explodes[0])) {
						$extractedRange = $explodes[1];
						$extractedRange = str_replace('$', '', $extractedRange);

						$this->_excel->addNamedRange( new PHPExcel_NamedRange((string)$definedName['name'], $docSheet, $extractedRange, true) );
					}
				}
			}
		}

		return $this->_excel;
	}

	/**
	 * Read CODEPAGE record
	 */
	private function _readCodepage()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$codepage = $this->_GetInt2d($this->_data, $pos + 4);

		switch($codepage) {

		case 367: // ASCII
			$this->_codepage ="ASCII";
			break;
		case 437: //OEM US
			$this->_codepage ="CP437";
			break;
		case 720: //OEM Arabic
			// currently not supported by libiconv
			$this->_codepage = "";
			break;
		case 737: //OEM Greek
			$this->_codepage ="CP737";
			break;
		case 775: //OEM Baltic
			$this->_codepage ="CP775";
			break;
		case 850: //OEM Latin I
			$this->_codepage ="CP850";
			break;
		case 852: //OEM Latin II (Central European)
			$this->_codepage ="CP852";
			break;
		case 855: //OEM Cyrillic
			$this->_codepage ="CP855";
			break;
		case 857: //OEM Turkish
			$this->_codepage ="CP857";
			break;
		case 858: //OEM Multilingual Latin I with Euro
			$this->_codepage ="CP858";
			break;
		case 860: //OEM Portugese
			$this->_codepage ="CP860";
			break;
		case 861: //OEM Icelandic
			$this->_codepage ="CP861";
			break;
		case 862: //OEM Hebrew
			$this->_codepage ="CP862";
			break;
		case 863: //OEM Canadian (French)
			$this->_codepage ="CP863";
			break;
		case 864: //OEM Arabic
			$this->_codepage ="CP864";
			break;
		case 865: //OEM Nordic
			$this->_codepage ="CP865";
			break;
		case 866: //OEM Cyrillic (Russian)
			$this->_codepage ="CP866";
			break;
		case 869: //OEM Greek (Modern)
			$this->_codepage ="CP869";
			break;
		case 874: //ANSI Thai
			$this->_codepage ="CP874";
			break;
		case 932: //ANSI Japanese Shift-JIS
			$this->_codepage ="CP932";
			break;
		case 936: //ANSI Chinese Simplified GBK
			$this->_codepage ="CP936";
			break;
		case 949: //ANSI Korean (Wansung)
			$this->_codepage ="CP949";
			break;
		case 950: //ANSI Chinese Traditional BIG5
			$this->_codepage ="CP950";
			break;
		case 1200: //UTF-16 (BIFF8)
			$this->_codepage ="UTF-16LE";
			break;
		case 1250:// ANSI Latin II (Central European)
			$this->_codepage ="CP1250";
			break;
		case 1251: //ANSI Cyrillic
			$this->_codepage ="CP1251";
			break;
		case 1252: //ANSI Latin I (BIFF4-BIFF7)
			$this->_codepage ="CP1252";
			break;
		case 1253: //ANSI Greek
			$this->_codepage ="CP1253";
			break;
		case 1254: //ANSI Turkish
			$this->_codepage ="CP1254";
			break;
		case 1255: //ANSI Hebrew
			$this->_codepage ="CP1255";
			break;
		case 1256: //ANSI Arabic
			$this->_codepage ="CP1256";
			break;
		case 1257: //ANSI Baltic
			$this->_codepage ="CP1257";
			break;
		case 1258: //ANSI Vietnamese
			$this->_codepage ="CP1258";
			break;
		case 1361: //ANSI Korean (Johab)
			$this->_codepage ="CP1361";
			break;
		case 10000: //Apple Roman
			// currently not supported by libiconv
			$this->_codepage = "";
			break;
		case 32768: //Apple Roman
			// currently not supported by libiconv
			$this->_codepage = "";
			break;
		case 32769: //ANSI Latin I (BIFF2-BIFF3)
			// currently not supported by libiconv
			$this->_codepage = "";
			break;

		}
		$this->_pos += 4 + $length;
	}

	/**
	 * Read a FONT record
	 */
	private function _readFont()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		if (!$this->_readDataOnly) {
			$font = array();
			// offset: 0; size: 2; height of the font (in twips = 1/20 of a point)
			$size = $this->_GetInt2d($recordData, 0);
			$font['size'] = $size / 20;

			// offset: 2; size: 2; option flags
				// bit: 0; mask 0x0001; bold (redundant in BIFF5-BIFF8)
				// bit: 1; mask 0x0002; italic
				$isItalic = (0x0002 & $this->_GetInt2d($recordData, 2)) >> 1;
				if ($isItalic) $font['italic'] = true;

				// bit: 2; mask 0x0004; underlined (redundant in BIFF5-BIFF8)
				// bit: 3; mask 0x0008; strike
				$isStrike = (0x0008 & $this->_GetInt2d($recordData, 2)) >> 3;
				if ($isStrike) $font['strike'] = true;

			// offset: 4; size: 2; colour index
			if ($color = $this->_mapColor($this->_GetInt2d($recordData, 4))) {
				$font['color'] = $color;
			}

			// offset: 6; size: 2; font weight
			$weight = $this->_GetInt2d($recordData, 6);
			switch ($weight) {
				case 0x02BC: $font['bold'] = true;
			}

			// offset: 8; size: 2; escapement type
			$escapement = $this->_GetInt2d($recordData, 8);
			switch ($escapement) {
				case 0x0001: $font['superScript'] = true; break;
				case 0x0002: $font['subScript'] = true; break;
			}

			// offset: 10; size: 1; underline type
			$underlineType = ord($recordData[10]);
			switch ($underlineType) {
				case 0x00: break; // no underline
				case 0x01: $font['underline'] = PHPExcel_Style_Font::UNDERLINE_SINGLE; break;
				case 0x02: $font['underline'] = PHPExcel_Style_Font::UNDERLINE_DOUBLE; break;
				case 0x21: $font['underline'] = PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING; break;
				case 0x22: $font['underline'] = PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING; break;
			}

			// offset: 11; size: 1; font family
			// offset: 12; size: 1; character set
			// offset: 13; size: 1; not used
			// offset: 14; size: var; font name
			if ($this->_version == self::XLS_BIFF8) {
				$string = $this->_readUnicodeStringShort(substr($recordData, 14));
			} else {
				$string = $this->_readByteStringShort(substr($recordData, 14));
			}
			$font['name'] = $string['value'];

			$this->_fonts[] = $font;
		}
		$this->_pos += 4 + $length;
	}

	/**
	 * Read a FORMAT record
	 */
	private function _readFormat()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		if (!$this->_readDataOnly) {
			$indexCode = $this->_GetInt2d($recordData, 0);
			
			if ($this->_version == self::XLS_BIFF8) {
				$string = $this->_readUnicodeStringLong(substr($recordData, 2));
			} else {
				// BIFF7
				$string = $this->_readByteStringShort(substr($recordData, 2));
			}
			
			$formatString = $string['value'];
			$this->_formats[$indexCode] = $formatString;
		}
		$this->_pos += 4 + $length;
	}

	/**
	 * Read an XF record
	 */
	private function _readXf()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);

		$style = array();
		if (!$this->_readDataOnly) {
			// offset:  0; size: 2; Index to FONT record
			if ($this->_GetInt2d($recordData, 0) < 4) {
				$fontIndex = $this->_GetInt2d($recordData, 0);
			} else {
				// this has to do with that index 4 is omitted in all BIFF versions for some strange reason
				// check the OpenOffice documentation of the FONT record
				$fontIndex = $this->_GetInt2d($recordData, 0) - 1;
			}
			$style['font'] = $this->_fonts[$fontIndex];

			// offset:  2; size: 2; Index to FORMAT record
			$numberFormatIndex = $this->_GetInt2d($recordData, 2);
			if (isset($this->_formats[$numberFormatIndex])) {
				// then we have user-defined format code
				$numberformat = array('code' => $this->_formats[$numberFormatIndex]);
			} elseif ($code = PHPExcel_Style_NumberFormat::builtInFormatCode($numberFormatIndex)) {
				// then we have built-in format code
				$numberformat = array('code' => $code);
			} else {
				// we set the general format code
				$numberformat = array('code' => 'General');
			}
			$style['numberformat'] = $numberformat;

			$style['protection'] = array();
			// offset:  4; size: 2; XF type, cell protection, and parent style XF
			// bit 2-0; mask 0x0007; XF_TYPE_PROT
			$xfTypeProt = $this->_GetInt2d($recordData, 4);
			// bit 0; mask 0x01; 1 = cell is locked
			$isLocked = (0x01 & $xfTypeProt) >> 0;
			$style['protection']['locked'] = $isLocked ?
				PHPExcel_Style_Protection::PROTECTION_INHERIT : PHPExcel_Style_Protection::PROTECTION_UNPROTECTED;
			// bit 1; mask 0x02; 1 = Formula is hidden
			$isHidden = (0x02 & $xfTypeProt) >> 1;
			$style['protection']['hidden'] = $isHidden ?
				PHPExcel_Style_Protection::PROTECTION_PROTECTED : PHPExcel_Style_Protection::PROTECTION_UNPROTECTED;
			// bit 2;

			if ($this->_version == self::XLS_BIFF8) {
				// offset:  6; size: 1; Alignment and text break
				$style['alignment'] = array();
					// bit 2-0, mask 0x07; horizontal alignment
					$horAlign = (0x07 & ord($recordData[6])) >> 0;
					switch ($horAlign) {
						case 0: $style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL; break;
						case 1: $style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT; break;
						case 2: $style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER; break;
						case 3: $style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT; break;
						case 5: $style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY; break;
					}
					// bit 3, mask 0x08; wrap text
					$wrapText = (0x08 & ord($recordData[6])) >> 3;
					switch ($wrapText) {
						case 0: $style['alignment']['wrap'] = false; break;
						case 1: $style['alignment']['wrap'] = true; break;
					}
					// bit 6-4, mask 0x70; vertical alignment
					$vertAlign = (0x70 & ord($recordData[6])) >> 4;
					switch ($vertAlign) {
						case 0: $style['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_TOP; break;
						case 1: $style['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_CENTER; break;
						case 2: $style['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_BOTTOM; break;
						case 3: $style['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_JUSTIFY; break;
					}

				// offset:  7; size: 1; XF_ROTATION: Text rotation angle
					$angle = ord($recordData[7]);
					$rotation = 0;
					if ($angle <= 90) {
						$rotation = $angle;
					} else if ($angle <= 180) {
						$rotation = 90 - $angle;
					} else if ($angle == 255) {
						$rotation = -165;
					}
					$style['alignment']['rotation'] = $rotation;

				// offset:  8; size: 1; Indentation, shrink to cell size, and text direction
					// bit: 3-0; mask: 0x0F; indent level
					$indent = (0x0F & ord($recordData[8])) >> 0;
					$style['alignment']['indent'] = $indent;
					
					// bit: 4; mask: 0x10; 1 = shrink content to fit into cell
					$shrinkToFit = (0x10 & ord($recordData[8])) >> 4;
					switch ($shrinkToFit) {
						case 0: $style['alignment']['shrinkToFit'] = false; break;
						case 1: $style['alignment']['shrinkToFit'] = true; break;
					}

				// offset:  9; size: 1; Flags used for attribute groups

				$style['borders'] = array(
					'left' => array(),
					'right' => array(),
					'top' => array(),
					'bottom' => array(),
				);
				// offset: 10; size: 4; Cell border lines and background area
					// bit: 3-0; mask: 0x0000000F; left style
					if ($bordersLeftStyle = $this->_mapBorderStyle((0x0000000F & $this->_GetInt4d($recordData, 10)) >> 0)) {
						$style['borders']['left']['style'] = $bordersLeftStyle;
					}
					// bit: 7-4; mask: 0x000000F0; right style
					if ($bordersRightStyle = $this->_mapBorderStyle((0x000000F0 & $this->_GetInt4d($recordData, 10)) >> 4)) {
						$style['borders']['right']['style'] = $bordersRightStyle;
					}
					// bit: 11-8; mask: 0x00000F00; top style
					if ($bordersTopStyle = $this->_mapBorderStyle((0x00000F00 & $this->_GetInt4d($recordData, 10)) >> 8)) {
						$style['borders']['top']['style'] = $bordersTopStyle;
					}
					// bit: 15-12; mask: 0x0000F000; bottom style
					if ($bordersBottomStyle = $this->_mapBorderStyle((0x0000F000 & $this->_GetInt4d($recordData, 10)) >> 12)) {
						$style['borders']['bottom']['style'] = $bordersBottomStyle;
					}
					// bit: 22-16; mask: 0x007F0000; left color
					if ($bordersLeftColor = $this->_mapColor((0x007F0000 & $this->_GetInt4d($recordData, 10)) >> 16)) {
						$style['borders']['left']['color'] = $bordersLeftColor;
					}
					// bit: 29-23; mask: 0x3F800000; right color
					if ($bordersRightColor = $this->_mapColor((0x3F800000 & $this->_GetInt4d($recordData, 10)) >> 23)) {
						$style['borders']['right']['color'] = $bordersRightColor;
					}

				$style['fill'] = array();
				// offset: 14; size: 4;
					// bit: 6-0; mask: 0x0000007F; top color
					if ($bordersTopColor = $this->_mapColor((0x0000007F & $this->_GetInt4d($recordData, 14)) >> 0)) {
						$style['borders']['top']['color'] = $bordersTopColor;
					}
					// bit: 13-7; mask: 0x00003F80; top color
					if ($bordersBottomColor = $this->_mapColor((0x00003F80 & $this->_GetInt4d($recordData, 14)) >> 7)) {
						$style['borders']['bottom']['color'] = $bordersBottomColor;
					}
					// bit: 31-26; mask: 0xFC000000 fill pattern
					if ($fillType = $this->_mapFillPattern((0xFC000000 & $this->_GetInt4d($recordData, 14)) >> 26)) {
						$style['fill']['type'] = $fillType;
					}
				// offset: 18; size: 2; pattern and background colour
					// bit: 6-0; mask: 0x007F; color index for pattern color
					if ($rgb = $this->_mapColor((0x007F & $this->_GetInt2d($recordData, 18)) >> 0)) {
						$style['fill']['startcolor'] = $rgb;
					}
					// bit: 13-7; mask: 0x3F80; color index for pattern background
					if ($rgb = $this->_mapColor((0x3F80 & $this->_GetInt2d($recordData, 18)) >> 7)) {
						$style['fill']['endcolor'] = $rgb;
					}
			} else {
				// BIFF7
				
			}
			$this->_xf[] = $style;
		}
		$this->_pos += 4 + $length;
	}

	/**
	 * Read HORIZONTALPAGEBREAKS record
	 */
	private function _readHorizontalPageBreaks()
	{
		$spos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $spos + 2);

		if ($this->_version == self::XLS_BIFF8 && !$this->_readDataOnly) {
			$recordData = substr($this->_data, $spos + 4, $length);
			// offset: 0; size: 2; number of the following row index structures
			$nm = $this->_GetInt2d($recordData, 0);
			// offset: 2; size: 6 * $nm; list of $nm row index structures
			for ($i = 0; $i < $nm; ++$i) {
				$r = $this->_GetInt2d($recordData, 2 + 6 * $i);
				$cf = $this->_GetInt2d($recordData, 2 + 6 * $i + 2);
				$cl = $this->_GetInt2d($recordData, 2 + 6 * $i + 4);
				// not sure why two column indexes are necessary?
				$this->_sheet->setBreakByColumnAndRow($cf, $r, PHPExcel_Worksheet::BREAK_ROW);
			}
		}

		$this->_pos += 4 + $length;
	}

	/**
	 * Read VERTICALPAGEBREAKS record
	 */
	private function _readVerticalPageBreaks()
	{
		$spos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $spos + 2);

		if ($this->_version == self::XLS_BIFF8 && !$this->_readDataOnly) {
			$recordData = substr($this->_data, $spos + 4, $length);
			// offset: 0; size: 2; number of the following column index structures
			$nm = $this->_GetInt2d($recordData, 0);
			// offset: 2; size: 6 * $nm; list of $nm row index structures
			for ($i = 0; $i < $nm; ++$i) {
				$c = $this->_GetInt2d($recordData, 2 + 6 * $i);
				$rf = $this->_GetInt2d($recordData, 2 + 6 * $i + 2);
				$rl = $this->_GetInt2d($recordData, 2 + 6 * $i + 4);
				// not sure why two row indexes are necessary?
				$this->_sheet->setBreakByColumnAndRow($c, $rf, PHPExcel_Worksheet::BREAK_COLUMN);
			}
		}

		$this->_pos += 4 + $length;
	}

	/**
	 * Read HCENTER record
	 */
	private function _readHcenter()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);

		if (!$this->_readDataOnly) {
			$recordData = substr($this->_data, $pos + 4, $length);

			// offset: 0; size: 2; 0 = print sheet left aligned, 1 = print sheet centered horizontally
			$isHorizontalCentered = (bool) $this->_GetInt2d($recordData, 0);

			$this->_sheet->getPageSetup()->setHorizontalCentered($isHorizontalCentered);
		}

		$this->_pos += 4 + $length;
	}

	/**
	 * Read VCENTER record
	 */
	private function _readVcenter()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);

		if (!$this->_readDataOnly) {
			$recordData = substr($this->_data, $pos + 4, $length);

			// offset: 0; size: 2; 0 = print sheet aligned at top page border, 1 = print sheet vertically centered
			$isVerticalCentered = (bool) $this->_GetInt2d($recordData, 0);

			$this->_sheet->getPageSetup()->setVerticalCentered($isVerticalCentered);
		}

		$this->_pos += 4 + $length;
	}

	/**
	 * Read RK record
	 * This record represents a cell that contains an RK value
	 * (encoded integer or floating-point value). If a
	 * floating-point value cannot be encoded to an RK value,
	 * a NUMBER record will be written. This record replaces the
	 * record INTEGER written in BIFF2.
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readRk()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; index to row
		$row = $this->_GetInt2d($this->_data, $pos);

		// offset: 2; size: 2; index to column
		$column = $this->_GetInt2d($this->_data, $pos + 2);
		$columnString = PHPExcel_Cell::stringFromColumnIndex($column);

		// Read cell?
		if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
			// offset: 4; size: 2; index to XF record
			$xfindex = $this->_GetInt2d($recordData, 4);

			// offset: 6; size: 4; RK value
			$rknum = $this->_GetInt4d($this->_data, $pos + 6);
			$numValue = $this->_GetIEEE754($rknum);

			// add style information
			if (!$this->_readDataOnly) {
				$this->_sheet->getStyleByColumnAndRow($column, $row + 1)->applyFromArray($this->_xf[$xfindex]);

				if (PHPExcel_Shared_Date::isDateTimeFormatCode($this->_xf[$xfindex]['numberformat']['code'])) {
					$numValue = PHPExcel_Shared_Date::ExcelToPHP($numValue);
				}
			}

			// add cell
			$this->_sheet->setCellValueExplicitByColumnAndRow($column, $row + 1, $numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read LABELSST record
	 * This record represents a cell that contains a string. It
	 * replaces the LABEL record and RSTRING record used in
	 * BIFF2-BIFF5.
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readLabelSst()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; index to row
		$row = $this->_GetInt2d($this->_data, $pos);

		// offset: 2; size: 2; index to column
		$column = $this->_GetInt2d($this->_data, $pos + 2);
		$columnString = PHPExcel_Cell::stringFromColumnIndex($column);

		// Read cell?
		if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
			// offset: 4; size: 2; index to XF record
			$xfindex = $this->_GetInt2d($this->_data, $pos + 4);

			// offset: 6; size: 4; index to SST record
			$index = $this->_GetInt4d($this->_data, $pos + 6);

			// add cell
			if (($fmtRuns = $this->_sst[$index]['fmtRuns']) && !$this->_readDataOnly) {
				// then we should treat as rich text
				$richText = new PHPExcel_RichText($this->_sheet->getCellByColumnAndRow($column, $row + 1));
				$charPos = 0;
				for ($i = 0; $i <= count($this->_sst[$index]['fmtRuns']); ++$i) {
					if (isset($fmtRuns[$i])) {
						$text = mb_substr($this->_sst[$index]['value'], $charPos, $fmtRuns[$i]['charPos'] - $charPos, 'UTF-8');
						$charPos = $fmtRuns[$i]['charPos'];
					} else {
						$text = mb_substr($this->_sst[$index]['value'], $charPos, mb_strlen($this->_sst[$index]['value']), 'UTF-8');
					}

					if (mb_strlen($text) > 0) {
						if ($i == 0) { // first text run, no style
							$richText->createText($text);
						} else {
							$textRun = $richText->createTextRun($text);
							if (isset($fmtRuns[$i - 1])) {
								if ($fmtRuns[$i - 1]['fontIndex'] < 4) {
									$fontIndex = $fmtRuns[$i - 1]['fontIndex'];
								} else {
									// this has to do with that index 4 is omitted in all BIFF versions for some strange reason
									// check the OpenOffice documentation of the FONT record
									$fontIndex = $fmtRuns[$i - 1]['fontIndex'] - 1;
								}
								$textRun->getFont()->applyFromArray($this->_fonts[$fontIndex]);
							}
						}
					}
				}
			} else {
				$this->_sheet->setCellValueExplicitByColumnAndRow($column, $row + 1, $this->_sst[$index]['value'], PHPExcel_Cell_DataType::TYPE_STRING);
			}

			// add style information
			if (!$this->_readDataOnly) {
				$this->_sheet->getStyleByColumnAndRow($column, $row + 1)->applyFromArray($this->_xf[$xfindex]);
			}
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read MULRK record
	 * This record represents a cell range containing RK value
	 * cells. All cells are located in the same row.
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readMulRk()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; index to row
		$row = $this->_GetInt2d($this->_data, $pos);
		
		// offset: 2; size: 2; index to first column
		$colFirst = $this->_GetInt2d($this->_data, $pos + 2);
		
		// offset: var; size: 2; index to last column
		$colLast = $this->_GetInt2d($this->_data, $pos + $length - 2);
		$columns = $colLast - $colFirst + 1;

		$tmppos = $pos + 4;
		for ($i = 0; $i < $columns; ++$i) {
			$columnString = PHPExcel_Cell::stringFromColumnIndex($colFirst + $i);

			// Read cell?
			if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
				// offset: 0; size: 2; index to XF record
				$xfindex = $this->_GetInt2d($recordData, 4 + 6 * $i);

				// offset: 2; size: 4; RK value
				$numValue = $this->_GetIEEE754($this->_GetInt4d($this->_data, $tmppos + 2));
				if (!$this->_readDataOnly) {
					// add style
					$this->_sheet->getStyleByColumnAndRow($colFirst + $i, $row + 1)->applyFromArray($this->_xf[$xfindex]);

					if (PHPExcel_Shared_Date::isDateTimeFormatCode($this->_xf[$xfindex]['numberformat']['code'])) {
						$numValue = PHPExcel_Shared_Date::ExcelToPHP($numValue);
					}
				}

				// add cell value
				$this->_sheet->setCellValueExplicitByColumnAndRow($colFirst + $i, $row + 1, $numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
			}

			$tmppos += 6;
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read NUMBER record
	 * This record represents a cell that contains a
	 * floating-point value.
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readNumber()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; index to row
		$row = $this->_GetInt2d($this->_data, $pos);

		// offset: 2; size 2; index to column
		$column = $this->_GetInt2d($this->_data, $pos + 2);
		$columnString = PHPExcel_Cell::stringFromColumnIndex($column);

		// Read cell?
		if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
			// offset 4; size: 2; index to XF record
			$xfindex = $this->_GetInt2d($recordData, 4);

			$numValue = $this->_createNumber($pos);

			// add cell style
			if (!$this->_readDataOnly) {
				$this->_sheet->getStyleByColumnAndRow($column, $row + 1)->applyFromArray($this->_xf[$xfindex]);
				if (PHPExcel_Shared_Date::isDateTimeFormatCode($this->_xf[$xfindex]['numberformat']['code'])) {
					$numValue = PHPExcel_Shared_Date::ExcelToPHP($numValue);
				}
			}

			// add cell value
			$this->_sheet->setCellValueExplicitByColumnAndRow($column, $row + 1, $numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read FORMULA record
	 * This record contains the token array and the result of a
	 * formula cell.
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readFormula()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; row index
		$row = $this->_GetInt2d($this->_data, $pos);

		// offset: 2; size: 2; col index
		$column = $this->_GetInt2d($this->_data, $pos + 2);
		$columnString = PHPExcel_Cell::stringFromColumnIndex($column);

		// Read cell?
		if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
			// offset: 4; size: 2; XF index
			$xfindex = $this->_GetInt2d($this->_data, $pos + 4);

			// offset: 6; size: 8; result of the formula
			if ((ord($this->_data[$pos + 6]) == 0) &&
			(ord($this->_data[$pos + 12]) == 255) &&
			(ord($this->_data[$pos + 13]) == 255)) {
				//String formula. Result follows in appended STRING record
				$dataType = PHPExcel_Cell_DataType::TYPE_STRING;
				$soff = $pos + $length;
				$scode = $this->_GetInt2d($this->_data, $soff);
				$slength = $this->_GetInt2d($this->_data, $soff + 2);
				$sdata = substr($this->_data, $soff + 4, $slength);
				if ($this->_version == self::XLS_BIFF8) {
					$string = $this->_readUnicodeStringLong($sdata);
					$value = $string['value'];
				} else {
					$string = $this->_readByteStringLong($sdata);
					$value = $string['value'];
				}
			} elseif ((ord($this->_data[$pos + 6]) == 1) &&
			(ord($this->_data[$pos + 12]) == 255) &&
			(ord($this->_data[$pos + 13]) == 255)) {
				//Boolean formula. Result is in +2; 0=false,1=true
				$dataType = PHPExcel_Cell_DataType::TYPE_BOOL;
				$value = (bool) ord($this->_data[$pos + 8]);
			} elseif ((ord($this->_data[$pos + 6]) == 2) &&
			(ord($this->_data[$pos + 12]) == 255) &&
			(ord($this->_data[$pos + 13]) == 255)) {
				//Error formula. Error code is in +2
				$dataType = PHPExcel_Cell_DataType::TYPE_ERROR;
				$value = $this->_mapErrorCode(ord($this->_data[$pos + 8]));
			} elseif ((ord($this->_data[$pos + 6]) == 3) &&
			(ord($this->_data[$pos + 12]) == 255) &&
			(ord($this->_data[$pos + 13]) == 255)) {
				//Formula result is a null string
				$dataType = PHPExcel_Cell_DataType::TYPE_NULL;
				$value = '';
			} else {
				// forumla result is a number, first 14 bytes like _NUMBER record
				$dataType = PHPExcel_Cell_DataType::TYPE_NUMERIC;
				$value = $this->_createNumber($pos);
			}

			// add cell style
			if (!$this->_readDataOnly) {
				$this->_sheet->getStyleByColumnAndRow($column, $row + 1)->applyFromArray($this->_xf[$xfindex]);
				if (PHPExcel_Shared_Date::isDateTimeFormatCode($this->_xf[$xfindex]['numberformat']['code'])) {
					$value = PHPExcel_Shared_Date::ExcelToPHP($value);
				}
			}

			// offset: 14: size: 2; option flags, recalculate always, recalculate on open etc.
			// offset: 16: size: 4; not used
			// offset: 20: size: variable; formula structure
			$formulaStructure = substr($recordData, 20);

			// add cell value
			try {
				if ($this->_version != self::XLS_BIFF8) {
					throw new Exception('Not BIFF8. Can only read BIFF8 formulas');
				}
				$formula = $this->_getFormulaFromStructure($formulaStructure); // get human language
				$this->_sheet->getCellByColumnAndRow($column, $row + 1)->setValueExplicit('=' . $formula, PHPExcel_Cell_DataType::TYPE_FORMULA);
			} catch (Exception $e) {
				$this->_sheet->setCellValueExplicitByColumnAndRow($column, $row + 1, $value, $dataType);
			}
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read BOOLERR record
	 * This record represents a Boolean value or error value
	 * cell.
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readBoolErr()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; row index
		$row = $this->_GetInt2d($this->_data, $pos);

		// offset: 2; size: 2; column index
		$column = $this->_GetInt2d($this->_data, $pos + 2);
		$columnString = PHPExcel_Cell::stringFromColumnIndex($column);

		// Read cell?
		if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
			// offset: 4; size: 2; index to XF record
			$xfindex = $this->_GetInt2d($recordData, 4);

			// offset: 6; size: 1; the boolean value or error value
			$boolErr = ord($recordData[6]);

			// offset: 7; size: 1; 0=boolean; 1=error
			$isError = ord($recordData[7]);

			switch ($isError) {

			case 0: // boolean
				$value = (bool) $boolErr;

				// add cell value
				$this->_sheet->getCellByColumnAndRow($column, $row + 1)->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_BOOL);
				break;

			case 1: // error type
				$value = $this->_mapErrorCode($boolErr);

				// add cell value
				$this->_sheet->getCellByColumnAndRow($column, $row + 1)->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_ERROR);
				break;
			}

			// add cell style
			if (!$this->_readDataOnly) {
				$this->_sheet->getStyleByColumnAndRow($column, $row + 1)->applyFromArray($this->_xf[$xfindex]);
			}
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read MULBLANK record
	 * This record represents a cell range of empty cells. All
	 * cells are located in the same row
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readMulBlank()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; index to row
		$row = $this->_GetInt2d($recordData, 0);

		// offset: 2; size: 2; index to first column
		$fc = $this->_GetInt2d($recordData, 2);

		// offset: 4; size: 2 x nc; list of indexes to XF records
		// add style information
		if (!$this->_readDataOnly) {
			for ($i = 0; $i < $length / 2 - 3; ++$i) {
				$columnString = PHPExcel_Cell::stringFromColumnIndex($fc + $i);

				// Read cell?
				if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
					$xfindex = $this->_GetInt2d($recordData, 4 + 2 * $i);
					$this->_sheet->getStyleByColumnAndRow($fc + $i, $row + 1)->applyFromArray($this->_xf[$xfindex]);
				}
			}
		}

		// offset: 6; size 2; index to last column (not needed)

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read LABEL record
	 * This record represents a cell that contains a string. In
	 * BIFF8 it is usually replaced by the LABELSST record.
	 * Excel still uses this record, if it copies unformatted
	 * text cells to the clipboard.
	 *
	 * --	"OpenOffice.org's Documentation of the Microsoft
	 * 		Excel File Format"
	 */
	private function _readLabel()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; index to row
		$row = $this->_GetInt2d($this->_data, $pos);

		// offset: 2; size: 2; index to column
		$column = $this->_GetInt2d($this->_data, $pos + 2);
		$columnString = PHPExcel_Cell::stringFromColumnIndex($column);

		// Read cell?
		if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
			// offset: 4; size: 2; XF index
			$xfindex = $this->_GetInt2d($recordData, 4);

			// add cell value
			// todo: what if string is very long? continue record
			if ($this->_version == self::XLS_BIFF8) {
				$string = $this->_readUnicodeStringLong(substr($recordData, 6));
				$value = $string['value'];
			} else {
				$string = $this->_readByteStringLong(substr($recordData, 6));
				$value = $string['value'];
			}
			$this->_sheet->setCellValueExplicitByColumnAndRow($column, $row + 1, $value, PHPExcel_Cell_DataType::TYPE_STRING);

			// add cell style
			if (!$this->_readDataOnly) {
				$this->_sheet->getStyleByColumnAndRow($column, $row + 1)->applyFromArray($this->_xf[$xfindex]);
			}
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read BLANK record
	 */
	private function _readBlank()
	{
		$pos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $pos + 2);
		$recordData = substr($this->_data, $pos + 4, $length);
		$pos += 4;

		// offset: 0; size: 2; row index
		$row = $this->_GetInt2d($recordData, 0);

		// offset: 2; size: 2; col index
		$col = $this->_GetInt2d($recordData, 2);
		$columnString = PHPExcel_Cell::stringFromColumnIndex($col);

		// Read cell?
		if ( !is_null($this->getReadFilter()) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->_sheet->getTitle()) ) {
			// offset: 4; size: 2; XF index
			$xfindex = $this->_GetInt2d($recordData, 4);

			// add style information
			if (!$this->_readDataOnly) {
				$this->_sheet->getStyleByColumnAndRow($col, $row + 1)->applyFromArray($this->_xf[$xfindex]);
			}
		}

		// move stream pointer to next record
		$this->_pos += 4 + $length;
	}

	/**
	 * Read IMDATA record
	 */
	private function _readImData()
	{

		$spos = $this->_pos;
		$length = $this->_GetInt2d($this->_data, $spos + 2);
		// get spliced record data
		$recordData = $this->_getSplicedRecordData();

		// UNDER CONSTRUCTION

		// offset: 0; size: 2; image format
		$cf = $this->_GetInt2d($recordData, 0);
		// offset: 2; size: 2; environment from which the file was written
		$env = $this->_GetInt2d($recordData, 2);
		// offset: 4; size: 4; length of the image data
		$lcb = $this->_GetInt4d($recordData, 4);
		// offset: 8; size: var; image data
		$iData = substr($recordData, 8);

		switch ($cf) {

		case 0x09: // Windows bitmap format
			// BITMAPCOREINFO
			// 1. BITMAPCOREHEADER
			// offset: 0; size: 4; bcSize, Specifies the number of bytes required by the structure
			$bcSize = $this->_GetInt4d($iData, 0);
			var_dump($bcSize);
			// offset: 4; size: 2; bcWidth, specifies the width of the bitmap, in pixels
			$bcWidth = $this->_GetInt2d($iData, 4);
			var_dump($bcWidth);
			// offset: 6; size: 2; bcHeight, specifies the height of the bitmap, in pixels.
			$bcHeight = $this->_GetInt2d($iData, 6);
			var_dump($bcHeight);
			$ih = imagecreatetruecolor($bcWidth, $bcHeight);
			// offset: 8; size: 2; bcPlanes, specifies the number of planes for the target device. This value must be 1
			// offset: 10; size: 2; bcBitCount specifies the number of bits-per-pixel. This value must be 1, 4, 8, or 24
			$bcBitCount = $this->_GetInt2d($iData, 10);
			var_dump($bcBitCount);

			$rgbString = substr($iData, 12);
			$rgbTriples = array();
			while (strlen($rgbString) > 0) {
				$rgbTriples[] = unpack('Cb/Cg/Cr', $rgbString);
				$rgbString = substr($rgbString, 3);
			}
			$x = 0;
			$y = 0;
			foreach ($rgbTriples as $i => $rgbTriple) {
				$color = imagecolorallocate($ih, $rgbTriple['r'], $rgbTriple['g'], $rgbTriple['b']);
				imagesetpixel($ih, $x, $bcHeight - 1 - $y, $color);
				$x = ($x + 1) % $bcWidth;
				$y = $y + floor(($x + 1) / $bcWidth);
			}
			//imagepng($ih, 'image.png');

			$drawing = new PHPExcel_Worksheet_Drawing();
			$drawing->setPath($filename);
			$drawing->setWorksheet($this->_sheet);

			break;

		case 0x02: // Windows metafile or Macintosh PICT format
		case 0x0e: // native format
		default;
			break;

		}

		// _getSplicedRecordData() takes care of moving current position in data stream
	}

	/**
	 * Reads a record from current position in data stream and continues reading data as long as CONTINUE
	 * records are found. Splices the record data pieces and returns the combined string as if record data
	 * is in one piece.
	 * Moves to next current position in data stream to start of next record different from a CONtINUE record
	 *
	 * @return string
	 */
	private function _getSplicedRecordData()
	{
		$data = '';

		do {
			// offset: 0; size: 2; identifier
			$identifier = $this->_GetInt2d($this->_data, $this->_pos);
			// offset: 2; size: 2; length
			$length = $this->_GetInt2d($this->_data, $this->_pos + 2);
			$data .= substr($this->_data, $this->_pos + 4, $length);

			$this->_pos += 4 + $length;
			$nextIdentifier = $this->_GetInt2d($this->_data, $this->_pos);
		}
		while ($nextIdentifier == self::XLS_Type_CONTINUE);

		return $data;

	}

	/**
	 * Convert formula structure into human readable Excel formula like 'A3+A5*5'
	 *
	 * @param string $formulaStructure The complete binary data for the formula
	 * @return string Human readable formula
	 */
	private function _getFormulaFromStructure($formulaStructure)
	{
		// offset: 0; size: 2; size of the following formula data
		$sz = $this->_GetInt2d($formulaStructure, 0);

		// offset: 2; size: sz
		$formulaData = substr($formulaStructure, 2, $sz);

		// for debug: dump the formula data
		//echo '<xmp>';
		//echo 'size: ' . $sz . "\n";
		//echo 'the entire formula data: ';
		//$this->_dump($formulaData);
		//echo "\n----\n";

		// offset: 2 + sz; size: variable (optional)
		if (strlen($formulaStructure) > 2 + $sz) {
			$additionalData = substr($formulaStructure, 2 + $sz);

			// for debug: dump the additional data
			//echo 'the entire additional data: ';
			//$this->_dump($additionalData);
			//echo "\n----\n";

		} else {
			$additionalData = '';
		}

		return $this->_getFormulaFromData($formulaData, $additionalData);
	}

	/**
	 * Take formula data and additional data for formula and return human readable formula
	 *
	 * @param string $formulaData The binary data for the formula itself
	 * @param string $additionalData Additional binary data going with the formula
	 * @return string Human readable formula
	 */
	private function _getFormulaFromData($formulaData,  $additionalData = '')
	{
		// start parsing the formula data
		$tokens = array();

		while (strlen($formulaData) > 0 and $token = $this->_getNextToken($formulaData)) {
			$tokens[] = $token;
			$formulaData = substr($formulaData, $token['size']);

			// for debug: dump the token
			//var_dump($token);
		}

		$formulaString = $this->_createFormulaFromTokens($tokens, $additionalData);

		return $formulaString;
	}

	/**
	 * Take array of tokens together with additional data for formula and return human readable formula
	 *
	 * @param array $tokens
	 * @param array $additionalData Additional binary data going with the formula
	 * @return string Human readable formula
	 */
	private function _createFormulaFromTokens($tokens, $additionalData)
	{
		$formulaStrings = array();
		foreach ($tokens as $token) {
			// initialize spaces
			$space0 = isset($space0) ? $space0 : ''; // spaces before next token, not tParen
			$space1 = isset($space1) ? $space1 : ''; // carriage returns before next token, not tParen
			$space2 = isset($space2) ? $space2 : ''; // spaces before opening parenthesis
			$space3 = isset($space3) ? $space3 : ''; // carriage returns before opening parenthesis
			$space4 = isset($space4) ? $space4 : ''; // spaces before closing parenthesis
			$space5 = isset($space5) ? $space5 : ''; // carriage returns before closing parenthesis

			switch ($token['name']) {
			case 'tAdd': // addition
			case 'tConcat': // addition
			case 'tDiv': // division
			case 'tEQ': // equaltiy
			case 'tGE': // greater than or equal
			case 'tGT': // greater than
			case 'tIsect': // intersection
			case 'tLE': // less than or equal
			case 'tList': // less than or equal
			case 'tLT': // less than
			case 'tMul': // multiplication
			case 'tNE': // multiplication
			case 'tPower': // power
			case 'tRange': // range
			case 'tSub': // subtraction
				$op2 = array_pop($formulaStrings);
				$op1 = array_pop($formulaStrings);
				$formulaStrings[] = "$op1$space1$space0{$token['data']}$op2";
				unset($space0, $space1);
				break;
			case 'tUplus': // unary plus
			case 'tUminus': // unary minus
				$op = array_pop($formulaStrings);
				$formulaStrings[] = "$space1$space0{$token['data']}$op";
				unset($space0, $space1);
				break;
			case 'tPercent': // percent sign
				$op = array_pop($formulaStrings);
				$formulaStrings[] = "$op$space1$space0{$token['data']}";
				unset($space0, $space1);
				break;
			case 'tAttrVolatile': // indicates volatile function
			case 'tAttrIf':
			case 'tAttrSkip':
			case 'tAttrChoose':
				// token is only important for Excel formula evaluator
				// do nothing
				break;
			case 'tAttrSpace': // space / carriage return
				// space will be used when next token arrives, do not alter formulaString stack
				switch ($token['data']['spacetype']) {
				case 'type0':
					$space0 = str_repeat(' ', $token['data']['spacecount']);
					break;
				case 'type1':
					$space1 = str_repeat("\n", $token['data']['spacecount']);
					break;
				case 'type2':
					$space2 = str_repeat(' ', $token['data']['spacecount']);
					break;
				case 'type3':
					$space3 = str_repeat("\n", $token['data']['spacecount']);
					break;
				case 'type4':
					$space4 = str_repeat(' ', $token['data']['spacecount']);
					break;
				case 'type5':
					$space5 = str_repeat("\n", $token['data']['spacecount']);
					break;
				}
				break;
			case 'tAttrSum': // SUM function with one parameter
				$op = array_pop($formulaStrings);
				$formulaStrings[] = "{$space1}{$space0}SUM($op)";
				unset($space0, $space1);
				break;
			case 'tFunc': // function with fixed number of arguments
			case 'tFuncV': // function with variable number of arguments
				$ops = array(); // array of operators
				for ($i = 0; $i < $token['data']['args']; ++$i) {
					$ops[] = array_pop($formulaStrings);
				}
				$ops = array_reverse($ops);
				$formulaStrings[] = "$space1$space0{$token['data']['function']}(" . implode(',', $ops) . ")";
				unset($space0, $space1);
				break;
			case 'tParen': // parenthesis
				$expression = array_pop($formulaStrings);
				$formulaStrings[] = "$space3$space2($expression$space5$space4)";
				unset($space2, $space3, $space4, $space5);
				break;
			case 'tArray': // array constant
				$constantArray = $this->_readBIFF8ConstantArray($additionalData);
				$formulaStrings[] = $space1 . $space0 . $constantArray['value'];
				$additionalData = substr($additionalData, $constantArray['size']); // bite of chunk of additional data
				unset($space0, $space1);
				break;
			case 'tMemArea':
				// bite off chunk of additional data
				$cellRangeAddressList = $this->_readBIFF8CellRangeAddressList($additionalData);
				$additionalData = substr($additionalData, $cellRangeAddressList['size']);
				$formulaStrings[] = "$space1$space0{$token['data']}";
				unset($space0, $space1);
				break;
			case 'tArea': // cell range address
			case 'tBool': // boolean
			case 'tErr': // error code
			case 'tInt': // integer
			case 'tMemErr':
			case 'tMemFunc':
			case 'tMissArg':
			case 'tName':
			case 'tNum': // number
			case 'tRef': // single cell reference
			case 'tRef3d': // 3d cell reference
			case 'tArea3d': // 3d cell range reference
			case 'tStr': // string
				$formulaStrings[] = "$space1$space0{$token['data']}";
				unset($space0, $space1);
				break;
			}
		}
		$formulaString = $formulaStrings[0];

		// for debug: dump the human readable formula
		//echo '----' . "\n";
		//echo 'Formula: ' . $formulaString;

		return $formulaString;
	}

	/**
	 * Fetch next token from binary formula data
	 *
	 * @param string Formula data
	 * @throws Exception
	 */
	private function _getNextToken($formulaData)
	{
		// offset: 0; size: 1; token id
		$id = ord($formulaData[0]); // token id
		$name = false; // initialize token name

		switch ($id) {
		case 0x03: $name = 'tAdd';		$size = 1;	$data = '+';	break;
		case 0x04: $name = 'tSub';		$size = 1;	$data = '-';	break;
		case 0x05: $name = 'tMul';		$size = 1;	$data = '*';	break;
		case 0x06: $name = 'tDiv';		$size = 1;	$data = '/';	break;
		case 0x07: $name = 'tPower';	$size = 1;	$data = '^';	break;
		case 0x08: $name = 'tConcat';	$size = 1;	$data = '&';	break;
		case 0x09: $name = 'tLT';		$size = 1;	$data = '<';	break;
		case 0x0A: $name = 'tLE';		$size = 1;	$data = '<=';	break;
		case 0x0B: $name = 'tEQ';		$size = 1;	$data = '=';	break;
		case 0x0C: $name = 'tGE';		$size = 1;	$data = '>=';	break;
		case 0x0D: $name = 'tGT';		$size = 1;	$data = '>';	break;
		case 0x0E: $name = 'tNE';		$size = 1;	$data = '<>';	break;
		case 0x0F: $name = 'tIsect';	$size = 1;	$data = ' ';	break;
		case 0x10: $name = 'tList';		$size = 1;	$data = ',';	break;
		case 0x11: $name = 'tRange';	$size = 1;	$data = ':';	break;
		case 0x12: $name = 'tUplus';	$size = 1;	$data = '+';	break;
		case 0x13: $name = 'tUminus';	$size = 1;	$data = '-';	break;
		case 0x14: $name = 'tPercent';	$size = 1;	$data = '%';	break;
		case 0x15: // parenthesis
			$name  = 'tParen';
			$size  = 1;
			$data = null;
			break;
		case 0x16: // missing argument
			$name = 'tMissArg';
			$size = 1;
			$data = '';
			break;
		case 0x17: // string
			$name = 'tStr';
			// offset: 1; size: var; Unicode string, 8-bit string length
			$string = $this->_readUnicodeStringShort(substr($formulaData, 1));
			$size = 1 + $string['size'];
			$data = $this->_UTF8toExcelDoubleQuoted($string['value']);
			break;
		case 0x19: // Special attribute
			// offset: 1; size: 1; attribute type flags:
			switch (ord($formulaData[1])) {
			case 0x01:
				$name = 'tAttrVolatile';
				$size = 4;
				$data = null;
				break;
			case 0x02:
				$name = 'tAttrIf';
				$size = 4;
				$data = null;
				break;
			case 0x04:
				$name = 'tAttrChoose';
				// offset: 2; size: 2; number of choices in the CHOOSE function ($nc, number of parameters decreased by 1)
				$nc = $this->_GetInt2d($formulaData, 2);
				// offset: 4; size: 2 * $nc
				// offset: 4 + 2 * $nc; size: 2
				$size = 2 * $nc + 6;
				$data = null;
				break;
			case 0x08:
				$name = 'tAttrSkip';
				$size = 4;
				$data = null;
				break;
			case 0x10:
				$name = 'tAttrSum';
				$size = 4;
				$data = null;
				break;
			case 0x40:
			case 0x41:
				$name = 'tAttrSpace';
				$size = 4;
				// offset: 2; size: 2; space type and position
				switch (ord($formulaData[2])) {
				case 0x00:
					$spacetype = 'type0';
					break;
				case 0x01:
					$spacetype = 'type1';
					break;
				case 0x02:
					$spacetype = 'type2';
					break;
				case 0x03:
					$spacetype = 'type3';
					break;
				case 0x04:
					$spacetype = 'type4';
					break;
				case 0x05:
					$spacetype = 'type5';
					break;
				default:
					throw new Exception('Unrecognized space type in tAttrSpace token');
					break;
				}
				// offset: 3; size: 1; number of inserted spaces/carriage returns
				$spacecount = ord($formulaData[3]);

				$data = array('spacetype' => $spacetype, 'spacecount' => $spacecount);
				break;
			default:
				throw new Exception('Unrecognized attribute flag in tAttr token');
				break;
			}
			break;
		case 0x1C: // error code
			// offset: 1; size: 1; error code
			$name = 'tErr';
			$size = 2;
			$data = $this->_mapErrorCode(ord($formulaData[1]));
			break;
		case 0x1D: // boolean
			// offset: 1; size: 1; 0 = false, 1 = true;
			$name = 'tBool';
			$size = 2;
			$data = ord($formulaData[1]) ? 'TRUE' : 'FALSE';
			break;
		case 0x1E: // integer
			// offset: 1; size: 2; unsigned 16-bit integer
			$name = 'tInt';
			$size = 3;
			$data = $this->_GetInt2d($formulaData, 1);
			break;
		case 0x1F: // number
			// offset: 1; size: 8;
			$name = 'tNum';
			$size = 9;
			$data = $this->_extractNumber(substr($formulaData, 1));
			break;
		case 0x40: // array constant
		case 0x60: // array constant
			// offset: 1; size: 7; not used
			$name = 'tArray';
			$size = 8;
			$data = null;
			break;
		case 0x41: // function with fixed number of arguments
			$name = 'tFunc';
			$size = 3;
			// offset: 1; size: 2; index to built-in sheet function
			switch ($this->_GetInt2d($formulaData, 1)) {
			case   2: $function = 'ISNA'; 			$args = 1; 	break;
			case   3: $function = 'ISERROR'; 		$args = 1; 	break;
			case  10: $function = 'NA'; 			$args = 0; 	break;
			case  15: $function = 'SIN'; 			$args = 1; 	break;
			case  16: $function = 'COS'; 			$args = 1; 	break;
			case  17: $function = 'TAN'; 			$args = 1; 	break;
			case  18: $function = 'ATAN'; 			$args = 1; 	break;
			case  19: $function = 'PI'; 			$args = 0; 	break;
			case  20: $function = 'SQRT'; 			$args = 1; 	break;
			case  21: $function = 'EXP'; 			$args = 1; 	break;
			case  22: $function = 'LN'; 			$args = 1; 	break;
			case  23: $function = 'LOG10'; 			$args = 1; 	break;
			case  24: $function = 'ABS'; 			$args = 1; 	break;
			case  25: $function = 'INT'; 			$args = 1; 	break;
			case  26: $function = 'SIGN'; 			$args = 1; 	break;
			case  27: $function = 'ROUND'; 			$args = 2; 	break;
			case  30: $function = 'REPT'; 			$args = 2; 	break;
			case  31: $function = 'MID'; 			$args = 3; 	break;
			case  32: $function = 'LEN'; 			$args = 1; 	break;
			case  33: $function = 'VALUE'; 			$args = 1; 	break;
			case  34: $function = 'TRUE'; 			$args = 0; 	break;
			case  35: $function = 'FALSE'; 			$args = 0; 	break;
			case  38: $function = 'NOT'; 			$args = 1; 	break;
			case  39: $function = 'MOD'; 			$args = 2;	break;
			case  40: $function = 'DCOUNT'; 		$args = 3;	break;
			case  41: $function = 'DSUM'; 			$args = 3;	break;
			case  42: $function = 'DAVERAGE'; 		$args = 3;	break;
			case  43: $function = 'DMIN'; 			$args = 3;	break;
			case  44: $function = 'DMAX'; 			$args = 3;	break;
			case  45: $function = 'DSTDEV'; 		$args = 3;	break;
			case  48: $function = 'TEXT'; 			$args = 2;	break;
			case  61: $function = 'MIRR'; 			$args = 3;	break;
			case  63: $function = 'RAND'; 			$args = 0;	break;
			case  65: $function = 'DATE'; 			$args = 3;	break;
			case  66: $function = 'TIME'; 			$args = 3;	break;
			case  67: $function = 'DAY'; 			$args = 1;	break;
			case  68: $function = 'MONTH'; 			$args = 1;	break;
			case  69: $function = 'YEAR'; 			$args = 1;	break;
			case  71: $function = 'HOUR'; 			$args = 1;	break;
			case  72: $function = 'MINUTE'; 		$args = 1;	break;
			case  73: $function = 'SECOND'; 		$args = 1;	break;
			case  74: $function = 'NOW'; 			$args = 0;	break;
			case  75: $function = 'AREAS'; 			$args = 1;	break;
			case  76: $function = 'ROWS'; 			$args = 1;	break;
			case  77: $function = 'COLUMNS'; 		$args = 1;	break;
			case  83: $function = 'TRANSPOSE'; 		$args = 1;	break;
			case  86: $function = 'TYPE'; 			$args = 1;	break;
			case  97: $function = 'ATAN2'; 			$args = 2;	break;
			case  98: $function = 'ASIN'; 			$args = 1;	break;
			case  99: $function = 'ACOS'; 			$args = 1;	break;
			case 105: $function = 'ISREF'; 			$args = 1;	break;
			case 111: $function = 'CHAR'; 			$args = 1;	break;
			case 112: $function = 'LOWER'; 			$args = 1;	break;
			case 113: $function = 'UPPER'; 			$args = 1;	break;
			case 114: $function = 'PROPER'; 		$args = 1;	break;
			case 117: $function = 'EXACT'; 			$args = 2;	break;
			case 118: $function = 'TRIM'; 			$args = 1;	break;
			case 119: $function = 'REPLACE'; 		$args = 4;	break;
			case 121: $function = 'CODE'; 			$args = 1;	break;
			case 126: $function = 'ISERR'; 			$args = 1;	break;
			case 127: $function = 'ISTEXT'; 		$args = 1;	break;
			case 128: $function = 'ISNUMBER'; 		$args = 1;	break;
			case 129: $function = 'ISBLANK'; 		$args = 1;	break;
			case 130: $function = 'T'; 				$args = 1;	break;
			case 131: $function = 'N'; 				$args = 1;	break;
			case 140: $function = 'DATEVALUE'; 		$args = 1;	break;
			case 141: $function = 'TIMEVALUE'; 		$args = 1;	break;
			case 142: $function = 'SLN'; 			$args = 3;	break;
			case 143: $function = 'SYD'; 			$args = 4;	break;
			case 162: $function = 'CLEAN'; 			$args = 1;	break;
			case 163: $function = 'MDETERM'; 		$args = 1;	break;
			case 164: $function = 'MINVERSE'; 		$args = 1;	break;
			case 165: $function = 'MMULT'; 			$args = 2;	break;
			case 184: $function = 'FACT'; 			$args = 1;	break;
			case 189: $function = 'DPRODUCT'; 		$args = 3;	break;
			case 190: $function = 'ISNONTEXT'; 		$args = 1;	break;
			case 195: $function = 'DSTDEVP'; 		$args = 3;	break;
			case 196: $function = 'DVARP'; 			$args = 3;	break;
			case 198: $function = 'ISLOGICAL'; 		$args = 1;	break;
			case 199: $function = 'DCOUNTA'; 		$args = 3;	break;
			case 207: $function = 'REPLACEB'; 		$args = 4;	break;
			case 210: $function = 'MIDB'; 			$args = 3;	break;
			case 211: $function = 'LENB'; 			$args = 1;	break;
			case 212: $function = 'ROUNDUP'; 		$args = 2;	break;
			case 213: $function = 'ROUNDDOWN'; 		$args = 2;	break;
			case 214: $function = 'ASC'; 			$args = 1;	break;
			case 215: $function = 'DBCS'; 			$args = 1;	break;
			case 221: $function = 'TODAY'; 			$args = 0;	break;
			case 229: $function = 'SINH'; 			$args = 1;	break;
			case 230: $function = 'COSH'; 			$args = 1;	break;
			case 231: $function = 'TANH'; 			$args = 1;	break;
			case 232: $function = 'ASINH'; 			$args = 1;	break;
			case 233: $function = 'ACOSH'; 			$args = 1;	break;
			case 234: $function = 'ATANH'; 			$args = 1;	break;
			case 235: $function = 'DGET'; 			$args = 3;	break;
			case 244: $function = 'INFO'; 			$args = 1;	break;
			case 252: $function = 'FREQUENCY'; 		$args = 2;	break;
			case 261: $function = 'ERROR.TYPE'; 	$args = 1;	break;
			case 271: $function = 'GAMMALN'; 		$args = 1;	break;
			case 273: $function = 'BINOMDIST'; 		$args = 4;	break;
			case 274: $function = 'CHIDIST'; 		$args = 2;	break;
			case 275: $function = 'CHIINV'; 		$args = 2;	break;
			case 276: $function = 'COMBIN'; 		$args = 2;	break;
			case 277: $function = 'CONFIDENCE'; 	$args = 3;	break;
			case 278: $function = 'CRITBINOM'; 		$args = 3;	break;
			case 279: $function = 'EVEN'; 			$args = 1;	break;
			case 280: $function = 'EXPONDIST'; 		$args = 3;	break;
			case 281: $function = 'FDIST'; 			$args = 3;	break;
			case 282: $function = 'FINV'; 			$args = 3;	break;
			case 283: $function = 'FISHER'; 		$args = 1;	break;
			case 284: $function = 'FISHERINV'; 		$args = 1;	break;
			case 285: $function = 'FLOOR'; 			$args = 2;	break;
			case 286: $function = 'GAMMADIST'; 		$args = 4;	break;
			case 287: $function = 'GAMMAINV'; 		$args = 3;	break;
			case 288: $function = 'CEILING'; 		$args = 2;	break;
			case 289: $function = 'HYPGEOMDIST';	$args = 4;	break;
			case 290: $function = 'LOGNORMDIST';	$args = 3;	break;
			case 291: $function = 'LOGINV';			$args = 3;	break;
			case 292: $function = 'NEGBINOMDIST';	$args = 3;	break;
			case 293: $function = 'NORMDIST';		$args = 4;	break;
			case 294: $function = 'NORMSDIST';		$args = 1;	break;
			case 295: $function = 'NORMINV';		$args = 3;	break;
			case 296: $function = 'NORMSINV';		$args = 1;	break;
			case 297: $function = 'STANDARDIZE';	$args = 3;	break;
			case 298: $function = 'ODD';			$args = 1;	break;
			case 299: $function = 'PERMUT';			$args = 2;	break;
			case 300: $function = 'POISSON';		$args = 3;	break;
			case 301: $function = 'TDIST';			$args = 3;	break;
			case 302: $function = 'WEIBULL';		$args = 4;	break;
			case 303: $function = 'SUMXMY2';		$args = 2;	break;
			case 304: $function = 'SUMX2MY2';		$args = 2;	break;
			case 305: $function = 'SUMX2PY2';		$args = 2;	break;
			case 306: $function = 'CHITEST';		$args = 2;	break;
			case 307: $function = 'CORREL';			$args = 2;	break;
			case 308: $function = 'COVAR';			$args = 2;	break;
			case 309: $function = 'FORECAST';		$args = 3;	break;
			case 310: $function = 'FTEST';			$args = 2;	break;
			case 311: $function = 'INTERCEPT';		$args = 2;	break;
			case 312: $function = 'PEARSON';		$args = 2;	break;
			case 313: $function = 'RSQ';			$args = 2;	break;
			case 314: $function = 'STEYX';			$args = 2;	break;
			case 315: $function = 'SLOPE';			$args = 2;	break;
			case 316: $function = 'TTEST';			$args = 4;	break;
			case 325: $function = 'LARGE';			$args = 2;	break;
			case 326: $function = 'SMALL';			$args = 2;	break;
			case 327: $function = 'QUARTILE';		$args = 2;	break;
			case 328: $function = 'PERCENTILE';		$args = 2;	break;
			case 331: $function = 'TRIMMEAN';		$args = 2;	break;
			case 332: $function = 'TINV';			$args = 2;	break;
			case 337: $function = 'POWER';			$args = 2;	break;
			case 342: $function = 'RADIANS';		$args = 1;	break;
			case 343: $function = 'DEGREES';		$args = 1;	break;
			case 346: $function = 'COUNTIF';		$args = 2;	break;
			case 347: $function = 'COUNTBLANK';		$args = 1;	break;
			case 350: $function = 'ISPMT';			$args = 4;	break;
			case 351: $function = 'DATEDIF';		$args = 3;	break;
			case 352: $function = 'DATESTRING';		$args = 1;	break;
			case 353: $function = 'NUMBERSTRING';	$args = 2;	break;
			case 360: $function = 'PHONETIC';		$args = 1;	break;
			default:
				throw new Exception('Unrecognized function in formula');
				break;
			}
			$data = array('function' => $function, 'args' => $args);
			break;
		case 0x42: // function with variable number of arguments
		case 0x62: // function with variable number of arguments
			$name = 'tFuncV';
			$size = 4;
			// offset: 1; size: 1; number of arguments
			$args = ord($formulaData[1]);
			// offset: 2: size: 2; index to built-in sheet function
			switch ($this->_GetInt2d($formulaData, 2)) {
			case   0: $function = 'COUNT';			break;
			case   1: $function = 'IF';				break;
			case   4: $function = 'SUM';			break;
			case   5: $function = 'AVERAGE';		break;
			case   6: $function = 'MIN';			break;
			case   7: $function = 'MAX';			break;
			case   8: $function = 'ROW';			break;
			case   9: $function = 'COLUMN';			break;
			case  11: $function = 'NPV';			break;
			case  12: $function = 'STDEV';			break;
			case  13: $function = 'DOLLAR';			break;
			case  14: $function = 'FIXED';			break;
			case  28: $function = 'LOOKUP';			break;
			case  29: $function = 'INDEX';			break;
			case  36: $function = 'AND';			break;
			case  37: $function = 'OR';				break;
			case  46: $function = 'VAR';			break;
			case  49: $function = 'LINEST';			break;
			case  50: $function = 'TREND';			break;
			case  51: $function = 'LOGEST';			break;
			case  52: $function = 'GROWTH';			break;
			case  56: $function = 'PV';				break;
			case  57: $function = 'FV';				break;
			case  58: $function = 'NPER';			break;
			case  59: $function = 'PMT';			break;
			case  60: $function = 'RATE';			break;
			case  62: $function = 'IRR';			break;
			case  64: $function = 'MATCH';			break;
			case  70: $function = 'WEEKDAY';		break;
			case  78: $function = 'OFFSET';			break;
			case  82: $function = 'SEARCH';			break;
			case 100: $function = 'CHOOSE';			break;
			case 101: $function = 'HLOOKUP';		break;
			case 102: $function = 'VLOOKUP';		break;
			case 109: $function = 'LOG';			break;
			case 115: $function = 'LEFT';			break;
			case 116: $function = 'RIGHT';			break;
			case 120: $function = 'SUBSTITUTE';		break;
			case 124: $function = 'FIND';			break;
			case 125: $function = 'CELL';			break;
			case 144: $function = 'DDB';			break;
			case 148: $function = 'INDIRECT';		break;
			case 167: $function = 'IPMT';			break;
			case 168: $function = 'PPMT';			break;
			case 169: $function = 'COUNTA';			break;
			case 183: $function = 'PRODUCT';		break;
			case 193: $function = 'STDEVP';			break;
			case 194: $function = 'VARP';			break;
			case 197: $function = 'TRUNC';			break;
			case 204: $function = 'USDOLLAR';		break;
			case 205: $function = 'FINDB';			break;
			case 206: $function = 'SEARCHB';		break;
			case 208: $function = 'LEFTB';			break;
			case 209: $function = 'RIGHTB';			break;
			case 216: $function = 'RANK';			break;
			case 219: $function = 'ADDRESS';		break;
			case 220: $function = 'DAYS360';		break;
			case 222: $function = 'VDB';			break;
			case 227: $function = 'MEDIAN';			break;
			case 228: $function = 'SUMPRODUCT';		break;
			case 247: $function = 'DB';				break;
			case 269: $function = 'AVEDEV';			break;
			case 270: $function = 'BETADIST';		break;
			case 272: $function = 'BETAINV';		break;
			case 317: $function = 'PROB';			break;
			case 318: $function = 'DEVSQ';			break;
			case 319: $function = 'GEOMEAN';		break;
			case 320: $function = 'HARMEAN';		break;
			case 321: $function = 'SUMSQ';			break;
			case 322: $function = 'KURT';			break;
			case 323: $function = 'SKEW';			break;
			case 324: $function = 'ZTEST';			break;
			case 329: $function = 'PERCENTRANK';	break;
			case 330: $function = 'MODE';			break;
			case 336: $function = 'CONCATENATE';	break;
			case 344: $function = 'SUBTOTAL';		break;
			case 345: $function = 'SUMIF';			break;
			case 354: $function = 'ROMAN';			break;
			case 358: $function = 'GETPIVOTDATA';	break;
			case 359: $function = 'HYPERLINK';		break;
			case 361: $function = 'AVERAGEA';		break;
			case 362: $function = 'MAXA';			break;
			case 363: $function = 'MINA';			break;
			case 364: $function = 'STDEVPA';		break;
			case 365: $function = 'VARPA';			break;
			case 366: $function = 'STDEVA';			break;
			case 367: $function = 'VARA';			break;
			default:
				throw new Exception('Unrecognized function in formula');
				break;
			}
			$data = array('function' => $function, 'args' => $args);
			break;
		case 0x23: // index to defined name
		case 0x43:
			$name = 'tName';
			$size = 5;
			// offset: 1; size: 2; one-based index to definedname record
			$definedNameIndex = $this->_GetInt2d($formulaData, 1) - 1;
			// offset: 2; size: 2; not used
			$data = $this->_definedname[$definedNameIndex]['name'];
			break;
		case 0x24: // single cell reference e.g. A5
		case 0x44:
		case 0x64:
			$name = 'tRef';
			$size = 5;
			$data = $this->_readBIFF8CellAddress(substr($formulaData, 1, 4));
			break;
		case 0x25: // cell range reference to cells in the same sheet
		case 0x45:
		case 0x65:
			$name = 'tArea';
			$size = 9;
			$data = $this->_readBIFF8CellRangeAddress(substr($formulaData, 1, 8));
			break;
		case 0x26:
		case 0x46:
			$name = 'tMemArea';
			// offset: 1; size: 4; not used
			// offset: 5; size: 2; size of the following subexpression
			$subSize = $this->_GetInt2d($formulaData, 5);
			$size = 7 + $subSize;
			$data = $this->_getFormulaFromData(substr($formulaData, 7, $subSize));
			break;
		case 0x47:
			$name = 'tMemErr';
			// offset: 1; size: 4; not used
			// offset: 5; size: 2; size of the following subexpression
			$subSize = $this->_GetInt2d($formulaData, 5);
			$size = 7 + $subSize;
			$data = $this->_getFormulaFromData(substr($formulaData, 7, $subSize));
			break;
		case 0x29:
		case 0x49:
			$name = 'tMemFunc';
			// offset: 1; size: 2; size of the following subexpression
			$subSize = $this->_GetInt2d($formulaData, 1);
			$size = 3 + $subSize;
			$data = $this->_getFormulaFromData(substr($formulaData, 3, $subSize));
			break;
		case 0x3A: // 3d reference to cell
		case 0x5A:
			$name = 'tRef3d';
			$size = 7;
			// offset: 1; size: 2; index to REF entry
			$sheetRange = $this->_readSheetRangeByRefIndex($this->_GetInt2d($formulaData, 1));
			// offset: 3; size: 4; cell address
			$cellAddress = $this->_readBIFF8CellAddress(substr($formulaData, 3, 4));
			$data = "$sheetRange!$cellAddress";
			break;
		case 0x3B: // 3d reference to cell range
			$name = 'tArea3d';
			$size = 11;
			// offset: 1; size: 2; index to REF entry
			$sheetRange = $this->_readSheetRangeByRefIndex($this->_GetInt2d($formulaData, 1));
			// offset: 3; size: 8; cell address
			$cellRangeAddress = $this->_readBIFF8CellRangeAddress(substr($formulaData, 3, 8));
			$data = "$sheetRange!$cellRangeAddress";
			break;
		// case 0x39: // don't know how to deal with
		default:
			throw new Exception('Unrecognized token ' . sprintf('%02X', $id) . ' in formula');
			break;
		}

		return array(
			'id' => $id,
			'name' => $name,
			'size' => $size,
			'data' => $data,
		);
	}

	/**
	 * Reads a cell address in BIFF8 e.g. 'A2' or '$A$2'
	 * section 3.3.4
	 */
	private function _readBIFF8CellAddress($cellAddressStructure)
	{
		// offset: 0; size: 2; index to row (0... 65535) (or offset (-32768... 32767))
			$row = $this->_GetInt2d($cellAddressStructure, 0) + 1;

		// offset: 2; size: 2; index to column or column offset + relative flags
			// bit: 7-0; mask 0x00FF; column index
			$column = PHPExcel_Cell::stringFromColumnIndex(0x00FF & $this->_GetInt2d($cellAddressStructure, 2));
			// bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
			if (!(0x4000 & $this->_GetInt2d($cellAddressStructure, 2))) {
				$column = '$' . $column;
			}
			// bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
			if (!(0x8000 & $this->_GetInt2d($cellAddressStructure, 2))) {
				$row = '$' . $row;
			}

		return $column . $row;
	}

	/**
	 * Reads a cell range address in BIFF8 e.g. 'A2:B6' or 'A1'
	 * always fixed range
	 * section 2.5.14
	 */
	private function _readBIFF8CellRangeAddressFixed($subData)
	{
		// offset: 0; size: 2; index to first row
		$fr = $this->_GetInt2d($subData, 0) + 1;

		// offset: 2; size: 2; index to last row
		$lr = $this->_GetInt2d($subData, 2) + 1;

		// offset: 4; size: 2; index to first column
		$fc = PHPExcel_Cell::stringFromColumnIndex($this->_GetInt2d($subData, 4));

		// offset: 6; size: 2; index to last column
		$lc = PHPExcel_Cell::stringFromColumnIndex($this->_GetInt2d($subData, 6));

		if ($fr == $lr and $fc == $lc) {
			return "$fc$fr";
		}
		return "$fc$fr:$lc$lr";
	}

	/**
	 * Reads a cell range address in BIFF8 e.g. 'A2:B6' or '$A$2:$B$6'
	 * there are flags indicating whether column/row index is relative
	 * section 3.3.4
	 */
	private function _readBIFF8CellRangeAddress($subData)
	{
		// todo: if cell range is just a single cell, should this funciton
		// not just return e.g. 'A1' and not 'A1:A1' ?

		// offset: 0; size: 2; index to first row (0... 65535) (or offset (-32768... 32767))
			$fr = $this->_GetInt2d($subData, 0) + 1;
		// offset: 2; size: 2; index to last row (0... 65535) (or offset (-32768... 32767))
			$lr = $this->_GetInt2d($subData, 2) + 1;
		// offset: 4; size: 2; index to first column or column offset + relative flags
			// bit: 7-0; mask 0x00FF; column index
			$fc = PHPExcel_Cell::stringFromColumnIndex(0x00FF & $this->_GetInt2d($subData, 4));
			// bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
			if (!(0x4000 & $this->_GetInt2d($subData, 4))) {
				$fc = '$' . $fc;
			}
			// bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
			if (!(0x8000 & $this->_GetInt2d($subData, 4))) {
				$fr = '$' . $fr;
			}
		// offset: 6; size: 2; index to last column or column offset + relative flags
			// bit: 7-0; mask 0x00FF; column index
			$lc = PHPExcel_Cell::stringFromColumnIndex(0x00FF & $this->_GetInt2d($subData, 6));
			// bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
			if (!(0x4000 & $this->_GetInt2d($subData, 6))) {
				$lc = '$' . $lc;
			}
			// bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
			if (!(0x8000 & $this->_GetInt2d($subData, 6))) {
				$lr = '$' . $lr;
			}

		return "$fc$fr:$lc$lr";
	}

	/**
	 * Read BIFF8 cell range address list
	 * section 2.5.15
	 */
	private function _readBIFF8CellRangeAddressList($subData)
	{
		$cellRangeAddresses = array();

		// offset: 0; size: 2; number of the following cell range addresses
		$nm = $this->_GetInt2d($subData, 0);

		$offset = 2;
		// offset: 2; size: 8 * $nm; list of $nm (fixed) cell range addresses
		for ($i = 0; $i < $nm; ++$i) {
			$cellRangeAddresses[] = $this->_readBIFF8CellRangeAddressFixed(substr($subData, $offset, 8));
			$offset += 8;
		}

		return array(
			'size' => 2 + 8 * $nm,
			'cellRangeAddresses' => $cellRangeAddresses,
		);
	}

	/**
	 * Get a sheet range like Sheet1:Sheet3 from REF index
	 * Note: If there is only one sheet in the range, one gets e.g Sheet1
	 */
	private function _readSheetRangeByRefIndex($index)
	{
		// we are assuming that ref index refers to internal workbook
		// in general, this is wrong, fix later
		if (isset($this->_ref[$index])) {
			$firstSheetName = $this->_boundsheets[$this->_ref[$index]['firstSheetIndex']]['name'];
			$lastSheetName = $this->_boundsheets[$this->_ref[$index]['lastSheetIndex']]['name'];

			if ($firstSheetName == $lastSheetName) {
				// collapsed sheet range
				$sheetRange = $firstSheetName;
			} else {
				$sheetRange = "$firstSheetName:$lastSheetName";
			}

			// escape the single-quotes
			$sheetRange = str_replace("'", "''", $sheetRange);

			// if there are special characters, we need to enclose the range in single-quotes
			// todo: check if we have identified the whole set of special characters
			// it seems that the following characters are not accepted for sheet names
			// and we may assume that they are not present: []*/:\?
			if (preg_match("/[ !\"@#£$%&{()}<>=+'|^,;-]/", $sheetRange)) {
				$sheetRange = "'$sheetRange'";
			}

			return $sheetRange;
		}
		return false;
	}

	/**
	 * read BIFF8 constant value array from array data
	 * returns e.g. array('value' => '{1,2;3,4}', 'size' => 40}
	 * section 2.5.8
	 */
	private function _readBIFF8ConstantArray($arrayData)
	{
		// offset: 0; size: 1; number of columns decreased by 1
		$nc = ord($arrayData[0]);
		// offset: 1; size: 2; number of rows decreased by 1
		$nr = $this->_GetInt2d($arrayData, 1);
		$size = 3; // initialize
		$arrayData = substr($arrayData, 3);
		// offset: 3; size: var; list of ($nc + 1) * ($nr + 1) constant values
		$matrixChunks = array();
		for ($r = 1; $r <= $nr + 1; ++$r) {
			$items = array();
			for ($c = 1; $c <= $nc + 1; ++$c) {
				$constant = $this->_readBIFF8Constant($arrayData);
				$items[] = $constant['value'];
				$arrayData = substr($arrayData, $constant['size']);
				$size += $constant['size'];
			}
			$matrixChunks[] = implode(',', $items); // looks like e.g. '1,"hello"'
		}
		$matrix = '{' . implode(';', $matrixChunks) . '}';

		return array(
			'value' => $matrix,
			'size' => $size,
		);
	}

	/**
	 * read BIFF8 constant value which may be 'Empty Value', 'Number', 'String Value', 'Boolean Value', 'Error Value'
	 * section 2.5.7
	 * returns e.g. array('value' => '5', 'size' => 9)
	 */
	private function _readBIFF8Constant($valueData)
	{
		// offset: 0; size: 1; identifier for type of constant
		$identifier = ord($valueData[0]);
		switch ($identifier) {
		case 0x00: // empty constant (what is this?)
			$value = '';
			$size = 9;
			break;
		case 0x01: // number
			// offset: 1; size: 8; IEEE 754 floating-point value
			$value = $this->_extractNumber(substr($valueData, 1, 8));
			$size = 9;
			break;
		case 0x02: // string value
			// offset: 1; size: var; Unicode string, 16-bit string length
			$string = $this->_readUnicodeStringLong(substr($valueData, 1));
			$value = '"' . $string['value'] . '"';
			$size = 1 + $string['size'];
			break;
		case 0x04: // boolean
			// offset: 1; size: 1; 0 = FALSE, 1 = TRUE
			if (ord($valueData[1])) {
				$value = 'TRUE';
			} else {
				$value = 'FALSE';
			}
			$size = 9;
			break;
		case 0x10: // error code
			// offset: 1; size: 1; error code
			$value = $this->_mapErrorCode(ord($valueData[1]));
			$size = 9;
			break;
		}
		return array(
			'value' => $value,
			'size' => $size,
		);
	}

	/**
	 * Read byte string (8-bit string length)
	 * OpenOffice documentation: 2.5.2
	 *
	 * @return array
	 */
	private function _readByteStringShort($subData)
	{
		// offset: 0; size: 1; length of the string (character count)
		$ln = ord($subData[0]);
		// offset: 1: size: var; character array (8-bit characters)
		$value = $this->_decodeCodepage(substr($subData, 1, $ln));

		return array(
			'value' => $value,
			'size' => 1 + $ln, // size in bytes of data structure
		);
	}

	/**
	 * Read byte string (16-bit string length)
	 * OpenOffice documentation: 2.5.2
	 *
	 * @return array
	 */
	private function _readByteStringLong($subData)
	{
		// offset: 0; size: 2; length of the string (character count)
		$ln = $this->_GetInt2d($subData, 0);
		// offset: 2: size: var; character array (8-bit characters)
		$value = $this->_decodeCodepage(substr($subData, 2));

		//return $string;
		return array(
			'value' => $value,
			'size' => 2 + $ln, // size in bytes of data structure
		);
	}

	/**
	 * Extracts an Excel Unicode short string (8-bit string length)
	 * OpenOffice documentation: 2.5.3
	 * function will automatically find out where the Unicode string ends.
	 */
	private function _readUnicodeStringShort($subData)
	{
		$value = '';

		// offset: 0: size: 1; length of the string (character count)
		$characterCount = ord($subData[0]);

		$string = $this->_readUnicodeString(substr($subData, 1), $characterCount);

		// add 1 for the string length
		$string['size'] += 1;

		return $string;
	}

	/**
	 * Extracts an Excel Unicode long string (16-bit string length)
	 * OpenOffice documentation: 2.5.3
	 * this function is under construction, needs to support rich text, and Asian phonetic settings
	 */
	private function _readUnicodeStringLong($subData)
	{
		$value = '';

		// offset: 0: size: 2; length of the string (character count)
		$characterCount = $this->_GetInt2d($subData, 0);

		$string = $this->_readUnicodeString(substr($subData, 2), $characterCount);

		// add 2 for the string length
		$string['size'] += 2;

		return $string;
	}

	/**
	 * Read Unicode string with no string length field, but with known character count
	 * this function is under construction, needs to support rich text, and Asian phonetic settings
	 * OpenOffice.org's Documentation of the Microsoft Excel File Format, section 2.5.3
	 *
	 * @return array
	 */
	private function _readUnicodeString($subData, $characterCount)
	{
		$value = '';

		// offset: 0: size: 1; option flags
			// bit: 0; mask: 0x01; character compression (0 = compressed 8-bit, 1 = uncompressed 16-bit)
			$isCompressed = !((0x01 & ord($subData[0])) >> 0);

			// bit: 2; mask: 0x04; Asian phonetic settings
			$hasAsian = (0x04) & ord($subData[0]) >> 2;

			// bit: 3; mask: 0x08; Rich-Text settings
			$hasRichText = (0x08) & ord($subData[0]) >> 3;

		// offset: 1: size: var; character array
		// this offset assumes richtext and Asian phonetic settings are off which is generally wrong
		// needs to be fixed
		$value = $this->_encodeUTF16(substr($subData, 1, $isCompressed ? $characterCount : 2 * $characterCount), $isCompressed);

		return array(
			'value' => $value,
			'size' => $isCompressed ? 1 + $characterCount : 1 + 2 * $characterCount, // the size in bytes including the option flags
		);
	}

	/**
	 * Convert UTF-8 string to string surounded by double quotes. Used for explicit string tokens in formulas.
	 * Example:  hello"world  -->  "hello""world"
	 *
	 * @param $value string UTF-8 encoded string
	 * @return string
	 */
	private function _UTF8toExcelDoubleQuoted($value)
	{
		return '"' . str_replace('"', '""', $value) . '"';
	}

	/**
	 * Reads 8 bytes and returns IEEE 754 float
	 */
	private function _createNumber($spos)
	{
		$rknumhigh = $this->_GetInt4d($this->_data, $spos + 10);
		$rknumlow = $this->_GetInt4d($this->_data, $spos + 6);
		$sign = ($rknumhigh & 0x80000000) >> 31;
		$exp = ($rknumhigh & 0x7ff00000) >> 20;
		$mantissa = (0x100000 | ($rknumhigh & 0x000fffff));
		$mantissalow1 = ($rknumlow & 0x80000000) >> 31;
		$mantissalow2 = ($rknumlow & 0x7fffffff);
		$value = $mantissa / pow( 2 , (20- ($exp - 1023)));

		if ($mantissalow1 != 0) {
			$value += 1 / pow (2 , (21 - ($exp - 1023)));
		}

		$value += $mantissalow2 / pow (2 , (52 - ($exp - 1023)));
		if ($sign) {
			$value = -1 * $value;
		}

		return	$value;
	}

	/**
	 * Same as _createNumber, but not hardcoded to read from $this->_data
	 */
	private function _extractNumber($subData)
	{
		$rknumhigh = $this->_GetInt4d($subData, 4);
		$rknumlow = $this->_GetInt4d($subData, 0);
		$sign = ($rknumhigh & 0x80000000) >> 31;
		$exp = ($rknumhigh & 0x7ff00000) >> 20;
		$mantissa = (0x100000 | ($rknumhigh & 0x000fffff));
		$mantissalow1 = ($rknumlow & 0x80000000) >> 31;
		$mantissalow2 = ($rknumlow & 0x7fffffff);
		$value = $mantissa / pow( 2 , (20- ($exp - 1023)));

		if ($mantissalow1 != 0) {
			$value += 1 / pow (2 , (21 - ($exp - 1023)));
		}

		$value += $mantissalow2 / pow (2 , (52 - ($exp - 1023)));
		if ($sign) {
			$value = -1 * $value;
		}

		return	$value;
	}

	private function _GetIEEE754($rknum)
	{
		if (($rknum & 0x02) != 0) {
			$value = $rknum >> 2;
		}
		else {
			// changes by mmp, info on IEEE754 encoding from
			// research.microsoft.com/~hollasch/cgindex/coding/ieeefloat.html
			// The RK format calls for using only the most significant 30 bits
			// of the 64 bit floating point value. The other 34 bits are assumed
			// to be 0 so we use the upper 30 bits of $rknum as follows...
			$sign = ($rknum & 0x80000000) >> 31;
			$exp = ($rknum & 0x7ff00000) >> 20;
			$mantissa = (0x100000 | ($rknum & 0x000ffffc));
			$value = $mantissa / pow( 2 , (20- ($exp - 1023)));
			if ($sign) {
				$value = -1 * $value;
			}
			//end of changes by mmp
		}
		if (($rknum & 0x01) != 0) {
			$value /= 100;
		}
		return $value;
	}

	/**
	 * Get UTF-8 string from (compressed or uncompressed) UTF-16 string
	 *
	 * @return string
	 */
	private function _encodeUTF16($string, $compressed = '')
	{
		$result = $string;
		if($compressed) {
			$string = $this->_uncompressByteString($string);
 		}
		switch ($this->_encoderFunction){
			case 'iconv' :
				$result = iconv('UTF-16LE', 'UTF-8', $string);
				break;
			case 'mb_convert_encoding' :
				$result = mb_convert_encoding($string, 'UTF-8', 'UTF-16LE');
				break;
		}
		return $result;
	}

	/**
	 * Convert UTF-16 string in compressed notation to uncompressed form. Only used for BIFF8.
	 *
	 * @return string
	 */
	private function _uncompressByteString($string)
	{
		$uncompressedString = '';
		for ($i = 0; $i < strlen($string); ++$i) {
			$uncompressedString .= $string[$i] . "\0";
		}

		return $uncompressedString;
	}

	/**
	 * Convert string to UTF-8. Only used for BIFF5.
	 *
	 * @return string
	 */
	private function _decodeCodepage($string)
	{
		$result = $string;
		if ($this->_codepage) {
			switch ($this->_encoderFunction) {
				case 'iconv' :
					$result = iconv($this->_codepage, 'UTF-8', $string);
					break;
				case 'mb_convert_encoding' :
					$result = mb_convert_encoding($string, 'UTF-8', $this->_codepage );
					break;
			}
		}
		return $result;
	}

	/**
	 * Read 16-bit unsigned integer
	 *
	 * @return int
	 */
	private function _GetInt2d($data, $pos)
	{
		return ord($data[$pos]) | (ord($data[$pos + 1]) << 8);
	}

	/**
	 * Read 32-bit signed integer
	 *
	 * @return int
	 */
	private function _GetInt4d($data, $pos)
	{
		//return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) |
		//	(ord($data[$pos + 2]) << 16) | (ord($data[$pos + 3]) << 24);

		// FIX: represent numbers correctly on 64-bit system
		// http://sourceforge.net/tracker/index.php?func=detail&aid=1487372&group_id=99160&atid=623334
		$_or_24 = ord($data[$pos + 3]);
		if ($_or_24 >= 128) {
			// negative number
			$_ord_24 = -abs((256 - $_or_24) << 24);
		} else {
			$_ord_24 = ($_or_24 & 127) << 24;
		}
		return ord($data[$pos]) | (ord($data[$pos + 1]) << 8) | (ord($data[$pos + 2]) << 16) | $_ord_24;
	}

	/**
	 * Map border style
	 * OpenOffice documentation: 2.5.11
	 *
	 * @return string
	 */
	private function _mapBorderStyle($index)
	{
		switch ($index) {
		case 0x00: return PHPExcel_Style_Border::BORDER_NONE;
		case 0x01: return PHPExcel_Style_Border::BORDER_THIN;
		case 0x02: return PHPExcel_Style_Border::BORDER_MEDIUM;
		case 0x03: return PHPExcel_Style_Border::BORDER_DASHED;
		case 0x04: return PHPExcel_Style_Border::BORDER_DOTTED;
		case 0x05: return PHPExcel_Style_Border::BORDER_THICK;
		case 0x06: return PHPExcel_Style_Border::BORDER_DOUBLE;
		case 0x07: return PHPExcel_Style_Border::BORDER_HAIR;
		case 0x08: return PHPExcel_Style_Border::BORDER_MEDIUMDASHED;
		case 0x09: return PHPExcel_Style_Border::BORDER_DASHDOT;
		case 0x0A: return PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT;
		case 0x0B: return PHPExcel_Style_Border::BORDER_DASHDOTDOT;
		case 0x0C: return PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT;
		case 0x0D: return PHPExcel_Style_Border::BORDER_SLANTDASHDOT;
		default: return false;
		}
	}

	/**
	 * Get fill pattern from index
	 * OpenOffice documentation: 2.5.12
	 *
	 * @return string
	 */
	private function _mapFillPattern($index)
	{
		switch ($index) {
		case 0x00: return PHPExcel_Style_Fill::FILL_NONE;
		case 0x01: return PHPExcel_Style_Fill::FILL_SOLID;
		case 0x02: return PHPExcel_Style_Fill::FILL_PATTERN_MEDIUMGRAY;
		case 0x03: return PHPExcel_Style_Fill::FILL_PATTERN_DARKGRAY;
		case 0x04: return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY;
		case 0x05: return PHPExcel_Style_Fill::FILL_PATTERN_DARKHORIZONTAL;
		case 0x06: return PHPExcel_Style_Fill::FILL_PATTERN_DARKVERTICAL;
		case 0x07: return PHPExcel_Style_Fill::FILL_PATTERN_DARKDOWN;
		case 0x08: return PHPExcel_Style_Fill::FILL_PATTERN_DARKUP;
		case 0x09: return PHPExcel_Style_Fill::FILL_PATTERN_DARKGRID;
		case 0x0A: return PHPExcel_Style_Fill::FILL_PATTERN_DARKTRELLIS;
		case 0x0B: return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTHORIZONTAL;
		case 0x0C: return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTVERTICAL;
		case 0x0D: return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTDOWN;
		case 0x0E: return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTUP;
		case 0x0F: return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRID;
		case 0x10: return PHPExcel_Style_Fill::FILL_PATTERN_LIGHTTRELLIS;
		case 0x11: return PHPExcel_Style_Fill::FILL_PATTERN_GRAY125;
		case 0x12: return PHPExcel_Style_Fill::FILL_PATTERN_GRAY0625;
		default: return false;
		}
	}

	/**
	 * Map error code, e.g. '#N/A'
	 *
	 * @return string
	 */
	private function _mapErrorCode($subData)
	{
		switch ($subData) {
		case 0x00: return '#NULL!';		break;
		case 0x07: return '#DIV/0!';	break;
		case 0x0F: return '#VALUE!';	break;
		case 0x17: return '#REF!';		break;
		case 0x1D: return '#NAME?';		break;
		case 0x24: return '#NUM!';		break;
		case 0x2A: return '#N/A';		break;
		default: return false;
		}
	}

	/**
	 * Map color array from BIFF8 built-in color index
	 *
	 * @return array
	 */
	private function _mapColor($subData)
	{
		switch ($subData) {
			case 0x08: return array('rgb' => '000000');
			case 0x09: return array('rgb' => 'FFFFFF');
			case 0x0A: return array('rgb' => 'FF0000');
			case 0x0B: return array('rgb' => '00FF00');
			case 0x0C: return array('rgb' => '0000FF');
			case 0x0D: return array('rgb' => 'FFFF00');
			case 0x0E: return array('rgb' => 'FF00FF');
			case 0x0F: return array('rgb' => '00FFFF');
			case 0x10: return array('rgb' => '800000');
			case 0x11: return array('rgb' => '008000');
			case 0x12: return array('rgb' => '000080');
			case 0x13: return array('rgb' => '808000');
			case 0x14: return array('rgb' => '800080');
			case 0x15: return array('rgb' => '008080');
			case 0x16: return array('rgb' => 'C0C0C0');
			case 0x17: return array('rgb' => '808080');
			case 0x18: return array('rgb' => '9999FF');
			case 0x19: return array('rgb' => '993366');
			case 0x1A: return array('rgb' => 'FFFFCC');
			case 0x1B: return array('rgb' => 'CCFFFF');
			case 0x1C: return array('rgb' => '660066');
			case 0x1D: return array('rgb' => 'FF8080');
			case 0x1E: return array('rgb' => '0066CC');
			case 0x1F: return array('rgb' => 'CCCCFF');
			case 0x20: return array('rgb' => '000080');
			case 0x21: return array('rgb' => 'FF00FF');
			case 0x22: return array('rgb' => 'FFFF00');
			case 0x23: return array('rgb' => '00FFFF');
			case 0x24: return array('rgb' => '800080');
			case 0x25: return array('rgb' => '800000');
			case 0x26: return array('rgb' => '008080');
			case 0x27: return array('rgb' => '0000FF');
			case 0x28: return array('rgb' => '00CCFF');
			case 0x29: return array('rgb' => 'CCFFFF');
			case 0x2A: return array('rgb' => 'CCFFCC');
			case 0x2B: return array('rgb' => 'FFFF99');
			case 0x2C: return array('rgb' => '99CCFF');
			case 0x2D: return array('rgb' => 'FF99CC');
			case 0x2E: return array('rgb' => 'CC99FF');
			case 0x2F: return array('rgb' => 'FFCC99');
			case 0x30: return array('rgb' => '3366FF');
			case 0x31: return array('rgb' => '33CCCC');
			case 0x32: return array('rgb' => '99CC00');
			case 0x33: return array('rgb' => 'FFCC00');
			case 0x34: return array('rgb' => 'FF9900');
			case 0x35: return array('rgb' => 'FF6600');
			case 0x36: return array('rgb' => '666699');
			case 0x37: return array('rgb' => '969696');
			case 0x38: return array('rgb' => '003366');
			case 0x39: return array('rgb' => '339966');
			case 0x3A: return array('rgb' => '003300');
			case 0x3B: return array('rgb' => '333300');
			case 0x3C: return array('rgb' => '993300');
			case 0x3D: return array('rgb' => '993366');
			case 0x3E: return array('rgb' => '333399');
			case 0x3F: return array('rgb' => '333333');
			default: return false;
		}
	}

	/**
	 * Dump a byte sequence, only used for debugging
	 */
	private function _dump($data)
	{
		for ($i = 0; $i < strlen($data); ++$i) {
			echo sprintf('%02X', ord($data[$i])) . ' ';
		}
	}

}
