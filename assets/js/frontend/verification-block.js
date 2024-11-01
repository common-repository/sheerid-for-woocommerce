import {Verification} from '@sheerid/helpers';

class VerificationBlock extends Verification {

    beforeVerificationCreate() {
        if (this.data.text.loading) {
            this.$button.find('a').text(this.data.text.loading);
        }
    }

    afterVerificationCreate() {
        this.$button.find('a').text(this.data.text.label);
    }
}

(new VerificationBlock()).initialize();