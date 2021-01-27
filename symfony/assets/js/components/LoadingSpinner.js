import React from 'react';
import { Spinner } from 'reactstrap';

const LoadingSpinner = (props) => {
    return (
        <Spinner className={props.className} style={{ width: '3rem', height: '3rem' }} />
    )
}

export default LoadingSpinner;