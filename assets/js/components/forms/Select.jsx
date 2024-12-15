import React from 'react';
const Select = ({
    name,
    value,
    error = "",
    label,
    onChange,
    children
}) => {
    return ( 
        <div className="form-group mt-3">
            <label htmlFor={name}>{label}</label>
            <select
                onChange={onChange}
                name={name}
                id={name}
                value={value}
                className={"form-select mt-2" + (error && " is-invalid")}
            >
            {children}
            </select>
            <p className="invalid-feedback">{error}</p>
        </div>
     );
}
 
export default Select;