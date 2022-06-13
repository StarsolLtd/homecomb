import React from 'react'
import PropTypes from 'prop-types'
import Rating from 'react-rating'
import { faStar } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

import '../../styles/question.scss'

const Scale = (props) => {
  return (
    <div className="scale-5">
      <span className="meaning low-meaning">{props.lowMeaning}</span>
      <Rating
        onChange={props.handleRatingChange}
        initialRating={props.rating}
        emptySymbol={
          <span className="text-rating-unchecked rating-icon"><FontAwesomeIcon icon={faStar}/></span>
        }
        fullSymbol={
          <span className="text-rating rating-icon"><FontAwesomeIcon icon={faStar}/></span>
        }
        stop={props.max}
      />
      <span className="meaning high-meaning">{props.highMeaning}</span>
    </div>
  )
}

Scale.propTypes = {
  lowMeaning: PropTypes.string,
  highMeaning: PropTypes.string,
  rating: PropTypes.number,
  max: PropTypes.number,
  handleRatingChange: PropTypes.func
}

Scale.defaultProps = {
  lowMeaning: '',
  highMeaning: 0,
  rating: 0,
  max: 5
}

export default Scale
