
(function ($) {

    $(function () {
        $('.checkbox input[type=checkbox]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
        $('.checkbox').addClass('icheck');
    })
})(jQuery);