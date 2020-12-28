import React, {Fragment} from 'react';
import LoadingSpinner from "./LoadingSpinner";
import FileNotFound from "../errors/FileNotFound";
import InternalServerError from "../errors/InternalServerError";

class LoadingInfo extends React.Component {
    render(){
        return (
            <Fragment>
                {this.props.info.loading &&
                    <LoadingSpinner />
                }
                {this.props.info.loadingError && this.props.info.loadingErrorCode === 404 &&
                    <FileNotFound />
                }
                {this.props.info.loadingError && this.props.info.loadingErrorCode === 500 &&
                    <InternalServerError />
                }
            </Fragment>
        )
    }
}

export default LoadingInfo;