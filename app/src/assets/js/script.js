;
(function($) {
    $(function() {
        $('input').on('focus', function() {
            $(this).closest('ion-item').addClass('input-active');
        })
    });
})(jQuery)