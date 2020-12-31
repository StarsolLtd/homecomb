import React from "react";
import {Link} from "react-router-dom";
import {Collapse, Container, Nav} from "reactstrap";
import TextLogo from "../../components/TextLogo";

const LayoutHeader = (props) => {
    return (
        <Nav className="navbar navbar-expand-md navbar-light navbar-header light-bronze shadow">
            <Container>
                <a className="navbar-brand font-weight-bold" href="/">
                    <TextLogo />
                </a>

                <span className="navbar-brand">
                    Agency Admin Area
                </span>

                <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                    <span className="navbar-toggler-icon" />
                </button>

                <Collapse className="navbar-collapse" id="collapsibleNavbar">
                    {props.user.agencyAdmin &&
                        <ul className="navbar-nav">
                            <li><Link to="/verified/dashboard">Dashboard</Link></li>
                            <li><Link to="/verified/agency">Your Agency</Link></li>
                            <li><Link to="/verified/request-review">Request Review</Link></li>
                        </ul>
                    }
                </Collapse>
            </Container>
        </Nav>
    );
}

export default LayoutHeader;