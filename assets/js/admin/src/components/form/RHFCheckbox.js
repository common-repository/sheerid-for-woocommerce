import {useFormContext, Controller} from "react-hook-form";
import {CheckboxControl} from '@wordpress/components';
import classnames from "classnames";

export const RHFCheckbox = (
    {
        name,
        ...props
    }) => {
    const {control} = useFormContext();
    return <Controller
        name={name}
        control={control}
        render={({field, fieldState: {error}}) => (
            <CheckboxControl
                {...field}
                {...props}
                checked={!!field.value}
                className={classnames('formTextField', {'invalid-field': !!error})}
                help={!!error && error.message ? error.message : null}/>
        )
        }/>
}