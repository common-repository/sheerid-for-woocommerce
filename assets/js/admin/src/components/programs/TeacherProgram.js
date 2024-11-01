import {Flex} from "@wordpress/components";
import ProgramSubSegments from "../ProgramSubSegments";
import * as Yup from "yup";
import SettingsSection from "../SettingsSection";

export const TeacherProgram = {};

TeacherProgram.type = 'teacher-trial-v2';

TeacherProgram.schema = {
    audience: Yup.object({
        segmentDetails: Yup.object({
            subSegments: Yup.array()
        })
    })
};

TeacherProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            subSegments: program.audience.segmentDetails.subSegments
        }
    }
})

export default TeacherProgram;