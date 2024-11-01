import {useContext} from "@wordpress/element";
import {ProgramContext} from "../providers/ProgramProvider";

const useProgramContext = () => useContext(ProgramContext);

export default useProgramContext;