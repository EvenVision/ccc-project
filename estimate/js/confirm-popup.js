(function($, Drupal) {
    //Popup in function so we can pass content title to the popup
    function Confirm_popup(form_id) {
        var content = '<div>Are you sure you want to submit?</div>';
        confirmationDialog = Drupal.dialog(content, {
            dialogClass: 'confirm-dialog',
            resizable: true,
            closeOnEscape: false,
            width: 600,
            title: "Confirmation",
            buttons: [
                {
                    text: 'Confirm',
                    class: 'button--primary button',
                    click: function() {
                        $("#" + form_id).submit();
                    }
                },
                {
                    text: 'Close',

                    click: function() {

                        $(this).dialog('close');

                    }
                }
            ],
        });
        confirmationDialog.showModal();
    }
    // call to function to open popup
    $("#edit-submit").click(function(e) {
        let form_id = $(this).closest("form").attr("id");
        e.preventDefault();
        Confirm_popup(form_id);
    });
})(jQuery, Drupal);