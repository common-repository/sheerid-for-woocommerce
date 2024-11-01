import useNoticeContext from "../contexts/hooks/useNoticeContext";
import {Flex, FlexItem, Notice} from "@wordpress/components";
import classnames from "classnames";

export default function Notices({context}) {
    const {getNoticesByContext, removeNotice} = useNoticeContext();

    const notices = getNoticesByContext(context);

    if (notices) {
        return (
            <Flex className={'sheerid-notices-container'} direction={'column'}>
                {notices.map(notice => (
                    /*<FlexItem key={notice.msg} className={classnames('sheerid-notice-msg', notice.type)}>
                        < div>
                            < p> {notice.msg}</p>
                        </div>
                    </FlexItem>*/
                    <Notice
                        key={notice.msg}
                        status={notice.type}
                        isDismissible={true}
                        onRemove={() => removeNotice(notice.msg)}>
                        {notice.msg}
                    </Notice>
                ))}
            </Flex>
        )
    }
    return null;
}