import {createRoot, render, useState, useEffect, useCallback} from '@wordpress/element';
import $ from 'jquery';
import {Button, Modal} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import NoticeProvider from "../contexts/providers/NoticeProvider";
import {FormProvider, RHFTextField, RHFCheckbox, RHFSelect} from "../components/form";
import {useForm} from "react-hook-form";
import * as Yup from 'yup';
import {yupResolver} from "@hookform/resolvers/yup";
import VerificationReminderModal from "../components/VerificationReminderModal";
import useNoticeContext from "../contexts/hooks/useNoticeContext";

export const VerificationReminderEmailApp = () => {
    const [verification, setVerification] = useState(null);
    const [processing, setProcessing] = useState(false);
    const {addSuccessNotice, addErrorNotice} = useNoticeContext();

    const openModal = useCallback((e) => {
        setVerification($(e.currentTarget).data('verification'));
    }, []);

    const onSendReminder = useCallback(async (data) => {
        try {
            setProcessing(true);
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'send_verification_reminder'),
                data: {
                    email: data.email,
                    verification: verification.id
                }
            });
            if (response.success) {
                addSuccessNotice(response.message, {context: 'emailReminder'});
            } else {
                addErrorNotice(response.message, {context: 'emailReminder'});
            }
        } catch (error) {
            console.log(error);
        } finally {
            setProcessing(false);
        }
    }, [verification, addErrorNotice, addSuccessNotice]);

    useEffect(() => {
        $(document.body).on('click', '.row-actions .send_reminder a', openModal);

        return () => {
            $(document.body).off('click', '.row-actions .send_reminder a', openModal);
        }
    }, [openModal]);

    if (verification) {
        return (
            <VerificationReminderModal
                verification={verification}
                onClose={() => setVerification(null)}
                onSubmit={onSendReminder}
                processing={processing}
            />
        )
    }
    return null;
}

const root = document.getElementById('sheerid-verification-reminder-app');

if (root) {
    if (createRoot) {
        createRoot(root).render(<NoticeProvider><VerificationReminderEmailApp/></NoticeProvider>);
    } else {
        render(<NoticeProvider><VerificationReminderEmailApp/></NoticeProvider>, root);
    }
}