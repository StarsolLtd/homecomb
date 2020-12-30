import React, {Fragment} from "react";
import {Link} from "react-router-dom";
import {Button, Collapse, Container, Nav} from "reactstrap";

const Footer = (props) => {
    return (
        <Nav className="navbar navbar-expand-md navbar-light light-bronze">
            <Container>
                <Collapse className="navbar-collapse" id="collapsibleFooterNavbar">
                    <ul className="navbar-nav">
                        {props.user &&
                        <li className="list-inline-item"><a href="/logout" className="nav-link">Log Out</a></li>
                        }
                        {!props.user &&
                        <li className="list-inline-item"><a href="/login" className="nav-link">Log In</a></li>
                        }
                        <li className="list-inline-item"><Link to="/" className="nav-link">Search</Link></li>
                        <li className="list-inline-item"><Link to="/about" className="nav-link">About</Link></li>
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