var readyFunc = function() {
	var galleryThumbs = new Swiper('.object-gallery-thumbs .swiper-container', {
		scrollbar: {
			el: '.object-gallery-thumbs .swiper-scrollbar',
			draggable: true,
		},
		slidesPerView: 3,
		paginationClickable: true,
		spaceBetween: 10,
		touchRatio: .5,
		scrollbarHide: false,
		breakpoints: {
			360: {
				slidesPerView: 2
			}
		}
	});

	var gallery = {
		change: function(index) {
			$(".object-gallery-previews figure.is-active").removeClass('is-active');
			$(".object-gallery-previews figure").eq(index).addClass('is-active');
			$(".object-gallery-thumbs a.gallery-thumbs.is-active").removeClass('is-active');
			$(".object-gallery-thumbs a.gallery-thumbs").eq(index).addClass('is-active');
		}
	};

	$(document).on('click', '.object-gallery-thumbs a.gallery-thumbs:not(".is-active")', function(event) {
		event.preventDefault();
		var index = $(this).parent(".swiper-slide").index(),
			SwiperMiddle = +Math.floor(galleryThumbs.params.slidesPerView/2);
		if (index > 0 && index>=SwiperMiddle && SwiperMiddle >= 1) {
			var index1 = index-SwiperMiddle;
		}
		galleryThumbs.slideTo(index1);
		gallery.change(index);
	});

    $('.personal_manager_link').on('click', function () {
        smoothScroll('#personal_manager', {
            offsetTop: 0,
            duration: 600,
        });
    });

	var galleryPreviews = $("a.gallery-previews");
    if (!galleryPreviews.data('pswpUid')) {
        galleryPreviews.initPhotoSwipe({
            loop: true,
            events: {
                afterChange: function() {
                    if (!galleryThumbs.slides) return;

                    var index = this.getCurrentIndex();
                    //листаем примерно до середины
                    var SwiperMiddle = +Math.floor(galleryThumbs.params.slidesPerView/2);
                    if(index > 0 && index>=SwiperMiddle && SwiperMiddle >= 2) index = index-SwiperMiddle;
                    galleryThumbs.slideTo(index);
                    gallery.change(index);
                }
            },
            bgOpacity: .8
      });
    }

  $('#object-edit-link').show().appendTo('.image-actions');
  $('#object-publish-link').show().appendTo('.image-actions');

    var $body = $('body');
    $body.on('click', '.share-social__link', function (e) {
		e.preventDefault();
		$(this).closest('.js-popup').addClass('js-popup_open');
	});
    $body.on('click', '.js-popup__close', function (e) {
		e.preventDefault();
		$(this).closest('.js-popup').removeClass('js-popup_open');
	});

	cui.clickOff($('.js-popup'), function ($el) {
		$($el).removeClass('js-popup_open');
	});
};

$(readyFunc);
BX.addCustomEvent('onAjaxSuccess', readyFunc);
BX.addCustomEvent('onComponentAjaxHistorySetState', readyFunc);
