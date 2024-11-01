import {useState} from '@wordpress/element';
import {Modal, Button, Flex, FlexItem, TabPanel} from "@wordpress/components";
import $ from 'jquery';
import * as Yup from 'yup';
import {yupResolver} from "@hookform/resolvers/yup";
import ModalActions from "./ModalActions";
import {FormProvider, RHFTextField} from "./form";
import {useForm} from "react-hook-form";
import apiFetch from "@wordpress/api-fetch";
import Notices from "./Notices";
import useNoticeContext from "../contexts/hooks/useNoticeContext";

export default function ConnectModal(
    {
        onClose
    }) {
    const [processing, setProcessing] = useState(false);
    const [connected, setConnected] = useState(false);
    const [activeTab, setActiveTab] = useState('login');
    const {addErrorNotice, addSuccessNotice} = useNoticeContext();
    const schema = Yup.object().shape({
        username: Yup.string().required(),
        password: Yup.string().required()
    });

    const defaultValues = {
        username: '',
        password: ''
    }

    const methods = useForm({
        defaultValues,
        resolver: yupResolver(schema)
    });

    const {handleSubmit, trigger} = methods;

    const onSubmit = handleSubmit(async data => {
        try {
            setProcessing(true);
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'connect_plugin'),
                data: {
                    username: data.username,
                    password: data.password
                }
            });
            if (response.code) {
                addErrorNotice(response.message, {context: 'connectModal'});
            } else {
                addSuccessNotice(response.message, {context: 'connectModal'});
                if (response.html) {
                    $('.sheerid-api-settings').replaceWith(response.html);
                }
                setConnected(true);
            }
        } catch (error) {
            addErrorNotice(error.message);
        } finally {
            setProcessing(false);
        }
    });

    const onClickConnect = async () => {
        try {
            await trigger();
            await onSubmit();
        } catch (error) {
            console.log(error);
        }
    }

    const onTabChange = value => setActiveTab(value);

    return (
        <Modal
            className={'connect-modal'}
            title={'Connect Account'}
            isDismissable={true}
            onRequestClose={onClose}>
            <div className={'modal-content connect-modal-content'}>
                <Notices context={'connectModal'}/>
                <FormProvider methods={methods}>
                    <div className={'tabs-container'}>
                        <TabPanel tabs={wcSheerIdApp.connectTabs} onSelect={onTabChange}>
                            {tab => <p style={{display: 'none'}}>{tab.title}</p>}
                        </TabPanel>
                    </div>
                    {activeTab === 'login' &&
                        <Flex className={'connect-options-container'} direction={'column'}>
                            <FlexItem>
                                <RHFTextField
                                    name={'username'}
                                    label={'Email'}/>
                            </FlexItem>
                            <FlexItem>
                                <RHFTextField
                                    name={'password'}
                                    type={'password'}
                                    label={'Password'}/>
                                <p>
                                    {wcSheerIdApp.text.passwordNotice}
                                </p>
                            </FlexItem>
                        </Flex>}
                    {activeTab === 'register' &&
                        <div className={'register-container'}>
                            <p>{wcSheerIdApp.text.registerNotice}</p>
                            <ol>
                                {wcSheerIdApp.registerSteps.map(text => (
                                    <li key={text}>{text}</li>
                                ))}
                            </ol>
                        </div>}
                </FormProvider>
                {activeTab === 'login' &&
                    <ModalActions>
                        <Button
                            className={'sheerid-button'}
                            variant={'secondary'}
                            onClick={onClose}>
                            {connected ? wcSheerIdApp.text.close : wcSheerIdApp.text.cancel}
                        </Button>
                        <Button
                            className={'sheerid-button'}
                            variant={'primary'}
                            onClick={onClickConnect}
                            disabled={processing}
                            isBusy={processing}>
                            {processing ? wcSheerIdApp.text.connecting : wcSheerIdApp.text.connect}
                        </Button>
                    </ModalActions>}
                {activeTab === 'register' &&
                    <ModalActions className={'register-tab-active'}>
                        <Button
                            className={'sheerid-button'}
                            variant={'primary'}
                            href={wcSheerIdApp.registerLink}
                            target={'_blank'}>
                            {wcSheerIdApp.text.createAccount}
                        </Button>
                    </ModalActions>
                }
            </div>
        </Modal>
    )
}