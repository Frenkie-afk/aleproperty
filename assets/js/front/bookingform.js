jQuery(document).ready(function($){
    const booking = {
        ajax: aleproperty_data.ajax, //var added by wp_localize_script
        i18n: aleproperty_data.i18n.validation, //var added by wp_localize_script
        init: function(){
            this.bookingForm();
        },
        bookingForm: function () {
            $('#aleproperty-booking-submit').on('click', function(e){
                e.preventDefault();

                const bookingForm =  $('#aleproperty-booking-form');
                const bookingResultContainer = $('#aleproperty-booking-result');
                const data = {
                    action: 'booking_form',
                    security: booking.ajax.nonce,
                    property: $('#aleproperty-property-title').text().trim(),
                    name: $('#aleproperty-booking-name').val().trim(),
                    email: $('#aleproperty-booking-email').val().trim(),
                    phone: $('#aleproperty-booking-phone').val().trim()
                };

                //basic validation
                //todo: make advance validation(email, phone, etc.) with separate validation methods
                if (!data.name || !data.email || !data.phone) {
                    bookingForm.addClass('aleproperty-validation-error');
                    bookingResultContainer.html(booking.i18n.empty_fields).addClass('aleproperty-form-error');
                    return;
                } else {
                    bookingForm.removeClass('aleproperty-validation-error');
                    bookingResultContainer.html('').removeClass('aleproperty-form-error');
                }

                bookingForm.addClass('aleproperty-form-processing'); // add opacity class
                $(this).prop('disabled', true);// disable submit button

                $.post(booking.ajax.url, data, function(response){
                    //delete form
                    bookingForm.remove();
                    // console.log(response);
                    $(bookingResultContainer).html(response.data).addClass('aleproperty-form-success');
                }, 'json').always(function () {
                    bookingForm.removeClass('aleproperty-form-processing'); //remove opacity class
                    $(this).prop('disabled', false);
                }).fail(function(error){
                    // console.log(error.responseJSON.data.error);

                    $(bookingResultContainer).html(error.responseJSON.data.error).addClass('aleproperty-form-error');
                })
            });
        }
    }

    booking.init();
})
