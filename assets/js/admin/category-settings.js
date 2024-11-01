import $ from 'jquery';

function onRequireCartChange() {
    const $checkbox = $('[name="sheerid[require_before_cart]"]');
    let hideClasses = '.hide_if_require_before_cart, .hide_if_not_require_before_cart';

    $(hideClasses).show();

    if (!$checkbox.is(':checked')) {
        hideClasses = '.hide_if_not_require_before_cart';
        $(hideClasses).hide();
    }

}

function onClickHereChange() {
    const $checkbox = $('[name="sheerid[click_here]"]');
    let hideClasses = '.hide_if_click_here, .hide_if_not_click_here';

    $(hideClasses).show();

    if (!$checkbox.is(':checked')) {
        hideClasses = '.hide_if_not_click_here';
        $(hideClasses).hide();
    }
}

function onClickHereBehaviorChange() {
    const $select = $('[name="sheerid[click_here_behavior]"]');
    const value = $select.val();

    let showClasses = '[class^="show_if_click_here_behavior"]';

    $(showClasses).hide();

    $('.show_if_click_here_behavior_' + value).show();

    if (value === 'stay') {
        $('.hide_if_click_here_behavior_stay').hide();
    }
}

$(document.body).on('change', '[name="sheerid[require_before_cart]"]', onRequireCartChange);
$(document.body).on('change', '[name="sheerid[click_here]"]', onClickHereChange);
$(document.body).on('change', '[name="sheerid[click_here_behavior]"]', onClickHereBehaviorChange);

$(() => {
    onRequireCartChange();
    onClickHereChange();
    onClickHereBehaviorChange();
})