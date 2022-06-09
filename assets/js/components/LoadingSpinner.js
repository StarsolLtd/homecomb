import React from 'react'
import PropTypes from 'prop-types'
import { Spinner } from 'reactstrap'

import '../../styles/loading-spinner.scss'

const LoadingSpinner = (props) => {
  return (
    <Spinner className={props.className + ' loading-spinner'}/>
  )
}

LoadingSpinner.propTypes = {
  className: PropTypes.string
}

export default LoadingSpinner
