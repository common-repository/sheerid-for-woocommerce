import {useCallback, useState} from "@wordpress/element";
import {Modal, Button} from "@wordpress/components";
import Notices from "./Notices";
import ModalActions from "./ModalActions";
import {FormProvider, RHFTextField} from "./form";
import {useForm} from "react-hook-form";
import {yupResolver} from "@hookform/resolvers/yup";
import * as Yup from "yup";
import useNoticeContext from "../contexts/hooks/useNoticeContext";

export default function VerificationReminderModal(
    {
        verification,
        onClose,
        onSubmit,
        processing = false
    }) {
    const schema = Yup.object({
        email: Yup.string().required(wcSheerIdApp.text.errors.email_required)
    });

    const methods = useForm({
        defaultValues: {
            email: verification.email
        },
        resolver: yupResolver(schema)
    });

    const {trigger, handleSubmit} = methods;

    const onClick = useCallback(async () => {
        await handleSubmit(onSubmit)();
    }, [onSubmit]);

    return (
        <Modal
            className={'program-modal'}
            style={{maxWidth: '500px !important'}}
            title={wcSheerIdApp.text.sendReminderEmailTitle}
            isDismissable={true}
            onRequestClose={onClose}>
            <div className='modal-content'>
                <Notices context={'emailReminder'}/>
                <FormProvider methods={methods}>
                    <div>
                        <RHFTextField name={'email'} label={wcSheerIdApp.text.emailLabel}/>
                    </div>
                    <p>
                        {wcSheerIdApp.text.emailReminderNotice}
                    </p>
                </FormProvider>
            </div>
            <div className={'modal-actions'}>
                <Button variant={'secondary'} onClick={onClose}>{wcSheerIdApp.text.cancel}</Button>
                <Button
                    variant={'primary'}
                    isBusy={processing}
                    disabled={processing}
                    onClick={onClick}>
                    {processing ? wcSheerIdApp.text.sending : wcSheerIdApp.text.sendReminder}
                </Button>
            </div>

        </Modal>

    )
}