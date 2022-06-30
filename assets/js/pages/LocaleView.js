import React, { useState } from 'react'
import { Container, Col, Row, Breadcrumb, BreadcrumbItem, Button } from 'reactstrap'
import Review from '../components/Review'
import RatedAgencies from '../components/RatedAgencies'
import DataLoader from '../components/DataLoader'
import Constants from '../Constants'
import { HashLink as Link } from 'react-router-hash-link'
import LocaleReview from '../components/LocaleReview'
import ReviewLocaleForm from '../components/ReviewLocaleForm'
import ReviewCompletedThankYou from '../content/ReviewCompletedThankYou'
import LocaleAutocomplete from '../components/LocaleAutocomplete'
import PropTypes from 'prop-types'

const LocaleView = (props) => {
  const [name, setName] = useState('')
  const [content, setContent] = useState('')
  const [localeReviews, setLocaleReviews] = useState([])
  const [tenancyReviews, setTenancyReviews] = useState([])
  const [agencyReviewsSummary, setAgencyReviewsSummary] = useState(null)
  const [loaded, setLoaded] = useState(false)
  const [reviewLocaleFormOpen, setReviewLocaleFormOpen] = useState(false)
  const [localeReviewCompletedThankYouOpen, setLocaleReviewCompletedThankYouOpen] = useState(false)

  const openReviewLocaleForm = () => {
    setReviewLocaleFormOpen(true)
  }

  const localeReviewCompletedThankYou = () => {
    setLocaleReviewCompletedThankYouOpen(true)
    setReviewLocaleFormOpen(false)
  }

  const loadData = (data) => {
    setName(data.name)
    setContent(data.content)
    setLocaleReviews(data.localeReviews)
    setTenancyReviews(data.tenancyReviews)
    setAgencyReviewsSummary(data.agencyReviewsSummary)
    setLoaded(true)

    document.title = 'Top Lettings Agents in ' + data.name + ' | ' + Constants.SITE_NAME
  }

  return (
    <Container className="locale-view">
      {localeReviewCompletedThankYouOpen &&
        <ReviewCompletedThankYou />
      }
      <DataLoader
        url={'/api/l/' + props.match.params.slug}
        loadComponentData={loadData}
      />
      {loaded &&
        <div>
          <Row>
            <Breadcrumb className="w-100">
              <BreadcrumbItem><Link to="/#">Home</Link></BreadcrumbItem>
              <BreadcrumbItem className="active locale-name">{name}</BreadcrumbItem>
            </Breadcrumb>
          </Row>
          <Row className="bg-white rounded shadow-sm mb-4">
            <Col md="6" className="p-4" dangerouslySetInnerHTML={{ __html: content }} />
            <Col md="6" className="p-4">
              <RatedAgencies
                heading={'Top rated agencies for lettings in ' + name}
                agencyReviewsSummary={agencyReviewsSummary}
              />
            </Col>
          </Row>
          <Row>
            <Col md="12" className="m-0 p-0">

              <ul className="nav nav-tabs">
                <li className="nav-item">
                  <a className="nav-link active" data-toggle="tab" href="#locale-reviews-pane">{name} reviews</a>
                </li>
                <li className="nav-item">
                  <a className="nav-link" data-toggle="tab" href="#tenancy-reviews-pane">Tenancy reviews</a>
                </li>
              </ul>

              <div className="tab-content bg-white rounded shadow-sm p-2 pt-4 mb-4">
                <div className="tab-pane active container" id="locale-reviews-pane">
                  <h5 className="mb-1">Reviews of {name} from residents</h5>

                  {localeReviews.length > 0 && localeReviews.map(
                    ({ id, slug, author, title, content, overallStars, createdAt, positiveVotes }) => (
                      <LocaleReview
                        {...props}
                        key={slug}
                        id={id}
                        slug={slug}
                        author={author}
                        title={title}
                        content={content}
                        overallStars={overallStars}
                        createdAt={createdAt}
                        positiveVotes={positiveVotes}
                        showVote={true}
                      />
                    )
                  ).reduce((prev, curr) => [prev, <hr key={'hr_' + prev.slug} />, curr])}

                  {localeReviews.length === 0 &&
                    <>
                      <hr />
                      <p>
                        There are no reviews of {name} yet.
                      </p>
                    </>
                  }
                </div>
                <div className="tab-pane container" id="tenancy-reviews-pane">
                  <h5 className="mb-1">Property reviews from tenants in {name}</h5>

                  {tenancyReviews.length > 0 && tenancyReviews.map(
                    ({ id, author, start, end, title, content, property, branch, agency, stars, createdAt, comments, positiveVotes }) => (
                      <Review
                        {...props}
                        key={id}
                        id={id}
                        author={author}
                        start={start}
                        end={end}
                        title={title}
                        content={content}
                        property={property}
                        branch={branch}
                        agency={agency}
                        stars={stars}
                        createdAt={createdAt}
                        comments={comments}
                        positiveVotes={positiveVotes}
                      />
                    )
                  ).reduce((prev, curr) => [prev, <hr key={'hr_' + prev.id} />, curr])}

                  {tenancyReviews.length === 0 &&
                    <>
                      <hr />
                      <p>
                        There are no tenant reviews yet for {name}.
                      </p>
                    </>
                  }
                </div>
              </div>

            </Col>
          </Row>
          <Row>
            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
              <h5 className="mb-4">Review {name}</h5>

              <p className="mb-2">
                Are you a current or past resident of {name}?
                We&apos;d love it if you could review your experience of living in {name}!
              </p>
              <hr />
              {!reviewLocaleFormOpen &&
                <Button onClick={openReviewLocaleForm} color="primary">Yes! I want to write a review</Button>
              }
              {reviewLocaleFormOpen &&
                <ReviewLocaleForm
                  localeName={name}
                  localeSlug={props.match.params.slug}
                  completedThankYou={localeReviewCompletedThankYou}
                  {...props}
                />
              }
            </Col>
          </Row>
          <Row>
            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
              <h5 className="mb-4">Search for another city, town or district</h5>
              <LocaleAutocomplete prependSearchIcon={true}/>
            </Col>
          </Row>
        </div>
      }
    </Container>
  )
}

LocaleView.propTypes = {
  match: PropTypes.shape({
    params: PropTypes.shape({
      slug: PropTypes.string
    })
  })
}

export default LocaleView
