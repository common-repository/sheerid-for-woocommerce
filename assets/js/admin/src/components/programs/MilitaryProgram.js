import {Flex} from "@wordpress/components";
import ProgramSubSegments from "../ProgramSubSegments";
import * as Yup from "yup";
import SettingsSection from "../SettingsSection";

export const MilitaryProgram = ({program}) => {

    return (
        <SettingsSection title={wcSheerIdApp.text.enabledSegments}>
            <Flex direction={'column'}>
                <ProgramSubSegments
                    name={'audience.segmentDetails.subSegments'}
                    title={wcSheerIdApp.text.enabledSegments}
                    description={wcSheerIdApp.text.militaryDesc}
                    subsegments={program.segmentDescription.subSegments}/>
            </Flex>
        </SettingsSection>
    )
}

MilitaryProgram.type = 'military-trial-v2';

MilitaryProgram.schema = {
    audience: Yup.object({
        segmentDetails: Yup.object({
            subSegments: Yup.array().required()
        })
    })
};

MilitaryProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            subSegments: program.audience.segmentDetails.subSegments
        }
    }
})

export default MilitaryProgram;