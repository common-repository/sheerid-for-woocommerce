import {Button, Flex} from "@wordpress/components";

export default function ModalActions({children, className = ''}) {
    return (
        <Flex className={'modal-actions' + ` ${className}`} direction='row' align='center' justify='flex-end'>
            {children}
        </Flex>
    )
}