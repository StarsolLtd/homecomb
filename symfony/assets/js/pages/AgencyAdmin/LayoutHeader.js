import React from "react";
import {Link} from "react-router-dom";
import {Button, Collapse, Container, Nav} from "reactstrap";

const LayoutHeader = (props) => {
    return (
        <Nav className="navbar navbar-expand-md navbar-light navbar-header light-bronze shadow">
            <Container>
                <a className="navbar-brand logo-sm" href="/">
                    <span className="red">Home</span><span className="bronze">Comb</span>
                </a>

                <span className="navbar-brand">
                    Agency Admin Area
                </span>

                <Button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                    <span className="navbar-toggler-icon" />
                </Button>

                <Collapse className="navbar-collapse" id="collapsibleNavbar">
                    {props.user.agencyAdmin &&
                        <ul className="navbar-nav">
                            <li className="nav-item">
                                <Link to="/verified/agency-admin" className="nav-link">Dashboard</Link>
                            </li>
                            <li className="nav-item">
                                <Link to="/verified/agency" className="nav-link">Your Agency</Link>
                            </li>
                            <li className="nav-item">
                                <Link to="/verified/request-review" className="nav-link">Request Review</Link>
                            </li>
                        </ul>
                    }
                </Collapse>
            </Container>
        </Nav>
    );
}

export default LayoutHeader;