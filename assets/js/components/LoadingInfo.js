import React from 'react'
import LoadingSpinner from './LoadingSpinner'
import FileNotFound from '../errors/FileNotFound'
import InternalServerError from '../errors/InternalServerError'

const LoadingInfo = (props) => {
  const CustomFileNotFound = props.customFileNotFound
  return (
    <>
      {props.info.loading &&
        <LoadingSpinner className="loading-spinner-large"/>
      }
      {props.info.loadingError && props.info.loadingErrorCode === 404 &&
        <>
          {props.customFileNotFound &&
            <CustomFileNotFound/>
          }
          {!props.customFileNotFound &&
            <FileNotFound/>
          }
        </>
      }
      {props.info.loadingError && props.info.loadingErrorCode === 500 &&
        <InternalServerError/>
      }
    </>
  )
}

export default LoadingInfo
