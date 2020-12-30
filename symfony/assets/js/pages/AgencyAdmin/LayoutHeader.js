import React, {Fragment} from "react";
import {Link} from "react-router-dom";
import {Col, Container, Row} from "reactstrap";

function LayoutHeader() {
    return (
        <Fragment>
            <nav className="navbar navbar-expand-md navbar-light light-bronze shadow">
                <Container>
                    <a className="navbar-brand logo-sm" href="/">
                        <span className="red">Home</span><span className="bronze">Comb</span>
                    </a>

                    <span className="navbar-brand">
                        Agency Admin Area
                    </span>

                    <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                        <span className="navbar-toggler-icon" />
                    </button>

                    <div className="collapse navbar-collapse" id="collapsibleNavbar">
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
                    </div>
                </Container>
            </nav>
        </Fragment>
    );
}

export default LayoutHeader;