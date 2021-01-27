import React from 'react';

const Address = (props) => {
    return (
        <div className="address" onClick={() => props.handleClick(props.addressLine1)}>
            {[props.addressLine1, props.addressLine2, props.addressLine3, props.city, props.postcode].filter(function (el) {return el.length > 0;}).join(', ')}
        </div>
    );
}

export default Address;