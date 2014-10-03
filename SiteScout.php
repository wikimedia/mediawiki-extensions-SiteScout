<?php
/**
 * SiteScout extension -- a social version of Special:RecentChanges
 *
 * @file
 * @ingroup Extensions
 * @version 2.0
 * @author David Pean <david.pean@gmail.com>
 * @author Jack Phoenix <jack@countervandalism.net>
 * @copyright Copyright © 2007 David Pean
 * @copyright Copyright © 2014 Jack Phoenix
 * @link https://www.mediawiki.org/wiki/Extension:SiteScout Documentation
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This is not a valid entry point.\n" );
}

// Extension credits that show up on Special:Version
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'SiteScout',
	'version' => '2.0',
	'author' => array( 'David Pean', 'Jack Phoenix' ),
	'description' => '[[Special:SiteScout|Displays recent social changes]]',
	'url' => 'https://www.mediawiki.org/wiki/Extension:SiteScout',
);

// Internationalization
$wgMessagesDirs['SiteScout'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['SiteScoutAliases'] = __DIR__ . '/SiteScout.alias.php';

// ResourceLoader support for MediaWiki 1.17+
$wgResourceModules['ext.sitescout.css'] = array(
	'styles' => 'resources/css/sitescout.css',
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'SiteScout',
	'position' => 'top'
);

$wgResourceModules['ext.sitescout.js'] = array(
	'scripts' => 'resources/js/SiteScout.js',
	'messages' => array(
		'sitescout-new', 'sitescout-minor'
	),
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'SiteScout'
);

// Set up the new special pages
$wgAutoloadClasses['SiteScout'] = __DIR__ . '/SiteScoutClass.php';
$wgAutoloadClasses['SiteScoutHTML'] = __DIR__ . '/SiteScoutClass.php';
$wgAutoloadClasses['SiteScoutXML'] = __DIR__ . '/SiteScoutClass.php';

$wgAutoloadClasses['SiteScoutPage'] = __DIR__ . '/SpecialSiteScout.php';
$wgSpecialPages['SiteScout'] = 'SiteScoutPage';

$wgAutoloadClasses['SiteScoutUpdate'] = __DIR__ . '/SiteScout_Update.php';
$wgSpecialPages['SiteScoutUpdate'] = 'SiteScoutUpdate';
