import React, {Fragment} from "react";
import {Collapse, Container, Nav} from "reactstrap";
import LogInOrOutNavLinks from "../../layout/LogInOrOutNavLinks";

const LayoutFooter = (props) => {
    return (
        <Nav className="navbar navbar-expand-md navbar-light light-bronze">
            <Container>
                <Collapse className="navbar-collapse" id="collapsibleFooterNavbar">
                    <ul className="navbar-nav">
                        <LogInOrOutNavLinks {...props } />
                        <li className="list-inline-item"><a href="/" className="nav-link">Main Site</a></li>
                    </ul>
                </Collapse>

                <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleFooterNavbar">
                    <span className="navbar-toggler-icon" />
                </button>
            </Container>
        </Nav>
    );
}

export default LayoutFooter;