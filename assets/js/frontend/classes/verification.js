import sheerId from '@sheerid';
import apiFetch from "@wordpress/api-fetch";
import $ from 'jquery';

class Verification {

    constructor(props) {
        this.searchParams = null;
    }


    initialize() {
        this.hooks();
        this.readSearchParams();
    }

    hooks() {
        $(document.body).on('click', '.wcSheerIDButton', this.onClick.bind(this));
        $(document).ready(this.launchOnPageLoad.bind(this));
    }

    readSearchParams() {
        if (window.location.search) {
            this.searchParams = new URLSearchParams(window.location.search);
        }
    }

    async onClick(e) {
        e.preventDefault();

        if (this.$button && this.$button.is('.processing')) {
            return;
        }

        this.$button = $(e.currentTarget);
        this.$button.addClass('processing');

        this.data = getVerificationData(this.$button);

        await this.renderVerificationScreen();

    }

    async renderVerificationScreen() {
        //create the verification object
        try {
            this.beforeVerificationCreate();

            const response = await apiFetch({
                method: 'post',
                url: this.data.routes.verification,
                data: {
                    program: this.data.program,
                    page_id: this.data.page_id,
                    context_args: this.data.context_args
                }
            });
            if (response.url) {
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
        } catch (error) {
            console.log(error);
        } finally {
            this.$button.removeClass('processing');
            this.afterVerificationCreate();
        }
    }

    beforeVerificationCreate() {
        if (this.$button) {
            this.$button.prop('disabled', true);
            if (this.data?.text?.loading) {
                this.$button.text(this.data.text.loading);
            }
        }
    }

    afterVerificationCreate() {
        if (this.$button) {
            this.$button.prop('disabled', false);
            if (this.data?.text?.label) {
                this.$button.text(this.data.text.label);
            }
        }
    }

    launchOnPageLoad() {
        if (this.searchParams && this.searchParams.has('launchModal')) {
            $('.wcSheerIDButton').trigger('click');
        }
    }
}

export default Verification;

/**
 *
 * @param $el jQuery element
 */
const getVerificationData = $el => {
    const data = $el.data('sheerid');
    if (data) {
        return data;
    }
    return getVerificationData($el.parent());
}