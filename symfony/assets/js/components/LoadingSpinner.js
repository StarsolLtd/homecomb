import React from 'react';
import { Spinner } from 'reactstrap';

import '../../styles/loading-spinner.scss';

const LoadingSpinner = (props) => {
    return (
        <Spinner className={props.className + ' loading-spinner'} />
    )
}

export default LoadingSpinner;