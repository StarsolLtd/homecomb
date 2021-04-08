import React from 'react';
import {Alert} from "reactstrap";

const FileNotFound = () => {
    return (
        <Alert color="warning">
            404 File Not Found. Sorry, whatever you were looking for we couldn't find it.
        </Alert>
    );
}

export default FileNotFound;
