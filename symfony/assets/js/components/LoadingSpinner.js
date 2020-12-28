import React from 'react';
import { Spinner } from 'reactstrap';

class LoadingSpinner extends React.Component {
    render(){
        return (
            <Spinner style={{ width: '3rem', height: '3rem' }} />
        )
    }
}

export default LoadingSpinner;