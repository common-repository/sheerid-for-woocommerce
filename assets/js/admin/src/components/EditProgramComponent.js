import {Button, Flex, FlexItem, TabPanel} from "@wordpress/components";
import $ from 'jquery';
import {useProgramContext} from "../contexts/hooks";
import ModalActions from "./ModalActions";
import * as Programs from './programs';
import {FormProvider, RHFTextField, RHFCheckbox} from "./form";
import {useForm} from "react-hook-form";
import * as Yup from 'yup';
import {yupResolver} from "@hookform/resolvers/yup";
import {useCallback, useEffect, useState} from "@wordpress/element";
import useNoticeContext from "../contexts/hooks/useNoticeContext";
import apiFetch from "@wordpress/api-fetch";
import TabContent from "./TabContent";
import ProgramEligibility from "./ProgramEligibility";

export default function EditProgramComponent({activeTab, onClose}) {
    const {program, setProgram, updateProgram, status} = useProgramContext();
    const {addErrorNotice, addSuccessNotice} = useNoticeContext();
    const [creatingWebhook, setCreatingWebhook] = useState(false);

    const ProgramComponent = getProgram(program.segmentDescription.name);

    const onCreateWebhook = useCallback(async () => {
        try {
            setCreatingWebhook(true);
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'create_webhook'),
                data: {
                    program_id: program.id
                }
            });
            if (response.webhooks) {
                setProgram({
                    ...program,
                    webhooks: response.webhooks
                });
            }
        } catch (error) {
            addErrorNotice(error);
        } finally {
            setCreatingWebhook(false);
        }
    }, [program, setProgram, addErrorNotice]);

    const props = {
        defaultValues: {
            name: program.name,
            ...(ProgramComponent && ProgramComponent.defaultValues(program)),
            ...{
                emails: program.emails
            }
        },
        resolver: yupResolver(
            Yup.object(ProgramComponent ? ProgramComponent.schema : {}).shape({
                name: Yup.string().required()
            })
        )
    };
    const methods = useForm(props);

    const {handleSubmit, trigger} = methods;

    const onSubmit = handleSubmit(async data => {
        try {
            const response = await updateProgram(program.id, data);
            if (response.code) {
                addErrorNotice(response.message, {context: 'programModal'});
            } else {
                addSuccessNotice(response.message, {context: 'programModal'});
                if (response.html) {
                    $('.programs-table').replaceWith(response.html);
                }
            }
        } catch (error) {
            addErrorNotice(error.message, {context: 'programModal'});
        }
    });

    const onClickUpdate = useCallback(async () => {
        try {
            await trigger();
            await onSubmit();
        } catch (error) {
            console.log(error);
        }
    }, [trigger]);

    const hasWebhook = program?.webhooks?.length > 0;

    return (
        <>
            <FormProvider methods={methods} className={'edit-program-form'}>
                <TabContent tab={'eligibility'} activeTab={activeTab}>
                    <ProgramEligibility program={program} ProgramComponent={ProgramComponent}/>
                </TabContent>
                <TabContent tab={'offer'} activeTab={activeTab}>
                    <Flex direction={'column'}>
                        <FlexItem>
                            <p dangerouslySetInnerHTML={{__html: wcSheerIdApp.text.offerDescription}}/>
                        </FlexItem>
                    </Flex>
                </TabContent>
                <TabContent tab={'text'} activeTab={activeTab}>
                    <Flex direction={'column'}>
                        <FlexItem>
                            <p dangerouslySetInnerHTML={{__html: wcSheerIdApp.text.textDescription}}/>
                        </FlexItem>
                    </Flex>
                </TabContent>
                <TabContent tab={'emails'} activeTab={activeTab}>
                    <p className={'description'}>{wcSheerIdApp.text.emailDesc}</p>
                    <Flex direction={'row'} className={'sheerid-items__container'}>
                        <FlexItem>
                            <RHFCheckbox
                                name={'emails.success'}
                                label={'Verification Success Email'}/>
                        </FlexItem>
                        <FlexItem>
                            <RHFCheckbox
                                name={'emails.failure'}
                                label={'Verification Failure'}/>
                        </FlexItem>
                        <FlexItem>
                            <RHFCheckbox
                                name={'emails.reminder'}
                                label={'Documents Reminder Email'}/>
                        </FlexItem>
                    </Flex>
                </TabContent>
                <TabContent tab={'general'} activeTab={activeTab}>
                    <Flex direction={'column'} align={'flex-start'} className={'sheerid-items__container webhook-domain'}>
                        <FlexItem>
                            <Flex direction={'column'} className={'sheerid-items__container'}>
                                <FlexItem>
                                    <RHFTextField
                                        style={{minWidth: '300px'}}
                                        name={'name'}
                                        label={'Display Name'}/>
                                </FlexItem>
                            </Flex>
                        </FlexItem>
                        <FlexItem style={{width: '100%'}}>
                            <Button
                                className='create-webhook'
                                variant={'secondary'}
                                disabled={creatingWebhook || hasWebhook}
                                isBusy={creatingWebhook}
                                onClick={onCreateWebhook}>
                                {creatingWebhook ? 'Creating...' : 'Create'}
                            </Button>
                            <table className={'widefat wp-list-table'}>
                                <thead>
                                <tr>
                                    <th>{wcSheerIdApp.text.webhookUrl}</th>
                                    <th>{wcSheerIdApp.text.actions}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {program.webhooks.map(webhook => (
                                    <WebhookTableRow key={webhook.callBackUri} webhook={webhook} program={program}/>
                                ))}
                                </tbody>
                            </table>
                        </FlexItem>
                    </Flex>
                </TabContent>
            </FormProvider>
            <ModalActions>
                <Button
                    className={'sheerid-button'}
                    variant={'secondary'}
                    onClick={onClose}>{wcSheerIdApp.text.close}</Button>
                <Button
                    className={'sheerid-button'}
                    variant={'primary'}
                    isBusy={status.updating}
                    disabled={status.updating}
                    onClick={onClickUpdate}
                >{status.updating ? wcSheerIdApp.text.saving : wcSheerIdApp.text.save}</Button>
            </ModalActions>
        </>
    )
}

const WebhookTableRow = (
    {
        webhook,
        program
    }) => {
    const {setProgram} = useProgramContext();
    const {addErrorNotice, addSuccessNotice} = useNoticeContext();
    const [deleting, setDeleting] = useState(false);

    const onDelete = useCallback(async () => {
        try {
            setDeleting(true);
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'delete_webhook'),
                data: {
                    ids: webhook.ids,
                    program_id: program.id
                }
            });
            if (response.webhooks) {
                setProgram({
                    ...program,
                    webhooks: response.webhooks
                });
            }
        } catch (error) {
            addErrorNotice(error);
        } finally {
            setDeleting(false);
        }
    }, [setProgram]);

    return (
        <tr>
            <td>
                <div>
                    {webhook.callBackUri}
                </div>
            </td>
            <td>
                <Button
                    variant={'secondary'}
                    size={'default'}
                    disabled={deleting}
                    isBusy={deleting}
                    onClick={onDelete}>
                    {deleting ? 'Deleting...' : 'Delete'}
                </Button>
            </td>
        </tr>
    )
}

const getProgram = id => {
    for (const key in Programs) {
        if (Programs[key].type === id) {
            return Programs[key];
        }
    }
    return null;
}