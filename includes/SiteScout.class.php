<?php

class SiteScout {
	public $showEdits = true;
	public $showVotes = true;
	public $showComments = true;
	public $showNetworkUpdates = true;

	public $itemMax = 50;
	public $timestamp = 0;
	public $items = array();

	/**
	 * Constructor
	 *
	 * @param int $uid User ID [unused, as far as I can see]
	 */
	function __construct( $uid = 0 ) {
		$this->user_id = $uid;
		return '';
	}

	function setItemMax( $max ) {
		if ( is_numeric( $max ) ) {
			$this->itemMax = $max;
		}
	}

	function setTimestamp( $ts ) {
		if ( is_numeric( $ts ) ) {
			$this->timestamp = $ts;
		}
	}

	function setShowEdits( $details ) {
		if ( is_numeric( $details ) && $details == 1 ) {
			$this->showEdits = true;
		} else {
			$this->showEdits = false;
		}
	}

	function setShowVotes( $details ) {
		if ( is_numeric( $details ) && $details == 1 ) {
			$this->showVotes = true;
		} else {
			$this->showVotes = false;
		}
	}

	function setShowComments( $details ) {
		if ( is_numeric( $details ) && $details == 1 ) {
			$this->showComments = true;
		} else {
			$this->showComments = false;
		}
	}

	function setShowNetworkUpdates( $details ) {
		if ( is_numeric( $details ) && $details == 1 ) {
			$this->showNetworkUpdates = true;
		} else {
			$this->showNetworkUpdates = false;
		}
	}

	/**
	 * Basically builds the HTML output, complete with some ugly JS stuff.
	 *
	 * @return string HTML
	 */
	function getControls() {
		global $wgExtensionAssetsPath;

		$edits = $this->getEditCount();
		$votes = $this->getVoteCount();
		$comments = $this->getCommentCount();
		if ( class_exists( 'UserStatus' ) ) {
			$networkupdates = $this->getNetworkUpdatesCount();
		} else {
			$networkupdates = 0;
		}
		$largest_value = max( $edits, $votes, $comments, $networkupdates );

		$imgPath = $wgExtensionAssetsPath . '/SocialProfile/images/';

		$output = '
			<table>
				<tr>
					<td>
						<table class="site-scout-stats">
							<tr>
								<td class="site-scout-stats-header" colspan="2">' . wfMessage( 'sitescout-today-stats' )->plain() . "</td>
							</tr>
							<tr>
								<td>
									<img src=\"{$imgPath}voteIcon.gif\" alt=\"Votes\"/>
								</td>
								<td>" . wfMessage( 'sitescout-votes' )->plain() . '</td>
								<td>
									<span id="vote_stats">
										<table>
											<tr>
												<td>
													<table style="background-color:#009900; height:7px; width:' . ( $votes / $largest_value * 300 ) . "px;\">
														<tr>
															<td></td>
														</tr>
													</table>
												</td>
												<td>{$votes}</td>
											</tr>
										</table>
									</span>
								</td>
							</tr>
							<tr>
								<td>
									<img src=\"{$imgPath}editIcon.gif\" alt=\"Edits\"/>
								</td>
								<td>" . wfMessage( 'sitescout-edits' )->plain() . '</td>
								<td>
									<span id="edit_stats">
										<table>
											<tr>
												<td>
													<table style="background-color:#285C98; height:7px; width:' . ( $edits / $largest_value * 300 ) . "px;\">
														<tr>
															<td></td>
														</tr>
													</table>
												</td>
												<td>{$edits}</td>
											</tr>
										</table>
									</span>
								</td>
							</tr>

							<tr>
								<td>
									<img src=\"{$imgPath}comment.gif\" alt=\"Comments\"/>
								</td>
								<td>" . wfMessage( 'sitescout-comments' )->plain() . '</td>
								<td>
									<span id="comment_stats">
										<table>
											<tr>
												<td>
													<table style="background-color:#990000; height:7px; width:' . ( $comments / $largest_value * 300 ) . "px;\">
														<tr>
															<td></td>
														</tr>
													</table>
												</td>
												<td>{$comments}</td>
											</tr>
										</table>
									</span>
								</td>
							</tr>";
		if ( class_exists( 'UserStatus' ) ) {
			$output .= "<tr>
									<td>
										<img src=\"{$imgPath}note.gif\" alt=\"Network Thoughts\"/>
									</td>
									<td>" . wfMessage( 'sitescout-thoughts' )->plain() . '</td>
									<td>
										<span id="networkupdates_stats">
											<table>
												<tr>
													<td>
														<table style="background-color: #FFFCA9; height: 7px; width:' . ( $networkupdates / $largest_value * 300 ) . "px;\">
															<tr>
																<td></td>
															</tr>
														</table>
													</td>
													<td>{$networkupdates}</td>
												</tr>
											</table>
										</span>
									</td>
								</tr>";
		}
		$output .= '</table>
					</td>
				</tr>
				<tr>
					<td>';
		$output .= "<div id=\"sitescout-toggles-container\">
			<img src=\"{$imgPath}editIcon.gif\" alt=\"" . wfMessage( 'sitescout-edits' )->plain() . "\"><input type=\"checkbox\" name=\"f_edits\" id=\"f_edits\" value=\"1\" " . ( ( $this->showEdits ) ? 'checked' : '' ) . " />
			<img src=\"{$imgPath}voteIcon.gif\" alt=\"" . wfMessage( 'sitescout-votes' )->plain() . "\"><input type=\"checkbox\" name=\"f_votes\" id=\"f_votes\" value=\"1\" " . ( ( $this->showVotes ) ? 'checked' : '' ) . " />
			<img src=\"{$imgPath}comment.gif\" alt=\"" . wfMessage( 'sitescout-comments' )->plain() . "\"><input type=\"checkbox\" name=\"f_comments\" id=\"f_comments\" value=\"1\" " . ( ( $this->showComments ) ? 'checked' : '' ) . " />";
		if ( class_exists( 'UserStatus' ) ) {
			$output .= "<img src=\"{$imgPath}note.gif\" alt=\"" . wfMessage( 'sitescout-thoughts' )->plain() . "\"><input type=\"checkbox\" name=\"f_networkupdates\" id=\"f_networkupdates\" value=\"1\" " . ( ( $this->showNetworkUpdates ) ? 'checked' : '' ) . " />";
		}
		$output .= '<a href="#" class="sitescout-filter-link">' . wfMessage( 'sitescout-change-filter' )->plain() . '</a>
			</div>';

		$output .= '</td>
				</tr>
			</table>

		</td><td style="width:25px;"></td><td valign="bottom">
		</td></tr></table>';
		// Hidden utility div for the JavaScript file; required (AFAIK) in the
		// new, ResourceLoader-ful world where inline JS is a strict no-no
		$output .= Html::element( 'div',
			array(
				'id' => 'sitescout-utility',
				'style' => 'display:none',
				'data-edits-count' => $edits,
				'data-votes-count' => $votes,
				'data-comments-count' => $comments,
				'data-networkupdates-count' => $networkupdates,
				'data-edits' => ( ( $this->showEdits ) ? 1 : 0 ),
				'data-comments' => ( ( $this->showEdits ) ? 1 : 0 ),
				'data-networkupdates' => ( ( $this->showNetworkUpdates ) ? 1 : 0 )
			)
		);

		return $output;
	}

	function getHeader() {
		return '<div id="items">
		<div class="item-header">
			<span class="item-info">' . wfMessage( 'sitescout-header-type' )->plain() . '</span>
			<span class="item-title">' . wfMessage( 'sitescout-header-page' )->plain() . '</span>
			<span class="item-comment">' . wfMessage( 'sitescout-header-comment' )->plain() . '</span>
			<span class="item-user">' . wfMessage( 'sitescout-header-user' )->plain() . '</span>
		</div>
		</div>';
	}

	/**
	 * Get the amount of edits from the recentchanges table within the past five hours
	 *
	 * @return int
	 */
	function getEditCount() {
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'recentchanges',
			array(
				'COUNT(*) AS edit_count',
				"Date_FORMAT(DATE_SUB(`rc_timestamp`, INTERVAL 5 HOUR), '%y %m %d')"
			),
			array(),
			__METHOD__,
			array(
				'GROUP BY' => "Date_FORMAT(DATE_SUB( `rc_timestamp`, INTERVAL 5 HOUR ), '%y %m %d')",
				'ORDER BY' => "Date_FORMAT(DATE_SUB( `rc_timestamp`, INTERVAL 5 HOUR ), '%y %m %d') DESC",
				'LIMIT' => 1
			)
		);
		$row = $dbr->fetchObject( $res );
		if ( $row ) {
			return $row->edit_count;
		} else {
			return 0;
		}
	}

	/**
	 * Get the amount of comments left within the past five hours
	 *
	 * @return int
	 */
	function getCommentCount() {
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'Comments',
			array(
				'COUNT(*) AS comment_count',
				"Date_FORMAT( DATE_SUB(`Comment_Date`, INTERVAL 5 HOUR), '%y %m %d' )"
			),
			array(),
			__METHOD__,
			array(
				'GROUP BY' => "Date_FORMAT( DATE_SUB(`Comment_Date`, INTERVAL 5 HOUR ), '%y %m %d' )",
				'ORDER BY' => "Date_FORMAT( DATE_SUB(`Comment_Date`, INTERVAL 5 HOUR ), '%y %m %d' ) DESC",
				'LIMIT' => 1
			)
		);
		$row = $dbr->fetchObject( $res );
		if ( $row ) {
			return $row->comment_count;
		} else {
			return 0;
		}
	}

	/**
	 * Get the amount of votes cast within the past five hours
	 *
	 * @return int
	 */
	function getVoteCount() {
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'Vote',
			array(
				'COUNT(*) AS vote_count',
				"Date_FORMAT( DATE_SUB(`Vote_Date`, INTERVAL 5 HOUR), '%y %m %d' )"
			),
			array(),
			__METHOD__,
			array(
				'GROUP BY' => "Date_FORMAT( DATE_SUB(`Vote_Date`, INTERVAL 5 HOUR), '%y %m %d' )",
				'ORDER BY' => "Date_FORMAT( DATE_SUB(`Vote_Date`, INTERVAL 5 HOUR), '%y %m %d' ) DESC",
				'LIMIT' => 1
			)
		);
		$row = $dbr->fetchObject( $res );
		if ( $row ) {
			return $row->vote_count;
		} else {
			return 0;
		}
	}

	/**
	 * Get the amount of network status updates done within the past five hours
	 *
	 * @return int
	 */
	function getNetworkUpdatesCount() {
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'user_status',
			array(
				'COUNT(*) AS count',
				"Date_FORMAT( DATE_SUB(`us_date`, INTERVAL 5 HOUR), '%y %m %d' )"
			),
			array(),
			__METHOD__,
			array(
				'GROUP BY' => "Date_FORMAT( DATE_SUB(`us_date`, INTERVAL 5 HOUR), '%y %m %d' )",
				'ORDER BY' => "Date_FORMAT( DATE_SUB(`us_date`, INTERVAL 5 HOUR), '%y %m %d' ) DESC",
				'LIMIT' => 1
			)
		);
		$row = $dbr->fetchObject( $res );
		if ( $row ) {
			return $row->count;
		} else {
			return 0;
		}
	}

	function fixItemComment( $comment ) {
		if ( !$comment ) {
			$comment = '-';
		} else {
			$comment = str_replace( '<', '&lt;', $comment );
			$comment = str_replace( '>', '&gt;', $comment );
			$comment = str_replace( '&', '%26', $comment );
			$comment = str_replace( '%26quot;', '"', $comment );
		}
		$preview = substr( $comment, 0, 50 );
		if ( $preview != $comment ) {
			$preview .= '...';
		}
		return $preview;
	}

	function populateItems() {
		global $wgMemc;

		$key = wfMemcKey( 'site_scout', $this->itemMax );
		$data = $wgMemc->get( $key );
		if ( $data ) {
			wfDebug( "Site scout loaded from cache\n" );
			$this->all_items = $data;
		} else {
			$this->populateItemsDB();
		}

		$this->filterItems();
	}

	function filterItems() {
		foreach ( $this->all_items as $item ) {
			$show_item = false;

			if ( $item['type'] == 'edit' && $this->showEdits == true ) {
				$show_item = true;
			}
			if ( $item['type'] == 'comment' && $this->showComments == true ) {
				$show_item = true;
			}
			if ( $item['type'] == 'vote' && $this->showVotes == true ) {
				$show_item = true;
			}
			if ( $item['type'] == 'networkupdate' && $this->showNetworkUpdates == true ) {
				$show_item = true;
			}

			if ( $this->timestamp != 0 && (int)$this->timestamp >= (int)$item['timestamp'] ) {
				$show_item = false;
			}

			if ( $show_item ) {
				$this->items[] = $item;
			}
		}
	}

	/**
	 * Populate the all_items class member variable by querying the database
	 * tables for the relevant data.
	 * The data is then cached in memcached for thirty seconds.
	 */
	function populateItemsDB() {
		global $wgUser;

		/**
		Edits
		**/
		$dbr = wfGetDB( DB_REPLICA );

		$where = array();
		if ( $this->timestamp > 0 ) {
			//$where[] = 'UNIX_TIMESTAMP(rc_timestamp) > ' . ( $this->timestamp );
		}
		$res = $dbr->select(
			'recentchanges',
			array(
				'rc_timestamp AS item_date', 'rc_title',
				'rc_user', 'rc_user_text', 'rc_comment', 'rc_id', 'rc_minor',
				'rc_new', 'rc_namespace', 'rc_cur_id', 'rc_this_oldid',
				'rc_last_oldid'
			),
			$where,
			__METHOD__,
			array( 'ORDER BY' => 'rc_id DESC', 'LIMIT' => $this->itemMax )
		);

		foreach ( $res as $row ) {
			$this->all_items[] = array(
				'id' => 0,
				'type' => 'edit',
				'timestamp' => wfTimestamp( TS_UNIX, $row->item_date ),
				'pagetitle' => $row->rc_title,
				'namespace' => $row->rc_namespace,
				'username' => $row->rc_user_text,
				'userid' => $row->rc_user,
				'comment' => $this->fixItemComment( $row->rc_comment ),
				'minor' => $row->rc_minor,
				'new' => $row->rc_new
			);
		}

		/**
		Votes
		**/
		$where = array();
		if ( $this->timestamp > 0 ) {
			// $where[] = 'UNIX_TIMESTAMP(vote_date) > ' . $this->timestamp;
		}
		$res = $dbr->select(
			array( 'Vote', 'page' ),
			array(
				'vote_date AS item_date', 'username', 'page_title',
				'vote_ip', 'vote_user_id', 'page_namespace'
			),
			$where,
			__METHOD__,
			array( 'ORDER BY' => 'vote_date DESC', 'LIMIT' => $this->itemMax )
		);
		foreach ( $res as $row ) {
			if ( $row->vote_user_id != 0 ) {
				$username = $row->username;
			} else {
				$username = wfMessage( 'sitescout-anon' )->plain();
			}

			$this->all_items[] = array(
				'id' => 0,
				'type' => 'vote',
				'timestamp' => wfTimestamp( TS_UNIX, $row->item_date ),
				'pagetitle' => $row->page_title,
				'namespace' => $row->page_namespace,
				'username' => $username,
				'userid' => $row->vote_user_id,
				'comment' => '-',
				'new' => '0',
				'minor' => 0
			);
		}

		/**
		Comments
		**/
		$block_list = array();
		if ( $wgUser->getId() != 0 ) {
			$block_list = CommentFunctions::getBlockList( $wgUser->getId() );
		}

		$dbr = wfGetDB( DB_REPLICA );
		$where = array(
			'comment_page_id = page_id'
		);
		if ( $this->timestamp > 0 ) {
			//$where[] = 'UNIX_TIMESTAMP(comment_date) > ' . $this->timestamp;
		}
		$res = $dbr->select(
			array( 'Comments', 'page' ),
			array(
				'Comment_Date AS item_date', 'Comment_Username',
				'page_title', 'Comment_Text', 'Comment_user_id', 'CommentID',
				'page_namespace'
			),
			$where,
			__METHOD__,
			array( 'ORDER BY' => 'comment_date DESC', 'LIMIT' => $this->itemMax )
		);
		foreach ( $res as $row ) {
			if ( !in_array( $row->Comment_Username, $block_list ) ) {
				if ( $row->Comment_user_id != 0 || $wgUser->isAllowed( 'commentadmin' ) ) {
					$username = $row->Comment_Username;
				} else {
					$username = wfMessage( 'sitescout-anon' )->plain();
				}

				$this->all_items[] = array(
					'id' => $row->CommentID,
					'type' => 'comment',
					'timestamp' => wfTimestamp( TS_UNIX, $row->item_date ),
					'pagetitle' => $row->page_title,
					'namespace' => $row->page_namespace,
					'username' => $username,
					'userid' => $row->Comment_user_id,
					'comment' => $this->fixItemComment( $row->Comment_Text ),
					'new' => '0',
					'minor' => 0
				);
			}
		}

		/**
		Network Thoughts
		TODO: Turn this into a hook so it's not in the base code
		**/
		if ( class_exists( 'UserStatus' ) ) {
			$dbr = wfGetDB( DB_REPLICA );
			$where = array();
			if ( $this->timestamp > 0 ) {
				// $where[] = 'UNIX_TIMESTAMP(us_date) > ' . $this->timestamp;
			}
			$res = $dbr->select(
				'user_status',
				array(
					'us_id', 'us_date AS item_date',
					'us_user_name', 'us_user_id', 'us_sport_id', 'us_team_id',
					'us_text'
				),
				$where,
				__METHOD__,
				array( 'ORDER BY' => 'us_id DESC', 'LIMIT' => $this->itemMax )
			);
			foreach ( $res as $row ) {
				$this->all_items[] = array(
					'id' => $row->us_id,
					'type' => 'networkupdate',
					'timestamp' => wfTimestamp( TS_UNIX, $row->item_date ),
					'pagetitle' => $row->us_user_name,
					'namespace' => NS_USER,
					'username' => $row->us_user_name,
					'userid' => $row->us_user_id,
					'comment' => strip_tags( $row->us_text ),
					'new' => '0',
					'minor' => 0,
					'sport_id' => $row->us_sport_id,
					'team_id' => $row->us_team_id
				);
			}
		}

		usort( $this->all_items, array( 'SiteScout', 'sortItems' ) );

		// Set cache
		global $wgMemc;
		$key = wfMemcKey( 'site_scout', $this->itemMax );
		$wgMemc->set( $key, $this->all_items, 30 );
	}

	function getTypeIcon( $type ) {
		if ( $type == 'edit' ) {
			$img = 'editIcon.gif';
		} elseif ( $type == 'vote' ) {
			$img = 'voteIcon.gif';
		} elseif ( $type == 'networkupdate' ) {
			$img = 'note.gif';
		} else {
			$img = 'comment.gif';
		}
		return $img;
	}

	function sortItems( $x, $y ) {
		if ( $x['timestamp'] == $y['timestamp'] ) {
			return 0;
		} elseif ( $x['timestamp'] > $y['timestamp'] ) {
			return -1;
		} else {
			return 1;
		}
	}
}
