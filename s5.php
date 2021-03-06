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

	// we need to split the page into separate slides
	$slides = explode( '<h1>', $gContent->getField('parsed_data') );
	//vd($slides);
	// manually set the first slide to page title and description
	$s5  = '<li class="slide"><h1>'.$gContent->getTitle().'</h1>';
	$s5 .= '<h3>'.$gContent->getField('summary').'</h3></li>';
	foreach( $slides as $slide ) {
		if( !empty( $slide ) ) {
			$s5 .= '<li class="slide">';
			if( preg_match( '/<\/h1[^>]*>/i', $slide ) ) {
				$s5 .= '<h1>';
			}
			$s5 .= $slide;
			$s5 .= '</li>';
		}
	}
	// manually set the last slide with a link back to the wiki page
	$s5 .= '<li class="slide"><h1 style="margin:30% 0 10% 0;">'.tra( 'The End' ).'</h1>';
	$s5 .= '<p><a href="'.WIKI_PKG_URL.'index.php?page_id='.$gContent->getField('page_id').'">'.tra( 'back to the wiki page' ).'</a></p></li>';
	$gBitSmarty->assign( 's5', $s5 );
	$gBitSmarty->display( 'bitpackage:wiki/s5.tpl' );
	die;
?>
