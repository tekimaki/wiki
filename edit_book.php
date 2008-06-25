<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_wiki/edit_book.php,v 1.13 2008/06/25 22:21:29 spiderr Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: edit_book.php,v 1.13 2008/06/25 22:21:29 spiderr Exp $
 * @package wiki
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

$gBitSystem->verifyPermission( 'p_wiki_edit_book' );

if( isset( $_COOKIE['book_section'] ) && $_COOKIE['book_section'] == 'o' ) {
	$book_section = 'block';
} else {
	$book_section = 'none';
}
$gBitSmarty->assign( 'book_section',$book_section );

include_once( WIKI_PKG_PATH.'lookup_page_inc.php');
include_once( LIBERTY_PKG_PATH.'LibertyStructure.php');
include_once( WIKI_PKG_PATH.'BitBook.php');

global $gStructure;

if( isset($_REQUEST["createstructure"]) ) {
	if ((empty($_REQUEST['name']))) {
		$gBitSmarty->assign('msg', tra("You must specify a name."));
		$gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'edit' ));
		die;
	}

	//try to add a new structure
	$bookPage = new BitBook();
	$pageId = $bookPage->findByPageName( $_REQUEST['name'] );
	if( $pageId ) {
		$bookPage->mPageId = $pageId;
		$bookPage->load();
	} else {
		$params['title'] = $_REQUEST['name'];
		$params['edit'] = '{toc}';
		$bookPage->store( $params );
	}

	if( $bookPage->isValid() ) {
		$gStructure = new LibertyStructure();
		// alias => '' is a temporary setting until alias stuff has been removed
		$structureHash = array( 'content_id' => $bookPage->mContentId, 'alias' => '' );
		$structure_id = $gStructure->storeNode( $structureHash );
		//Cannot create a structure if a structure already exists
		if (!isset($structure_id)) {
			$gBitSmarty->assign('msg', $_REQUEST['name'] . " " . tra("page not added (Exists)"));
			$gBitSystem->display( 'error.tpl' , NULL, array( 'display_mode' => 'edit' ));
			die;
		}

		$chapters = explode("\n", $_REQUEST["chapters"]);
		foreach ($chapters as $chapter) {
			$chapterName = trim($chapter);
			if( !empty( $chapterName ) ) {
				unset( $params );
				unset( $nodeHash );
				$nodeHash['parent_id'] = $structure_id;
				$nodeHash['root_structure_id'] = $structure_id;
				$nodeHash['level'] = 1;
				//try to add a new structure
				$nodePage = new BitPage();
				$pageId = $nodePage->findByPageName( $chapterName );
				if( $pageId ) {
					$nodePage->mPageId = $pageId;
					$nodePage->load();
				} else {
					$params['title'] = trim($chapterName);
					$params['edit'] = '';
					if( !$nodePage->store( $params ) ) {
						$gBitSystem->fatalError( "There was an error storing the page: ".vc( $bookPage->mErrors ));
					}
				}
				$nodeHash['content_id'] = $nodePage->mContentId;
				$nodeHash['after_ref_id'] = $gStructure->storeNode( $nodeHash );
			}
		}
		header( "location: ".WIKI_PKG_URL."edit_book.php?structure_id=".$structure_id );
	}
} elseif( @BitBase::verifyId( $_REQUEST["structure_id"] ) ) {
	// Get all wiki pages for the select box
	$_REQUEST['content_type_guid'] = !isset( $_REQUEST['content_type_guid'] ) ? 'bitpage' : $_REQUEST['content_type_guid'];
	// verify the book permission on structure load
	$verifyStructurePermission = 'p_wiki_admin_book';

	// we need to load some javascript and css for this page
	$gBitThemes->loadCss( UTIL_PKG_PATH.'javascript/libs/mygosu/DynamicTree.css' );
	if( $gSniffer->_browser_info['browser'] == 'ie' && $gSniffer->_browser_info['maj_ver'] == 5 ) {
		$gBitThemes->loadJavascript( UTIL_PKG_PATH.'javascript/libs/mygosu/ie5.js' );
	}
	$gBitThemes->loadJavascript( UTIL_PKG_PATH.'javascript/libs/mygosu/DynamicTreeBuilder.js' );

	// set the correct display template
	$mid = 'bitpackage:wiki/edit_book.tpl';
	include_once( LIBERTY_PKG_PATH.'edit_structure_inc.php');
} else {
	$gBitSystem->setBrowserTitle( 'Create Wiki Book' );
	$mid = 'bitpackage:wiki/create_book.tpl';
}
$gBitSystem->setBrowserTitle( !empty($gStructure) ? 'Edit Wiki Book:'.$gStructure->mInfo["title"] : NULL );
// Display the template
$gBitSystem->display( $mid , NULL, array( 'display_mode' => 'edit' ));
?>
