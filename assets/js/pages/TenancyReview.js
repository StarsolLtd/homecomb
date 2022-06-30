import React, { useEffect, useState } from 'react'
import { Container, Col, Row, Breadcrumb, BreadcrumbItem } from 'reactstrap'
import ReviewTenancyForm from '../components/ReviewTenancyForm'
import Constants from '../Constants'
import ReviewCompletedThankYou from '../content/ReviewCompletedThankYou'
import { HashLink as Link } from 'react-router-hash-link'

const TenancyReview = (props) => {
  const [showCompletedThankYou, setShowCompletedThankYou] = useState(false)

  useEffect(() => {
    document.title = Constants.SITE_NAME + ' | Review Tenancy'
  }, [])

  const completedThankYou = () => {
    setShowCompletedThankYou(true)
  }

  if (showCompletedThankYou) {
    return <ReviewCompletedThankYou />
  }

  return (
    <Container>
      <Row className="desktop-only">
        <Breadcrumb className="w-100">
          <BreadcrumbItem><Link to="/#">{Constants.SITE_NAME}</Link></BreadcrumbItem>
          <BreadcrumbItem className="active">Review your tenancy</BreadcrumbItem>
        </Breadcrumb>
      </Row>

      <Row>
        <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
          <p>
            Please complete the form below to submit your review:
          </p>

          <hr />

          <ReviewTenancyForm
            fixedBranch={false}
            fixedProperty={false}
            {...props}
            completedThankYou={completedThankYou}
          />
        </Col>
      </Row>
    </Container>
  )
}

export default TenancyReview
