import React, {Fragment} from 'react';
import LoadingSpinner from "./LoadingSpinner";
import FileNotFound from "../errors/FileNotFound";
import InternalServerError from "../errors/InternalServerError";

const LoadingInfo = (props) => {
    const CustomFileNotFound = props.customFileNotFound;
    return (
        <Fragment>
            {props.info.loading &&
                <LoadingSpinner />
            }
            {props.info.loadingError && props.info.loadingErrorCode === 404 &&
                <Fragment>
                    {props.customFileNotFound &&
                        <CustomFileNotFound />
                    }
                    {!props.customFileNotFound &&
                        <FileNotFound />
                    }
                </Fragment>
            }
            {props.info.loadingError && props.info.loadingErrorCode === 500 &&
                <InternalServerError />
            }
        </Fragment>
    )
}

export default LoadingInfo;