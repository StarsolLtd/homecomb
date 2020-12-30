import React, {Fragment} from "react";
import {Collapse, Container, Nav} from "reactstrap";

const LayoutFooter = (props) => {
    return (
        <Fragment>
            <Nav className="navbar navbar-expand-md navbar-light light-bronze">
                <Container>
                    <Collapse className="navbar-collapse" id="collapsibleFooterNavbar">
                        <ul className="navbar-nav">
                            {props.user &&
                            <li className="nav-item"><a href="/logout" className="nav-link">Log Out</a></li>
                            }
                            {!props.user &&
                            <li className="nav-item"><a href="/login" className="nav-link">Log In</a></li>
                            }
                        </ul>
                    </Collapse>

                    <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleFooterNavbar">
                        <span className="navbar-toggler-icon" />
                    </button>
                </Container>
            </Nav>
        </Fragment>
    );
}

export default LayoutFooter;