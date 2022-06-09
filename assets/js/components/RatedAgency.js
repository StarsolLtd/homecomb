import React from 'react'
import PropTypes from 'prop-types'
import { HashLink as Link } from 'react-router-hash-link'

const RatedAgency = (props) => {
  return (
    <div className="agency clearfix">
      <div className="float-left agency-logo-container">
        {props.agencyLogoImageFilename &&
          <Link to={'/agency/' + props.agencySlug + '#'}>
            <img src={'/images/images/' + props.agencyLogoImageFilename}
             className="agency-logo float-left"
             alt={props.agencyName + ' Logo'}
            />
          </Link>
        }
      </div>
      <div>
        <p>
          <a href={'/agency/' + props.agencySlug}>{props.agencyName}</a><br/>
          Average rating: {props.meanRating} stars from {props.ratedCount} review
          {props.ratedCount > 1 &&
            <>s</>
          }
        </p>
      </div>
    </div>
  )
}

RatedAgency.propTypes = {
  agencyLogoImageFilename: PropTypes.string,
  agencySlug: PropTypes.string,
  agencyName: PropTypes.string,
  meanRating: PropTypes.number,
  ratedCount: PropTypes.number
}

export default RatedAgency
