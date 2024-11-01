import {__experimentalInputControl as InputControl} from '@wordpress/components';
import {Controller, useFormContext} from "react-hook-form";
import classnames from "classnames";

export const RHFTextField = (
    {
        name,
        type = ' text',
        label,
        placeholder = '',
        ...props
    }) => {
    const {control} = useFormContext();
    return (
        <Controller
            name={name}
            control={control}
            render={({field, fieldState: {error}}) => (
                <InputControl
                    type={type}
                    className={classnames('formTextField', {'invalid-field': !!error})}
                    label={label}
                    placeholder={placeholder}
                    help={!!error && error.message ? error.message : null}
                    {...field}
                    {...props}/>
            )}/>
    )
}