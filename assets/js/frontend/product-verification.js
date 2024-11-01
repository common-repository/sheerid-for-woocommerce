import {Verification} from '@sheerid/helpers';
import $ from 'jquery';

class ProductVerification extends Verification {

    hooks() {
        $(document.body).on('click', '.wcSheerIDButton:not([data-redirect="true"])', this.onClick.bind(this));
    }

    beforeVerificationCreate() {
        $.blockUI(this.data.blockUI);
    }

    afterVerificationCreate() {
        $.unblockUI();
    }
}

(new ProductVerification()).initialize();