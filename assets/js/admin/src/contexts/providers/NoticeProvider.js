import {createContext, useState, useCallback, useReducer} from '@wordpress/element';

export const NoticeContext = createContext({});

export const NoticeProvider = ({children}) => {
    const [notices, setNotices] = useState([]);

    const addNotice = useCallback((msg, {context, type}) => {
        setNotices(prev => ({
            ...prev,
            [msg]: {
                msg,
                context,
                type
            }
        }));
    }, []);

    const addErrorNotice = useCallback((msg, {context}) => {
        addNotice(msg, {context, type: 'error'});
    }, [addNotice]);

    const addSuccessNotice = useCallback((msg, {context}) => {
        addNotice(msg, {context, type: 'success'});
    }, [addNotice]);

    const getNoticesByContext = useCallback((context) => {
        return Object.entries(notices).filter(([key, notice]) => {
            return notice.context === context;
        }).map(([key, notice]) => notice);
    }, [notices]);

    const removeNotice = useCallback((key) => {
        setNotices(prev => {
            delete prev[key];
            return {...prev};
        });
    }, []);

    const context = {
        notices,
        addNotice,
        removeNotice,
        addErrorNotice,
        addSuccessNotice,
        getNoticesByContext
    };

    return (
        <NoticeContext.Provider value={context}>
            {children}
        </NoticeContext.Provider>
    )
}

export default NoticeProvider;