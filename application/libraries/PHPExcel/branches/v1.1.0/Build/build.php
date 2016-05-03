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
 * @copyright  Copyright (c) 2006 - 2007 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/gpl.txt	GPL
 */

/**
 * This file creates a build of PHPExcel
 */

// Starting build
echo date('H:i:s') . " Starting build...\n";

// Specify paths and files to include
$aFilesToInclude = array('../changelog.txt', '../install.txt', '../license.txt');
$aPathsToInclude = array('../Classes', '../Tests', '../Documentation');

// Resulting file
$strResultingFile = 'LatestBuild.zip';

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
	$objZip->addFile($strFile, cleanFileName($strFile));
}

// Add paths to include
foreach ($aPathsToInclude as $strPath) {
	addPathToZIP($strPath, $objZip);
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
 */
function addPathToZIP($strPath, $objZip) {
	echo date('H:i:s') . " Adding path $strPath...\n";
	
	$currentDir = opendir($strPath);
	while ($strFile = readdir($currentDir)) {
		if ($strFile != '.' && $strFile != '..') {
			if (is_file($strPath . '/' . $strFile)) {
				$objZip->addFile($strPath . '/' . $strFile, cleanFileName($strPath . '/' . $strFile));
			} else if (is_dir($strPath . '/' . $strFile)) {
				if (!eregi('.svn', $strFile)) {
					addPathToZIP( ($strPath . '/' . $strFile), $objZip );
				}
			}
		}
	}
}

/**
 * Cleanup a filename
 *
 * @param 	string	$strFile	Filename
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