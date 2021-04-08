import React from 'react';
import {Alert} from "reactstrap";

const InternalServerError = () => {
    return (
        <Alert color="error">
            500 Internal Server Error. It's not you, it's us.
        </Alert>
    );
}

export default InternalServerError;
