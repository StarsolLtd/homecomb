import React from 'react'
import { Container, Nav, Row } from 'reactstrap'
import LogInOrOutNavLinks from '../../layout/LogInOrOutNavLinks'
import TextLogo from '../../components/TextLogo'

const LayoutFooter = (props) => {
  return (
    <Nav className="bg-gradient-primary text-white">
      <Container className="p-4 pb-5">
        <Row>
          <div className="col-lg-6 col-md-12 mb-4 mb-md-0">
            <h5><TextLogo className="logo-white" /></h5>

            <p>© 2021 <a className="text-white" href="http://starsol.com/">Starsol Ltd</a></p>
          </div>

          <div className="col-lg-3 col-md-6 mb-4 mb-md-0">
            <ul className="list-unstyled mb-0">
              <LogInOrOutNavLinks className="text-white" {...props } />
              <li><a href="/" className="text-white">Main Site</a></li>
            </ul>
          </div>

          <div className="col-lg-3 col-md-6 mb-4 mb-md-0">
            <ul className="list-unstyled mb-0">
              <li><a href="/contact" className="text-white">Contact Us</a></li>
              <li><a href="/privacy-policy" className="text-white">Privacy Policy</a></li>
              <li><a href="/terms" className="text-white">Terms and Conditions</a></li>
            </ul>
          </div>
        </Row>
      </Container>
    </Nav>
  )
}

export default LayoutFooter
