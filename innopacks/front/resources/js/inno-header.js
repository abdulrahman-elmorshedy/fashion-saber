$(function () {
  // Page header scroll effect handling
  const headerContentHeight = $('.header-box').outerHeight(true);

  $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
      $('.header-box').addClass('header-fixed');
      // If not on homepage and no placeholder exists, add placeholder to prevent page jumping
      if (!$('body').hasClass('page-home') && !$('.header-placeholder').length) {
        $('.header-box').before('<div class="header-placeholder" style="height:' + headerContentHeight + 'px"></div>');
      }
    } else {
      $('.header-box').removeClass('header-fixed');
      $('.header-placeholder').remove();
    }
  });

  // Search Autocomplete
  const $searchInput = $('input[name="keyword"]');
  if ($searchInput.length && typeof $.fn.autocomplete === 'function') {
    $searchInput.autocomplete({
      source: function(request, response) {
        $.ajax({
          url: '/api/products/skus',
          dataType: 'json',
          data: { keyword: request, limit: 10 },
          success: function(res) {
            if (res.success && res.data) {
              response($.map(res.data, function(item) {
                return {
                  label: item.product_name,
                  value: item.product_id,
                  image: item.image_url,
                  price: item.price_format,
                  url: '/products/' + item.product_id
                }
              }));
            }
          }
        });
      },
      select: function(item) {
        window.location.href = item.url;
      }
    });
  }
});
