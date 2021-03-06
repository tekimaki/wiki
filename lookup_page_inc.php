<?php
/**
 * $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id$
 * @package wiki
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( WIKI_PKG_PATH.'BitBook.php');
require_once( LIBERTYSTRUCTURE_PKG_PATH.'LibertyStructure.php' );

global $gContent;
include_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// this is needed when the center module is applied to avoid abusing $_REQUEST
if( empty( $lookupHash )) {
	$lookupHash = &$_REQUEST;
}

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || strtolower( get_class( $gContent ) ) != 'bitpage' ) {
	$gContent = new BitPage( @BitBase::verifyId( $lookupHash['page_id'] ) ? $lookupHash['page_id'] : NULL, @BitBase::verifyId( $lookupHash['content_id'] ) ? $lookupHash['content_id'] : NULL );

	$loadPage = (!empty( $lookupHash['page'] ) ? $lookupHash['page'] : NULL);
	if( empty( $gContent->mPageId ) && empty( $gContent->mContentId )  ) {
		//handle legacy forms that use plain 'page' form variable name
		
		//if page had some special enities they were changed to HTML for for security reasons.
		//now we deal only with string so convert it back - so we can support this case:
		//You&Me --(detoxify in kernel)--> You&amp;Me --(now)--> You&Me
		//we could do htmlspecialchars_decode but it allows <> marks here, so we just transform &amp; to & - it's not so scary. 
		$loadPage = str_replace("&amp;", "&", $loadPage );

		if( $loadPage && $existsInfo = $gContent->pageExists( $loadPage ) ) {
			if (count($existsInfo)) {
				if (count($existsInfo) > 1) {
					// Display page so user can select which wiki page they want (there are multiple that share this name)
					$gBitSmarty->assign( 'choose', $lookupHash['page'] );
					$gBitSmarty->assign('dupePages', $existsInfo);
					$gBitSystem->display('bitpackage:wiki/page_select.tpl', NULL, array( 'display_mode' => 'display' ));
					die;
				} else {
					$gContent->mPageId = $existsInfo[0]['page_id'];
					$gContent->mContentId = $existsInfo[0]['content_id'];
				}
			}
		} elseif( $loadPage ) {
			$gBitSmarty->assign('page', $loadPage);//to have the create page link in the error
		}
	}

	$parse = ( !isset( $lookupHash['parse'] ) or $lookupHash['parse'] ) ? true : false;
	if( $gContent->load( $parse ) && $loadPage ) {
		$gContent->mInfo['title'] = $loadPage;
	}
}

// we weren't passed a structure, but maybe this page belongs to one. let's check...
if( empty( $gStructure ) ) {
	//Get the structures this page is a member of
	if( !empty($lookupHash['structure']) ) {
		$structure=$lookupHash['structure'];
	} else {
		$structure='';
	}
	$structs = LibertyStructure::getStructures( $gContent );
	if (count($structs)==1) {
		$gStructure = new LibertyStructure( $structs[0]['structure_id'] );
		if( $gStructure->load() ) {
			$gStructure->loadNavigation();
			$gStructure->loadPath();
			$gBitSmarty->assign( 'structureInfo', $gStructure->mInfo );
		}
	} else {
		$gBitSmarty->assign('showstructs', $structs);
	}
}

$gBitSmarty->clear_assign( 'gContent' );
$gBitSmarty->assign_by_ref( 'gContent', $gContent );
?>
