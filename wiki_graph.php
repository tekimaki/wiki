<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_wiki/wiki_graph.php,v 1.8 2010/02/08 21:27:27 wjames5 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id: wiki_graph.php,v 1.8 2010/02/08 21:27:27 wjames5 Exp $
 * @package wiki
 * @subpackage functions
 */

/**
 * required setup
 */
include_once( '../kernel/setup_inc.php' );
include_once( WIKI_PKG_PATH.'BitPage.php');
include_once( WIKI_PKG_PATH.'lookup_page_inc.php');
include_once( 'Image/GraphViz.php' );
$graph = new Image_GraphViz();

$params = array(
	'graph' => $gBitThemes->getGraphvizGraphAttributes( $_REQUEST ),
	'node'  => $gBitThemes->getGraphvizNodeAttributes( $_REQUEST ),
	'edge'  => $gBitThemes->getGraphvizEdgeAttributes( $_REQUEST ),
);

$linkStructure = $gContent->getLinkStructure( $gContent->mPageName, !empty( $_REQUEST['level'] ) ? $_REQUEST['level'] : 0 );
$gContent->linkStructureGraph( $linkStructure, $params, $graph );
$graph->image( 'png' );
?>
