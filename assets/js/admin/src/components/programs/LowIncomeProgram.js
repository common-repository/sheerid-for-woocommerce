const LowIncomeProgram = {};

LowIncomeProgram.Component = ({program}) => {
    return (
        <p className={'description'}>
            {program.segmentDescription.description}
        </p>
    )
}

LowIncomeProgram.type = 'lowincome-trial-v2';

LowIncomeProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            subSegments: program.audience.segmentDetails.subSegments
        }
    }
});

export default LowIncomeProgram;

