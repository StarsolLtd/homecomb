import React from 'react'
import { Container, Nav, Row } from 'reactstrap'

import '../../styles/how-it-works.scss'
import TextLogo from '../components/TextLogo'
import LogInOrOutNavLinks from './LogInOrOutNavLinks'
import { HashLink as Link } from 'react-router-hash-link'

const FooterLarge = (props) => {
  return (
    <Nav className="bg-gradient-primary text-white">
      <Container className="p-4 pb-5">
        <Row>
          <div className="col-lg-3 col-md-6 mb-4 mb-md-0">
            <h5><TextLogo className="logo-white" /></h5>

            <p>© 2021 <a className="text-white" href="http://starsol.com/">Starsol Ltd</a></p>
          </div>

          <div className="col-lg-3 col-md-6 mb-4 mb-md-0">
            <ul className="list-unstyled mb-0">
              <LogInOrOutNavLinks className="text-white" {...props } />
              {props.user && props.user.agencyAdmin &&
                <li><a href="/verified/dashboard" className="text-white agency-admin-link">Agency Admin</a></li>
              }
              {props.user && !props.user.agencyAdmin &&
                <li><a href="/verified/agency/create" className="text-white create-agency-link">Add Your Agency</a></li>
              }
            </ul>
          </div>

          <div className="col-lg-3 col-md-6 mb-4 mb-md-0">
            <ul className="list-unstyled mb-0">
              <li><Link to="/find-by-postcode#" className="text-white">Find by Postcode</Link></li>
            </ul>
          </div>

          <div className="col-lg-3 col-md-6 mb-4 mb-md-0">
            <ul className="list-unstyled mb-0">
              <li><Link to="/#" className="text-white">Search</Link></li>
              <li><Link to="/about#" className="text-white">About</Link></li>
              <li><Link to="/contact#" className="text-white">Contact Us</Link></li>
              <li><Link to="/privacy-policy#" className="text-white">Privacy Policy</Link></li>
              <li><Link to="/terms#" className="text-white">Terms and Conditions</Link></li>
            </ul>
          </div>
        </Row>
      </Container>
    </Nav>
  )
}

export default FooterLarge
