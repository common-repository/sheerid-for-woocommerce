import {SelectControl} from '@wordpress/components';
import {Controller, useFormContext} from "react-hook-form";
import classnames from "classnames";

export const RHFSelect = (
    {
        name,
        label = '',
        options = [],
        ...props
    }) => {

    const {control} = useFormContext();
    return (
        <Controller
            name={name}
            control={control}
            render={({field, fieldState: {error}}) => (
                <SelectControl
                    className={classnames('formSelectField', {'invalid-field': !!error})}
                    label={label}
                    options={options}
                    help={!!error && error.message ? error.message : null}
                    {...props}
                    {...field}/>
            )}/>
    )
}