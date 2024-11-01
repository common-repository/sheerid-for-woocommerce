import {Flex, FlexItem, CheckboxControl} from "@wordpress/components";
import {useFormContext, Controller, useFieldArray} from "react-hook-form";
import * as Yup from 'yup';
import {RHFCheckbox} from "../form";
import ProgramSubSegments from "../ProgramSubSegments";
import SettingsSection from "../SettingsSection";

export const FirstResponderProgram = {}

FirstResponderProgram.type = 'firstresponder-trial-v2';

FirstResponderProgram.schema = {
    audience: Yup.object({
        segmentDetails: Yup.object({
            subSegments: Yup.array()
        })
    })
};

FirstResponderProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            subSegments: program.audience.segmentDetails.subSegments
        }
    }
})

export default FirstResponderProgram;