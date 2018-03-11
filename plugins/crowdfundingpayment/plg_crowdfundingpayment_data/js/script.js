jQuery(document).ready(function() {

    jQuery("#js-cfdata-btn-submit").on("click", function(event){
		event.preventDefault();

        var $loader = jQuery("#js-cfdata-ajax-loading");
        var $form   = jQuery("#js-cfdata-form");
        var $alert  = jQuery("#js-cfdata-alert");
        var $alertText  = jQuery("#js-cfdata-alert-text");

		jQuery.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: $form.serialize(),
            dataType: "text json",
            cache: false,
            beforeSend: function () {
                $loader.show();
                $alert.hide();
            }
        }).done(function(response) {
            $loader.hide();

            // Display the button that points to next step
            if(response.success) {

                // Hide the submit button.
                jQuery("#js-cfdata-btn-submit").hide();

                // Show the submit button.
                jQuery("#js-continue-btn").show();
            } else {
                $alertText.text(response.text).show();
                $alert.show();
            }
        });
    });
});