import {Verification} from '@sheerid/helpers';
import $ from 'jquery';

class CheckoutVerification extends Verification {

    constructor(data) {
        super();
        this.wcCheckoutForm = null;
        this.data = data;
    }

    hooks() {
        $('form.checkout').on('checkout_place_order', this.onCheckoutPlaceOrder.bind(this));
        $(window).on('load', this.renderOnPageLoad.bind(this));
        window.addEventListener('hashchange', this.onHashChange.bind(this));
    }

    onCheckoutPlaceOrder(e, wc_checkout_form) {
        this.wcCheckoutForm = wc_checkout_form;
    }

    async onHashChange(e) {
        const match = window.location.hash.match(/sheerid-response=(.*)/);
        if (match) {
            history.pushState({}, '', window.location.pathname);
            const obj = JSON.parse(window.atob(decodeURIComponent(match[1])));
            if (this.wcCheckoutForm) {
                this.wcCheckoutForm.$checkout_form.removeClass('processing').unblock();
            }

            // launch the verification modal
            await this.renderVerificationScreen(obj);
        }
    }

    async renderVerificationScreen(response) {
        this.modalInstance = sheerId.loadInModal(response.url, {
            mobileRedirect: false
        });
        if (response.view_model) {
            this.modalInstance.setViewModel(response.view_model);
        }
        this.modalInstance.setOptions({
            customCss: this.data.customCss,
            messagesWithLocale: response.messages
        });
    }

    async renderOnPageLoad() {
        if (this.data.config.onPageLoad && this.data.config.needsVerification) {
            await super.renderVerificationScreen();
        }
    }
}

if (typeof wc_sheerid_checkout_verification !== 'undefined') {
    (new CheckoutVerification(wc_sheerid_checkout_verification)).initialize();
}