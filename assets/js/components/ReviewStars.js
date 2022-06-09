import React from 'react'
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

export default ReviewStars
