import React from 'react'
import PropTypes from 'prop-types'
import Moment from 'react-moment'

const MonthRange = (props) => {
  return (
    <span className="month-range">
      {props.start !== null &&
        <span className="month-range-from">
          from <Moment format="MMM YYYY" className="month-range-start">{props.start}</Moment>
        </span>
      }
      {' '}
      {props.end !== null &&
        <span className="month-range-to">
          to <Moment format="MMM YYYY" className="month-range-end">{props.end}</Moment>
        </span>
      }
    </span>
  )
}

MonthRange.propTypes = {
  start: PropTypes.string,
  end: PropTypes.string
}

export default MonthRange
