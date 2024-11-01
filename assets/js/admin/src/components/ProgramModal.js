import {useState} from "@wordpress/element";
import {Modal, Button, TabPanel} from "@wordpress/components";
import SegmentsComponent from "./SegmentsComponent";
import ProgramCreateSteps from "./ProgramCreateSteps";
import ProgramCreateStep from "./ProgramCreateStep";
import EditProgramComponent from "./EditProgramComponent";
import Notices from "./Notices";
import TabContent from "./TabContent";

export default function ProgramModal(
    {
        open,
        step = 'create',
        onClose,
    }) {
    const [currentStep, setCurrentStep] = useState(step);

    const nextStep = (step) => {
        setCurrentStep(step);
    }

    if (open) {
        return (
            <Modal
                className={'sheerid-program-modal'}
                title={currentStep === 'create' ? 'Create Program' : 'Edit Program'}
                isDismissable={true}
                onRequestClose={onClose}>
                <div className={'modal-content'}>
                    <Notices context={'programModal'}/>
                    <ProgramCreateSteps>
                        <ProgramCreateStep
                            index={'create'}
                            value={currentStep}
                            onClose={onClose}
                            nextStep={() => nextStep('edit')}>
                            <SegmentsComponent/>
                        </ProgramCreateStep>
                        <ProgramCreateStep
                            index={'edit'}
                            value={currentStep}
                            nextStep={() => nextStep('edit')}>
                            <TabPanel
                                tabs={wcSheerIdApp.editProgramTabs}
                                children={(tab) => (
                                    <EditProgramComponent activeTab={tab.name} onClose={onClose}/>
                                )}
                            />
                        </ProgramCreateStep>
                    </ProgramCreateSteps>
                </div>
            </Modal>
        )
    }
    return null;
}