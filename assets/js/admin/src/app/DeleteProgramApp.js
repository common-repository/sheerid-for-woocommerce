import $ from 'jquery';
import {createRoot, render, useCallback, useEffect, useState} from "@wordpress/element";
import ProgramProvider from "../contexts/providers/ProgramProvider";
import {Button, Modal} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import {addAction, doAction} from "@wordpress/hooks";
import ModalActions from "../components/ModalActions";

export const DeleteProgramApp = () => {
    const [program, setProgram] = useState(null);
    const [deleting, setDeleting] = useState(false);

    const openModal = useCallback((e) => {
        const program = $(e.currentTarget).data('program');
        if (program) {
            setProgram(program);
        }
    }, []);

    useEffect(() => {
        $(document.body).on('click', '.delete-program', openModal);
        $(document.body).on('click', '.row-actions .delete', openModal);

        return () => {
            $(document.body).off('click', '.delete-program', openModal);
            $(document.body).off('click', '.row-actions .delete', openModal);
        }
    }, [program, openModal]);

    const onDelete = useCallback(async () => {
        setDeleting(true);
        try {
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'delete_program'),
                data: {
                    program
                }
            });
            setProgram(null);
            // trigger hook so other code can respond
            doAction('wc_sheerid_program_deleted', response);
        } catch (error) {
            console.log(error);
        } finally {
            setDeleting(false);
        }
    }, [program]);

    useEffect(() => {
        addAction('wc_sheerid_program_deleted', 'wcSheerID', response => {
            if (response.html) {
                $('.programs-table').replaceWith(response.html);
            }
        });
    }, []);

    if (Boolean(program)) {
        return (
            <Modal
                className={'program-modal'}
                style={{maxWidth: '500px !important'}}
                title={'Delete program?'}
                isDismissable={true}
                onRequestClose={() => setProgram(null)}>
                <div className='modal-content'>
                    <p>
                        {wcSheerIdApp.text.programDeleteNotice}
                    </p>
                </div>
                <ModalActions>
                    <Button
                        className={'sheerid-button'}
                        variant={'secondary'}
                        onClick={() => setProgram(null)}>
                        Cancel
                    </Button>
                    <Button
                        className={'sheerid-button delete-program-button'}
                        variant={'primary'}
                        isBusy={deleting}
                        disabled={deleting}
                        onClick={onDelete}>
                        {deleting ? 'Deleting...' : 'Delete'}
                    </Button>
                </ModalActions>
            </Modal>
        )
    }
}

const root = document.getElementById('program-delete-app');

if (root) {
    if (createRoot) {
        createRoot(root).render(<DeleteProgramApp/>);
    } else {
        render(<DeleteProgramApp/>, root);
    }
}

