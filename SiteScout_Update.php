<?php

class SiteScoutUpdate extends UnlistedSpecialPage {

	public function __construct() {
		parent::__construct( 'SiteScoutUpdate' );
	}

	public function execute( $par ) {
		global $wgMimeType, $wgOutputEncoding;

		$request = $this->getRequest();

		$wgMimeType = 'text/xml';
		$wgOutputEncoding = 'UTF-8';

		$scout = new SiteScoutXML;
		$scout->setShowEdits( $request->getVal( 'edits' ) );
		$scout->setShowVotes( $request->getVal( 'votes' ) );
		$scout->setShowComments( $request->getVal( 'comments' ) );
		$scout->setShowNetworkUpdates( $request->getVal( 'networkupdates' ) );
		$scout->setTimestamp( $request->getVal( 'timestamp' ) );
		$output = $scout->displayItems();
		echo $output;

		// This line removes the navigation and everything else from the
		// page, if you don't set it, you get what looks like a regular wiki
		// page, with the body you defined above.
		$this->getOutput()->setArticleBodyOnly( true );
	}
}