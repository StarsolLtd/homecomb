import React from 'react';
import {Alert} from "reactstrap";

class FileNotFound extends React.Component {
    render(){
        return (
            <Alert color="warning">
                404 File Not Found. Sorry, whatever you were looking for we couldn't find it.
            </Alert>
        )
    }
}

export default FileNotFound;