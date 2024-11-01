const RecentMoverProgram = {};

RecentMoverProgram.Component = ({program}) => {
    return (
        <p className={'description'}>
            {program.segmentDescription.description}
        </p>
    )
}

RecentMoverProgram.type = 'movers-trial';

RecentMoverProgram.defaultValues = program => ({
    audience: {
        segmentDetails: {
            subSegments: program.audience.segmentDetails.subSegments
        }
    }
});

export default RecentMoverProgram;

