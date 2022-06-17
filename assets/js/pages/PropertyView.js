import React, { useState } from 'react'
import { Button, Container, Col, Row, Breadcrumb, BreadcrumbItem } from 'reactstrap'
import Review from '../components/Review'
import ReviewTenancyForm from '../components/ReviewTenancyForm'
import DataLoader from '../components/DataLoader'
import Constants from '../Constants'
import PropertyAutocomplete from '../components/PropertyAutocomplete'
import ReviewCompletedThankYou from '../content/ReviewCompletedThankYou'
import Map from '../components/Map'
import { HashLink as Link } from 'react-router-hash-link'
import LocaleReview from '../components/LocaleReview'
import PropTypes from 'prop-types'

const PropertyView = (props) => {
  const [addressLine1, setAddressLine1] = useState('')
  const [locality, setLocality] = useState(null)
  const [postcode, setPostcode] = useState('')
  const [city, setCity] = useState(null)
  const [district, setDistrict] = useState(null)
  const [tenancyReviews, setTenancyReviews] = useState([])
  const [reviewTenancyFormOpen, setReviewTenancyFormOpen] = useState(false)
  const [loaded, setLoaded] = useState(false)
  const [reviewCompletedThankYou, setReviewCompletedThankYou] = useState(false)
  const [latitude, setLatitude] = useState(null)
  const [longitude, setLongitude] = useState(null)

  const loadData = (data) => {
    setAddressLine1(data.addressLine1)
    setLocality(data.locality)
    setPostcode(data.postcode)
    setLatitude(data.latitude)
    setLongitude(data.longitude)
    setTenancyReviews(data.tenancyReviews)
    setCity(data.city)
    setDistrict(data.district)
    setLoaded(true)

    document.title = data.addressLine1 + ' | ' + Constants.SITE_NAME
  }

  const openReviewTenancyForm = () => {
    setReviewTenancyFormOpen(true)
  }

  const openReviewCompletedThankYou = () => {
    setReviewCompletedThankYou(true)
  }

  const showDistrictInBreadcrumb = () => {
    return district && city.name === 'London'
  }

  return (
    <Container>
      {reviewCompletedThankYou &&
        <ReviewCompletedThankYou />
      }
      <DataLoader
        url={'/api/property/' + props.match.params.slug}
        loadComponentData={loadData}
      />
      {loaded &&
        <div className="property-view">
          <Row>
            <Breadcrumb className="w-100">
              <BreadcrumbItem><Link to="/#">Home</Link></BreadcrumbItem>
              {city &&
                <BreadcrumbItem className="city">
                  <a href={'/c/' + city.slug}>
                    {city.name}
                  </a>
                </BreadcrumbItem>
              }
              {showDistrictInBreadcrumb() &&
                <BreadcrumbItem className="district desktop-only">
                  <a href={'/d/' + district.slug}>
                    {district.name}
                  </a>
                </BreadcrumbItem>
              }
              {locality &&
                <BreadcrumbItem className="locality">{locality}</BreadcrumbItem>
              }
              <BreadcrumbItem className="desktop-only postcode">{postcode}</BreadcrumbItem>
              <BreadcrumbItem className="active address-line-1">{addressLine1}</BreadcrumbItem>
            </Breadcrumb>
          </Row>
          {latitude && longitude &&
            <Row className="desktop-only">
              <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
                <Map
                  className="property-map"
                  addressLine1={addressLine1}
                  latitude={latitude}
                  longitude={longitude}
                />
              </Col>
            </Row>
          }
          <Row>
            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
              <h5 className="mb-1">Reviews from tenants</h5>

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
                    showProperty={false}
                  />
                )
              ).reduce((prev, curr) => [prev, <hr key={'hr_' + prev.id} />, curr])}

              {tenancyReviews.length === 0 &&
                <>
                  <p className="mt-3">
                    There are no tenant reviews yet for this property.
                  </p>
                  <hr />
                  <h5 className="mb-4">Search for another property address</h5>
                  <PropertyAutocomplete prependSearchIcon={true}/>
                </>
              }
            </Col>
          </Row>
          <Row>
            <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
              <h5 className="mb-4">Review your tenancy here</h5>

              <p className="mb-2">
                Are you a current or past tenant at {addressLine1}?
                We&apos;d love it if you could review your tenant experience!
              </p>
              <hr />
              {!reviewTenancyFormOpen &&
                <Button onClick={openReviewTenancyForm} color="primary">Yes! I want to write a review</Button>
              }
              {reviewTenancyFormOpen &&
                <ReviewTenancyForm
                  fixedProperty={true}
                  propertySlug={props.match.params.slug}
                  completedThankYou={openReviewCompletedThankYou}
                  {...props}
                />
              }
            </Col>
          </Row>
          {city &&

            <Row>
              <Col md="12" className="p-0 mb-4">

                <ul className="nav nav-tabs">
                  {city.localeReviews.length > 0 &&
                    <li className="nav-item">
                      <a className="nav-link active" data-toggle="tab"
                         href="#locale-reviews-city-pane">{city.name} reviews</a>
                    </li>
                  }
                </ul>

                {city.localeReviews.length > 0 &&
                  <div className="bg-white rounded shadow-sm tab-content p-2 pt-4 mb-4">
                    <div className="tab-pane active container" id="locale-reviews-city-pane">
                      <h6 className="mb-1">
                        Here is what residents have said about <a href={'/c/' + city.slug}>{city.name}</a> generally:
                      </h6>

                      {city.localeReviews.map(
                        ({
                          id,
                          slug,
                          author,
                          title,
                          content,
                          overallStars,
                          createdAt,
                          positiveVotes
                        }) => (
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
                    </div>
                  </div>
                }
              </Col>
            </Row>
          }
        </div>
      }
    </Container>
  )
}

PropertyView.propTypes = {
  match: PropTypes.shape({
    params: PropTypes.shape({
      slug: PropTypes.string
    })
  })
}

export default PropertyView
