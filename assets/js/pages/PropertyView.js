import React, { Fragment, Component } from 'react'
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

export default class PropertyView extends Component {
  state = {
    addressLine1: '',
    locality: null,
    postcode: '',
    city: null,
    district: null,
    tenancyReviews: [],
    reviewTenancyFormOpen: false,
    loaded: false,
    reviewCompletedThankYou: false,
    latitude: null,
    longitude: null
  }

  openReviewTenancyForm = () => {
    this.setState({ reviewTenancyFormOpen: true })
  }

  reviewCompletedThankYou = () => {
    this.setState({ reviewCompletedThankYou: true })
  }

  render () {
    return (
      <Container>
        {this.state.reviewCompletedThankYou &&
          <ReviewCompletedThankYou />
        }
        <DataLoader
          url={'/api/property/' + this.props.match.params.slug}
          loadComponentData={this.loadData}
        />
        {this.state.loaded &&
          <div className="property-view">
            <Row>
              <Breadcrumb className="w-100">
                <BreadcrumbItem><Link to="/#">Home</Link></BreadcrumbItem>
                {this.state.city &&
                  <BreadcrumbItem className="city">
                    <a href={'/c/' + this.state.city.slug}>
                      {this.state.city.name}
                    </a>
                  </BreadcrumbItem>
                }
                {this.showDistrictInBreadcrumb() &&
                  <BreadcrumbItem className="district desktop-only">
                    <a href={'/d/' + this.state.district.slug}>
                      {this.state.district.name}
                    </a>
                  </BreadcrumbItem>
                }
                {this.state.locality &&
                  <BreadcrumbItem className="locality">{this.state.locality}</BreadcrumbItem>
                }
                <BreadcrumbItem className="desktop-only postcode">{this.state.postcode}</BreadcrumbItem>
                <BreadcrumbItem className="active address-line-1">{this.state.addressLine1}</BreadcrumbItem>
              </Breadcrumb>
            </Row>
            {this.state.latitude && this.state.longitude &&
              <Row className="desktop-only">
                <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
                  <Map
                    className="property-map"
                    addressLine1={this.state.addressLine1}
                    latitude={this.state.latitude}
                    longitude={this.state.longitude}
                  />
                </Col>
              </Row>
            }
            <Row>
              <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                <h5 className="mb-1">Reviews from tenants</h5>

                {this.state.tenancyReviews.length > 0 && this.state.tenancyReviews.map(
                  ({ id, author, start, end, title, content, property, branch, agency, stars, createdAt, comments, positiveVotes }) => (
                    <Review
                      {...this.props}
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

                {this.state.tenancyReviews.length === 0 &&
                  <Fragment>
                    <p className="mt-3">
                      There are no tenant reviews yet for this property.
                    </p>
                    <hr />
                    <h5 className="mb-4">Search for another property address</h5>
                    <PropertyAutocomplete prependSearchIcon={true}/>
                  </Fragment>
                }
              </Col>
            </Row>
            <Row>
              <Col md="12" className="bg-white rounded shadow-sm p-4 mb-4">
                <h5 className="mb-4">Review your tenancy here</h5>

                <p className="mb-2">
                  Are you a current or past tenant at {this.state.addressLine1}?
                  We&apos;d love it if you could review your tenant experience!
                </p>
                <hr />
                {!this.state.reviewTenancyFormOpen &&
                  <Button onClick={this.openReviewTenancyForm} color="primary">Yes! I want to write a review</Button>
                }
                {this.state.reviewTenancyFormOpen &&
                  <ReviewTenancyForm
                    fixedProperty={true}
                    propertySlug={this.props.match.params.slug}
                    completedThankYou={this.reviewCompletedThankYou}
                    {...this.props}
                  />
                }
              </Col>
            </Row>
            {this.state.city &&

              <Row>
                <Col md="12" className="p-0 mb-4">

                  <ul className="nav nav-tabs">
                    {this.state.city.localeReviews.length > 0 &&
                      <li className="nav-item">
                        <a className="nav-link active" data-toggle="tab"
                           href="#locale-reviews-city-pane">{this.state.city.name} reviews</a>
                      </li>
                    }
                  </ul>

                  {this.state.city.localeReviews.length > 0 &&
                    <div className="bg-white rounded shadow-sm tab-content p-2 pt-4 mb-4">
                      <div className="tab-pane active container" id="locale-reviews-city-pane">
                        <h6 className="mb-1">
                          Here is what residents have said about <a href={'/c/' + this.state.city.slug}>{this.state.city.name}</a> generally:
                        </h6>

                        {this.state.city.localeReviews.map(
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
                              {...this.props}
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

  showDistrictInBreadcrumb = () => {
    return this.state.district && this.state.city.name === 'London'
  }

  loadData = (data) => {
    this.setState({
      addressLine1: data.addressLine1,
      locality: data.locality,
      postcode: data.postcode,
      latitude: data.latitude,
      longitude: data.longitude,
      tenancyReviews: data.tenancyReviews,
      city: data.city,
      district: data.district,
      loaded: true
    })

    document.title = data.addressLine1 + ' | ' + Constants.SITE_NAME
  }
}
