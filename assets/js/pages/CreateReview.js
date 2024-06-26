import React from 'react'
import { Container, Col, Row } from 'reactstrap'
import ReviewTenancyForm from '../components/ReviewTenancyForm'
import DataLoader from '../components/DataLoader'
import ReviewSolicitationNotFound from '../errors/ReviewSolicitationNotFound'
import Constants from '../Constants'
import ReviewCompletedThankYou from '../content/ReviewCompletedThankYou'

export default class CreateReview extends React.Component {
  state = {
    agency: null,
    branch: null,
    property: null,
    reviewerFirstName: '',
    reviewerLastName: '',
    reviewerEmail: '',
    loaded: false,
    completedThankYou: false
  }

  componentDidMount = () => {
    document.title = Constants.SITE_NAME + ' | Review Tenancy'
  }

  completedThankYou = () => {
    this.setState({ completedThankYou: true })
  }

  render () {
    if (this.state.completedThankYou) {
      return <ReviewCompletedThankYou />
    }

    return (
      <Container>
        <DataLoader
          url={'/api/rs/' + this.props.match.params.code}
          loadComponentData={this.loadData}
          customFileNotFound={ReviewSolicitationNotFound}
        />
        {this.state.loaded &&
          <>
            <Row>
              <Col md="12" className="page-title">
                <h1>Hello {this.state.reviewerFirstName}!</h1>
              </Col>
            </Row>

            <Row>
              <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
                <p>
                  {this.state.agency.name} has asked that you review your tenancy at {this.state.property.addressLine1}
                </p>

                <p>
                  Please complete the form below to submit your review:
                </p>

                <hr />

                <ReviewTenancyForm
                  code={this.props.match.params.code}
                  fixedBranch={true}
                  fixedProperty={true}
                  branch={this.state.branch}
                  agency={this.state.agency}
                  propertySlug={this.state.property.slug}
                  reviewerEmail={this.state.reviewerEmail}
                  reviewerName={this.state.reviewerFirstName + ' ' + this.state.reviewerLastName}
                  {...this.props}
                  completedThankYou={this.completedThankYou}
                />
              </Col>
            </Row>
          </>
        }
      </Container>
    )
  }

  loadData = (data) => {
    this.setState({
      agency: data.agency,
      branch: data.branch,
      property: data.property,
      reviewerFirstName: data.reviewerFirstName,
      reviewerLastName: data.reviewerLastName,
      reviewerEmail: data.reviewerEmail,
      loaded: true
    })
  }
}
