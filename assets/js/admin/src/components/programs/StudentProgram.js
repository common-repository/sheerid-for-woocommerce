import {Flex} from "@wordpress/components";
import ProgramSubSegments from "../ProgramSubSegments";
import * as Yup from "yup";
import SettingsSection from "../SettingsSection";

export const StudentProgram = {};

StudentProgram.type = 'student-trial-v2';

StudentProgram.schema = {
    audience: Yup.object({
        segmentDetails: Yup.object({
            subSegments: Yup.array().required()
        })
    })
};

StudentProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            subSegments: program.audience.segmentDetails.subSegments
        }
    }
})

export default StudentProgram;