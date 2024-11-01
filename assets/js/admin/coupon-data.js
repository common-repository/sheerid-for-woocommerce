import $ from 'jquery';

$(document.body).on('change', '.sheerid_settings_coupon_data :input', () => {
    hideAndShowOptions();
});

const hideAndShowOptions = () => {
    $('.sheerid_settings_coupon_data :input').each((idx, el) => {
        const $el = $(el);
        let showIfClass = `.show_if_${$el.attr('name')}_`;
        let hideIfClass = `.hide_if_${$el.attr('name')}_`;
        let conditions = [$el.val()];
        if ($el.is(':checkbox')) {
            conditions = $el.is(':checked') ? ['checked'] : ['unchecked'];
        }

        for (const condition of conditions) {

            // show all hidden elements
            $(hideIfClass + condition).show();
            $(showIfClass + condition).hide();

            $(showIfClass + condition).show();
            $(hideIfClass + condition).hide();
        }
    });
}

hideAndShowOptions();