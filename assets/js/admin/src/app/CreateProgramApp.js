import {createRoot, render, useState, useEffect, useCallback} from '@wordpress/element';
import {addAction} from "@wordpress/hooks";
import $ from 'jquery';
import {Modal, Button} from '@wordpress/components';
import ProgramProvider from "../contexts/providers/ProgramProvider";
import ProgramModal from "../components/ProgramModal";
import {useProgramContext} from "../contexts/hooks";
import NoticeProvider from "../contexts/providers/NoticeProvider";
import apiFetch from "@wordpress/api-fetch";

const CreateProgramApp = () => {
    const [open, setOpen] = useState(false);
    const {status, segments, fetchSegments} = useProgramContext();

    const onClick = useCallback(async (e) => {
        const $button = $(e.currentTarget);
        const text = $button.text();
        if (!segments.length) {
            try {
                $button.prop('disabled', true);
                $button.text($button.data('processing-text'));
                await fetchSegments();
            } catch (error) {

            } finally {
                $button.prop('disabled', false);
                $button.text(text);
            }
            setOpen(true);
        } else {
            setOpen(true);
        }
    }, [segments]);

    const onSyncPrograms = useCallback(async (e) => {
        const $button = $(e.currentTarget);
        const text = $button.text();
        try {
            $button.prop('disabled', true);
            $button.text($button.data('processing-text'));
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'sync_programs'),
            });
            if (response.html) {
                $('.programs-table').replaceWith(response.html);
            }
        } catch (error) {
            alert(error.message);
        } finally {
            $button.prop('disabled', false);
            $button.text(text);
        }
    }, []);

    const onClose = useCallback(() => {
        setOpen(false);
    }, []);

    useEffect(() => {
        const button = document.getElementById('sheerid-add-program');
        button.addEventListener('click', onClick);
        return () => button.removeEventListener('click', onClick);
    }, [onClick]);

    useEffect(() => {
        const button = document.getElementById('sheerid-sync-programs');
        button.addEventListener('click', onSyncPrograms);
        return () => button.removeEventListener('click', onSyncPrograms);
    }, []);

    useEffect(() => {
        addAction('wc_sheerid_program_created', 'wcSheerID', response => {
            if (response.html) {
                $('.programs-table').replaceWith(response.html);
            }
        });
    }, []);

    return (
        <>
            <NoticeProvider>
                <ProgramModal
                    open={open}
                    onClose={onClose}/>
            </NoticeProvider>
        </>
    )
}

const root = document.getElementById('sheerid-program-app');

if (root) {
    if (createRoot) {
        createRoot(root).render(<ProgramProvider><CreateProgramApp/></ProgramProvider>);
    } else {
        render(<ProgramProvider><CreateProgramApp/></ProgramProvider>, root);
    }
}

export {CreateProgramApp};