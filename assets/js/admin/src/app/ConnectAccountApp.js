import {createRoot, render, useState, useEffect, useCallback} from '@wordpress/element';
import $ from 'jquery';
import ConnectModal from "../components/ConnectModal";
import NoticeProvider from "../contexts/providers/NoticeProvider";

let isModalOpen = false;

const ConnectAccountApp = () => {
    const [modalOpen, setModalOpen] = useState(false);

    const listener = useCallback(e => {
        e.preventDefault();
        setModalOpen(true);
    }, []);

    useEffect(() => {
        $(document.body).on('click', '.connect-sheerid-account', listener);
        return () => $(document.body).off('click', '.connect-sheerid-account', listener);
    }, [listener]);

    if (modalOpen) {
        return <ConnectModal onClose={() => setModalOpen(false)}/>
    }
    return null;
}

const root = document.getElementById('sheerid-connect-app');

if (root) {
    if (createRoot) {
        createRoot(root).render(<NoticeProvider><ConnectAccountApp/></NoticeProvider>);
    } else {
        render(<NoticeProvider><ConnectAccountApp/></NoticeProvider>, root);
    }
}