import React from 'react';
import {Alert} from "reactstrap";

class FileNotFound extends React.Component {
    render(){
        return (
            <Alert color="error">
                500 Internal Server Error. It's not you, it's us.
            </Alert>
        )
    }
}

export default FileNotFound;