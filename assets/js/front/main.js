jQuery(document).ready(function($){
    const main = {
        ajax: aleproperty_data.ajax, //var added by wp_localize_script
        i18n: aleproperty_data.i18n.validation, //var added by wp_localize_script
        init: function(){
            this.wishlistHandler();
        },
        wishlistHandler: function () {
            $('.aleproperty-wishlist-btn').on('click', function(e){
                e.preventDefault();

                const data = {
                    action: 'add_property_to_wishlist',
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
        }
    }

    main.init();
})
