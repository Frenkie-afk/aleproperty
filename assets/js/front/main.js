jQuery(document).ready(function($){
    const main = {
        ajax: aleproperty_data.ajax, //var added by wp_localize_script
        i18n: aleproperty_data.i18n, //var added by wp_localize_script
        init: function(){
            this.wishlistHandler();
            this.removeWishlistItem();
        },
        wishlistHandler: function () {
            $('.aleproperty-wishlist-btn').on('click', function(e){
                e.preventDefault();

                const data = {
                    action: 'update-wishlist',
                    security: main.ajax.nonce,
                    item_id: $(this).data('property-id'),
                };

                $(this).prop('disabled', true);// disable submit button

                $.post(main.ajax.url, data, (response) => {
                    console.log(response.data); //todo: add snackbar with message?
                    $(this).toggleClass('item-added');
                }, 'json').always(()=> {
                    $(this).prop('disabled', false);
                }).fail(function(error){
                    console.log(error.responseJSON.data);
                });
            })
        },
        removeWishlistItem: function () {
            $('.aleproperty-wishlist-remove-item').on('click', function(e){
                e.preventDefault();
                console.log('fired');
                const wishlistContainer = $('.aleproperty-wishlist');
                const data = {
                    action: 'update-wishlist',
                    security: main.ajax.nonce,
                    item_id: $(this).data('property-id'),
                };

                $(this).prop('disabled', true);// disable submit button
                $(wishlistContainer).addClass('aleproperty-ajax-active');
                $.post(main.ajax.url, data, (response) => {
                    console.log(response.data);
                    $(this).parent('.aleproperty-wishlist-item').remove();

                    //empty wishlist state
                    if ( !wishlistContainer.find('.aleproperty-wishlist-item').length ) {
                        wishlistContainer.html(`<p style="text-align: center">${main.i18n.wishlist.empty_wishlist}</p>`)
                    }
                }).always(()=> {
                    $(this).prop('disabled', false);
                    $(wishlistContainer).removeClass('aleproperty-ajax-active');
                }).fail(function(error){
                    console.log(error.responseJSON.data);
                });
            })
        }
    }

    main.init();
})
