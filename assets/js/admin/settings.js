import $ from 'jquery';

$(document.body).on('init_tooltips', () => {
    $('.woocommerce-help-tip').tipTip({
        attribute: 'data-tip',
        fadeIn: 50,
        fadeOut: 50,
        delay: 200,
        keepAlive: true,
    });
});

$(document.body).triggerHandler('init_tooltips');

if (!window.fetch) {
    $('.sheerid-webhook-test').closest('tr').hide();
}

$(document.body).on('click', '.sheerid-webhook-test', async e => {
    const $el = $(e.currentTarget);
    const text = $el.text();
    $el.text($el.data('processing-text'));
    $el.prop('disabled', true);
    try {
        const result = await fetch($el.data('rest-url'), {
            method: 'POST',
            credentials: 'omit',
            headers: {
                'x-sheerid-webhook-type': 'TEST',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                webhook_nonce: $el.data('nonce'),
            })
        });
        const response = await result.json();
        if (result.ok) {
            window.alert($el.data('success-message'));
        } else {
            switch (result.status) {
                case 401:
                case 403:
                    window.alert($el.data('error-texts')[result.status]);
                    break;
                default:

            }
        }
    } catch (error) {
        alert(error.message);
    } finally {
        $el.text(text);
        $el.prop('disabled', false);
    }
});