<?php
/**
 * $Header$
 *
 * Copyright( c ) 2004 bitweaver.org
 * Copyright( c ) 2003 tikwiki.org
 * Copyright( c ) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
require_once( '../kernel/setup_inc.php' );
include_once( WIKI_PKG_PATH.'BitBook.php' );
require_once( LIBERTYSTRUCTURE_PKG_PATH.'LibertyStructure.php' );

$gBitSystem->verifyPackage( 'wiki' );

// bypass lookup_content_inc.php as we can't prevent it parsing faulty pages
unset($_REQUEST['content_id']);
// Disable parsing data if not asking to preview page
$_REQUEST["parse"] = false;
include( WIKI_PKG_PATH.'lookup_page_inc.php' );

$wiki_sandbox = FALSE;
if( ( !empty( $_REQUEST['page'] ) && $_REQUEST['page'] == 'SandBox' ) || ( !empty( $_REQUEST['title'] ) && $_REQUEST['title'] == 'SandBox' ) ) {
	$gContent->mInfo['title'] = 'SandBox';
	$wiki_sandbox = TRUE;
}

if( $wiki_sandbox && !$gBitSystem->isFeatureActive( 'wiki_sandbox' ) ) {
	$gBitSystem->fatalError( tra( "The SandBox is disabled" ));
} elseif( !$wiki_sandbox ){
	if( $gContent->isValid() ) {
		$gContent->verifyUpdatePermission();
	} else {
		$gContent->verifyCreatePermission();
	}
}

//make comment count for this page available for templates
if( $gBitSystem->isFeatureActive( 'wiki_comments' ) && !empty( $_REQUEST['page_id'] ) ) {
	require_once( LIBERTY_PKG_PATH.'LibertyComment.php' );
	$gComment = new LibertyComment();
	$numComments = $gComment->getNumComments($gContent->mContentId);
	$gBitSmarty->assign('comments_count', $numComments);
}

#edit preview needs this
if( !isset( $_REQUEST['title'] ) && isset( $gContent->mInfo['title'] ) ) {
	$_REQUEST['title'] = $gContent->mInfo['title'];
}

if( $gContent->isLocked() ) {
	$gBitSystem->fatalError( 'Cannot edit page because it is locked' );
}


$gContent->invokeServices( 'content_edit_function' );

if( !empty( $gContent->mInfo ) ) {
	$formInfo = $gContent->mInfo;
	$data_to_edit = !empty( $gContent->mInfo['data'] ) ? $gContent->mInfo['data'] : '';
	if (!empty($_REQUEST['section'])) {
		$section = $_REQUEST['section'];
		$data_to_edit = extract_section($data_to_edit,$section);
		$formInfo['data'] = $data_to_edit;
		$formInfo['edit_section'] = 1;
		$formInfo['section'] = $_REQUEST['section'];
	}

	$formInfo['edit'] = $data_to_edit;
	$formInfo['edit_comment'] = '';
}

$gBitSmarty->assign( 'footnote', '' );
$gBitSmarty->assign( 'has_footnote', 'n' );
if( $gBitSystem->isFeatureActive( 'wiki_footnotes' ) ) {
	if( $gBitUser->mUserId ) {
		$footnote = $gContent->getFootnote( $gBitUser->mUserId );
		$gBitSmarty->assign( 'footnote', $footnote );
		if( $footnote )
			$gBitSmarty->assign( 'has_footnote', 'y' );
		$gBitSmarty->assign( 'parsed_footnote', $gContent->parseData( $footnote ) );
		if( isset( $_REQUEST['footnote'] ) ) {

			$gBitSmarty->assign( 'parsed_footnote', $gContent->parseData( $_REQUEST['footnote'] ) );
			$gBitSmarty->assign( 'footnote', $_REQUEST['footnote'] );
			$gBitSmarty->assign( 'has_footnote', 'y' );
			if( empty( $_REQUEST['footnote'] ) ) {
				$gContent->expungeFootnote( $gBitUser->mUserId );
			} else {
				$gContent->storeFootnote( $gBitUser->mUserId, $_REQUEST['footnote'] );
			}
		}
	}
}
if( isset( $_REQUEST["edit"] ) ) {
	$formInfo['edit'] = $_REQUEST["edit"];
}
if(isset($_REQUEST["section"])) {
	$formInfo['section'] = $_REQUEST["section"];
	$formInfo['edit_section'] = 1;
}
if( isset( $_REQUEST['title'] ) ) {
	$formInfo['title'] = $_REQUEST['title'];
} elseif( isset( $_REQUEST['page'] ) ) {
	$formInfo['title'] = $_REQUEST['page'];
}
if( isset( $_REQUEST["description"] ) ) {
	$formInfo['description'] = $_REQUEST["description"];
}
if( isset( $_REQUEST["edit_comment"] ) ) {
	$formInfo['edit_comment'] = $_REQUEST["edit_comment"];
} else {
	$formInfo['edit_comment'] = '';
}

$cat_obj_type = BITPAGE_CONTENT_TYPE_GUID;

if( $gBitSystem->isFeatureActive( 'wiki_copyrights' ) ) {
	if( isset( $_REQUEST['copyrightTitle'] ) ) {
		$gBitSmarty->assign( 'copyrightTitle', $_REQUEST["copyrightTitle"] );
	}
	if( isset( $_REQUEST['copyrightYear'] ) ) {
		$gBitSmarty->assign( 'copyrightYear', $_REQUEST["copyrightYear"] );
	}
	if( isset( $_REQUEST['copyrightAuthors'] ) ) {
		$gBitSmarty->assign( 'copyrightAuthors', $_REQUEST["copyrightAuthors"] );
	}
}

// Pro
// Check if the page has changed
if( isset( $_REQUEST["fCancel"] ) ) {
	if( @BitBase::verifyId( $gContent->mContentId ) ) {
		header( "Location: ".$gContent->getDisplayUrl() );
	} else {
		header( "Location: ".WIKI_PKG_URL );
	}
	die;
} elseif( isset( $_REQUEST["fSavePage"] ) ) {

	// Check if all Request values are delivered, and if not, set them
	// to avoid error messages. This can happen if some features are
	// disabled
	// add permisions here otherwise return error!
	if( $gBitSystem->isFeatureActive( 'wiki_copyrights' )
		&& isset( $_REQUEST['copyrightAuthors'] )
		&& !empty( $_REQUEST['copyrightYear'] )
		&& !empty( $_REQUEST['copyrightTitle'] )
	) {
		require_once( WIKI_PKG_PATH.'copyrights_lib.php' );
		$copyrightYear = $_REQUEST['copyrightYear'];
		$copyrightTitle = $_REQUEST['copyrightTitle'];
		$copyrightAuthors = $_REQUEST['copyrightAuthors'];
		$copyrightslib->add_copyright( $gContent->mPageId, $copyrightTitle, $copyrightYear, $copyrightAuthors, $gBitUser->mUserId );
	}
	// Parse $edit and eliminate image references to external URIs( make them internal )
	if( $gBitSystem->isPackageActive( 'imagegals' ) ) {
		include_once( IMAGEGALS_PKG_PATH.'imagegal_lib.php' );
		$edit = $imagegallib->capture_images( $edit );
	}

	if( $gContent->mPageId )
	{	if( isset( $_REQUEST['isminor'] ) && $_REQUEST['isminor']=='on' ) {
			$_REQUEST['minor']=true;
		} else {
			$_REQUEST['minor']=false;
//			$links = $gContent->get_links( $edit );
//			$wikilib->cache_links( $links );
//			$gContent->storeLinks( $links );
		}
	} else {
//		$links = $gContent->get_links( $_REQUEST["edit"] );
//		$notcachedlinks = $gContent->get_links_nocache( $_REQUEST["edit"] );
//		$cachedlinks = array_diff( $links, $notcachedlinks );
//		$gContent->cache_links( $cachedlinks );
//		$gContent->storeLinks( $cachedlinks );
	}

	$data_to_parse = $formInfo['edit'];
	if (!empty($formInfo['section']) && !empty($gContent->mInfo['data']) ) {
		$full_page_data = $gContent->mInfo['data'];
		$data_to_parse = replace_section($full_page_data,$formInfo['section'],$formInfo['edit']);
		$_REQUEST["edit"] = $data_to_parse;
	}

	if( $gContent->store( $_REQUEST ) ) {
		if( $gBitSystem->isFeatureActive( 'wiki_watch_author' ) ) {
			$gBitUser->storeWatch( "wiki_page_changed", $gContent->mPageId, $gContent->mContentTypeGuid, $_REQUEST['title'], $gContent->getDisplayUrl() );
		}

		header( "Location: ".$gContent->getDisplayUrl() );
		die;

	} else {
		$formInfo = $_REQUEST;
		$formInfo['data'] = &$_REQUEST['edit'];
	}
} elseif( !empty( $_REQUEST['edit'] ) ) {
	// perhaps we have a javascript non-saving form submit
	$formInfo = $_REQUEST;
	$formInfo['data'] = &$_REQUEST['edit'];
}

if( isset( $_REQUEST['format_guid'] ) && !isset( $gContent->mInfo['format_guid'] ) ) {
	$formInfo['format_guid'] = $gContent->mInfo['format_guid'] = $_REQUEST['format_guid'];
}

if( isset( $_REQUEST["preview"] ) ) {
	$gBitSmarty->assign( 'preview',1 );
	$gBitSmarty->assign( 'title',!empty( $_REQUEST["title"] ) ? $_REQUEST["title"]:$gContent->mPageName );

	if (!empty($formInfo['section'])) {
		$formInfo['edit_section'] = 1;
	}

	$data_to_parse = $formInfo['edit'];
	if( !empty( $formInfo['section'] ) && !empty( $gContent->mInfo['data'] )) {
		$full_page_data = $gContent->mInfo['data'];
	}


	$formInfo['parsed_data'] = $gContent->parseData(
		$data_to_parse,
		( !empty( $_REQUEST['format_guid'] ) ? $_REQUEST['format_guid'] : ( isset( $gContent->mInfo['format_guid'] ) ? $gContent->mInfo['format_guid'] : 'tikiwiki' ))
	);
	$gContent->invokeServices( 'content_preview_function' );
}

if( $gContent->isValid() && LbertyStructure::contentIsInStructure( $gContent, $gContent->mContentId ) ) {
	$gBitSmarty->assign( 'showstructs', LibertyStructure::getStructures( $gContent ) );
}

// Flag for 'page bar' that currently 'Edit' mode active
// so no need to show comments & attachments, but need
// to show 'wiki quick help'
$gBitSmarty->assign( 'edit_page', 'y' );

// formInfo might be set due to a error on submit
if( empty( $formInfo ) ) {
	$formInfo = &$gContent->mInfo;
}

// make original page title available for template
$formInfo['original_title'] =( !empty( $gContent->mInfo['title'] ) ) ? $gContent->mInfo['title']  : "" ;

$gBitSmarty->assign_by_ref( 'pageInfo', $formInfo );
$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );

$gBitSystem->display( 'bitpackage:wiki/edit_page.tpl', 'Edit: '.$gContent->getTitle() , array( 'display_mode' => 'edit' ));








//******************* WIKI Edit Functions

function htmldecode( $string ) {
	$string = strtr( $string, array_flip( get_html_translation_table( HTML_ENTITIES ) ) );
	$string = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $string );
	return $string;
}
function parse_output( &$obj, &$parts,$i ) {
	if( !empty( $obj->parts ) ) {
		for( $i=0; $i<count( $obj->parts ); $i++ ) {
			parse_output( $obj->parts[$i], $parts,$i );
		}
	} else {
		$ctype = $obj->ctype_primary.'/'.$obj->ctype_secondary;
		switch( $ctype ) {
			case 'application/x-tikiwiki':
				$aux["body"] = $obj->body;
				$ccc=$obj->headers["content-type"];
				$items = split( ';',$ccc );
				foreach( $items as $item ) {
					$portions = split( '=',$item );
					if( isset( $portions[0] ) &&isset( $portions[1] ) ) {
						$aux[trim( $portions[0] )]=trim( $portions[1] );
					}
				}
				$parts[]=$aux;
		}
	}
}

function  extract_section($data,$section) {
	global $gContent, $gBitSystem;
	if( $gContent->mInfo['format_guid'] == PLUGIN_GUID_TIKIWIKI ) {
		$section_data = preg_split( "/\n(".( str_repeat( "!", $gBitSystem->getConfig( "wiki_section_edit" ) ) )."[^!])/", "\n$data", -1, PREG_SPLIT_DELIM_CAPTURE );
		$a = 1 + ($section - 1) * 2;
		$b = $a + 1;
		return $section_data[$a] . $section_data[$b];
	}
}

function  replace_section($data,$section,$new_section_data) {
	global $gContent, $gBitSystem;
	if( $gContent->mInfo['format_guid'] == PLUGIN_GUID_TIKIWIKI ) {
		$section_data = preg_split("/(\n".( str_repeat( "!", $gBitSystem->getConfig( "wiki_section_edit" ) ) )."[^!])/", "\n$data" ,-1,PREG_SPLIT_DELIM_CAPTURE);
		$a = 1 + ($section - 1) * 2;
		$b = $a + 1;
		$section_data[$a] = "\n";
		$section_data[$b] = $new_section_data;
		return substr(implode('',$section_data),1);
	}
}

function compare_import_versions( $a1, $a2 ) {
	return $a1["version"] - $a2["version"];
}

/**
 * \brief Parsed HTML tree walker( used by HTML sucker )
 *
 * This is initial implementation( stupid... w/o any intellegence( almost : ) )
 * It is rapidly designed version... just for test: 'can this feature be useful'.
 * Later it should be replaced by well designed one :) don't bash me now : )
 *
 * \param &$c array -- parsed HTML
 * \param &$src string -- output string
 * \param &$p array -- ['stack'] = closing strings stack,
					   ['listack'] = stack of list types currently opened
					   ['first_td'] = flag: 'is <tr> was just before this <td>'
 */
function walk_and_parse( &$c, &$src, &$p ) {
	for( $i=0; $i <= $c["contentpos"]; $i++ ) {
		// If content type 'text' output it to destination...
		if( $c[$i]["type"] == "text" ) {
			$src .= $c[$i]["data"];
		} elseif( $c[$i]["type"] == "tag" ) {
			if( $c[$i]["data"]["type"] == "open" ) {
				// Open tag type
				switch( $c[$i]["data"]["name"] ) {
					case "br": $src .= "\n"; break;
					case "title": $src .= "\n!"; $p['stack'][] = array( 'tag' => 'title', 'string' => "\n" ); break;
					case "p": $src .= "\n"; $p['stack'][] = array( 'tag' => 'p', 'string' => "\n" ); break;
					case "b": $src .= '__'; $p['stack'][] = array( 'tag' => 'b', 'string' => '__' ); break;
					case "i": $src .= "''"; $p['stack'][] = array( 'tag' => 'i', 'string' => "''" ); break;
					case "u": $src .= "=="; $p['stack'][] = array( 'tag' => 'u', 'string' => "==" ); break;
					case "center": $src .= '::'; $p['stack'][] = array( 'tag' => 'center', 'string' => '::' ); break;
					case "code": $src .= '-+';  $p['stack'][] = array( 'tag' => 'code', 'string' => '+-' ); break;
					// headers detection looks like real suxx code...
					// but possible it run faster :) I don't know where is profiler in PHP...
					case "h1": $src .= "\n!"; $p['stack'][] = array( 'tag' => 'h1', 'string' => "\n" ); break;
					case "h2": $src .= "\n!!"; $p['stack'][] = array( 'tag' => 'h2', 'string' => "\n" ); break;
					case "h3": $src .= "\n!!!"; $p['stack'][] = array( 'tag' => 'h3', 'string' => "\n" ); break;
					case "h4": $src .= "\n!!!!"; $p['stack'][] = array( 'tag' => 'h4', 'string' => "\n" ); break;
					case "h5": $src .= "\n!!!!!"; $p['stack'][] = array( 'tag' => 'h5', 'string' => "\n" ); break;
					case "h6": $src .= "\n!!!!!!"; $p['stack'][] = array( 'tag' => 'h6', 'string' => "\n" ); break;
					case "pre": $src .= '~pp~'; $p['stack'][] = array( 'tag' => 'pre', 'string' => '~/pp~' ); break;
					// Table parser
					case "table": $src .= '||'; $p['stack'][] = array( 'tag' => 'table', 'string' => '||' ); break;
					case "tr": $p['first_td'] = true; break;
					case "td": $src .= $p['first_td'] ? '' : '|'; $p['first_td'] = false; break;
					// Lists parser
					case "ul": $p['listack'][] = '*'; break;
					case "ol": $p['listack'][] = '#'; break;
					case "li":
						// Generate wiki list item according to current list depth.
						//( ensure '*/#' starts from begining of line )
						for( $l = ''; strlen( $l ) < count( $p['listack'] ); $l .= end( $p['listack'] ) );
						$src .= "\n$l ";
						break;
					case "font":
						// If color attribute present in <font> tag
						if( isset( $c[$i]["pars"]["color"]["value"] ) ) {
							$src .= '~~'.$c[$i]["pars"]["color"]["value"].':';
							$p['stack'][] = array( 'tag' => 'font', 'string' => '~~' );
						}
						break;
					case "img":
						// If src attribute present in <img> tag
						if( isset( $c[$i]["pars"]["src"]["value"] ) ) {
							// Note what it produce( img ) not {img}! Will fix this below...
							$src .= '( img src='.$c[$i]["pars"]["src"]["value"].' )';
						}
						break;
					case "a":
						// If href attribute present in <a> tag
						if( isset( $c[$i]["pars"]["href"]["value"] ) ) {
							$src .= '['.$c[$i]["pars"]["href"]["value"].'|';
							$p['stack'][] = array( 'tag' => 'a', 'string' => ']' );
						}
						break;
				}
			} else {
				// This is close tag type. Is that smth we r waiting for?
				switch( $c[$i]["data"]["name"] ) {
				case "ul":
					if( end( $p['listack'] ) == '*' ) array_pop( $p['listack'] );
					break;
				case "ol":
					if( end( $p['listack'] ) == '#' ) array_pop( $p['listack'] );
					break;
				default:
					$e = end( $p['stack'] );
					if( $c[$i]["data"]["name"] == $e['tag'] ) {
						$src .= $e['string'];
						array_pop( $p['stack'] );
					}
					break;
				}
			}
		}
		// Recursive call on tags with content...
		if( isset( $c[$i]["content"] ) ) {
//			if( substr( $src, -1 )!= " " )$src .= " ";
			walk_and_parse( $c[$i]["content"], $src, $p );
		}
	}
}
if( isset( $_REQUEST["suck_url"] ) ) {
	if( $wiki_sandbox && !$gBitSystem->isFeatureActive( 'wiki_url_import' ) ) {
		$gBitSystem->fatalError( tra( "Importing remote URLs is disabled" ));
	}
	// Suck another page and append to the end of current
	require_once( UTIL_PKG_PATH.'htmlparser/html_parser_inc.php' );
	$suck_url = isset( $_REQUEST["suck_url"] ) ? $_REQUEST["suck_url"] : '';
	$parsehtml = isset( $_REQUEST["parsehtml"] ) ? ( $_REQUEST["parsehtml"] == 'on' ? 'y' : 'n' ): 'n';
	if( isset( $_REQUEST['do_suck'] ) && strlen( $suck_url ) > 0 ) {
		// \note by zaufi
		//   This is ugly implementation of wiki HTML import.
		//   I think it should be plugable import/export converters with ability
		//   to choose from edit form what converter to use for operation.
		//   In case of import converter, it can try to guess what source
		//   file is( using mime type from remote server response ).
		//   Of couse converters may have itsown configuration panel what should be
		//   pluged into wiki page edit form too...( like HTML importer may have
		//   flags 'strip HTML tags' and 'try to convert HTML to wiki' : )
		//   At least one export filter for wiki already coded : ) -- PDF exporter...
		$parsed_url = parse_url($suck_url);
		//Disallow urls without schema (usually relative urls), or http(s)
		if(!isset($parsed_url['scheme']) || ($parsed_url['scheme']!='http' && $parsed_url['scheme']!='https')){
			$gBitSystem->fatalError( tra( "Invalid URL; not absolute or not HTTP" ));
		}
		//Make sure the passed host isn't local
		if(!isset($parsed_url['host']) || ($parsed_url['host']=='localhost') || strncmp($parsed_url['host'],"127.",4)==0){
			$gBitSystem->fatalError( tra( "The host specified is either empty or local." ));
		}
		$sdta = @file_get_contents( $suck_url );
		if( isset( $php_errormsg ) && strlen( $php_errormsg ) ) {
			$gBitSystem->fatalError( tra( "Can't import remote HTML page" ));
		}
		// Need to parse HTML?
		if( $parsehtml == 'y' ) {
			// Read compiled( serialized ) grammar
			$grammarfile = UTIL_PKG_PATH.'htmlparser/htmlgrammar.cmp';
			if( !$fp = @fopen( $grammarfile,'r' ) ) {
				$gBitSystem->fatalError( tra( "Can't parse remote HTML page" ));
			}
			$grammar = unserialize( fread( $fp, filesize( $grammarfile ) ) );
			fclose( $fp );
			// create parser object, insert html code and parse it
			$htmlparser = new HtmlParser( $sdta, $grammar, '', 0 );
			$htmlparser->Parse();
			// Should I try to convert HTML to wiki?
			$parseddata = '';
			$p =  array( 'stack' => array(), 'listack' => array(), 'first_td' => false );
			walk_and_parse( $htmlparser->content, $parseddata, $p );
			// Is some tags still opened?( It can be if HTML not valid, but this is not reason
			// to produce invalid wiki : )
			while( count( $p['stack'] ) ) {
				$e = end( $p['stack'] );
				$sdta .= $e['string'];
				array_pop( $p['stack'] );
			}
			// Unclosed lists r ignored... wiki have no special start/end lists syntax....
			// OK. Things remains to do:
			// 1 ) fix linked images
			$parseddata = preg_replace( ',\[(.*)\|\( img src=(.*)\)\],mU','{img src=$2 link=$1}', $parseddata );
			// 2 ) fix remains images( not in links )
			$parseddata = preg_replace( ',\( img src=(.*)\),mU','{img src=$1}', $parseddata );
			// 3 ) remove empty lines
			$parseddata = preg_replace( ",[\n]+,mU","\n", $parseddata );
			// Reassign previous data
			$sdta = $parseddata;
		}
		$_REQUEST['edit'] .= $sdta;
	}
}

//***************************************



?>
