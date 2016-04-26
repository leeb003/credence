jQuery(document).ready(function($) {
	/**
	 * This is used for template single-blogtime, and tracks current post load number to alternate sides
	 **/

	// The number of the next page to load (/page/x/).
	var pageNum = parseInt(pbd_alp.startPage) + 1;
	
	// The maximum number of pages the current query can return.
	var max = parseInt(pbd_alp.maxPages);
	
	// The link of the next page of posts.
	var nextLink = pbd_alp.nextLink;

	// load more text
	var loadMore = pbd_alp.loadMore;
	// loading text
	var loadingText = pbd_alp.loadingText;
	// No more posts
	var noMore = pbd_alp.noMore;
	// current post count
	var timelineCount = parseInt(pbd_alp.count);
	
	/**
	 * Load new posts when the link is clicked.
	 */
	$(document).on('click', '.loadmore', function() {
		// Are there more posts to load?
		if(pageNum <= max) {
		
			// Show that we're working.
			$(this).text(loadingText);
			var nextLinkCount = nextLink + '?count=' + timelineCount;
			// We need to increment the variable for the next request 
			timelineCount = timelineCount + timelineCount;  
			$.get(nextLinkCount, function(data) {
	
				$(data).find('.post').hide().appendTo('ul.timeline').fadeIn();
				$(".page_nav").remove();
            	var button = '<div class="page_nav">'
                        + '<a class="loadmore" href="#">' + loadMore + '</a>'
                        + '</div>';
				$('ul.timeline').append(button);
				pageNum++;
				nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);
				if(pageNum <= max) {
                    $('.loadmore').text(loadMore);
                } else {
                    $('.loadmore').text(noMore);
                }
				$('.wp-audio-shortcode').css('visibility', 'visible');
			});
		} else {
			$('.loadmore').append('.');
		}	
		return false;
	});
});
