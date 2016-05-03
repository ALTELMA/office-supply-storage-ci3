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
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/**
 * This file creates a build of PHPExcel
 */

// Build parameters
$sVersion	= "";
$sDate		= "";

// Read build parameters from STDIN
$stdin = fopen("php://stdin", 'r');
echo "PHPExcel build script\n";
echo "---------------------\n";
echo "Enter the version number you want to add to the build:\t";
$sVersion	= rtrim(fread($stdin, 1024));

echo "Enter the date number you want to add to the build:\t";
$sDate 		= rtrim(fread($stdin, 1024));

echo "\n\n\n";
fclose($stdin);

// Starting build
echo date('H:i:s') . " Starting build...\n";

// Specify paths and files to include
$aFilesToInclude = array('../changelog.txt', '../install.txt', '../license.txt');
$aPathsToInclude = array('../Classes', '../Tests', '../Documentation');

// Resulting file
$strResultingFile = $sVersion . '.zip';

// Create new ZIP file and open it for writing
echo date('H:i:s') . " Creating ZIP archive...\n";
$objZip = new ZipArchive();
			
// Try opening the ZIP file
if ($objZip->open($strResultingFile, ZIPARCHIVE::OVERWRITE) !== true) {
	throw new Exeption("Could not open " . $strResultingFile . " for writing!");
}

// Add files to include
foreach ($aFilesToInclude as $strFile) {
	echo date('H:i:s') . " Adding file $strFile\n";
	AddFile($strFile, $objZip, $sVersion, $sDate);
}

// Add paths to include
foreach ($aPathsToInclude as $strPath) {
	addPathToZIP($strPath, $objZip, $sVersion, $sDate);
}

// Set archive comment...
echo date('H:i:s') . " Set archive comment...\n";
$objZip->setArchiveComment('PHPExcel - http://www.codeplex.com/PHPExcel');

// Close file
echo date('H:i:s') . " Saving ZIP archive...\n";
$objZip->close();

// Finished build
echo date('H:i:s') . " Finished build!\n";



/**
 * Add a specific path's files and folders to a ZIP object
 *
 * @param string 		$strPath	Path to add
 * @param ZipArchive 	$objZip		ZipArchive object
 * @param string		$strVersion		Version string
 * @param string		$strDate		Date string
 */
function addPathToZIP($strPath, $objZip, $strVersion, $strDate) {
	echo date('H:i:s') . " Adding path $strPath...\n";
	
	$currentDir = opendir($strPath);
	while ($strFile = readdir($currentDir)) {
		if ($strFile != '.' && $strFile != '..') {
			if (is_file($strPath . '/' . $strFile)) {
				AddFile($strPath . '/' . $strFile, $objZip, $strVersion, $strDate);
			} else if (is_dir($strPath . '/' . $strFile)) {
				if (!eregi('.svn', $strFile)) {
					addPathToZIP( ($strPath . '/' . $strFile), $objZip, $strVersion, $strDate );
				}
			}
		}
	}
}

/**
 * Add a specific file to ZIP
 *
 * @param string 		$strFile		File to add
 * @param ZipArchive 	$objZip			ZipArchive object
 * @param string		$strVersion		Version string
 * @param string		$strDate		Date string
 */
function AddFile($strFile, $objZip, $strVersion, $strDate) {
	$fileContents = file_get_contents($strFile);
	$fileContents = str_replace('##VERSION##', $strVersion, $fileContents);
	$fileContents = str_replace('##DATE##', $strDate, $fileContents);
	
	//$objZip->addFile($strFile, cleanFileName($strFile));
	$objZip->addFromString( cleanFileName($strFile), $fileContents );
}

/**
 * Cleanup a filename
 *
 * @param 	string	$strFile			Filename
 * @return	string	Filename
 */
function cleanFileName($strFile) {
	 $strFile = str_replace('../', '', $strFile);
	 $strFile = str_replace('WINDOWS', '', $strFile);
	 
	 while (eregi('//', $strFile)) {
	 	$strFile = str_replace('//', '/', $strFile);
	 }
	 
	 return $strFile;
}