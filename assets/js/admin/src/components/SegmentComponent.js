import classnames from 'classnames';

export default function SegmentComponent(
    {
        segment,
        onClick,
        selected
    }) {
    return (
        <div
            className={classnames('sheerid-segment-component', segment.name, {'selected': selected})}
            onClick={onClick}>
            <div>
                <div className='segment-icon'>
                    <div className={'program-icon'}>
                        <svg viewBox={'-10 -10 100 100'}>
                            <image href={segment.displayInfo.iconSrc}/>
                        </svg>
                    </div>
                </div>
                <div className={'segment-info'}>
                    <div className={'segment-display-name'}>
                        {segment.displayName}
                    </div>
                    <div className={'segment-description'}>
                        {segment.description}
                    </div>
                </div>
            </div>
        </div>
    )
}