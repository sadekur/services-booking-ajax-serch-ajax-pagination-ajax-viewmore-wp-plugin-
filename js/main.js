jQuery(document).ready(function($) {
  $('#tf-services-search-form').submit(function(e) {
    e.preventDefault();
    var searchTerm = $('#tf-services-search-input').val();
    searchTFServices(searchTerm);
  });

  function searchTFServices(term) {
    $.ajax({
      url: TFSB.ajax_url,
      type: 'post',
      data: {
        action: 'tf_services_search',
        term: term,
      },
      success: function(results) {
        $('.main-cls').html(results);
      }
    });
  }

  /*View More*/
  var count = TFSB.post_count;
  var ajaxurl = TFSB.ajax_url;
  var page = 2;
  jQuery(document).on('click', '#view_more', function(){
  	var data = {
  		'action': 'view_action',
  		'page': page
  	};
  	jQuery.post(ajaxurl, data, function(response) {
  		jQuery('.js-xyz').append(response);
  		if(count == page){
  			jQuery('#view_more').hide();
  		}
  		page += page + 1; /*When All Post shown then another time click button then it hide */
  	});
  });

/*pagination*/
$('.main-cls').on('click', '.pagination-fst a', function(e){
	e.preventDefault();
	var link = $(this).attr('href');
	$('.main-cls').fadeOut(100, function() {
		$(this).load(link + ' .main-cls', function(){
			$(this).fadeIn(100);
		});
	});
});
})( jQuery );

