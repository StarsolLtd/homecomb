import React, {Fragment} from 'react';
import LoadingSpinner from "./LoadingSpinner";
import FileNotFound from "../errors/FileNotFound";
import InternalServerError from "../errors/InternalServerError";

class LoadingInfo extends React.Component {
    render(){
        const CustomFileNotFound = this.props.customFileNotFound;
        return (
            <Fragment>
                {this.props.info.loading &&
                <LoadingSpinner />
                }
                {this.props.info.loadingError && this.props.info.loadingErrorCode === 404 &&
                <Fragment>
                    {this.props.customFileNotFound &&
                    <CustomFileNotFound />
                    }
                    {!this.props.customFileNotFound &&
                    <FileNotFound />
                    }
                </Fragment>
                }
                {this.props.info.loadingError && this.props.info.loadingErrorCode === 500 &&
                <InternalServerError />
                }
            </Fragment>
        )
    }
}

export default LoadingInfo;