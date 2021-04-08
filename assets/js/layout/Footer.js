import React from "react";
import {Link} from "react-router-dom";
import {Button, Collapse, Container, Nav} from "reactstrap";
import LogInOrOutNavLinks from "./LogInOrOutNavLinks";

const Footer = (props) => {
    return (
        <Nav className="navbar navbar-expand-md navbar-light light-bronze">
            <Container>
                <Collapse className="navbar-collapse" id="collapsibleFooterNavbar">
                    <ul className="navbar-nav">
                        <LogInOrOutNavLinks {...props } />
                        {props.user && props.user.agencyAdmin &&
                        <li className="list-inline-item"><a href="/verified/dashboard" className="nav-link agency-admin-link">Agency Admin</a></li>
                        }
                        {props.user && !props.user.agencyAdmin &&
                        <li className="list-inline-item"><a href="/verified/agency/create" className="nav-link create-agency-link">Add Your Agency</a></li>
                        }
                        <li className="list-inline-item"><Link to="/" className="nav-link">Search</Link></li>
                        <li className="list-inline-item"><Link to="/about" className="nav-link about-link">About</Link></li>
                        <li className="list-inline-item"><Link to="/contact" className="nav-link">Contact Us</Link></li>
                        <li className="list-inline-item"><Link to="/privacy-policy" className="nav-link">Privacy Policy</Link></li>
                    </ul>
                </Collapse>

                <Button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleFooterNavbar">
                    <span className="navbar-toggler-icon" />
                </Button>
            </Container>
        </Nav>
    );
}

export default Footer;