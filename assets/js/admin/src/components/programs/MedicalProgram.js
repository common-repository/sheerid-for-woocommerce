import {Flex} from "@wordpress/components";
import ProgramSubSegments from "../ProgramSubSegments";
import * as Yup from "yup";
import SettingsSection from "../SettingsSection";

export const MedicalProgram = {};

MedicalProgram.type = 'medical-trial-v2';

MedicalProgram.schema = {
    audience: Yup.object({
        segmentDetails: Yup.object({
            subSegments: Yup.array()
        })
    })
};

MedicalProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            subSegments: program.audience.segmentDetails.subSegments
        }
    }
})

export default MedicalProgram;