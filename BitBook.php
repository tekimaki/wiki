<?php
/**
 * BitBook class
 *
 * @author   spider <spider@steelsun.com>
 * @version  $Revision$
 * @package  wiki
 */
// +----------------------------------------------------------------------+
// | Copyright (c) 2004, bitweaver.org
// +----------------------------------------------------------------------+
// | All Rights Reserved. See below for details and a complete list of authors.
// | Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
// |
// | For comments, please use phpdocu.sourceforge.net documentation standards!!!
// | -> see http://phpdocu.sourceforge.net/
// +----------------------------------------------------------------------+
// | Authors: spider <spider@steelsun.com>
// +----------------------------------------------------------------------+
//
// $Id$


/**
 * required setup
 */
require_once( WIKI_PKG_PATH.'BitPage.php' );
require_once( LIBERTYSTRUCTURE_PKG_PATH.'LibertyStructure.php' );

define('BITBOOK_CONTENT_TYPE_GUID', 'bitbook' );

/**
 * BitBook class
 *
 * @abstract
 * @author   spider <spider@steelsun.com>
 * @version  $Revision$
 * @package  wiki
 */
class BitBook extends BitPage {
	var $mPageId;
	var $mPageName;
	function BitBook( $pPageId=NULL, $pContentId=NULL ) {
		BitPage::BitPage( $pPageId, $pContentId );
		$this->registerContentType( BITBOOK_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITBOOK_CONTENT_TYPE_GUID,
			'content_name' => 'Wiki Book',
			'handler_class' => 'BitBook',
			'handler_package' => 'wiki',
			'handler_file' => 'BitBook.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		$this->mContentTypeGuid = BITBOOK_CONTENT_TYPE_GUID;

		// Permission setup
		$this->mViewContentPerm  = 'p_wiki_view_page';
		$this->mCreateContentPerm  = 'p_wiki_create_book';
		$this->mUpdateContentPerm  = 'p_wiki_update_book';
		$this->mAdminContentPerm = 'p_wiki_admin_book';
	}

	function getList( &$pListHash ) {
		$struct = new LibertyStructure();
		return $struct->getList( $pListHash );
	}

	function getStats() {
		return FALSE;
	}
}
?>
