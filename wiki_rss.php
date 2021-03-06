<?php
/**
 * @version $Header$
 * @package wiki
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( "../kernel/setup_inc.php" );

$gBitSystem->verifyPackage( 'wiki' );
$gBitSystem->verifyPackage( 'rss' );
$gBitSystem->verifyFeature( 'wiki_rss' );

require_once( WIKI_PKG_PATH."BitPage.php" );
require_once( RSS_PKG_PATH."rss_inc.php" );

$rss->title = $gBitSystem->getConfig( 'wiki_rss_title', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'Wiki' ) );
$rss->description = $gBitSystem->getConfig( 'wiki_rss_description', $gBitSystem->getConfig( 'site_title' ).' - '.tra( 'RSS Feed' ) );

// check permission to view wiki pages
if( !$gBitUser->hasPermission( 'p_wiki_view_page' ) ) {
	require_once( RSS_PKG_PATH."rss_error.php" );
} else {
	// check if we want to use the cache file
	$cacheFile = TEMP_PKG_PATH.RSS_PKG_NAME.'/'.WIKI_PKG_NAME.'/'.$cacheFileTail;
	$rss->useCached( $rss_version_name, $cacheFile, $gBitSystem->getConfig( 'rssfeed_cache_time' ));

	$wiki = new BitPage();
	$listHash = array(
		'max_records' => $gBitSystem->getConfig( 'wiki_rss_max_records', 10 ),
		'sort_mode' => 'last_modified_desc',
		'get_data' => TRUE,
	);
	$feeds = $wiki->getList( $listHash );

	// set the rss link
	$rss->link = 'http://'.$_SERVER['HTTP_HOST'].WIKI_PKG_URL;

	// get all the data ready for the feed creator
	foreach( $feeds as $feed ) {
		$item = new FeedItem();
		$item->title = $feed['title'];
		$item->link = BIT_BASE_URI.$wiki->getDisplayUrl( $feed['title'] );
		$item->description = $wiki->parseData( $feed );

		$item->date = ( int )$feed['last_modified'];
		$item->source = 'http://'.$_SERVER['HTTP_HOST'].BIT_ROOT_URL;
		$item->author = $gBitUser->getDisplayName( FALSE, array( 'real_name' => $feed['modifier_real_name'], 'login' => $feed['modifier_user'] ) );

		$item->descriptionTruncSize = $gBitSystem->getConfig( 'rssfeed_truncate', 5000 );
		$item->descriptionHtmlSyndicated = FALSE;

		// pass the item on to the rss feed creator
		$rss->addItem( $item );
	}

	// finally we are ready to serve the data
	echo $rss->saveFeed( $rss_version_name, $cacheFile );
}
?>
