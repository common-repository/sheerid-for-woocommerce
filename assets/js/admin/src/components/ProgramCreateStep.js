import {cloneElement} from "@wordpress/element";
import {Flex, FlexItem} from "@wordpress/components";
import classnames from 'classnames';

export default function ProgramCreateStep({index, value, children, ...props}) {
    if (index === value) {
        return (
            <div className={classnames('program-step', index)}>
                {cloneElement(children, props)}
            </div>
        )
    }
    return null;
}