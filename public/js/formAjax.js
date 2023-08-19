
$('#ajaxForm').ajaxForm({
    delegation: true,
    beforeSubmit: function (formData, jqForm, options) {

        $(jqForm[0])
            .find('.text-danger')
            .remove();

        var $submitButton = $(jqForm[0]).find('input[type=submit]');
	    $('.loading-modal').modal('show');

    },
    error: function (data, statusText, xhr, $form) {
        // Form validation error.
        if (422 == data.status) {
            processFormErrors($form, $.parseJSON(data.responseText));
            return;
        }
        var $submitButton = $form.find('input[type=submit]');
        toggleSubmitDisabled($submitButton);
    },
    success: function (data, statusText, xhr, $form) {
        switch (data.status) {
            case 'success':
                var $submitButton = $form.find('input[type=submit]');
                toggleSubmitDisabled($submitButton);
                if (typeof data.redirectUrl !== 'undefined') {
                    window.location.href = data.redirectUrl;
                }
                break;
            case 'error':
                processFormErrors($form, data.messages);
                break;

            default:
                break;
        }
    },
    dataType: 'json'
});

function toggleSubmitDisabled($submitButton) {
	$('.loading-modal').modal('hide');
}

function processFormErrors($form, errors) {
    $.each(errors.errors, function (index, error) {
        var $input = $(':input[name=' + index + ']', $form);
        $input.after('<span class="text-danger">' + error + '</span>');
    });

    var $submitButton = $form.find('input[type=submit]');
    toggleSubmitDisabled($submitButton);
}
