import React from 'react'
import PropTypes from 'prop-types'
import Stars from './Stars'

const ReviewStars = (props) => {
  return (
    <div className="review-stars">
      {props.overall &&
        <Stars label="Overall" score={props.overall}/>
      }
      {props.agency &&
        <Stars label="Agency" score={props.agency}/>
      }
      {props.landlord &&
        <Stars label="Landlord" score={props.landlord}/>
      }
      {props.property &&
        <Stars label="Property" score={props.property}/>
      }
    </div>
  )
}

ReviewStars.propTypes = {
  overall: PropTypes.number,
  agency: PropTypes.number,
  landlord: PropTypes.number,
  property: PropTypes.number
}

export default ReviewStars
