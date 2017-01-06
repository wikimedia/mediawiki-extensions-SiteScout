var SiteScout = window.SiteScout = {
	itemMax: 30,
	timestamp: 0,
	edits: 1,
	comments: 1,
	votes: 1,
	networkupdates: 1,
	play: 1,
	items: new Array( 0 ),
	reload: 0,

	edits_count: 0,
	votes_count: 0,
	comments_count: 0,
	networkupdates_count: 0,

	largest_value: 0,

	changeFilter: function() {
		if ( !document.getElementById( 'f_edits' ).checked ) {
			SiteScout.edits = 0;
		} else {
			SiteScout.edits = 1;
		}
		if ( !document.getElementById( 'f_votes' ).checked ) {
			SiteScout.votes = 0;
		} else {
			SiteScout.votes = 1;
		}
		if ( !document.getElementById( 'f_comments' ).checked ) {
			SiteScout.comments = 0;
		} else {
			SiteScout.comments = 1;
		}
		if (
			document.getElementById( 'f_networkupdates' ) &&
			!document.getElementById( 'f_networkupdates' ).checked
		)
		{
			SiteScout.networkupdates = 0;
		} else {
			SiteScout.networkupdates = 1;
		}
		document.cookie = 'scout_edits=' + SiteScout.edits;
		document.cookie = 'scout_votes=' + SiteScout.votes;
		document.cookie = 'scout_comments=' + SiteScout.comments;
		document.cookie = 'scout_networkupdates=' + SiteScout.networkupdates;
		window.location = mw.util.getUrl(
			'Special:SiteScout',
			{
				'edits': SiteScout.edits,
				'votes': SiteScout.votes,
				'comments': SiteScout.comments,
				'networkupdates': SiteScout.networkupdates
			}
		);
	},

	setTimestamp: function( value ) {
		SiteScout.timestamp = value;
	},

	getItems: function() {
		$.ajax( {
			type: 'GET',
			url: mw.util.wikiScript( 'index' ),
			data: {
				title: 'Special:SiteScoutUpdate',
				edits: SiteScout.edits,
				votes: SiteScout.votes,
				comments: SiteScout.comments,
				networkupdates: SiteScout.networkupdates,
				timestamp: SiteScout.timestamp,
				rnd: Math.random()
			}
		} ).done( function( r ) {
			SiteScout.processItems( r );
		} );
	},

	processItems: function( request ) {
		var itemsXML;
		var item;
		try {
			itemsXML = request.responseXML.getElementsByTagName( 'items' )[0];
			item = itemsXML.getElementsByTagName( 'item' );
		} catch ( e ) {
			if ( !SiteScout.reload ) {
				setTimeout( 'SiteScout.getItems()', 10000 );
			}
			return;
		}
		for ( var i = 0; i < item.length; i++ ) {
			SiteScout.items[i] = {
				type: item[i].getElementsByTagName( 'type' )[0].firstChild.nodeValue,
				type_icon: item[i].getElementsByTagName( 'type_icon' )[0].firstChild.nodeValue,
				date: item[i].getElementsByTagName( 'date' )[0].firstChild.nodeValue,
				timestamp: item[i].getElementsByTagName( 'timestamp' )[0].firstChild.nodeValue,
				title: item[i].getElementsByTagName( 'title' )[0].firstChild.nodeValue,
				url: item[i].getElementsByTagName( 'url' )[0].firstChild.nodeValue,
				comment: item[i].getElementsByTagName( 'comment' )[0].firstChild.nodeValue,
				username: item[i].getElementsByTagName( 'user' )[0].firstChild.nodeValue,
				user_page: item[i].getElementsByTagName( 'user_page' )[0].firstChild.nodeValue,
				user_talkpage: item[i].getElementsByTagName( 'user_talkpage' )[0].firstChild.nodeValue,
				avatar: item[i].getElementsByTagName( 'avatar' )[0].firstChild.nodeValue,
				is_new: item[i].getElementsByTagName( 'edit_new' )[0].firstChild.nodeValue,
				is_minor: item[i].getElementsByTagName( 'edit_minor' )[0].firstChild.nodeValue,
				item_id: item[i].getElementsByTagName( 'id' )[0].firstChild.nodeValue
			};
			if ( i === 0 ) {
				timestamp = SiteScout.items[i].timestamp;
			}
		}
		if ( !SiteScout.reload ) {
			SiteScout.push();
		}
	},

	push: function() {
		if ( SiteScout.play === 0 ) {
			setTimeout( 'SiteScout.push()', 1000 );
			return;
		}
		var cell;
		var cellnext;
		var text;
		var style = '';
		var item = SiteScout.items.pop();
		text = SiteScout.displayLine( item );

		$( '#comment-1' ).css( 'opacity', 0.0 );
		for ( var i = ( itemMax - 1 ); i >= 1; i-- ) {
			cell = document.getElementById( 'comment-' + i );
			cellnext = document.getElementById( 'comment-' + ( i + 1 ) );
			if ( cell.innerHTML !== '' ) {
				cellnext.innerHTML = cell.innerHTML;
			}
		}

		$( '#comment-1' ).show( 2000 ).html( text );

		if ( SiteScout.items.length > 0 ) {
			setTimeout( 'SiteScout.push()', 2000 );
		} else {
			setTimeout( 'SiteScout.getItems()', 5000 );
		}
	},

	start: function() {
		SiteScout.getItems();
	},

	displayLine: function( item ) {
		if ( !SiteScout.reload ) {
			SiteScout.updateStat( item.type );
		}
		comment = item.comment;
		if ( item.type == 'comment' ) {
			comment = '<a href="' + item.url + '#' + item.item_id + '">' + comment + '</a>';
		}
		text = '<div class="site-scout">';
		text += '<span class="item-info">'
		+		'<img src="' + mw.config.get( 'wgExtensionAssetsPath' ) + '/SocialProfile/images/' + item.type_icon + '" alt="" />'
		+			( ( item.is_new == 1 ) ? '<br /><span class="edit-new">' + mw.msg( 'sitescout-new' ) + '</span>' : ( ( item.is_minor == 1 ) ? '<br /><span class="edit-minor">' + mw.msg( 'sitescout-minor' ) + '</span>' : '' ) )
		+	'</span>'
		+	'<a href="' + item.url + '" class="item-title">'
		+		item.title
		+	'</a>'

		+	'<span class="item-comment">'
		+		( ( comment ) ? comment : '-' )
		+	'</span>'
		+	'<span class="item-user">'
		+	'<a href="' + item.user_page + '" class="item-user-link">'
		+		'<img src="' + mw.config.get( 'wgUploadPath' ) + '/avatars/' + item.avatar + '" alt="" /> '
		+		item.username
		+	'</a>'
		+	'<a href="' + item.user_talkpage + '" class="item-user-talk"><img src="' + mw.config.get( 'wgExtensionAssetsPath' ) + '/SiteScout/resources/images/talkPageIcon.png" hspace="3" align="middle" alt="" /></a>'
		+	'</span>'
		+	'</div>';
		return text;
	},

	setLargestValue: function() {
		if ( SiteScout.edits_count > SiteScout.largest_value ) {
			SiteScout.largest_value = SiteScout.edits_count;
		}
		if ( SiteScout.comments_count > SiteScout.largest_value ) {
			SiteScout.largest_value = SiteScout.comments_count;
		}
		if ( SiteScout.votes_count > SiteScout.largest_value ) {
			SiteScout.largest_value = SiteScout.votes_count;
		}
		if (
			document.getElementById( 'networkupdates_stats' ) &&
			SiteScout.networkupdates_count > SiteScout.largest_value
		)
		{
			SiteScout.largest_value = SiteScout.networkupdates_count;
		}
	},

	/**
	 * @param {String} stat 'edit', 'vote', 'networkupdate' or 'comment'
	 */
	updateStat: function( stat ) {
		SiteScout[stat + 's_count']++;
		SiteScout.setLargestValue();
		SiteScout.updateStatChart();
	},

	updateStatChart: function() {
		document.getElementById( 'edit_stats' ).innerHTML = '<table><tr><td><table style="background-color:#285C98; height:7px;" width="' + ( SiteScout.edits_count / SiteScout.largest_value * 300 ) + '"><tr><td></td></tr></table></td><td>' + SiteScout.edits_count + '</td></tr></table>';
		document.getElementById( 'vote_stats' ).innerHTML = '<table><tr><td><table style="background-color:#009900; height:7px;" width="' + ( SiteScout.votes_count / SiteScout.largest_value * 300 ) + '"><tr><td></td></tr></table></td><td>' + SiteScout.votes_count + '</td></tr></table>';
		document.getElementById( 'comment_stats' ).innerHTML = '<table><tr><td><table style="background-color:#990000; height:7px;" width="' + ( SiteScout.comments_count / SiteScout.largest_value * 300 ) + '"><tr><td></td></tr></table></td><td>' + SiteScout.comments_count + '</td></tr></table>';
		if ( document.getElementById( 'networkupdates_stats' ) ) {
			document.getElementById( 'networkupdates_stats' ).innerHTML = '<table><tr><td><table style="background-color:#FFFCA9; height:7px;" width="' + ( SiteScout.networkupdates_count / SiteScout.largest_value * 300 ) + '"><tr><td></td></tr></table></td><td>' + SiteScout.networkupdates_count + '</td></tr></table>';
		}
	}
};

$( function() {
	// Warning: ugly code ahead
	// This replaces some old JS by pulling the data from the data attributes
	// (yay for HTML5!) of a hidden div...or rather, two; the first batch of
	// data (everything but the timestamp) is set in SiteScout::getControls()
	// and the timestamp is set in SiteScoutPage::execute()
	var $util = $( 'div#sitescout-utility' );
	SiteScout.edits_count = $util.data( 'edits-count' );
	SiteScout.votes_count = $util.data( 'votes-count' );
	SiteScout.comments_count = $util.data( 'comments-count' );
	SiteScout.networkupdates_count = $util.data( 'networkupdates-count' );

	SiteScout.edits = $util.data( 'edits' );
	SiteScout.comments = $util.data( 'comments' );
	SiteScout.networkupdates = $util.data( 'networkupdates' );

	SiteScout.setTimestamp( $( 'div#sitescout-utility-time' ).html() );

	SiteScout.start();

	$( 'a.sitescout-filter-link' ).on( 'click', function ( event ) {
		event.preventDefault();
		SiteScout.changeFilter();
		return false;
	} );
} );