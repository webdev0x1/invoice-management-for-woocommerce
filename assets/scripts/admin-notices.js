jQuery(document).ready(function($) {
    $(document).on('click', '#inv_an1 .notice-dismiss', function(event) {
        data = {
            action : 'inv_supplier_dismiss_admin_notice',
        };

    $.post(ajaxurl, data, function (response) {
            console.log(response, 'DONE!');
        });
    });
});
