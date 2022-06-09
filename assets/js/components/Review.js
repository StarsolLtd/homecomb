import React from 'react'
import PropTypes from 'prop-types'
import ReviewStars from './ReviewStars'
import Moment from 'react-moment'
import ReviewOptions from './ReviewOptions'
import { HashLink as Link } from 'react-router-hash-link'
import Comment from './Comment'
import { faUser } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

import '../../styles/review.scss'
import MonthRange from './MonthRange'
import Vote from './Vote'

const Review = (props) => {
  return (
    <div className={'review pt-4 pb-4 ' + props.className}>
      {props.showOptions &&
        <div className="dropdown float-right review-options">
          <ReviewOptions reviewId={props.id} {...props} />
        </div>
      }

      <div>
        <p className="mb-3">
          <FontAwesomeIcon icon={faUser} className="text-primary"/>
          {' '}<span className="author">{props.author}</span>
          {' '}reviewed their tenancy <MonthRange start={props.start} end={props.end}/>
          {props.property && props.showProperty &&
            <span>&nbsp;at <Link
              to={'/property/' + props.property.slug}>{props.property.addressLine1}, {props.property.postcode}</Link></span>
          }
          <span className="review-date">
              <br/>Date of review:
              <Moment format="Do MMM YYYY" className="date">{props.createdAt}</Moment>
            </span>
        </p>
      </div>

      <div>
        <h3>{props.title}</h3>

        <p>{props.content}</p>

        {props.branch && props.showBranch &&
          <p>
            {props.agency && props.showAgency &&
              <>
                Agency:&nbsp;
                {props.agency.published &&
                  <Link to={'/agency/' + props.agency.slug + '#'} className="agency-name">{props.agency.name}</Link>
                }
                {!props.agency.published &&
                  <span className="agency-name">{props.agency.name}</span>
                }
                <br/>
              </>
            }
            Branch:&nbsp;
            {props.branch.published &&
              <Link to={'/branch/' + props.branch.slug + '#'} className="branch-name">{props.branch.name}</Link>
            }
            {!props.branch.published &&
              <span className="branch-name">{props.branch.name}</span>
            }
            <br/>
          </p>
        }
      </div>

      <ReviewStars
        overall={props.stars.overall}
        agency={props.stars.agency}
        landlord={props.stars.landlord}
        property={props.stars.property}
      />

      {props.showVote &&
        <Vote
          className="mt-3"
          entityName="TenancyReview"
          entityId={props.id}
          positiveTerm="Helpful"
          positiveVotes={props.positiveVotes}
        />
      }

      {props.comments && props.comments.map(
        ({ id, author, content, createdAt }) => (
          <Comment key={id} author={author} createdAt={createdAt} content={content}/>
        )
      )}
    </div>
  )
}

Review.propTypes = {
  showProperty: PropTypes.bool,
  showBranch: PropTypes.bool,
  showAgency: PropTypes.bool,
  showOptions: PropTypes.bool,
  showVote: PropTypes.bool,
  className: PropTypes.string,
  id: PropTypes.string,
  author: PropTypes.string,
  start: PropTypes.string,
  end: PropTypes.string,
  property: PropTypes.object,
  createdAt: PropTypes.string,
  title: PropTypes.string,
  content: PropTypes.string,
  branch: PropTypes.shape({
    slug: PropTypes.string,
    name: PropTypes.string,
    published: PropTypes.bool
  }),
  agency: PropTypes.shape({
    slug: PropTypes.string,
    name: PropTypes.string,
    published: PropTypes.bool
  }),
  stars: PropTypes.shape({
    overall: PropTypes.number,
    agency: PropTypes.number,
    landlord: PropTypes.number,
    property: PropTypes.number
  }),
  positiveVotes: PropTypes.number,
  comments: PropTypes.object
}

Review.defaultProps = {
  showProperty: true,
  showBranch: true,
  showAgency: true,
  showOptions: true,
  showVote: true,
  stars: {
    overall: 0,
    agency: 0,
    landlord: 0,
    property: 0
  }
}

export default Review
