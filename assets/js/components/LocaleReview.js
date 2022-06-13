import React from 'react'
import PropTypes from 'prop-types'
import ReviewStars from './ReviewStars'
import Moment from 'react-moment'
import { faUser } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

import '../../styles/review.scss'
import Vote from './Vote'

const LocaleReview = (props) => {
  return (
    <div className={'review pt-4 pb-4 ' + props.className}>
      <div>
        <p className="mb-3">
          <FontAwesomeIcon icon={faUser} className="text-primary"/>
          {' '}<span className="author">{props.author}</span>
          <span className="review-date"><br/>Date of review:
            <Moment format="Do MMM YYYY" className="date">{props.createdAt}</Moment>
          </span>
        </p>
      </div>

      <h3>{props.title}</h3>

      <p>{props.content}</p>

      <ReviewStars overall={props.overallStars}/>

      {props.showVote &&
        <Vote
          className="mt-3"
          entityName="LocaleReview"
          entityId={props.id}
          positiveTerm="Helpful"
          positiveVotes={props.positiveVotes}
        />
      }
    </div>
  )
}

LocaleReview.propTypes = {
  id: PropTypes.number,
  slug: PropTypes.string,
  className: PropTypes.string,
  author: PropTypes.string,
  createdAt: PropTypes.string,
  title: PropTypes.string,
  content: PropTypes.string,
  overallStars: PropTypes.number,
  positiveVotes: PropTypes.number,
  showVote: PropTypes.bool
}

LocaleReview.defaultProps = {
  showVote: true,
  overallStars: 0,
  positiveVotes: 0
}

export default LocaleReview
