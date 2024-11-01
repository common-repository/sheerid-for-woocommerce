import {createRoot, render, useState, useEffect, useCallback} from '@wordpress/element';
import $ from 'jquery';
import ProgramProvider from "../contexts/providers/ProgramProvider";
import ProgramModal from "../components/ProgramModal";
import useProgramContext from "../contexts/hooks/useProgramContext";
import NoticeProvider from "../contexts/providers/NoticeProvider";

const EditProgramApp = () => {
    const [open, setOpen] = useState(false);
    const {fetchProgram, toggleProgramMode} = useProgramContext();

    const onClick = useCallback(async (e) => {
        const $el = $(e.currentTarget);
        $el.closest('.menu-items-container').addClass('active');
        const text = $el.text();
        const programId = $el.data('program');
        $el.text($el.data('text'));
        await fetchProgram(programId);
        $el.text(text);
        setOpen(true);
        $el.closest('.menu-items-container').removeClass('active');
    }, []);

    const onToggleProgramMode = useCallback(async (e) => {
        const $el = $(e.currentTarget);
        $el.closest('.menu-items-container').addClass('active');
        const text = $el.text();
        const programId = $el.data('program');
        $el.text($el.data('text'));
        const result = await toggleProgramMode(programId);
        if (result.html) {
            if (result.html) {
                $('.programs-table').replaceWith(result.html);
            }
        }
        $el.text(text);
        $el.closest('.menu-items-container').removeClass('active');
    }, []);

    const onClose = useCallback(() => {
        setOpen(false);
    }, []);

    useEffect(() => {
        $(document.body).on('click', '.edit-program', onClick);
        $(document.body).on('click', '.row-actions a.edit', onClick);
        return () => {
            $(document.body).off('click', 'edit-program', onClick);
            $(document.body).off('click', '.row-actions a.edit', onClick);
        };
    }, [onClick]);

    useEffect(() => {
        $(document.body).on('click', '.toggle-program-mode', onToggleProgramMode);
        return () => $(document.body).off('click', 'edit-program', onToggleProgramMode);
    }, [onClick]);

    return (
        <ProgramModal
            step={'edit'}
            open={open}
            onClose={onClose}/>
    )
}

const root = document.getElementById('sheerid-edit-program-app');

if (root) {
    if (createRoot) {
        createRoot(root).render(<NoticeProvider><ProgramProvider><EditProgramApp/></ProgramProvider></NoticeProvider>);
    } else {
        render(<NoticeProvider><ProgramProvider><EditProgramApp/></ProgramProvider></NoticeProvider>, root);
    }
}

export {EditProgramApp};