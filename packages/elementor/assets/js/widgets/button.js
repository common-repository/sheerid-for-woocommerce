import {Verification} from "@sheerid/helpers";

class ButtonWidget extends Verification {

    beforeVerificationCreate() {
        this.$button.prop('disabled', true);
        this.$button.find('.elementor-button-text').text(this.data.text.loading);
    }

    afterVerificationCreate() {
        this.$button.prop('disabled', false);
        this.$button.find('.elementor-button-text').text(this.data.text.label);
    }
}

const button = new ButtonWidget();

button.initialize();