<?php
class SiteScoutHTML extends SiteScout {

	function displayItems( $user ) {
		global $wgExtensionAssetsPath, $wgUserBoard;

		$output = '';
		$imgPath = $wgExtensionAssetsPath . '/SocialProfile/images/';
		$this->populateItems( $user );
		$x = 1;

		foreach ( $this->items as $item ) {
			if ( $x <= 30 ) {
				$title = Title::makeTitle( $item['namespace'], $item['pagetitle'] );
				$user_title = Title::makeTitle( NS_USER, $item['username'] );
				$output .= '<div id="comment-' . $x . '" class="site-scout"><span class="item-info">';
				$output .= '<img src="' . $imgPath . $this->getTypeIcon( $item['type'] ) . '" alt="" />';

				if ( $item['minor'] == 1 ) {
					$output .= '<br /><span class="edit-minor">' . wfMessage( 'sitescout-minor' )->plain() . '</span>';
				}
				if ( $item['new'] == 1 ) {
					$output .= '<br /><span class="edit-new">' . wfMessage( 'sitescout-new' )->plain() . '</span>';
				}
				$output .= '</span>';

				if ( $item['type'] != 'networkupdate' ) {
					$output .= Linker::link(
						$title,
						$title->getPrefixedText(),
						array( 'class' => 'item-title' )
					);
				} else {
					if ( $item['team_id'] ) {
						$team = SportsTeams::getTeam( $item['team_id'] );
						$network_name = $team['name'];
					} else {
						$sport = SportsTeams::getSport( $item['sport_id'] );
						$network_name = $sport['name'];
					}
					$output .= '<span class="site-scout-network"><a href="' .
						htmlspecialchars(
							SpecialPage::getTitleFor( 'FanHome' )->getFullURL( [
								'sport_id' => $item['sport_id'],
								'team_id' => $item['team_id']
							] ),
							ENT_QUOTES
						) .
						'" class="item-title">' . $network_name . '</a></span>';
				}

				// $output .= '<span class="item-time">' . date( 'g:i a, m.d.y', $item['timestamp'] ) . '</span>';
				$comment = $item['comment'];
				if ( $item['type'] == 'comment' ) {
					$comment = '<a href="' .
						htmlspecialchars( $title->getFullURL(), ENT_QUOTES ) .
						'#comment-' . $item['id'] . '" title="' .
						htmlspecialchars( $title->getText(), ENT_QUOTES ) . '">' .
						htmlspecialchars( $item['comment'], ENT_QUOTES ) . '</a>';
				}
				$output .= '<span class="item-comment">' . $comment . '</span>';

				$avatar = new wAvatar( $item['userid'], 's' );
				$commentIcon = $avatar->getAvatarURL();
				$talk_page = htmlspecialchars( $user_title->getTalkPage()->getFullURL(), ENT_QUOTES );
				if ( $wgUserBoard ) {
					$talk_page = htmlspecialchars(
						SpecialPage::getTitleFor( 'UserBoard' )->getFullURL( [ 'user' => $item['username'] ] ),
						ENT_QUOTES
					);
				}
				$output .= '<span class="item-user"><a href="' . htmlspecialchars( $user_title->getFullURL(), ENT_QUOTES ) . '" class="item-user-link">' . $commentIcon . ' ' . $item['username'] . '</a><a href="' . $talk_page . '" class="item-user-talk"><img src="' . $wgExtensionAssetsPath . '/SiteScout/resources/images/talkPageIcon.png" alt="" /></a></span>';
				$output .= '</div>';
				$x++;
			}
		}

		return $output;
	}
}
