import {FormProvider as Form} from "react-hook-form";

export const FormProvider = ({onSubmit, methods, children, className = ''}) => {
    return (
        <Form {...methods}>
            <form onSubmit={onSubmit} className={className}>
                {children}
            </form>
        </Form>
    )
}