import React from 'react';
import {Alert} from "reactstrap";

class ReviewSolicitationNotFound extends React.Component {
    render(){
        return (
            <Alert color="info">
                Your review has been successfully received. Thank you!
            </Alert>
        )
    }
}

export default ReviewSolicitationNotFound;
