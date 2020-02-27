<?php
class SiteScoutXML extends SiteScout {

	function displayItems( $user ) {
		global $wgUserBoard;

		$output = '';
		$this->populateItems( $user );
		$x = 1;

		foreach ( $this->items as $item ) {
			$avatar = new wAvatar( $item['userid'], 's' );
			$commentIcon = $avatar->getAvatarImage();
			if ( $item['username'] == '' ) {
				$item['username'] = '-';
			}
			$title = Title::makeTitle( $item['namespace'], $item['pagetitle'] );
			$user_title = Title::makeTitle( NS_USER, $item['username'] );
			$talk_page = $user_title->getTalkPage()->getFullURL();
			if ( $wgUserBoard ) {
				$talk_page = htmlspecialchars(
					SpecialPage::getTitleFor( 'UserBoard' )->getFullURL( [ 'user' => $item['username'] ] ),
					ENT_QUOTES
				);
			}

			$page_title = str_replace( '&', '%26', $title->getPrefixedText() );
			$page_url = $title->getFullURL();

			if ( $item['type'] == 'networkupdate' ) {
				if ( $item['team_id'] ) {
					$team = SportsTeams::getTeam( $item['team_id'] );
					$network_name = $team['name'];
				} else {
					$sport = SportsTeams::getSport( $item['sport_id'] );
					$network_name = $sport['name'];
				}
				$page_title = $network_name;
				$page_url = htmlspecialchars(
					SpecialPage::getTitleFor( 'FanHome' )->getFullURL( [
						'sport_id' => $item['sport_id'],
						'team_id' => $item['team_id']
					] ),
					ENT_QUOTES
				);
			}

			$output .= '<item>';
			$output .= '<type_icon>' . $this->getTypeIcon( $item['type'] ) . '</type_icon>
			<type>' . $item['type'] . '</type>
			<date>' . date( 'g:i a , m.d.y', $item['timestamp'] ) . '</date>
			<timestamp>' . $item['timestamp'] . "</timestamp>
			<title>{$page_title}</title>
			<url>{$page_url}</url>
			<comment>" . $item['comment'] . '</comment>
			<user>' . str_replace( '&', '%26', $item['username'] ) . '</user>
			<user_page>' . $user_title->getFullURL() . '</user_page>
			<user_talkpage>' . $talk_page . '</user_talkpage>
			<avatar>' . $commentIcon . '</avatar>
			<edit_new>' . $item['new'] . '</edit_new>
			<edit_minor>' . $item['minor'] . '</edit_minor>
			<id>' . $item['id'] . '</id>';
			$output .= '</item>';

			$x++;
		}

		if ( $output ) {
			$output = '<items>' . $output . '</items>';
		}

		return $output;
	}

}
