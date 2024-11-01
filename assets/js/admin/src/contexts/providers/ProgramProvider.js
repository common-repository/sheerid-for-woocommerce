import {createContext, useState, useCallback, useReducer} from '@wordpress/element';
import reducer, {DEFAULT_STATE} from '../data/ProgramReducer';
import {default as actions} from '../data/actions';
import apiFetch from "@wordpress/api-fetch";
import {doAction} from "@wordpress/hooks";


export const ProgramContext = createContext({
    segments: [],
    segment: {
        id: null
    }
});

export const ProgramProvider = ({children}) => {
    const [state, dispatch] = useReducer(reducer, DEFAULT_STATE);
    const {
        segment,
        segments,
        status,
        program,
    } = state;

    const fetchSegments = useCallback(async () => {
        try {
            dispatch({action: actions.LOADING, loading: true});
            const response = await apiFetch({
                method: 'get',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'fetch_segments')
            });
            dispatch({action: actions.SET_SEGMENTS, segments: response.segments});
        } catch (error) {
            console.log(error);
        } finally {
            dispatch({action: actions.LOADING, loading: false});
        }
    });

    const selectSegment = useCallback(segment => {
        dispatch({action: actions.SET_SEGMENT, segment});
    }, []);

    const setProgram = useCallback((program) => {
        dispatch({action: actions.SET_PROGRAM, program});
    }, []);

    const fetchProgram = useCallback(async program => {
        try {
            dispatch({action: actions.STATUS, status: {fetching: true}});
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'fetch_program'),
                data: {
                    program
                }
            });
            if (response.program) {
                setProgram(response.program);
            }
        } catch (error) {
            console.log(error);
        } finally {
            dispatch({action: actions.STATUS, status: {fetching: false}});
        }
    });

    const createProgram = useCallback(async name => {
        try {
            dispatch({action: actions.CREATING, creating: true});
            const response = await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'create_program'),
                data: {
                    segmentName: name
                }
            });
            if (response.program) {
                setProgram(response.program);
            }
            doAction('wc_sheerid_program_created', response);
            return response;
        } catch (error) {
            console.log(error);
        } finally {
            dispatch({action: actions.CREATING, creating: false});
        }
    }, [setProgram]);

    const updateProgram = useCallback(async (programId, data) => {
        try {
            dispatch({action: actions.STATUS, status: {updating: true}});
            return await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'update_program'),
                data: {
                    ...data,
                    program_id: programId
                }
            });
        } catch (error) {

        } finally {
            dispatch({action: actions.STATUS, status: {updating: false}});
        }
    }, []);

    const toggleProgramMode = useCallback(async programId => {
        try {
            return await apiFetch({
                method: 'post',
                url: wcSheerIdApp.ajaxUrl.replace('%%action%%', 'toggle_program_mode'),
                data: {
                    program_id: programId
                }
            });
        } catch (error) {

        } finally {

        }
    }, []);

    const context = {
        program,
        status,
        segment,
        segments,
        fetchSegments,
        selectSegment,
        setProgram,
        fetchProgram,
        updateProgram,
        createProgram,
        toggleProgramMode
    };

    return (
        <ProgramContext.Provider value={context}>
            {children}
        </ProgramContext.Provider>
    )
}

export default ProgramProvider;