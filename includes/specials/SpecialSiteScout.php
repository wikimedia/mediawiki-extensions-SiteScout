<?php

class SiteScoutPage extends SpecialPage {

	/**
	 * Constructor -- set up the new special page
	 */
	public function __construct() {
		parent::__construct( 'SiteScout' );
	}

	/**
	 * Group this special page under the correct group in Special:SpecialPages
	 *
	 * @return string
	 */
	protected function getGroupName() {
		return 'changes';
	}

	/**
	 * Show the special page
	 *
	 * @param mixed|null $par Parameter passed to the special page or null
	 */
	public function execute( $par ) {
		global $wgUploadPath;

		$out = $this->getOutput();
		$request = $this->getRequest();

		$this->setHeaders();

		// Add CSS & JS via ResourceLoader
		$out->addModuleStyles( 'ext.sitescout.css' );
		$out->addModules( 'ext.sitescout.js' );

		// Expose $wgUploadPath to JavaScript so it can use it (it's needed so
		// that the JS file can build the correct path to the avatars directory)
		$out->addJsConfigVars( 'wgUploadPath', $wgUploadPath );

		$output = '';

		if ( isset( $_COOKIE['scout_edits'] ) ) {
			$show_edits = $_COOKIE['scout_edits'];
		} else {
			$show_edits = $request->getVal( 'edits', 1 );
		}

		if ( isset( $_COOKIE['scout_votes'] ) ) {
			$show_votes = $_COOKIE['scout_votes'];
		} else {
			$show_votes = $request->getVal( 'votes', 1 );
		}

		if ( isset( $_COOKIE['scout_comments'] ) ) {
			$show_comments = $_COOKIE['scout_comments'];
		} else {
			$show_comments = $request->getVal( 'comments', 1 );
		}

		if ( isset( $_COOKIE['scout_network_updates'] ) ) {
			$show_network_updates = $_COOKIE['scout_network_updates'];
		} else {
			$show_network_updates = $request->getVal( 'networkupdates', 1 );
		}

		$scout = new SiteScoutHTML;
		$scout->setShowVotes( $show_votes );
		$scout->setShowEdits( $show_edits );
		$scout->setShowComments( $show_comments );
		$scout->setShowNetworkUpdates( $show_network_updates );

		$output .= $scout->getControls();
		$output .= $scout->getHeader() . $scout->displayItems( $this->getUser() );

		$output .= '<div id="sitescout-utility-time" style="display:none;">' . time() . '</div>';
		$out->addHTML( $output );
	}
}