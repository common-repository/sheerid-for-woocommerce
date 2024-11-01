import {Controller, useFormContext} from "react-hook-form";
import {CheckboxControl, Flex, FlexItem} from "@wordpress/components";
import SettingsSection from "./SettingsSection";

const ProgramSubSegments = (
    {
        name,
        description = '',
        subsegments = []
    }) => {
    if (!subsegments.length) {
        return null;
    }
    return (
        <SettingsSection>
            <p className={'description'}>{description}</p>
            <Flex direction={'row'} style={{flexWrap: 'wrap'}} justify={'flex-start'} className={'sheerid-items__container'}>
                {subsegments.map((segment, idx) => (
                    <FlexItem key={segment.value}>
                        <ProgramSubSegment name={name} segment={segment}/>
                    </FlexItem>
                ))}
            </Flex>
        </SettingsSection>
    )

}

const ProgramSubSegment = ({name, segment}) => {
    const {control, setValue} = useFormContext();
    const onClick = (value) => {
        const idx = value.indexOf(segment.value);
        if (idx > -1) {
            value.splice(idx, 1);
            setValue(name, [...value]);
        } else {
            setValue(name, [...value, segment.value]);
        }
    }

    return (
        <Controller
            name={name}
            control={control}
            render={({field, fieldState}) => {
                return (
                    <CheckboxControl
                        label={segment.label}
                        checked={field.value.includes(segment.value)}
                        onChange={() => onClick(field.value)}/>
                )
            }}/>
    )
}

export default ProgramSubSegments;