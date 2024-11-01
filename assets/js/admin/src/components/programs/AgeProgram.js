import {Flex, FlexItem} from "@wordpress/components";
import * as Yup from 'yup';
import {RHFTextField} from "../form";
import SettingsSection from "../SettingsSection";

export const AgeProgram = {};

AgeProgram.Component = ({program}) => {
    return (
        <SettingsSection title={wcSheerIdApp.text.ageSettings}>
            <p className={'description'}>{wcSheerIdApp.text['age-trial'].desc}</p>
            <Flex direction={'column'} className={'sheerid-items__container'}>
                <FlexItem>
                    <RHFTextField label='Min Age' name={'audience.segmentDetails.age.min'}/>
                </FlexItem>
                <FlexItem>
                    <RHFTextField label='Max Age' name={'audience.segmentDetails.age.max'}/>
                </FlexItem>
            </Flex>
        </SettingsSection>
    );
}

AgeProgram.type = 'age-trial';

AgeProgram.schema = {
    audience: Yup.object({
        segmentDetails: Yup.object({
            age: Yup.object({
                min: Yup.number().required().typeError(wcSheerIdApp.text['age-trial'].minRequired),
                max: Yup.number().required().typeError(wcSheerIdApp.text['age-trial'].maxRequired)
            })
        })
    })
};

AgeProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            age: {
                min: program.audience.segmentDetails.age.min,
                max: program.audience.segmentDetails.age.max
            }
        }
    }
})

export default AgeProgram;