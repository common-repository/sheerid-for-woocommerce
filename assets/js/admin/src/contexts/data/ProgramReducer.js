import {default as actions} from './actions';

export const DEFAULT_STATE = {
    segment: null,
    segments: [],
    status: {
        creating: false,
        loading: false
    },
    program: null
}

export default function ProgramReducer(state = DEFAULT_STATE, payload) {
    switch (payload.action) {
        case actions.SET_SEGMENTS:
            state = {
                ...state,
                segments: payload.segments
            };
            break;
        case actions.SET_SEGMENT:
            state = {
                ...state,
                segment: payload.segment
            };
            break;
        case actions.STATUS:
            state = {
                ...state,
                status: {
                    ...state.status,
                    ...payload.status
                }
            };
            break;
        case actions.LOADING:
            state = {
                ...state,
                status: {
                    ...state.status,
                    loading: payload.loading
                }
            };
            break;
        case actions.UPDATING:
            state = {
                ...state,
                status: {
                    ...state.status,
                    updating: payload.updating
                }
            };
            break;
        case actions.CREATING:
            state = {
                ...state,
                status: {
                    ...state.status,
                    creating: payload.creating
                }
            };
            break;
        case actions.SET_PROGRAM:
            state = {
                ...state,
                program: payload.program
            }
            break;

    }
    return state;
}