import SegmentComponent from "./SegmentComponent";
import {Button, Flex, FlexItem} from "@wordpress/components";
import {useProgramContext} from "../contexts/hooks";
import {useCallback} from "@wordpress/element";
import ModalActions from "./ModalActions";

export default function SegmentsComponent(
    {
        onClose,
        nextStep
    }) {

    const {status, segment, segments, selectSegment, createProgram} = useProgramContext();

    const onCreateProgram = useCallback(async name => {
        try {
            const response = await createProgram(name);
            nextStep();
        } catch (error) {

        }
    }, [
        onClose,
        createProgram
    ]);
    return (
        <>
            <div className={'segments-component'}>
                {segments.map(item => (
                    <SegmentComponent
                        key={item.name}
                        segment={item}
                        onClick={() => selectSegment(item)}
                        selected={segment?.name === item.name}/>
                ))}
            </div>
            <ModalActions>
                <Button className={'sheerid-button'} variant={'secondary'} onClick={onClose}>{wcSheerIdApp.text.cancel}</Button>
                <Button
                    disabled={!segment || status.creating}
                    className={'sheerid-button button-next'}
                    variant={'primary'}
                    onClick={() => onCreateProgram(segment.name)}
                    isBusy={status.creating}>
                    {status.creating ? 'Creating...' : 'Create'}
                </Button>
            </ModalActions>
        </>
    )
}