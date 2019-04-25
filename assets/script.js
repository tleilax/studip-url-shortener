/*jslint browser: true*/
/*global jQuery, STUDIP, _*/
(function ($, STUDIP, _) {
    'use strict';

    var last_value = null;

    $(document).on('keyup', '.url-add input[name="keyword"]', _.debounce(function () {
        if (this.value.trim().length > 0 && this.value === last_value) {
            return;
        }

        last_value = this.value;

        var url = STUDIP.URLHelper.getURL('api.php/url-shortener/keyword', {
            keyword: this.value
        }, true);

        $(this).removeClass('keyword-available keyword-unavailable').addClass('loading');

        $.get(url).fail(function () {
            this.setCustomValidity('');

            $(this).addClass('keyword-available').attr(
                'title',
                'Dieser Kurzlink ist verfügbar.'.toLocaleString()
            ).closest('form').removeAttr('data-invalid');
        }.bind(this)).done(function () {
            this.setCustomValidity('Dieser Kurzlink ist nicht verfügbar.'.toLocaleString());

            $(this).addClass('keyword-unavailable').attr(
                'title',
                'Dieser Kurzlink ist nicht verfügbar.'.toLocaleString()
            ).closest('form').attr('data-invalid', 1);
        }.bind(this)).always(function () {
            $(this).removeClass('loading');
        }.bind(this));
    }, 300)).on('submit', '.url-add[data-invalid]', function () {
        return false;
    });

}(jQuery, STUDIP, _));
