import {useState, useEffect, useCallback} from "@wordpress/element";
import {useSelect, dispatch} from '@wordpress/data';
import {CHECKOUT_STORE_KEY} from '@woocommerce/block-data';
import sheerId from '@sheerid';
import apiFetch from "@wordpress/api-fetch";
import {getSetting} from '@woocommerce/settings';

const checkoutData = getSetting('sheerIDCheckout_data');

export const Block = ({checkoutExtensionData, extensions}) => {
    const [processing, setProcessing] = useState(false);
    const [extensionData, setExtensionData] = useState(null);

    const {
        hasError
    } = useSelect(select => {
        const store = select(CHECKOUT_STORE_KEY);
        return {
            hasError: store.hasError()
        }
    });

    const {removeNotice} = dispatch('core/notices');

    useEffect(() => {
        setExtensionData(extensions);
    }, [extensions]);

    useEffect(() => {
            if (hasError && extensionData?.sheerId?.data) {
                //removeNotice('sheerid_invalid_verification', 'wc/checkout');
                createVerification(extensions.sheerId.data);
            }
        }, [
            hasError,
            extensionData,
            setExtensionData,
            createVerification
        ]
    );

    const createVerification = useCallback(async (data) => {
        try {
            if (processing) {
                return false;
            }
            setProcessing(true);
            const response = data;
            if (data.url) {
                const modalInstance = sheerId.loadInModal(data.url, {
                    mobileRedirect: false
                });

                if (data.view_model) {
                    modalInstance.setViewModel(data.view_model);
                }
                modalInstance.setOptions({
                    customCss: data.customCss,
                    messagesWithLocale: data.messages
                });
                modalInstance.iframeInstance.onCleanupEvents.push(() => {
                    setProcessing(false);
                    setExtensionData(null);
                });
            }
        } catch (error) {
            console.log(error);
        } finally {

        }
    }, [processing]);

    return (
        <div className={'sheerid-verification-block'}></div>
    );
}