import React, { useEffect } from 'react'
import { Container, Col, Row, Breadcrumb, BreadcrumbItem } from 'reactstrap'
import Constants from '../Constants'
import RegisterForm from '../components/RegisterForm'
import { HashLink as Link } from 'react-router-hash-link'

const Register = (props) => {
  useEffect(() => {
    document.title = Constants.SITE_NAME + ' | Register'
  })

  return (
    <Container>
      <Row className="desktop-only">
        <Breadcrumb className="w-100">
          <BreadcrumbItem><Link to="/#">{Constants.SITE_NAME}</Link></BreadcrumbItem>
          <BreadcrumbItem className="active">Register with {Constants.SITE_NAME}</BreadcrumbItem>
        </Breadcrumb>
      </Row>

      <Row>
        <Col md={12} className="bg-white rounded shadow-sm p-4 mb-4">
          <p>
            To create an account with us, please complete the form below:
          </p>

          <hr />

          <RegisterForm {...props} />
        </Col>
      </Row>
    </Container>
  )
}

export default Register
