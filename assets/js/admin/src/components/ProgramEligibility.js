import {cloneElement} from "@wordpress/element";
import ProgramSubSegments from "./ProgramSubSegments";

export default function ProgramEligibility({program, ProgramComponent = null}) {
    if (ProgramComponent && ProgramComponent.Component) {
        return cloneElement(<ProgramComponent.Component/>, {
            program
        });
    }
    if (program?.segmentDescription?.subSegments.length > 0) {
        return (
            <ProgramSubSegments
                name={'audience.segmentDetails.subSegments'}
                subsegments={program.segmentDescription.subSegments}
                description={wcSheerIdApp.text[program.segmentDescription.name].desc}/>
        )
    }
    return null;
}