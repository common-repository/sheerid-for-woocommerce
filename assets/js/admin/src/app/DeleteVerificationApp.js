import $ from 'jquery';
import {createRoot, render, useCallback, useEffect, useState} from "@wordpress/element";
import {Button, Modal} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import ModalActions from "../components/ModalActions";

export const DeleteVerificationApp = () => {
    const [verification, setVerification] = useState(null);
    const [deleting, setDeleting] = useState(false);

    const onDelete = useCallback(async () => {
        setDeleting(true);
        try {
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'delete_verification'),
                data: {
                    verification: verification.id
                }
            });
            setVerification(null);
            if (response.html) {
                $('.verifications-table').replaceWith(response.html);
            }
        } catch (error) {
            console.log(error);
        } finally {
            setDeleting(false);
        }
    }, [verification]);

    const openModal = useCallback((e) => {
        setVerification($(e.currentTarget).data('verification'));
    }, []);

    useEffect(() => {
        $(document.body).on('click', '.row-actions .delete a', openModal);

        return () => {
            $(document.body).on('click', '.row-actions .delete a', openModal);
        }
    }, [openModal])

    if (verification) {
        return (
            <Modal
                className={'program-modal'}
                style={{maxWidth: '500px !important'}}
                title={wcSheerIdApp.text.deleteVerification}
                isDismissable={true}
                onRequestClose={() => setVerification(null)}>
                <div className='modal-content'>
                    <p>
                        {wcSheerIdApp.text.verificationDeleteNotice}
                    </p>
                </div>
                <ModalActions>
                    <Button
                        className={'sheerid-button'}
                        variant={'secondary'}
                        onClick={() => setVerification(false)}>
                        {wcSheerIdApp.text.cancel}
                    </Button>
                    <Button
                        className='delete-verification-button sheerid-button'
                        variant={'primary'}
                        isBusy={deleting}
                        disabled={deleting}
                        onClick={onDelete}>
                        {deleting ? wcSheerIdApp.text.deleting : wcSheerIdApp.text.delete}
                    </Button>
                </ModalActions>

            </Modal>
        )
    }
    return null;
}

const root = document.getElementById('verification-delete-app');

if (root) {
    if (createRoot) {
        createRoot(root).render(<DeleteVerificationApp/>);
    } else {
        render(<DeleteVerificationApp/>, root);
    }
}