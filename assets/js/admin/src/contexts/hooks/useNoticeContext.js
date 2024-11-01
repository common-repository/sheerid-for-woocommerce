import {useContext} from "@wordpress/element";
import {NoticeContext} from "../providers/NoticeProvider";

export const useNoticeContext = () => useContext(NoticeContext);

export default useNoticeContext;